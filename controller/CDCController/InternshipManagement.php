<?php
defined('BASE_PATH') or die('Direct access not allowed');
switch ($method) :

    default:
        Router::handleError(404, 'Method not found');
    break;

    case 'data':
        try {
            // 1. Inisialisasi Koneksi & Session
            $pdo = $_SERVER['PDO'];
            $id_kampus = $_SESSION['CDC_Data']['id_kampus'] ?? 0;

            // 2. Ambil Parameter DataTable
            $draw        = $_POST['draw'] ?? 1;
            $start       = $_POST['start'] ?? 0;
            $length      = $_POST['length'] ?? 10;
            $searchValue = $_POST['search']['value'] ?? '';
            $orderColumn = $_POST['order'][0]['column'] ?? 0;
            $orderDir    = $_POST['order'][0]['dir'] ?? 'asc';

            // 3. Ambil Parameter Filter Custom
            $filterMajor      = $_POST['major'] ?? 'All';
            $filterProdi      = $_POST['program_study'] ?? '';
            $filterGeneration = $_POST['generation'] ?? 'All';
            $filterStartDate = $_POST['start_date'] ?? '';
            $filterEndDate   = $_POST['end_date'] ?? '';
            
            // 4. Base Query
            $queryBase = "
                FROM internship i
                JOIN student_internship s ON i.nim = s.nim
                LEFT JOIN program_study p ON s.program_study = p.kode_prodi
                LEFT JOIN company c ON i.id_company = c.id_company
            ";

            // 5. Build WHERE Clause
            $whereConditions = [];
            $params = [];

            // a. Filter Kampus
            $whereConditions[] = "s.id_kampus = :id_kampus";
            $params[':id_kampus'] = $id_kampus;

            // b. Filter Major
            if ($filterMajor !== 'All' && !empty($filterMajor)) {
                $whereConditions[] = "p.major = :major";
                $params[':major'] = $filterMajor;
            }

            // c. Filter Prodi
            if (!empty($filterProdi) && $filterProdi !== 'All') {
                if (strpos($filterProdi, 'All-') === 0) {
                    $majorFromOption = str_replace('All-', '', $filterProdi);
                    $whereConditions[] = "p.major = :major_prodi";
                    $params[':major_prodi'] = $majorFromOption;
                } else {
                    $whereConditions[] = "s.program_study = :kode_prodi";
                    $params[':kode_prodi'] = $filterProdi;
                }
            }

            // d. Filter Generation
            if ($filterGeneration !== 'All' && !empty($filterGeneration)) {
                $whereConditions[] = "SUBSTRING(s.nim, 4, 2) = :generation";
                $params[':generation'] = $filterGeneration;
            }

            // e. Filter Date Range (Logika: Start Inside OR End Inside)
            if (!empty($filterStartDate) && !empty($filterEndDate)) {
                
                // Logika: Tampilkan jika (Start Date ada di dalam range) ATAU (End Date ada di dalam range)
                $whereConditions[] = "(i.start_date BETWEEN :start_date AND :end_date
                AND i.end_date   BETWEEN :start_date AND :end_date)";
                
                $params[':start_date'] = $filterStartDate;
                $params[':end_date']   = $filterEndDate;

            } elseif (!empty($filterStartDate)) {
                
                // Case: Hanya Start Date (Mulai dari tanggal ini ke depan)
                $whereConditions[] = "i.start_date >= :start_date";
                $params[':start_date'] = $filterStartDate;

            } elseif (!empty($filterEndDate)) {
                
                // Case: Hanya End Date (Berakhir sebelum atau pas tanggal ini)
                $whereConditions[] = "i.end_date <= :end_date";
                $params[':end_date'] = $filterEndDate;
            }

            // f. Global Search
            if (!empty($searchValue)) {
                $whereConditions[] = "(
                    s.nim LIKE :search 
                    OR s.name LIKE :search 
                    OR c.name LIKE :search
                    OR i.internship_position LIKE :search
                )";
                $params[':search'] = "%$searchValue%";
            }

            $sqlWhere = "";
            if (count($whereConditions) > 0) {
                $sqlWhere = " WHERE " . implode(" AND ", $whereConditions);
            }

            // 6. Count Total Data (Filtered)
            $sqlCount = "SELECT COUNT(*) as total " . $queryBase . $sqlWhere;
            $stmtCount = $pdo->prepare($sqlCount);
            $stmtCount->execute($params);
            $recordsFiltered = $stmtCount->fetchColumn();

            // 7. Count Total Data (All - Per Kampus)
            $sqlTotal = "SELECT COUNT(*) as total " . $queryBase . " WHERE s.id_kampus = :id_kampus_total";
            $stmtTotal = $pdo->prepare($sqlTotal);
            $stmtTotal->execute([':id_kampus_total' => $id_kampus]);
            $recordsTotal = $stmtTotal->fetchColumn();

            // 8. Sorting
            $columnsMap = [
                0 => 's.nim',
                1 => 's.name',
                2 => 'c.name',
                3 => 'i.start_date',
                4 => 'i.status',
                5 => 'i.id_internship'
            ];
            $orderBy = $columnsMap[$orderColumn] ?? 'i.timestamp_register';
            
            // 9. Main Query
            $sqlData = "
                SELECT 
                    i.id_internship, i.start_date, i.end_date, i.status, i.internship_position, i.timestamp_register,
                    s.nim, s.name as student_name, s.profile_picture,
                    p.jenjang, p.study_program,
                    c.name as company_name, c.logo as company_logo
                " . $queryBase . $sqlWhere . "
                ORDER BY $orderBy $orderDir
                LIMIT :limit OFFSET :offset
            ";

            $stmtData = $pdo->prepare($sqlData);
            
            foreach ($params as $key => $val) {
                $stmtData->bindValue($key, $val);
            }
            $stmtData->bindValue(':limit', (int)$length, PDO::PARAM_INT);
            $stmtData->bindValue(':offset', (int)$start, PDO::PARAM_INT);
            $stmtData->execute();
            
            $data = $stmtData->fetchAll(PDO::FETCH_ASSOC);

            // 10. Formatting Data
            $formattedData = [];
            
            foreach ($data as $row) {

                // A. Checkbox (Centered)
                $checkbox = '
                    <div class="custom-control custom-checkbox d-flex justify-content-center align-items-center">
                        <input type="checkbox" class="custom-control-input row-check" 
                            id="check_' . $row['id_internship'] . '" 
                            data-id="' . $row['id_internship'] . '" 
                            data-name="' . htmlspecialchars($row['student_name']) . '">
                        <label class="custom-control-label" for="check_' . $row['id_internship'] . '"></label>
                    </div>';

                // B. Student Info (No Photo, Clean Layout)
                $studentInfo = '
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-1 font-weight-bold text-dark" style="font-size: 14px;">' . htmlspecialchars($row['student_name']) . '</h6>
                        <div class="text-muted small" style="line-height: 1.2;">
                            <i class="far fa-id-card mr-1"></i> ' . $row['nim'] . '
                        </div>
                        <div class="text-primary small mt-1" style="line-height: 1.2;">
                            <i class="fas fa-graduation-cap mr-1"></i> ' . $row['jenjang'] . ' ' . $row['study_program'] . '
                        </div>
                    </div>';

                // C. Company Info
                $companyName = !empty($row['company_name']) ? $row['company_name'] : '<i class="text-muted">Unknown Company</i>';
                // $position = !empty($row['internship_position']) ? $row['internship_position'] : '-';
                
                $companyInfo = '
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-1 font-weight-bold text-dark" style="font-size: 14px;">' . htmlspecialchars($companyName) . '</h6>
                    </div>';

                // D. Period
                $startDate = !empty($row['start_date']) ? date('d M Y', strtotime($row['start_date'])) : '-';
                $endDate = !empty($row['end_date']) ? date('d M Y', strtotime($row['end_date'])) : '-';
                
                $period = '
                    <div class="small text-muted" style="line-height: 1.5;">
                        <div><i class="far fa-calendar-alt mr-2 text-success"></i> ' . $startDate . '</div>
                        <div><i class="far fa-calendar-check mr-2 text-danger"></i> ' . $endDate . '</div>
                    </div>';

                // E. Status
                $statusBadge = '';
                switch ($row['status']) {
                    case 'Yes':      $statusBadge = '<span class="badge badge-success px-3">Active</span>'; break;
                    case 'Waiting':  $statusBadge = '<span class="badge badge-warning px-3">Waiting</span>'; break;
                    case 'No':       $statusBadge = '<span class="badge badge-danger px-3">Rejected</span>'; break;
                    case 'Finished': $statusBadge = '<span class="badge badge-info px-3">Finished</span>'; break;
                    default:         $statusBadge = '<span class="badge badge-secondary px-3">' . $row['status'] . '</span>';
                }

                // F. Aksi (Detail Only)
                /* // --- EDIT & DELETE DI-COMMENT DULU ---
                $btnEdit = '<a href="..." class="btn btn-xs btn-primary btn-icon rounded-circle"><i class="fas fa-pen"></i></a>';
                $btnDelete = '<button onclick="..." class="btn btn-xs btn-danger btn-icon rounded-circle"><i class="fas fa-trash"></i></button>';
                */

                // Tombol Detail
                // Asumsi route detail adalah: cdc.internship.detail
                $linkDetail = router('cdc.internship.detail', ['id' => base64_encode($row['id_internship'])]);
                
                $btnDetail = '
                    <a href="' . $linkDetail . '" class="btn btn-xs btn-info shadow-sm">
                        <i class="fas fa-eye mr-1"></i> Detail
                    </a>';

                $aksi = '<div class="text-center">' . $btnDetail . '</div>';

                $formattedData[] = [
                    'checkbox'     => $checkbox,
                    'student_info' => $studentInfo,
                    'company_info' => $companyInfo,
                    'period'       => $period,
                    'status'       => $statusBadge,
                    'aksi'         => $aksi
                ];
            }

            echo json_encode([
                "draw"            => intval($draw),
                "recordsTotal"    => intval($recordsTotal),
                "recordsFiltered" => intval($recordsFiltered),
                "data"            => $formattedData
            ]);
            exit;

        } catch (PDOException $e) {
            // Log Error Database
            // Format Log: [Type] Pesan | File:Baris
            $errorMsg = "[Database Error] " . $e->getMessage() . " | " . $e->getFile() . ":" . $e->getLine();
            
            // Asumsi class helper Router memiliki method log() statis
            Router::log( 'Database_Error' , ['message' => $errorMsg]);

            // Kembalikan JSON error kosong agar DataTables tidak alert popup (opsional)
            echo json_encode([
                "draw" => intval($_POST['draw'] ?? 1),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "Internal Server Error. Please check logs."
            ]);
            exit;
        } catch (Exception $e) {
            // Log General Error
            $errorMsg = "[System Error] " . $e->getMessage();
            Router::log( 'System_Error' , ['message' => $errorMsg]);

            echo json_encode([
                "draw" => intval($_POST['draw'] ?? 1),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "System Error."
            ]);
            exit;
        }
    break;

    case 'add':
        try {
            $pdo = $_SERVER['PDO'];
            
            // 1. Ambil ID Kampus dari Session (Wajib untuk filter)
            $id_kampus = $_SESSION['CDC_Data']['id_kampus'] ?? 0;
            $selectedNim = $nim ?? null;
            
            $studentData = null;

            // 2. Get Specific Student Data (Jika ada parameter NIM)
            // Digunakan untuk auto-fill form jika user klik "Add Internship" dari profil mahasiswa tertentu
            if (!empty($selectedNim)) {
                $sqlStudent = "
                    SELECT 
                        s.*, 
                        p.jenjang, 
                        p.study_program,
                        p.major
                    FROM student_internship s 
                    LEFT JOIN program_study p ON p.kode_prodi = s.program_study 
                    WHERE s.nim = :nim 
                    AND s.id_kampus = :id_kampus 
                    LIMIT 1
                ";
                $stmt = $pdo->prepare($sqlStudent);
                $stmt->execute([
                    ':nim' => $selectedNim,
                    ':id_kampus' => $id_kampus
                ]);
                $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // 3. Get All Lecturers (Dosen) - Filter per Kampus
            // Untuk dropdown "Supervisor" / Dosen Pembimbing
            $sqlLecturers = "
                SELECT nim_nik_unit, name 
                FROM lecturer 
                WHERE id_kampus = :id_kampus 
                ORDER BY name ASC
            ";
            $stmtLec = $pdo->prepare($sqlLecturers);
            $stmtLec->execute([':id_kampus' => $id_kampus]);
            $allLecturers = $stmtLec->fetchAll(PDO::FETCH_ASSOC);

            // 4. Get All Students (Mahasiswa) - Filter per Kampus
            // Untuk dropdown pencarian mahasiswa (Select2)
            // Note: Mengambil kolom penting saja agar hemat memori
            $sqlAllStudents = "
                SELECT nim, name, program_study 
                FROM student_internship 
                WHERE id_kampus = :id_kampus 
                ORDER BY name ASC
            ";
            $stmtAllStud = $pdo->prepare($sqlAllStudents);
            $stmtAllStud->execute([':id_kampus' => $id_kampus]);
            $allStudents = $stmtAllStud->fetchAll(PDO::FETCH_ASSOC);

            // 5. Get All Companies (Opsional - Jika form butuh pilih perusahaan)
            $sqlCompanies = "
                SELECT id_company, name 
                FROM company 
                ORDER BY name ASC
            ";
            $stmtComp = $pdo->query($sqlCompanies);
            $allCompanies = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

            // 6. Render View
            renderView('cdc.internship_management.add', [
                'title'        => 'Add New Internship',
                'route'        => 'internship_management', // Untuk highlight menu sidebar
                'selectedNim'  => $selectedNim,      // String NIM yang dipilih (jika ada)
                'studentData'  => $studentData,      // Array detail mahasiswa terpilih (jika ada)
                'lecturers'    => $allLecturers,     // List semua dosen
                'students'     => $allStudents,      // List semua mahasiswa
                'companies'    => $allCompanies      // List perusahaan
            ]);

        } catch (PDOException $e) {
            Router::log("[Add Form Error] " . $e->getMessage());
            make_swal2('error', 'Database Error: Failed to load form data.');
            redirect(router('cdc.internship.management'));
        }
    break;

    case 'store':
        try {
            $pdo = $_SERVER['PDO'];

            $resolveAction = $_POST['resolve_action'] ?? null;

            // ============================================================
            // 1. VALIDASI DATA (Menggunakan Class Validator Baru)
            // ============================================================
            
            // Skip validasi jika ini aksi "Finish" (karena data dianggap aman)
            if (!$resolveAction) {
                
                // A. Definisikan Rules
                $rules = [
                    'nim'                 => 'required',
                    'id_company'          => 'required',
                    'internship_position' => 'required',
                    'start_date'          => 'required|valid_date',
                    'end_date'            => 'required|valid_date|after_date:start_date',
                ];

                // B. Custom Messages (Lengkap)
                $messages = [
                    // NIM
                    'nim.required'                 => 'Student NIM is required.',

                    // Company
                    'id_company.required'          => 'Company is required.',

                    // Position
                    'internship_position.required' => 'Internship Position is required.',

                    // Start Date
                    'start_date.required'          => 'Start Date is required.',
                    'start_date.valid_date'        => 'Start Date must be a valid date format.',

                    // End Date
                    'end_date.required'            => 'End Date is required.',
                    'end_date.valid_date'          => 'End Date must be a valid date format.',
                    'end_date.after_date'          => 'End Date must be later than the Start Date.',
                ];

                // C. Jalankan Validator
                $validator = Validator::make($_POST, $rules, [], $messages);

                if ($validator->fails()) {
                    form_validation();
                    // Simpan Error & Old Input ke Session Flash
                    Session::flash('validation_errors', $validator->errors());
                    Session::flash('old', $_POST);

                    // Feedback UI
                    make_swal2('error', 'Form Validation Failed', 'Please check your input.');
                    redirect_back(100); 
                    exit;
                }
            }

            // ============================================================
            // 2. PROSES LOGIKA DATABASE
            // ============================================================

            // Ambil Input Bersih
            $nim         = trim($_POST['nim'] ?? '');
            $id_company  = trim($_POST['id_company'] ?? '');
            $position    = trim($_POST['internship_position'] ?? '');
            $id_lecturer = trim($_POST['id_lecturer'] ?? '');
            $start_date  = $_POST['start_date'] ?? null;
            $end_date    = $_POST['end_date'] ?? null;
            $status      = $_POST['status'] ?? 'Active';

            $pdo->beginTransaction();

            // A. LOGIKA RESOLUSI vs CEK DUPLIKASI
            if ($resolveAction === 'Finish') {
                // Bulk Update status 'Finish' sebelum insert baru
                $sqlFinish = "UPDATE internship SET status = 'Finish' WHERE nim = :nim AND status IN ('Yes', 'yes', 'Active', 'Approved')";
                $stmtFinish = $pdo->prepare($sqlFinish);
                $stmtFinish->execute([':nim' => $nim]);
            } 
            else {
                // Cek Duplikasi (Active Internship)
                $stmtCheck = $pdo->prepare("
                    SELECT i.*, c.name as company_name 
                    FROM internship i
                    LEFT JOIN company c ON i.id_company = c.id_company
                    WHERE i.nim = :nim 
                    AND i.status IN ('Yes', 'yes', 'Active', 'Approved')
                    ORDER BY i.id_internship DESC
                ");
                $stmtCheck->execute([':nim' => $nim]);
                $existingInternship = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($existingInternship) > 0) {
                    // Gunakan Session::flash untuk data duplikat
                    Session::flash('old', ['existingInternship' => $existingInternship]);
                    
                    make_swal2('warning', 'Duplicate Data', 'Student has active internship(s). Action required.');
                    redirect_back(100); 
                    exit;
                }
            }

            // B. BERSIHKAN STATUS WAITING
            $stmtReject = $pdo->prepare("UPDATE internship SET status = 'No' WHERE nim = :nim AND status = 'Waiting'");
            $stmtReject->execute([':nim' => $nim]);

            // C. INSERT DATA BARU
            $sqlInsert = "
                INSERT INTO internship (
                    nim, id_company, id_user_company, nik,
                    internship_position, start_date, end_date, status, 
                    timestamp_register,
                    file_1, file_2, file_3, date_finalreport, final_report,
                    internship_period
                ) VALUES (
                    :nim, :id_company, :id_user_company, :nik,
                    :position, :start_date, :end_date, :status, 
                    NOW(),
                    :file_1, :file_2, :file_3, :date_finalreport, :final_report,
                    TIMESTAMPDIFF(MONTH, :start_date, :end_date)
                )
            ";

            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                ':nim'             => $nim,
                ':id_company'      => $id_company,
                ':id_user_company' => null, // Set NULL sesuai saran
                ':nik'             => null, 
                ':position'        => $position,
                ':start_date'      => $start_date,
                ':end_date'        => $end_date,
                ':status'          => 'Yes',
                ':file_1'          => 'https://talenthub.polibatam.ac.id/index.php?page=mhsprofile&nim='. base64_encode($nim),
                ':file_2'          => '',
                ':file_3'          => '',
                ':date_finalreport'=> '',
                ':final_report'    => ''
            ]);

            // D. UPDATE DOSEN PEMBIMBING (Opsional)
            if (!empty($id_lecturer)) {
                $stmtUpdate = $pdo->prepare("UPDATE student_internship SET nik_dospem = :id_lecturer WHERE nim = :nim");
                $stmtUpdate->execute([':id_lecturer' => $id_lecturer, ':nim' => $nim]);
            }

            $pdo->commit();

            make_swal2('success', 'Success', 'Internship data successfully added.');
            redirect(router('cdc.internship.management'), 100); 
            exit;

        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            
            // Log Error
            Router::log('System_Error', ['message' => "[Internship Store Error] " . $e->getMessage()]);
            
            // Simpan inputan user biar gak hilang
            Session::flash('old', $_POST);
            
            make_swal2('error', 'Database Error', 'Failed to save data.');
            redirect_back(100); 
            exit;
        }
    break;


    case 'detail':
        
        if (!$id_encoded) {
            abort(404, 'Invalid ID');
        }

        try {
            $id_internship = base64_decode($id_encoded);
            $pdo = $_SERVER['PDO'];

            // Query Kompleks untuk mengambil SEMUA detail
            $sql = "
                SELECT 
                    i.*,
                    -- Data Mahasiswa
                    s.name AS student_name, 
                    s.nim, 
                    s.phone AS student_phone, 
                    s.email AS student_email, 
                    s.profile_picture,
                    s.program_study AS kode_prodi,
                    
                    -- Data Prodi
                    p.jenjang, 
                    p.study_program,
                    p.major,

                    -- Data Perusahaan
                    c.name AS company_name, 
                    c.address AS company_address, 
                    c.city AS company_city, 
                    c.province AS company_province,
                    c.phone AS company_phone,
                    c.logo AS company_logo,

                    -- Data Dosen Pembimbing (Opsional, jika ada relasi)
                    l.name AS lecturer_name

                FROM internship i
                JOIN student_internship s ON i.nim = s.nim
                LEFT JOIN program_study p ON s.program_study = p.kode_prodi
                LEFT JOIN company c ON i.id_company = c.id_company
                LEFT JOIN lecturer l ON s.nik_dospem = l.nim_nik_unit
                WHERE i.id_internship = :id
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id_internship]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                // Redirect atau tampilkan error jika data tidak ditemukan
                make_swal2('error', 'Not Found','Internship data not found.');
                redirect(router('cdc.internship.management'));
            }

            // Render View
            renderView('cdc.internship_management.detail', [
                'title' => 'Internship Detail - ' . $data['student_name'],
                'route' => 'internship_management',
                'data'  => $data
            ]);

        } catch (Exception $e) {
            Router::log('System_Error', ['message' => "[Detail Error] " . $e->getMessage()]);
            Router::handleError(500, 'Internal Server Error');
        }
    break;

endswitch;