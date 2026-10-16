<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

$token = bin2hex(random_bytes(16));
$_SESSION['PRINT_TOKEN'] = $token;

switch ($method):

    default:
        http_response_code(404);
        break;

    case 'index':

        $pdo = $_SERVER['PDO'];

        // =======================================
        // AMBIL SELURUH DATA PROGRAM STUDY
        // =======================================
        $stmt = $pdo->prepare("
            SELECT 
                kode_prodi,
                jenjang,
                study_program,
                jurusan,
                major
            FROM program_study
            WHERE id_kampus = :id_kampus
            ORDER BY major ASC, study_program ASC
        ");
        $stmt->execute([':id_kampus' => $_SESSION['CDC-Data'] ['id_kampus']]);
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // =======================================
        // AMBIL MAJOR UNIK
        // =======================================
        $stmt2 = $pdo->prepare("
            SELECT DISTINCT major
            FROM program_study
            WHERE (major IS NOT NULL AND major != '') AND id_kampus = :id_kampus
            ORDER BY major ASC
        ");
        $stmt2->execute([':id_kampus' => $_SESSION['CDC-Data'] ['id_kampus']]);
        $majors = $stmt2->fetchAll(PDO::FETCH_COLUMN);

        // =======================================
        // KIRIM KE VIEW
        // =======================================
        renderView('cdc.internship_letter.index', [
            'title'   => 'Internship Letter',
            'route'   => 'cdc/internship_letter',
            'routes'  => 'cdc/internship_letter',

            // NEW — data untuk filter
            'majors'     => $majors,
            'programs'   => $programs
        ]);

        break;

    case 'detail':
        $pdo = $_SERVER['PDO'];

        if (!$id_internship_letter) {
            make_swal2("error", 'Internship Letter Not Found', 'Provide Valid Letter ID !');
            redirect_back(1);
            die;
        }

        // [TAMBAHAN] Query untuk mengambil Nomor Surat Terakhir sebagai referensi
        $sql_last_no = "SELECT full_letter_number FROM internship_letter 
                        WHERE full_letter_number IS NOT NULL AND full_letter_number != '' 
                        ORDER BY created_at DESC LIMIT 1";
        $stmt_last_no = $pdo->query($sql_last_no);
        $last_letter_number = $stmt_last_no->fetchColumn() ?: 'No records found';

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
            echo "Internship letter not found.";
            exit;
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
        renderView('cdc.internship_letter.detail', [
            'title'     => 'Internship Letter Detail',
            'routes'    => 'cdc/internship_letter',
            'route'    => 'cdc/internship_letter',
            'letter'    => $letter,
            'approvals' => $approvals,
            'histories' => $histories,
            'last_letter_number' => $last_letter_number
        ]);
        break;


    case 'letter_approval':
        $pdo = $_SERVER['PDO'];
        $id = $_POST['id_internship_letter'] ?? '';
        $action = $_POST['action'] ?? '';
        $remark = trim($_POST['remark'] ?? '');
        $letter_number = trim($_POST['letter_number'] ?? '');
        
        // Data Session CDC
        $username = $_SESSION['CDC-Data']['username'] ?? '';
        $id_kampus_session = $_SESSION['CDC-Data']['id_kampus'] ?? '';
        $user_name = $_SESSION['CDC-Data']['name'] ?? 'System';
        $user_id_label = 'CDC Administrator';

        // Validasi Input Dasar
        if (empty($id) || empty($action)) {
            Notification::swal2('Invalid Request', 'Missing required data.', 'error');
            return Redirect::back();
        }

        // Validasi Nomor Surat jika Action = Approved
        if ($action === 'approved' && empty($letter_number)) {
            Notification::swal2('Required Letter Number', 'Please enter the letter number for approval.', 'warning');
            return Redirect::back();
        }

        try {
            $pdo->beginTransaction(); // Mulai Transaksi

            // === 1. Validasi Keberadaan & Hak Akses Kampus ===
            $stmt = $pdo->prepare("
                SELECT il.id_internship_letter, il.status, s.id_kampus
                FROM internship_letter il
                JOIN student_internship s ON il.student_nim = s.nim
                WHERE il.id_internship_letter = :id
                LIMIT 1
            ");
            $stmt->execute([':id' => $id]);
            $letter = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$letter) {
                throw new Exception("Internship letter not found.");
            }

            // Keamanan: Pastikan CDC hanya mengelola mahasiswa dari kampusnya sendiri
            if ($letter['id_kampus'] != $id_kampus_session) {
                throw new Exception("Unauthorized: This letter belongs to another campus.");
            }

            // === 2. Insert ke Tabel Approval (Log Persetujuan) ===
            $stmt = $pdo->prepare("
                INSERT INTO internship_letter_approval 
                    (id_internship_letter, role, role_username, role_name, status_approval, remark, created_at)
                VALUES
                    (:id, 'cdc', :username, :user_name, :status, :remark, NOW())
            ");
            $stmt->execute([
                ':id'        => $id,
                ':username'  => $username,
                ':user_name' => $user_name,
                ':status'    => $action,
                ':remark'    => $remark
            ]);

            // Persiapan Pesan History
            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $dateFormatted = $now->format('F j, Y \a\t H:i');
            
            if ($action === 'approved') {
                $historyMessage = "Internship Letter APPROVED by CDC Polibatam on {$dateFormatted}. Letter Number: {$letter_number}. Status changed to APPROVED.";
            } else {
                $historyMessage = "Internship Letter REJECTED by CDC Polibatam on {$dateFormatted}. Status changed to REJECTED.";
            }

            if (!empty($remark)) {
                $historyMessage .= " Remark: " . $remark;
            }
            $historyMessage .= " (Processed by: {$user_name})";

            // === 3. Insert ke Tabel History (Timeline) ===
            $stmt = $pdo->prepare("
                INSERT INTO internship_letter_history 
                (id_internship_letter, status, created_at)
                VALUES (:id, :status, :created_at)
            ");
            $stmt->execute([
                ':id'         => $id,
                ':status'     => $historyMessage,
                ':created_at' => $now->format('Y-m-d H:i:s')
            ]);

            // === 4. Update Tabel Utama (internship_letter) ===
            if ($action === 'approved') {
                $stmt = $pdo->prepare("
                    UPDATE internship_letter
                    SET cdc_approval       = 'approved',
                        status             = 'APPROVED',
                        full_letter_number = :letter_number,
                        letter_date        = CURRENT_DATE(),
                        updated_at         = NOW()
                    WHERE id_internship_letter = :id
                ");
                $stmt->bindValue(':letter_number', $letter_number);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE internship_letter
                    SET cdc_approval = 'rejected',
                        status       = 'REJECTED',
                        updated_at   = NOW()
                    WHERE id_internship_letter = :id
                ");
            }
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $pdo->commit(); // Selesaikan semua proses jika tidak ada error

            Notification::swal2('Success', "Letter request has been " . strtoupper($action), 'success');
            return Redirect::to( router('cdc/internship_letter.detail', ['id' => $id]), 10);

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack(); // Batalkan semua jika ada error ditengah jalan
            }
            
            Notification::swal2('Error', $e->getMessage(), 'error');
            return Redirect::back();
        }
    break;

endswitch;
