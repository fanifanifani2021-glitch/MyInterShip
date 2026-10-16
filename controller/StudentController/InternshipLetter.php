<?php
$token = bin2hex(random_bytes(16));
$_SESSION['PRINT_TOKEN'] = $token;

switch ($method):

    default:
        http_response_code(404);
    break;

    case 'index':
        renderView('student.my_internship_letter.index', ['title' => 'Internship Letter', 'routes' => 'my_internship_letter']);
    break;

    case 'detail':
        $pdo = $_SERVER['PDO'];
        $id_internship_letter = $_GET['id'] ?? null;

        if (!$id_internship_letter) {
            make_swal2("error", 'Internship Letter Not Found', 'Provide Valid Letter ID !');
            redirect_back(1); die;
        }

        // === QUERY 1: Detail surat magang ===
        $sql_letter = "
            SELECT 
                il.*,
                s.name AS student_name,
                s.email,
                s.phone,
                ps.study_program,
                ps.jenjang,
                ps.jurusan,
                c.name AS company_name,
                c.address AS company_address,
                c.email AS company_email,
                c.phone AS company_phone,
                c.city AS company_city,
                c.province AS company_province
            FROM internship_letter il
            JOIN student_internship s ON il.student_nim = s.nim
            JOIN program_study ps ON s.program_study = ps.kode_prodi
            LEFT JOIN company c ON il.id_company = c.id_company
            WHERE il.id_internship_letter = :id
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql_letter);
        $stmt->execute([':id' => $id_internship_letter]);
        $letter = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$letter) {
            make_swal2("error", 'Internship Letter Not Found', 'Provide Valid Letter ID !');
            redirect('my_internship_letter', 1); die;
        }

        // === QUERY 2: Approval history ===
        $sql_approval = "
            SELECT 
                role,
                role_username,
                role_name,
                status_approval,
                remark,
                created_at
            FROM internship_letter_approval
            WHERE id_internship_letter = :id
            ORDER BY created_at ASC
        ";
        $stmt = $pdo->prepare($sql_approval);
        $stmt->execute([':id' => $id_internship_letter]);
        $approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // === QUERY 3: Status change history ===
        $sql_history = "
            SELECT 
                status,
                created_at
            FROM internship_letter_history
            WHERE id_internship_letter = :id
            ORDER BY id_internship_letter_history DESC
        ";
        $stmt = $pdo->prepare($sql_history);
        $stmt->execute([':id' => $id_internship_letter]);
        $histories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // === Render ke Blade ===
        renderView('student.my_internship_letter.detail', [
            'title'     => 'Internship Letter Detail',
            'routes'    => 'my_internship_letter',
            'letter'    => $letter,
            'approvals' => $approvals,
            'histories' => $histories
        ]);
    break;

    
    case 'add':

        try {

            $pdo = $_SERVER['PDO'];

            // =====================================================
            // 🔍 1. CEK SURAT TERBARU MAHASISWA
            // =====================================================
            $sqlCheck = "
                SELECT 
                    id_internship_letter,
                    id_company,
                    status,
                    coor_approval,
                    cdc_approval,
                    acceptance_status,
                    created_at
                FROM internship_letter
                WHERE student_nim = :nim
                ORDER BY created_at DESC
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sqlCheck);
            $stmt->execute([ ':nim' => $_SESSION['nim'] ]);
            $letter = $stmt->fetch(PDO::FETCH_ASSOC);


            // =====================================================
            // 2. JIKA BELUM PERNAH MENGAJUKAN → BOLEH LANJUT
            // =====================================================
            if (!$letter) {
                goto ALLOW_ADD;
            }


            // =====================================================
            // 3. JIKA DITOLAK → BOLEH AJUKAN LAGI
            // =====================================================
            if (
                strtolower($letter['coor_approval']) === 'rejected' ||
                strtolower($letter['cdc_approval'])  === 'rejected'
            ) {
                goto ALLOW_ADD;
            }


            // =====================================================
            // 4. JIKA SUDAH DISETUJUI (KOOR + CDC) 
            //    TAPI BELUM ISI ACCEPTANCE → BLOKIR
            // =====================================================
            if (
                strtolower($letter['coor_approval']) === 'approved' &&
                strtolower($letter['cdc_approval'])  === 'approved' &&
                (empty($letter['acceptance_status']))
            ) {

                make_swal2(
                    'warning',
                    'You already have an approved internship letter!',
                    'Please complete the Acceptance Response Form before creating a new request.'
                );

                redirect('student/internship_letter/detail&id='.$letter['id_internship_letter'], 1);
                exit();
            }


            // =====================================================
            // 5. JIKA STATUS MASIH WAITING / PROCESSING → BLOKIR
            // =====================================================
            if (in_array($letter['status'], ['WAITING', 'PROCESSING'])) {

                make_swal2(
                    'warning',
                    'Your previous internship letter is still being processed.',
                    'Please wait until Coordinator and CDC finish reviewing it.'
                );

                redirect('student/internship_letter/detail&id='.$letter['id_internship_letter'], 1);
                exit();
            }


            // =====================================================
            // 6. FAILSAFE — Default blokir jika kondisi tidak dikenal
            // =====================================================
            make_swal2(
                'warning',
                'You cannot create a new internship letter yet.',
                'Please complete the previous one first.'
            );

            redirect('student/internship_letter/detail&id='.$letter['id_internship_letter'], 1);
            exit();



            // =====================================================
            // 7. ✔ IZINKAN MASUK KE HALAMAN ADD
            // =====================================================
            ALLOW_ADD:


            // === Fetch company list ===
            $sql = "SELECT id_company, name FROM company ORDER BY name ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // === Fetch student internship data + join program_study ===
            $sql = "
                SELECT 
                    si.*,
                    ps.kode_prodi,
                    ps.jenjang,
                    ps.prodi,
                    ps.study_program,
                    ps.jurusan,
                    ps.major
                FROM student_internship si
                INNER JOIN program_study ps ON ps.kode_prodi = si.program_study
                WHERE si.nim = :nim
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nim' => $_SESSION['nim']]);
            $student_internship = $stmt->fetch(PDO::FETCH_ASSOC);

            $minimal_start_date = date('Y-m-d', strtotime('-7 days'));


            // === Render Halaman Add ===
            renderView(
                'student.my_internship_letter.add',
                [
                    'title' => 'Request New Internship Letter',
                    'routes' => 'my_internship_letter',
                    'student_internship' => $student_internship,
                    'companies' => $companies,
                    'minimal_start_date' => $minimal_start_date
                ]
            );

        } catch (PDOException $e) {
            die("Something error");
        }

    break;


    case 'print':
        renderView('student.my_internship_letter.print', ['title' => 'Print Internship Letter', 'routes' => 'my_internship_letter']);
    break;

    case 'post':

        // ===============================================
        // CEK: mahasiswa sudah punya surat aktif ke company yg sama?
        // ===============================================


        // ==== FORM VALIDATION ====
        set_rules('nim', 'NIM', ['required'], ['required' => 'Cannot be empty !']);
        set_rules('class', 'Class', ['required'], ['required' => 'Cannot be empty !']);
        set_rules('semester', 'Semester', ['required'], ['required' => 'Cannot be empty !']);
        set_rules('company_not_listed', 'Company Not Listed', ['required'], ['required' => 'Cannot be empty !']);

        if ($_POST['company_not_listed'] == 0) {
            set_rules('company_id', 'Company', ['required'], ['required' => 'Cannot be empty !']);
        } else {
            set_rules('newCompanyName', 'Company Name', ['required'], ['required' => 'Cannot be empty !']);
            set_rules('newCompanyEmail', 'Company Email', ['required'], ['required' => 'Cannot be empty !']);
            set_rules('newCompanyPhone', 'Company Phone Number', ['required'], ['required' => 'Cannot be empty !']);
            set_rules('newCompanyAddress', 'Company Address', ['required'], ['required' => 'Cannot be empty !']);
        }

        $cek_form = form_validation();
        if ($cek_form === false) {
            make_snackbar('Check your submitted data and try again later');
            redirect_back(1);
            exit;
        }

        // ==== MAIN PROCESS ====
        try {
            $pdo = $_SERVER['PDO'];
            $pdo->beginTransaction();

            $id_internship_letter = generateUUIDv4();

            $id_company = null;
            $is_new_company = (int) $_POST['company_not_listed'];


            // Tentukan ID company yg akan dicek
            $company_to_check = ( $is_new_company === 1) 
                ? $id_company      // company baru
                : $_POST['company_id'];  // company lama yg dipilih

            $sqlCheck = "
                SELECT 
                    id_internship_letter,
                    id_company,
                    status,
                    coor_approval,
                    cdc_approval,
                    acceptance_status,
                    created_at
                FROM internship_letter
                WHERE student_nim = :nim
                AND id_company = :company
                AND (
                        -- Masih menunggu approval (awal)
                        status IN ('WAITING', 'PROCESSING')

                        OR

                        -- Sudah approved koordinatori/CDC tapi acceptance belum submit
                        (coor_approval IN ('waiting','approved')
                        AND cdc_approval IN ('pending','approved')
                        AND (acceptance_status IS NULL OR acceptance_status = '')
                        )
                    )
                ORDER BY created_at DESC
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sqlCheck);
            $stmt->execute([
                ':nim'     => $_SESSION['nim'],
                ':company' => $company_to_check
            ]);

            $lastLetter = $stmt->fetch(PDO::FETCH_ASSOC);

            // Jika ditemukan surat yg masih aktif
            if ($lastLetter) {

                make_swal2(
                    'warning',
                    'You Already Have an Active Internship Letter',
                    'You already submitted an internship letter for this company.
                    Please complete the acceptance response before creating a new one.'
                );

                redirect('student/internship_letter/detail&id=' . $lastLetter['id_internship_letter'], 1);
                exit();
            }


            // ==== IF NEW COMPANY INSERT FIRST ====
            if ($is_new_company === 1) {
                $sql_company = "INSERT INTO company 
                    (name, email, phone, address, status, province, city, access_type, id_kampus)
                    VALUES (:name, :email, :phone, :address, :status, :province, :city, :access_type, :id_kampus)";
                $stmt_company = $pdo->prepare($sql_company);

                $stmt_company->execute([
                    ':name'        => $_POST['newCompanyName'],
                    ':email'       => $_POST['newCompanyEmail'],
                    ':phone'       => $_POST['newCompanyPhone'],

                    ':address'     => $_POST['newCompanyAddress'],
                    ':province'    => $_POST['newCompanyProvince'] ?? '',
                    ':city'        => $_POST['newCompanyCity'] ?? '',

                    ':status'      => 'Not Verified',
                    ':access_type' => '1',
                    ':id_kampus'   => $_SESSION['Student-Data']['id_kampus'] ?? '1',
                ]);


                $id_company = $pdo->lastInsertId();
            } else {
                $id_company = $_POST['company_id'];
            }

            // ==== INSERT INTO internship_letter ====
            $sql_letter = "INSERT INTO internship_letter (
                    id_internship_letter,
                    id_company, 
                    student_nim, 
                    class, 
                    semester, 
                    is_new_company, 
                    expected_start, 
                    expected_end, 
                    letter_languange, 
                    status, 
                    coor_approval, 
                    cdc_approval, 
                    created_at
                ) VALUES (
                    :id_internship_letter,
                    :id_company,
                    :student_nim,
                    :class,
                    :semester,
                    :is_new_company,
                    :expected_start,
                    :expected_end,
                    :letter_languange,
                    :status,
                    :coor_approval,
                    :cdc_approval,
                    :created_at
                )";

            $stmt = $pdo->prepare($sql_letter);
            $stmt->execute([
                ':id_internship_letter' => $id_internship_letter,
                ':id_company'        => $id_company,
                ':student_nim'       => $_SESSION['nim'] ?? '',
                ':class'             => $_POST['class'],
                ':semester'          => $_POST['semester'],
                ':is_new_company'    => $is_new_company,
                ':expected_start'    => $_POST['start_date'],
                ':expected_end'      => $_POST['end_date'],
                ':letter_languange'  => $_POST['language'] ?? 'EN',
                ':status'            => 'WAITING',
                ':coor_approval'     => 'waiting',
                ':cdc_approval'      => 'pending',
                ':created_at'        => date('Y-m-d H:i:s')
            ]);

            // === Insert History Log ===
            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $dateFormatted = $now->format('F j, Y \a\t H:i');

            $user_id   = $_SESSION['nim']  ?? 'unknown';
            $user_name = $_SESSION['name'] ?? 'System';
            $new_status = 'Waiting for Internship Coordinator Approval';

            // Pesan log
            $statusMessage = "Internship Letter Submitted on {$dateFormatted}, status changed to {$new_status} by {$user_id} | {$user_name}.";

            $sql_history = "INSERT INTO internship_letter_history (
                id_internship_letter,
                status,
                created_at
            ) VALUES (
                :id_internship_letter,
                :status,
                :created_at
            )";

            $stmt = $pdo->prepare($sql_history);
            $stmt->execute([
                ':id_internship_letter' => $id_internship_letter,
                ':status'               => $statusMessage,
                ':created_at'           => date('Y-m-d H:i:s')
            ]);


            $pdo->commit();

            make_snackbar('Internship letter request submitted successfully!');
            redirect('my_internship_letter', 100); exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            var_dump( $e->getMessage() ); die;
            make_snackbar('An error occurred: ' . $e->getMessage(), 'danger');
            redirect_back(1);
            exit;
        }

    break;

    case 'acceptance_form':

        $pdo = $_SERVER['PDO'];
        $nim = $_SESSION['nim'] ?? null;
        $id = $_GET['id'] ?? null;

        if (!$nim || !$id) {
            die("Invalid request.");
        }

        try {
            // ============================================
            // GET INTERNSHIP LETTER + COMPANY + STUDENT
            // ============================================
            $sql = "
                SELECT 
                    il.*,
                    c.id_company,
                    c.name AS company_name,
                    c.address AS company_address,
                    c.city AS company_city,
                    c.province AS company_province,
                    
                    s.name AS student_name,
                    s.nim AS student_nim,
                    ps.jenjang,
                    ps.study_program,
                    ps.prodi,
                    ps.jurusan

                FROM internship_letter il
                LEFT JOIN company c ON il.id_company = c.id_company
                LEFT JOIN student_internship s ON il.student_nim = s.nim
                LEFT JOIN program_study ps ON s.program_study = ps.kode_prodi

                WHERE il.id_internship_letter = :id
                AND il.student_nim = :nim
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id'  => $id,
                ':nim' => $nim,
            ]);

            $letter = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$letter) {
                die("Internship letter not found.");
            }

        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }



        // ============================================
        // BUSINESS DAYS CALCULATION (for reply = NO)
        // ============================================
        $letterDate = new DateTime($letter['letter_date']);
        $today      = new DateTime();

        $businessDays = 0;
        $cursor = clone $letterDate;

        while ($cursor <= $today) {
            if ($cursor->format('N') < 6) {
                $businessDays++;
            }
            $cursor->modify('+1 day');
        }

        // can submit NO reply?
        $canSubmitWithoutReply = $businessDays >= 10;



        // ============================================
        // SEND ALL DATA TO VIEW
        // ============================================
        renderView('student.my_internship_letter.acceptance_form', [
            'title'   => 'Internship Acceptance Form',
            'routes'  => 'my_internship_letter',

            'letter'  => $letter,

            'company' => [
                'id'       => $letter['id_company'],
                'name'     => $letter['company_name'],
                'address'  => $letter['company_address'],
                'city'     => $letter['company_city'],
                'province' => $letter['company_province'],
            ],

            'student' => [
                'name'          => $letter['student_name'],
                'nim'           => $letter['student_nim'],
                'jenjang'       => $letter['jenjang'],
                'study_program' => $letter['study_program'],
                'jurusan'       => $letter['jurusan'],
            ],

            // === IMPORTANT NEW VARIABLE ===
            'canSubmitWithoutReply' => $canSubmitWithoutReply
        ]);

    break;


    case 'submit_acceptance_form':

        try {

            $pdo = $_SERVER['PDO'];

            // =====================================================
            // 1. POST DATA
            // =====================================================
            $letter_id      = $_POST['id_internship_letter'] ?? null;
            $has_reply      = $_POST['has_reply'] ?? null;
            $is_accepted    = $_POST['is_accepted'] ?? null;
            $changed_date   = $_POST['changed_date'] ?? null;

            $hr_name        = $_POST['hr_name'] ?? null;
            $hr_email       = $_POST['hr_email'] ?? null;

            $new_start_date = $_POST['new_start_date'] ?? null;
            $new_end_date   = $_POST['new_end_date'] ?? null;

            $file_response  = $_FILES['response_pdf'] ?? null;
            $nim            = $_SESSION['nim'] ?? null;

            $user_id        = $_SESSION['nim'] ?? 'unknown';
            $user_name      = $_SESSION['name'] ?? 'System';


            if (!$letter_id || !$nim) {
                die("Invalid request.");
            }


            // =====================================================
            // 2. VALIDATE OWNERSHIP
            // =====================================================
            $stmt = $pdo->prepare("
                SELECT *
                FROM internship_letter
                WHERE id_internship_letter = :id
                AND student_nim = :nim
                LIMIT 1
            ");
            $stmt->execute([':id' => $letter_id, ':nim' => $nim]);
            $letter = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$letter) {
                make_swal2('error', 'Forbidden', 'Unauthorized access.');
                redirect_back(1); exit; 
                // die("Unauthorized access.");
            }


            // =====================================================
            // 3. BASIC VALIDATION
            // =====================================================
            if (!$has_reply) {
                make_swal2('error', 'Not Allowed', 'Please select whether you have received a reply.');
                redirect_back(1); exit; 
            }

            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $timestamp = $now->format('F j, Y \a\t H:i');


            // =====================================================
            // 4. CHECK 10 WORKING DAYS
            // =====================================================
            $letter_date = new DateTime($letter['letter_date']);
            $today = new DateTime();

            $businessDays = 0;
            $current = clone $letter_date;

            while ($current <= $today) {
                if ($current->format('N') < 6) {
                    $businessDays++;
                }
                $current->modify('+1 day');
            }

            $allowSubmit = $businessDays >= 10;



            // =====================================================
            // 5. HAS REPLY = NO
            // =====================================================
            if ($has_reply === 'no') {

                if (!$allowSubmit) {
                    die("You cannot proceed because it has not been 10 business days.");
                }

                $stmt = $pdo->prepare("
                    UPDATE internship_letter
                    SET has_reply = 'NO',
                        acceptance_status = 'NO REPLY',
                        updated_at = NOW()
                    WHERE id_internship_letter = :id
                ");
                $stmt->execute([':id' => $letter_id]);


                // INSERT HISTORY LOG
                $historyMessage = 
                    "Internship Letter - Company Response submitted on {$timestamp} — ".
                    "Internship Acceptance Status changed to NO REPLY FROM COMPANY ".
                    "by {$user_id} | {$user_name}";

                $stmt = $pdo->prepare("
                    INSERT INTO internship_letter_history
                        (id_internship_letter, status, created_at)
                    VALUES
                        (:id_internship_letter, :status, :created_at)
                ");
                $stmt->execute([
                    ':id_internship_letter' => $letter_id,
                    ':status'               => $historyMessage,
                    ':created_at'           => $timestamp
                ]);

                make_swal2("success", 'Data Saved', 'No reply confirmation saved.');
                redirect('student/internship_letter/detail&id=' .$letter_id, 1);
                die;
            }



            // =====================================================
            // 6. HAS REPLY = YES → validate acceptance option
            // =====================================================
            if (!$is_accepted) {
                die("Please select whether the company accepted you.");
            }

            // =====================================================
            // 8. YES Reply → ACCEPTED (file required)
            // =====================================================
            if (!$file_response || $file_response['error'] !== 0) {
                die("Please upload the company's response letter (PDF).");
            }

            if ($file_response['type'] !== 'application/pdf') {
                die("The uploaded file must be a PDF.");
            }

            $dir = BASE_DIR . "/upload/internship_letter/company_reply/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $fileName = $letter_id . "_response.pdf";
            $filePath = $dir . $fileName;

            move_uploaded_file($file_response['tmp_name'], $filePath);





            // =====================================================
            // 7. YES Reply → NOT ACCEPTED
            // =====================================================
            if ($is_accepted === 'no') {

                $stmt = $pdo->prepare("
                    UPDATE internship_letter
                    SET 
                        has_reply = 'yes',
                        acceptance_status = 'rejected',
                        company_reply_letter = :company_reply_letter,
                        updated_at = NOW()
                    WHERE id_internship_letter = :id
                ");
                $stmt->execute([':id' => $letter_id, ':company_reply_letter' => basename( $filePath ) ]);


                // HISTORY
                $historyMessage = 
                    "Internship Letter - Company Response submitted on {$timestamp} — ".
                    "Internship Acceptance Status changed to REJECTED WITH COMPANY REPLY ".
                    "by {$user_id} | {$user_name}";

                $stmt = $pdo->prepare("
                    INSERT INTO internship_letter_history
                        (id_internship_letter, status, created_at)
                    VALUES
                        (:id, :status, :created_at)
                ");
                $stmt->execute([
                    ':id'        => $letter_id,
                    ':status'    => $historyMessage,
                    ':created_at'=> $timestamp
                ]);

                make_swal2("error", 'Not Accepted', 'Your status has been saved.');
                redirect('student/internship_letter/detail&id=' .$letter_id, 1);
                die;
            }


            // =====================================================
            // 10. HANDLE DATE CHANGE
            // =====================================================
            $final_start = $letter['expected_start'];
            $final_end   = $letter['expected_end'];

            if ($changed_date === 'yes') {

                if (!$new_start_date || !$new_end_date) {
                    die("Please provide new start and end dates.");
                }

                $final_start = $new_start_date;
                $final_end   = $new_end_date;

                
            }

            $start  = new DateTime($final_start);
            $end    = new DateTime($final_end);

            // hitung selisih
            $diff = $start->diff($end);

            // total bulan = (tahun × 12) + bulan
            $totalMonths = ($diff->y * 12) + $diff->m;


            // =====================================================
            // 11. SAVE ACCEPTED STATUS
            // =====================================================
            $stmt = $pdo->prepare("
                UPDATE internship_letter
                SET 
                    has_reply = 'yes',
                    acceptance_status = 'accepted',
                    company_reply_letter = :company_reply_letter,
                    updated_at = NOW()
                WHERE id_internship_letter = :id
            ");

            $stmt->execute([
                ':company_reply_letter'        => basename($filePath),
                ':id'         => $letter_id
            ]);


            // Claim Internship
            $stmt = $pdo->prepare("
                INSERT INTO `internship`
                (`nim`, `id_company`, `start_date`, `end_date`, `status`, `internship_position`, `internship_period`, `timestamp_register`, `id_user_company`, `nik`, `file_1`, `file_2`, `file_3`, `date_finalreport`, `final_report` ) 
                VALUES (:nim, :id_company, :start_date, :end_date, 'Yes', :internship_position, :internship_period, CURRENT_TIMESTAMP,  '', '', '', '', '', '',  '')
            ");

            $stmt->execute([
                ':nim' => $nim,
                ':id_company' => $letter['id_company'],
                ':start_date' => $final_start,
                ':end_date' => $final_end,
                ':internship_position' => $_POST['internship_position'],   // from Blade input
                ':internship_period' => $totalMonths
            ]);

            

            // =====================================================
            // 9. NEW COMPANY → HR REQUIRED
            // =====================================================
            if ($letter['is_new_company'] == 1) {

                // Insert Data HRD yang baru untu user_company

                if (!$hr_name || !$hr_email) {
                    die("HR name and HR email are required.");
                }
            }


            // HISTORY
            $historyMessage = 
                "Internship Letter - Company Response submitted on {$timestamp} — ".
                "Internship Acceptance Status changed to ACCEPTED WITH COMPANY REPLY ".
                "by {$user_id} | {$user_name}";

            $stmt = $pdo->prepare("
                INSERT INTO internship_letter_history
                    (id_internship_letter, status, created_at)
                VALUES
                    (:id, :status, :created_at)
            ");
            $stmt->execute([
                ':id'        => $letter_id,
                ':status'    => $historyMessage,
                ':created_at'=> $timestamp
            ]);


            make_swal2("success", 'Accepted', 'Your internship acceptance has been submitted.');
            redirect('student/internship_letter/detail&id=' .$letter_id, 1);
            die;



        } catch (Exception $e) {

            die("Error: " . $e->getMessage());
        }

    break;


endswitch;
