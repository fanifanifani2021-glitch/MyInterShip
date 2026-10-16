<?php

$pdo = $_SERVER['PDO'];

switch ($method):

    default:
        http_response_code(404);
    break;

    case 'index':
		renderView('lecturer.internship_management.index', ['title' => 'My Student', 'routes' => 'intern_student_management']);
    break;


    case 'data':
        try {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $pdo = $_SERVER['PDO'];
        $nik_dosen = $_SESSION['nim_nik_unit'] ?? $_SESSION['Lecturer-Data']['nim_nik_unit'];

        $columns = [
            0 => 'si.nim',
            1 => 'si.name',
            2 => 'ps.study_program',
            3 => 'co.name',
            4 => 'i.status',
        ];

        // 2. BASE QUERY DENGAN SUBQUERY UNTUK MENGAMBIL MAGANG TERBARU
        // Kita join ke tabel internship hanya untuk record TERBARU (MAX id) dari status yang diinginkan
        $sql_base = "
            SELECT 
                si.nim, 
                si.name AS student_name,
                SUBSTRING(si.nim, 4, 2) AS generation,
                ps.jenjang, 
                ps.study_program AS prodi,
                ps.kode_prodi,
                i.id_internship,
                i.status,
                i.start_date,
                i.end_date,
                co.name AS company_name
            FROM student_internship si
            JOIN program_study ps ON si.program_study = ps.kode_prodi
            LEFT JOIN (
                -- Subquery: Ambil ID magang terakhir untuk setiap NIM yang statusnya valid
                SELECT nim, MAX(id_internship) as latest_id
                FROM internship
                WHERE status IN ('Yes', 'Finish', 'Waiting')
                GROUP BY nim
            ) i_latest ON si.nim = i_latest.nim
            LEFT JOIN internship i ON i_latest.latest_id = i.id_internship
            LEFT JOIN company co ON i.id_company = co.id_company
            WHERE si.nik_dospem = :nik_dosen
        ";

        $params = [':nik_dosen' => $nik_dosen];

        // 3. DYNAMIC FILTERING
        if (!empty($_POST['study_program']) && $_POST['study_program'] != 'All') {
            $val = $_POST['study_program'];
            if (strpos($val, 'All-') === 0) {
                $majorName = str_replace('All-', '', $val);
                $sql_base .= " AND ps.major = :major";
                $params[':major'] = $majorName;
            } else {
                $sql_base .= " AND ps.kode_prodi = :kode_prodi";
                $params[':kode_prodi'] = $val;
            }
        }

        if (!empty($_POST['generation']) && $_POST['generation'] != 'All') {
            $sql_base .= " AND SUBSTRING(si.nim, 4, 2) = :generation";
            $params[':generation'] = $_POST['generation'];
        }

        if (!empty($_POST['internship_status']) && $_POST['internship_status'] != 'All') {
            $statusFilter = $_POST['internship_status'];
            if ($statusFilter == 'Not Applied Yet') {
                $sql_base .= " AND i.id_internship IS NULL";
            } else {
                $statusMap = [
                    'Waiting for Validation' => 'Waiting',
                    'Internship in Progress' => 'Yes',
                    'Internship Completed'   => 'Finish',
                    'Internship Rejected'    => 'No'
                ];
                $dbStatus = $statusMap[$statusFilter] ?? $statusFilter;
                $sql_base .= " AND i.status = :status";
                $params[':status'] = $dbStatus;
            }
        }

        if (!empty($_POST['from_date'])) {
            $sql_base .= " AND i.start_date >= :from_date";
            $params[':from_date'] = $_POST['from_date'];
        }
        if (!empty($_POST['to_date'])) {
            $sql_base .= " AND i.start_date <= :to_date";
            $params[':to_date'] = $_POST['to_date'];
        }

        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $sql_base .= " AND (si.name LIKE :search OR si.nim LIKE :search OR co.name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // 4. Hitung Total Data (Filtered)
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM ($sql_base) AS count_table");
        $stmtCount->execute($params);
        $totalFiltered = $stmtCount->fetchColumn();
        $totalData = $totalFiltered;

        // 5. Ordering & Pagination
        if (isset($_POST['order'])) {
            $colIdx = $_POST['order'][0]['column'];
            $colDir = $_POST['order'][0]['dir'];
            $colName = $columns[$colIdx] ?? 'si.name';
            $sql_base .= " ORDER BY $colName $colDir";
        } else {
            $sql_base .= " ORDER BY si.name ASC";
        }

        if (isset($_POST['start']) && $_POST['length'] != -1) {
            $sql_base .= " LIMIT " . (int)$_POST['start'] . ", " . (int)$_POST['length'];
        }

        // 6. Eksekusi Query
        $stmt = $pdo->prepare($sql_base);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $dataRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 7. Formatting JSON
        $data = [];
        foreach ($dataRaw as $row) {
            $period_string = '';
            if ($row['start_date'] && $row['end_date'] && $row['start_date'] != '0000-00-00') {
                $start = date('d M Y', strtotime($row['start_date']));
                $end   = date('d M Y', strtotime($row['end_date']));
                $period_string = "$start - $end";
            }

            // Enkripsi NIM untuk URL Detail
            $encNim = Encrypter::encrypt($row['nim']);

            $data[] = [
                'nim'           => $row['nim'],
                'student_name'  => $row['student_name'],
                'jenjang'       => $row['jenjang'],
                'prodi'         => $row['prodi']. " ({$row['kode_prodi']})",
                'company_name'  => $row['company_name'], 
                'period'        => $period_string,
                'id_internship' => $row['id_internship'],
                'status'        => $row['status'],
                'detail_url'    => router('lecturer/my/student.detail', ['nim' => $row['nim']]),
                'enc_nim'       => $encNim, // Kirim NIM asli untuk keperluan lain (misal: reset password)
                'action'       => ''
            ];
        }

        echo json_encode([
            "draw"            => intval($_POST['draw'] ?? 1),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ]);
        exit;

    } catch (Exception $e) {
            if (class_exists('Logger')) {
                Logger::write('Error_MyStudent_Lecturer', $e->getMessage());
            }
            
            echo json_encode([
                "draw"            => intval($_POST['draw'] ?? 1),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => [],
                "error"           => "Server Error: " . $e->getMessage()
            ]);
            exit;
        }
    break;

    case 'add':
        $id_kampus = $_SESSION['Lecturer-Data']['id_kampus'];

        // 1. Ambil data mahasiswa (Tetap sama)
        $student_data = DB::table('student_internship si')
            ->select('si.*', 'ps.jenjang', 'ps.study_program')
            ->join('program_study ps', 'ps.kode_prodi', '=', 'si.program_study', 'LEFT')
            ->where('si.id_kampus', $id_kampus)
            ->get();

        $groupedStudents = [];
        foreach ($student_data as $student) {
            $programStudy = ($student['jenjang'] ?? '') . ' ' . ($student['study_program'] ?? 'Unknown');
            $groupedStudents[$programStudy][] = $student;
        }

        // 2. REVISI: Ambil & Grouping Prodi berdasarkan Jurusan
        $prodi_raw = DB::table('program_study')->where('id_kampus', $id_kampus)->orderBy('jurusan', 'ASC')->orderBy('study_program', 'ASC')->get();
        
        $groupedProdis = [];
        foreach ($prodi_raw as $p) {
            $jurusanName = $p['jurusan'] ?? 'Lainnya';
            $groupedProdis[$jurusanName][] = $p;
        }

        renderView('lecturer.internship_management.add', [
            'title'           => 'Add Supervised Student',
            'routes'          => 'intern_student_management',
            'groupedStudents' => $groupedStudents,
            'groupedProdis'   => $groupedProdis // Kirim data yang sudah di-group
        ]);
    break;

    case 'details':
        try {
            // 1. Ambil NIM dari POST
            $nim = Request::post('studentNIM');

            if (empty($nim)) {
                echo json_encode(['error' => 'NIM is required.']);
                exit;
            }

            /**
             * 2. Cari data mahasiswa
             * Tetap gunakan SQL JOIN untuk mendapatkan data Program Study (Jenjang & Nama Prodi)
             */
            $sql = "SELECT si.*, ps.jenjang, ps.study_program 
                    FROM student_internship si
                    LEFT JOIN program_study ps ON ps.kode_prodi = si.program_study
                    WHERE si.nim = :nim";
            
            $stmt = $_SERVER['PDO']->prepare($sql);
            $stmt->execute(['nim' => $nim]);
            $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

            // 3. Validasi Keberadaan Data
            if (!$studentData) {
                echo json_encode(['error' => 'Student not found in database.']);
                exit;
            }

            // 4. Keamanan: Cek apakah mahasiswa ini satu kampus dengan Dosen
            if ($studentData['id_kampus'] != $_SESSION['Lecturer-Data']['id_kampus']) {
                echo json_encode(['error' => 'Access Denied: Student belongs to another campus.']);
                exit;
            }

            /**
             * 5. Integrasi dengan Model untuk Foto Profil
             * Kita masukkan array $studentData ke dalam Model StudentInternship
             */
            $studentModel = new StudentInternship($studentData);
            $studentData['avatar_url'] = $studentModel->getAvatarUrl();

            /**
             * 6. Cek Info Dosen Pembimbing (Eksisting)
             */
            if (!empty($studentData['nik_dospem'])) {
                $stmtDosen = $_SERVER['PDO']->prepare("SELECT name FROM lecturer WHERE nim_nik_unit = :nik");
                $stmtDosen->execute(['nik' => $studentData['nik_dospem']]);
                $dosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);
                
                $studentData['lecturer_name'] = $dosen['name'] ?? 'Unknown Lecturer';
            } else {
                $studentData['lecturer_name'] = 'No supervisor assigned';
                $studentData['nik_dospem'] = '-';
            }

            // 7. Return Data dalam format JSON
            // Pastikan header dikirim sebagai JSON
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data'   => $studentData
            ]);
            exit;

        } catch (Exception $e) {
            Router::log('AJAX_Details_Error', ['message' => $e->getMessage()]);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);
            exit;
        }
    break;

    case 'store':  
        try {
            // 1. Validasi Input Dasar
            $rules = [
                'nim'       => 'required',
                'name'      => 'required',
                'nik_dosen' => 'required',
                'mode'      => 'required'
            ];

            // Jika mode manual, program_study wajib diisi dari dropdown
            if (Request::post('mode') == 'manual') {
                $rules['program_study'] = 'required';
            }

            $validation = Validator::make($_POST, $rules);

            if ($validation->fails()) {
                Notification::swal2('Validation Error', $validation->firstError(), 'error');
                Redirect::back(100); 
                exit;
            }

            // 2. Ambil Data
            $nim      = Request::post('nim');
            $name     = Request::post('name');
            $nikDosen = Request::post('nik_dosen');
            $mode     = Request::post('mode');
            $idKampus = $_SESSION['Lecturer-Data']['id_kampus'];

            // 3. Cek apakah mahasiswa sudah ada di database
            $student = StudentInternship::where('nim', $nim)->first();

            if ($student) {
                // --- LOGIKA UPDATE ---
                // Jika mahasiswa sudah ada, kita update Dosen Pembimbingnya
                $process = StudentInternship::where('nim', $nim)->update([
                    'nik_dospem' => $nikDosen,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                // Note: Jika dospemnya sama, $process akan bernilai 0. 
                // Kita cek apakah dospem lama memang sudah sama dengan dospem baru.
                if ($process || $student['nik_dospem'] == $nikDosen) {
                    $status = ($student['nik_dospem'] == $nikDosen) ? 'info' : 'success';
                    $msg    = ($student['nik_dospem'] == $nikDosen) 
                            ? 'This student is already supervised by you.' 
                            : 'Supervisor updated successfully.';
                    
                    Notification::swal2(ucfirst($status), $msg, $status);
                }
            } else {
                // --- LOGIKA INSERT (Untuk Manual Mode) ---
                // Jika mahasiswa belum ada, kita buat record baru
                $process = StudentInternship::create([
                    'nim'           => $nim,
                    'name'          => $name,
                    'email'         => '-','other_email'   => '-',
                    'phone'         => '-','no_whatsapp'   => '-',
                    'program_study' => Request::post('program_study'),
                    'id_kampus'     => $idKampus,
                    'nik_dospem'    => $nikDosen,
                    'password'      => password_hash( $nim, PASSWORD_DEFAULT), // Set password default (bisa di-reset oleh mahasiswa)
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);

                if ($process) {
                    Notification::swal2('Success', 'New student registered and assigned to you.', 'success');
                }
            }


            if ($process) {
                Redirect::to(router('lecturer/my/student.index'), 100);
            } else {
                Notification::swal2('No Changes', 'Process could not be completed or no data changed.', 'info');
                Redirect::back(100);
            }
            exit;

        } catch (Exception $e) {
            Router::log('Store_Error', ['message' => $e->getMessage()]);
            Notification::swal2('Error', 'An internal error occurred.', 'error');
            Redirect::back(100);
            exit;
        }
    break;

    case 'detail':
        try {
            $nik_dosen = $_SESSION['nim_nik_unit'] ?? $_SESSION['Lecturer-Data']['nim_nik_unit'];

            $student = StudentInternship::query()
                ->select('student_internship.*', 'ps.prodi AS program_study_name', 'ps.kode_prodi')
                ->leftJoin('program_study ps', 'ps.kode_prodi', '=', 'student_internship.program_study')
                ->where('student_internship.nim', $nim)
                ->where('student_internship.nik_dospem', $nik_dosen)
                ->first();

            if (!$student) {
                Notification::swal2('Error', 'Student not found.', 'error');
                Redirect::back(1); exit;
            }

            // AMBIL DATA PROFIL (CV & TRANSKRIP)
            $profile = $student->profile();

            $history = DB::table('internship i')
                ->select('i.*', 'co.name AS company_name')
                ->join('company co', 'co.id_company', '=', 'i.id_company')
                ->where('i.nim', $nim)
                ->orderByRaw("CASE WHEN i.status = 'Yes' THEN 1 WHEN i.status = 'Finish' THEN 2 ELSE 3 END ASC")
                ->orderBy('i.timestamp_register', 'DESC')
                ->get();

            renderView('lecturer.internship_management.detail', [
                'title'   => 'Detail ' . $student->name,
                'routes'  => 'intern_student_management',
                'student' => $student,
                'profile' => $profile, // Kirim ke view
                'history' => $history
            ]);

        } catch (Exception $e) {
            Router::log('Detail_Error', ['msg' => $e->getMessage()]);
            die("Error processing detail.");
        }
    break;


    case 'reset_password':
        try {
            $encryptedNim = Request::post('nim');
            $nim = Encrypter::decrypt($encryptedNim);

            if (!$nim) throw new Exception("Invalid Student ID");

            $update = StudentInternship::where('nim', $nim)->update([
                'password'   => password_hash($nim, PASSWORD_DEFAULT),
                'password_reset' => true,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // LOGIKA BARU: Cek apakah request AJAX
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status'  => 'success',
                    'message' => 'Password for ' . $nim . ' has been reset.'
                ]);
                exit; // Matikan eksekusi agar tidak lari ke Redirect
            }

            // Untuk request biasa (non-AJAX)
            Notification::swal2('Success', 'Password reset successfully.', 'success');
            Redirect::back(100); exit;

        } catch (Exception $e) {
            if ($isAjax) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit;
            }
            Notification::swal2('Error', $e->getMessage(), 'error');
            Redirect::back(100); exit;
        }
    break;

    case 'cancel_supervision':
        try {
            $encryptedNim = Request::post('nim');
            $nim = Encrypter::decrypt($encryptedNim);
            // Set nik_dospem jadi NULL
            $update = StudentInternship::where('nim', $nim)->update([
                'nik_dospem' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($update) {
                Notification::swal2('Success', 'You are no longer supervising this student.', 'success');
                Redirect::to(router('lecturer/my/student.index'), 100); // Tendang ke hal depan karena sudah bukan bimbingan
                exit;
            }
            Redirect::back(100); exit;
        } catch (Exception $e) {
            Notification::swal2('Error', 'Operation failed.', 'error');
            Redirect::back(100); exit;
        }
    break;


endswitch;