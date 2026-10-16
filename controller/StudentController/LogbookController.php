<?php

try {
    // 1. Dekripsi ID Internship (Selalu dibutuhkan di setiap case)
    $id_internship = Encrypter::decrypt($id);
    $nim = Session::get('nim');

    // 2. Keamanan: Cek apakah Internship ini memang milik Mahasiswa ybs
    $internship = DB::table('internship')
        ->where('id_internship', '=', $id_internship)
        ->where('nim', '=', $nim)
        ->first();

    if (!$internship) {
        Notification::swal2('error', 'Not Allowed', 'Internship data not found!');
        Response::redirect(router('student/internship.index'))->send();
        exit;
    }

} catch (Exception $e) {
    Notification::swal2('Security Warning', 'Invalid Access Identifier.', 'warning');
    Response::redirect(router('student.internship'))->send();
    exit;
}

switch ($method):
    case 'index':
        $rawId = $id;
        try {
            $id_internship = Encrypter::decrypt($rawId);
        } catch (Exception $e) {
            $id_internship = base64_decode($rawId);
        }

        $nim = Session::get('nim');
        $internship = DB::table('internship')->where('id_internship', $id_internship)->where('nim', $nim)->first();

        // Query Logbook & Stats
        $logbooks = DB::table('logbook')
            ->where('id_internship', $id_internship)
            ->orderBy('week_num', 'DESC')
            ->get();

        $start = new DateTime($internship['start_date']);
        $end = new DateTime($internship['end_date']);
        $totalWeeks = ceil($start->diff($end)->days / 7);
        $filledWeeks = count($logbooks);


        $startDate = new DateTime($internship['start_date']);
        $endDate = new DateTime($internship['end_date']);
        $totalWeeks = ceil($startDate->diff($endDate)->days / 7);

        // Generate Daftar Referensi Minggu
        $weekSchedule = [];
        $tempStart = clone $startDate;
        
        for ($i = 1; $i <= $totalWeeks; $i++) {
            $tempEnd = clone $tempStart;
            $tempEnd->modify('+6 days');
            
            // Cek apakah mahasiswa sudah isi minggu ini
            $isFilled = collect($logbooks)->contains('week_num', $i);

            $weekSchedule[] = [
                'week'  => $i,
                'start' => $tempStart->format('d M Y'),
                'end'   => $tempEnd->format('d M Y'),
                'filled'=> $isFilled
            ];
            
            $tempStart->modify('+7 days');
        }

        return renderView('student.my_internship.logbook.index', [
            'title'         => 'Logbook',
            'routes'        => 'my_internship',
            'internship' => $internship,
            'exec'          => $logbooks,
            'id_plain'      => $id_internship, // ID Integer asli
            'id_enc'        => $rawId,        // ID terenkripsi dari URL
            'stats' => [
                'total'      => $totalWeeks,
                'filled'     => $filledWeeks,
                'percentage' => round(($totalWeeks > 0 ? ($filledWeeks / $totalWeeks) * 100 : 0), 2)
            ],
            'weekSchedule' => $weekSchedule,
        ]);
    break;

    case 'add':
        $rawId = $id; // Menggunakan $id sesuai parameter router kamu
        try {
            $id_internship = Encrypter::decrypt($rawId);
        } catch (Exception $e) {
            $id_internship = base64_decode($rawId);
        }
        
        $nim = Session::get('nim');

        // 1. Ambil data internship
        $internship = DB::table('internship')
            ->where('id_internship', $id_internship)
            ->where('nim', $nim)
            ->first();

        // 2. Hitung minggu terakhir yang sudah diisi
        $last_logbook = DB::table('logbook')
            ->where('id_internship', $id_internship)
            ->orderBy('week_num', 'DESC')
            ->first();

        $next_week_num = ($last_logbook) ? (int)$last_logbook['week_num'] + 1 : 1;

        // 3. Generate Schedule untuk Auto-fill
        $startDate = new DateTime($internship['start_date']);
        $endDate = new DateTime($internship['end_date']);
        $totalWeeks = ceil($startDate->diff($endDate)->days / 7);
        
        $weekSchedule = [];
        $tempStart = clone $startDate;
        for ($i = 1; $i <= $totalWeeks; $i++) {
            $tempEnd = clone $tempStart;
            $tempEnd->modify('+6 days');
            
            $isFilled = DB::table('logbook')
                ->where('id_internship', $id_internship)
                ->where('week_num', $i)
                ->exists();

            $weekSchedule[] = [
                'week'  => $i,
                'start' => $tempStart->format('Y-m-d'),
                'end'   => $tempEnd->format('Y-m-d'),
                'filled'=> $isFilled
            ];
            $tempStart->modify('+7 days');
        }

        return renderView('student.my_internship.logbook.add', [
            'title'         => 'Add New Logbook',
            'routes'        => 'my_internship', // Sidebar Active
            'id_plain'      => $id_internship,
            'id_enc'        => $rawId,
            'last_week'     => $next_week_num,
            'weekSchedule'  => $weekSchedule,
            'internship'    => $internship
        ]);
    break;

    case 'store':
        // 1. Jalankan Validasi menggunakan class Validator
        set_rules('week_num', 'Week Number', ['required']);
        set_rules('start_date', 'Start Date', ['required', 'valid_date']);
        set_rules('end_date', 'End Date', ['required', 'valid_date']);
        set_rules('description', 'Description', ['required']);
        set_rules('documentation', 'Documentation', ['required']);

        if (form_validation() === false) {
            make_swal2('error', 'Validation Error', 'Please fill all required columns! (00)');
            Redirect::back(1);
            exit;
        }

        // 2. Ambil data melalui class Request
        $week_num = Request::integer('week_num');
        $start_date = Request::string('start_date');
        $end_date = Request::string('end_date');
        $description = Request::input('description');
        $documentation = Request::input('documentation');

        // 3. Pengecekan Duplikasi (Cek apakah minggu tersebut sudah pernah diisi)
        $isDuplicate = DB::table('logbook')
            ->where('id_internship', '=', $id_internship)
            ->where('week_num', '=', $week_num)
            ->exists();

        if ($isDuplicate) {
            make_swal2('error', 'Duplicate Entry', "Logbook for week {$week_num} has already been added! (01)");
            Redirect::back(1);
            exit;
        }

        // 4. Proses Insert Data menggunakan Query Builder
        $logbook_data = [
            'id_logbook'    => NULL,
            'id_internship' => $id_internship,
            'nim'           => $nim,
            'description'   => $description,
            'documentation' => $documentation,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'week_num'      => $week_num,
            'approval_spv'  => 'Waiting',
            'approval_dpm'  => 'Waiting',
        ];

        try {
            $insert = DB::table('logbook')->insert($logbook_data);

            if ($insert) {
                make_swal2('success', 'Success', 'Logbook data has been added successfully!');
                // Redirect ke halaman index logbook
                Response::redirect(router('student.logbook.index', ['id' => $id]))->send();
                exit;
            } else {
                throw new Exception("Database insert operation returned false.");
            }

        } catch (Exception $e) {
            // Log error untuk kebutuhan debugging NIM tertentu
            if ($nim == '3311801013') {
                var_dump($e->getMessage());
                exit;
            }

            dump_to_file('logbook_error_log', $nim . ' => ' . $e->getMessage() . "\n");
            make_swal2('error', 'System Error', 'Failed to add logbook, please try again later! (02)');
            Redirect::back(1);
            exit;
        }
    break;

    case 'print':
        try {
            $id_logbook = Encrypter::decrypt($logbook_id);
            
            // Ambil logbook dan join data internship + perusahaan
            $data = DB::table('logbook as l')
                ->join('internship as i', 'l.id_internship', '=', 'i.id_internship')
                ->where('l.id_logbook', $id_logbook)
                ->first();

            return renderView('student.my_internship.logbook.print', [
                'title' => "Print Week " . $data['week_num'],
                'data'  => $data
            ]);
        } catch (Exception $e) {
            redirect_back();
        }
    break;

        
    case 'edit':
    case 'detail':
        try {
            $id_logbook = Encrypter::decrypt($logbook_id);
            $nim = Session::get('nim');
            
            $data_logbook = DB::table('logbook')
                ->where('id_internship', '=', $id_internship)
                ->where('id_logbook', '=', $id_logbook)
                ->first();

            if (!$data_logbook) {
                Notification::swal2('error', 'Error', 'Logbook not found.');
                redirect_back(); exit;
            }

            // Ambil data internship untuk hitung jadwal mingguan
            $internship = DB::table('internship')->where('id_internship', $id_internship)->first();
            
            $startDate = new DateTime($internship['start_date']);
            $endDate = new DateTime($internship['end_date']);
            $totalWeeks = ceil($startDate->diff($endDate)->days / 7);
            
            $weekSchedule = [];
            $tempStart = clone $startDate;
            for ($i = 1; $i <= $totalWeeks; $i++) {
                $tempEnd = clone $tempStart;
                $tempEnd->modify('+6 days');
                
                // Cek apakah minggu ini diisi di entry LAIN (bukan yang sedang diedit)
                $isFilledOther = DB::table('logbook')
                    ->where('id_internship', $id_internship)
                    ->where('week_num', $i)
                    ->where('id_logbook', '!=', $id_logbook)
                    ->exists();

                $weekSchedule[] = [
                    'week'     => $i,
                    'start'    => $tempStart->format('Y-m-d'),
                    'end'      => $tempEnd->format('Y-m-d'),
                    'isFilled' => $isFilledOther,
                    'isCurrent'=> ($i == $data_logbook['week_num'])
                ];
                $tempStart->modify('+7 days');
            }

            $viewPath = ($method === 'edit') ? 'student.my_internship.logbook.edit' : 'student.my_internship.logbook.detail';
            $title = ($method === 'edit') ? 'Edit Logbook' : 'Logbook Detail';

            return renderView($viewPath, [
                'title'         => $title,
                'routes'        => 'my_internship',
                'id_plain'      => $id_internship,
                'id_enc'        => $id,

                'logbook_id'   => $logbook_id,
                'data'          => $data_logbook,
                'weekSchedule'  => $weekSchedule,
                'last_week'     => $data_logbook['week_num'] // Untuk auto-select awal
            ]);

        } catch (Exception $e) {
            Notification::swal2('error', 'Security Error', 'Invalid Logbook ID.');
            redirect_back(); exit;
        }
    break;

    case 'update':
        // 1. Jalankan Validasi menggunakan class Validator
        set_rules('week_num', 'Week Number', ['required']);
        set_rules('old_week', 'Old Week', ['required']);
        set_rules('start_date', 'Start Date', ['required', 'valid_date']);
        set_rules('end_date', 'End Date', ['required', 'valid_date']);
        set_rules('description', 'Description', ['required']);
        set_rules('documentation', 'Documentation', ['required']);

        if (form_validation() === false) {
            make_swal2('error', 'Validation Error', 'Please check your submitted data and try again! (00)');
            Redirect::back(1);
            exit;
        }

        try {
            // 2. Dekripsi ID Logbook dan ambil data input
            $id_logbook = Encrypter::decrypt($logbook_id);
            $new_week = Request::integer('week_num');
            $old_week = Request::integer('old_week');

            // 3. Pengecekan Duplikasi jika nomor minggu diubah
            if ($old_week !== $new_week) {
                $isDuplicate = DB::table('logbook')
                    ->where('id_internship', '=', $id_internship)
                    ->where('week_num', '=', $new_week)
                    ->where('id_logbook', '!=', $id_logbook)
                    ->exists();

                if ($isDuplicate) {
                    make_swal2('error', 'Duplicate Entry', "Logbook for week {$new_week} already exists! (01)");
                    Redirect::back(1);
                    exit;
                }
            }

            // 4. Siapkan Data untuk Update
            $logbook_data = [
                'description'   => Request::input('description'),
                'documentation' => Request::input('documentation'),
                'start_date'    => Request::string('start_date'),
                'end_date'      => Request::string('end_date'),
                'week_num'      => $new_week,
                'approval_spv'  => 'Waiting', // Reset status saat diedit
                'approval_dpm'  => 'Waiting', // Reset status saat diedit
            ];

            // 5. Eksekusi Update ke Database
            $update = DB::table('logbook')
                ->where('id_logbook', '=', $id_logbook)
                ->where('id_internship', '=', $id_internship) // Tambahan keamanan
                ->update($logbook_data);

            if ($update) {
                make_swal2('success', 'Success', 'Logbook report has been updated successfully!');
                Response::redirect(router('student.logbook.index', ['id' => $id]))->send();
                exit;
            } else {
                // Jika tidak ada perubahan data, tetap dianggap sukses atau berikan info
                make_swal2('info', 'No Changes', 'No data was modified or updated.');
                Response::redirect(router('student.logbook.index', ['id' => $id]))->send();
                exit;
            }

        } catch (Exception $e) {
            // Log error untuk kebutuhan debugging
            dump_to_file('logbook_update_error', $nim . ' => ' . $e->getMessage() . "\n");
            make_swal2('error', 'System Error', 'Failed to update logbook report. Please try again! (02)');
            Redirect::back(1);
            exit;
        }
    break;


    case 'delete':
        try {
            $id_logbook = Encrypter::decrypt($logbook_id);
            $id_internship_dec = Encrypter::decrypt($id); // ID dari prefix rute
            
            // 1. Cari data dan pastikan milik mahasiswa yang bersangkutan
            $logbook = DB::table('logbook')
                ->where('id_logbook', $id_logbook)
                ->where('id_internship', $id_internship_dec)
                ->first();

            if (!$logbook) {
                make_swal2('error', 'Failed', 'Data not found.');
                Redirect::to(router('student.logbook.index', ['id' => $id]), 10);
                exit;
            }

            // 2. Opsional: Cegah hapus jika sudah disetujui
            if (strtolower($logbook['approval_spv']) == 'yes' || strtolower($logbook['approval_dpm']) == 'yes') {
                make_swal2('warning', 'Restricted', 'Cannot delete approved reports.');
                Redirect::to(router('student.logbook.index', ['id' => $id]), 10);
                exit;
            }

            // 3. Proses Hapus
            DB::table('logbook')->where('id_logbook', $id_logbook)->delete();

            Notification::swal2('Deleted!', 'Weekly report has been removed.', 'success');
            Redirect::to(router('student.logbook.index', ['id' => $id]), 10);
            exit;
        } catch (Exception $e) {
            make_swal2('error', 'Error', 'Invalid security token.');
            Redirect::to(router('student.logbook.index', ['id' => $id]), 10); exit;
        }
    break;

    default:
        view('exception/error_404'); exit;
        break;
endswitch;