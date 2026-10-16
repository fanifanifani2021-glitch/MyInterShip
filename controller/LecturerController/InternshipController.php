<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

$token = bin2hex(random_bytes(16));
$_SESSION['PRINT_TOKEN'] = $token;

switch ($method):
    case 'internship_detail':
        try {
            // 1. Ambil data Internship (Otomatis mendapatkan akses ke Student & Company via Model)
            $internship = Internship::where('id_internship', $id_internship)
                ->where('nim', $nim)
                ->first();

            if (!$internship) {
                Notification::swal2('Error', 'Internship data not found.', 'error');
                Redirect::back(); exit;
            }

            // 2. Ambil data Mahasiswa (Otomatis dari relasi Internship)
            $student = $internship->student;

            // 3. Ambil data Surat (Gunakan Model jika sudah ada, jika belum gunakan DB::table)
            $letter = null;
            $letter_history = [];
            if (!empty($internship->id_internship_letter)) {
                $letter = DB::table('internship_letter')->where('id_internship_letter', $internship->id_internship_letter)->first();
                if ($letter) {
                    $letter_history = DB::table('internship_letter_history')
                        ->where('id_internship_letter', $internship->id_internship_letter)
                        ->orderBy('created_at', 'ASC')
                        ->get();
                }
            }

            // 4. Ambil Nama Dospem (Gunakan helper getDospem dari Model StudentInternship)
            $dospem = $student->getDospem();
            $dospemName = $dospem ? $dospem->name : 'Not Set';

            // 5. Ambil Supervisor Industri
            $supervisorName = 'Not Set';
            if (!empty($internship->id_user_company)) {
                $userCo = DB::table('user_company')->where('id_user_company', $internship->id_user_company)->first();
                $supervisorName = $userCo['user_fullname'] ?? 'Not Set';
            }

            // 6. Logika Akhir Magang & Status (Manfaatkan method di Model Internship)
            $is_end_phase = (date('Y-m-d') >= $internship->end_date);
            
            $statusLabels = [
                'Yes'    => ['label' => $internship->getStatusLabel('en'), 'accepted' => true, 'class' => 'badge-' . $internship->getStatusColor()],
                'Finish' => ['label' => $internship->getStatusLabel('en'), 'accepted' => true, 'class' => 'badge-' . $internship->getStatusColor()],
                'Waiting'=> ['label' => $internship->getStatusLabel('en'), 'accepted' => false, 'class' => 'badge-' . $internship->getStatusColor()],
                'No'     => ['label' => $internship->getStatusLabel('en'), 'accepted' => false, 'class' => 'badge-' . $internship->getStatusColor()],
            ];
            
            $currentStatus = $statusLabels[$internship->status] ?? ['label' => 'Unknown', 'accepted' => null, 'class' => 'badge-secondary'];

            // 7. Render ke View
            renderView('lecturer.internship_management.internship_detail', [
                'title'      => 'Internship Detail',
                'routes'     => 'intern_student_management',
                'internship' => $internship, // Sekarang berupa OBJECT
                'student'    => $student,    // Sekarang berupa OBJECT
                'dospem'     => $dospemName,
                'supervisor' => $supervisorName,
                'status'     => $currentStatus,
                'letter'     => $letter,
                'letter_history' => $letter_history,
                'is_end_phase' => $is_end_phase,
                'nim'        => $nim,
            ]);

        } catch (Exception $e) {
            Notification::swal2('Error', $e->getMessage(), 'error');
            Redirect::back(); exit;
        }
    break;


    case 'add_internship':
        try {
            // 1. Ambil data mahasiswa lengkap dengan nama prodi menggunakan join
            // Kita gunakan DB::table atau Query Builder agar lebih fleksibel melakukan join
            $student = DB::table('student_internship as si')
                ->select('si.*', 'ps.kode_prodi', 'ps.major', 'ps.study_program as prodi_name', 'ps.jenjang')
                ->leftJoin('program_study as ps', 'si.program_study', '=', 'ps.kode_prodi')
                ->where('si.nim', $nim)
                ->first();
            
            // 2. Cek jika mahasiswa tidak ditemukan
            if (!$student) { 
                Notification::swal2('Error', 'Data mahasiswa tidak ditemukan.', 'error');
                Redirect::back(100); 
                exit; 
            }

            // 3. Ambil daftar perusahaan untuk dropdown Select2
            $companies = DB::table('company')->orderBy('name', 'ASC')->get();

            // 4. Render ke view dengan data yang sudah lengkap
            renderView('lecturer.internship_management.add_internship', [
                'title'     => 'Add Internship Record',
                'routes'    => 'intern_student_management',
                'student'   => $student,
                'companies' => $companies,
                'nim'       => $nim // NIM ini biasanya masih dalam bentuk terenkripsi dari URL
            ]);

        } catch (Exception $e) {
            // Log error jika diperlukan: Logger::write('Error_Add_Internship', $e->getMessage());
            Redirect::back(100); 
            exit;
        }
    break;

    case 'store_internship':
        try {
            // Get data dari tabel student_profile
            $studentProfile = StudentProfile::where('nim', Request::post('nim'))->first();
            if ( !$studentProfile ) {
                $studentProfile = [];
            }

            $start_date = Request::post('start_date');
            $end_date   = Request::post('end_date');
            $period     = Request::post('internship_period');

            // LOGIKA FALLBACK: Jika period kosong, hitung manual dari start & end date
            if (empty($period) && !empty($start_date) && !empty($end_date)) {
                $start = new DateTime($start_date);
                $end   = new DateTime($end_date);
                
                // Hitung selisih
                $interval = $start->diff($end);
                
                // Rumus: (Tahun * 12) + Bulan
                $period = ($interval->y * 12) + $interval->m;

                // Opsional: Jika ada sisa hari (misal 1 hari pun), kita bulatkan ke atas jadi 1 bulan
                if ($interval->d > 0) {
                    $period++;
                }
                
                // Jika hasilnya masih 0 (misal input tanggal ngawur), set minimal 1
                $period = ($period <= 0) ? 1 : $period;
            }
            
            // 3. Siapkan data untuk Insert
            $data = [
                'nim'                 => Request::post('nim'), // Simpan NIM asli ke tabel
                'id_company'          => Request::post('id_company'),
                'internship_position' => Request::post('internship_position'),
                'start_date'          => Request::post('start_date'),
                'end_date'            => Request::post('end_date'),
                'internship_period'   => $period,
                'file_1'              => $studentProfile['talenthub_cv'] ?? '',
                'file_2'              => $studentProfile['other_cv'] ?? '',
                'file_3'              => $studentProfile['academic_transcript'] ?? '',
                'nik'                 => $_SESSION['nim_nik_unit'],

                'date_finalreport'   => '',
                'final_report'   => '',
                'id_user_company'     => '',
                'status'              => 'Yes', // Otomatis aktif

                'timestamp_register'  => date('Y-m-d H:i:s')
            ];

            // 4. Eksekusi ke Database
            DB::table('internship')->insert($data);

            // 5. Notifikasi Sukses
            Notification::swal2('Success', 'Internship record has been registered successfully.', 'success');
            
            // 6. Redirect kembali ke detail mahasiswa menggunakan NIM TERENKRIPSI
            // Gunakan router yang sesuai dengan rute detail mahasiswa kamu
            Redirect::to(router('lecturer/my/student.detail', ['nim' => Request::post('nim')]), 100); 
            exit;

        } catch (Exception $e) {
            // Notifikasi Error
            Notification::swal2('Error', 'Failed to save record: ' . $e->getMessage(), 'error');
            Redirect::back(100); 
            exit;
        }
    break;


    case 'edit_internship':
        try {
            // 1. Ambil data mahasiswa (sama seperti add)
            $student = DB::table('student_internship as si')
                ->select('si.*', 'ps.kode_prodi', 'ps.major', 'ps.study_program as prodi_name', 'ps.jenjang')
                ->leftJoin('program_study as ps', 'si.program_study', '=', 'ps.kode_prodi')
                ->where('si.nim', $nim)
                ->first();

            // 2. Ambil data Internship yang akan diedit
            $internship = DB::table('internship')->where('id_internship', $id_internship)->first();

            if (!$student || !$internship) {
                Notification::swal2('Error', 'Data not found.', 'error');
                Redirect::back(100); exit;
            }

            $companies = DB::table('company')->orderBy('name', 'ASC')->get();

            renderView('lecturer.internship_management.edit_internship', [
                'title'      => 'Edit Internship Record',
                'routes'     => 'intern_student_management',
                'student'    => $student,
                'internship' => $internship,
                'companies'  => $companies,
                'nim'        => $nim,
                'id_internship' => $id_internship
            ]);
        } catch (Exception $e) {
            Redirect::back(100); exit;
        }
    break;

    case 'update_internship':
        try {
            $start_date = Request::post('start_date');
            $end_date   = Request::post('end_date');
            $period     = Request::post('internship_period');

            // Fallback hitung period jika kosong (logika yang kita bahas sebelumnya)
            if (empty($period) && !empty($start_date) && !empty($end_date)) {
                $start = new DateTime($start_date);
                $end   = new DateTime($end_date);
                $interval = $start->diff($end);
                $period = ($interval->y * 12) + $interval->m;
                if ($interval->d > 0) $period++;
                $period = ($period <= 0) ? 1 : $period;
            }

            $data = [
                'id_company'          => Request::post('id_company'),
                'internship_position' => Request::post('internship_position'),
                'start_date'          => $start_date,
                'end_date'            => $end_date,
                'internship_period'   => $period,
                'status'              => Request::post('status'),
            ];

            DB::table('internship')->where('id_internship', $id_internship)->update($data);
            
            Notification::swal2('Success', 'Internship record updated successfully.', 'success');
            Redirect::to(router('lecturer/my/student.internship.detail', ['nim' => $nim, 'id_internship' => $id_internship]), 100);
            exit;
        } catch (Exception $e) {
            Notification::swal2('Error', $e->getMessage(), 'error');
            Redirect::back(100); exit;
        }
    break;

endswitch;