<?php
$token = bin2hex(random_bytes(16));
$_SESSION['PRINT_TOKEN'] = $token;

switch ($method):
    
    default:
        http_response_code(404);
    break;

    case 'index':
        renderView('coordinator.internship_letter.index', ['title' => 'Internship Letter', 'routes' => 'coordinator/internship_letter']);
    break;

    case 'detail':
        $pdo = $_SERVER['PDO'];
        $prodiKoor = $_SESSION['prodi_koor'] ?? '';

        if (empty($id)) {
            renderView('errors.404', ['message' => 'Letter ID not provided']);
            exit;
        }

        try {
            // === 1️⃣ Ambil data surat + info mahasiswa + company ===
            $sql = "
                SELECT 
                    il.*, 
                    s.name AS student_name,
                    s.program_study,
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

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $letter = $stmt->fetch(PDO::FETCH_ASSOC);

            // === 2️⃣ Jika surat tidak ditemukan ===
            if (!$letter) {
                renderView('errors.404', ['message' => 'Internship letter not found']);
                exit;
            }

            // === 3️⃣ Cek hak akses koordinator (hanya boleh akses prodi sendiri) ===
            if ($letter['program_study'] !== $prodiKoor) {
                renderView('errors.403', [
                    'message' => 'Access denied. This letter belongs to another study program.'
                ]);
                exit;
            }

            // === 4️⃣ Ambil approval record ===
            $sql_approval = "
                SELECT *
                FROM internship_letter_approval
                WHERE id_internship_letter = :id
                ORDER BY created_at ASC
            ";
            $stmt = $pdo->prepare($sql_approval);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // === 5️⃣ Ambil riwayat perubahan status ===
            $sql_history = "
                SELECT *
                FROM internship_letter_history
                WHERE id_internship_letter = :id
                ORDER BY created_at DESC
            ";
            $stmt = $pdo->prepare($sql_history);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $histories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // === 6️⃣ Kirim ke view ===
            renderView('coordinator.internship_letter.detail', [
                'title' => 'Internship Letter',
                'routes' => 'coordinator/internship_letter',
                'letter' => $letter,
                'approvals' => $approvals,
                'histories' => $histories
            ]);

        } catch (Exception $e) {
            renderView('errors.500', [
                'message' => 'Unexpected error occurred: ' . htmlspecialchars($e->getMessage())
            ]);
        }
    break;


    case 'letter_approval':
        $pdo = $_SERVER['PDO'];
        $id = $_POST['id_internship_letter'] ?? '';
        $action = $_POST['action'] ?? '';
        $remark = trim($_POST['remark'] ?? '');
        $username = $_SESSION['Lecturer-Data'] ['nim_nik_unit'] ?? '';
        $prodiKoor = $_SESSION['Lecturer-Data'] ['prodi_koor'] ?? '';
        $user_id = $_SESSION['Lecturer-Data'] ['nim_nik_unit'] ?? 'unknown';
        $user_name = $_SESSION['Lecturer-Data'] ['name'] ?? 'System';

        $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $timestamp = $now->format('F d, Y \a\t H:i');       

        if (empty($id) || empty($action)) {
            make_swal2('error', 'Invalid Request', 'Missing required data.');
            redirect_back(1);
            exit;
        }

        try {
            // === 1️⃣ Validasi apakah surat valid dan milik prodi yang sesuai ===
            $stmt = $pdo->prepare("
                SELECT il.id_internship_letter, il.status, s.program_study
                FROM internship_letter il
                JOIN student_internship s ON il.student_nim = s.nim
                WHERE il.id_internship_letter = :id
                LIMIT 1
            ");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $letter = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$letter) {
                make_swal2('error', 'Not Found', 'Letter not found.');
                redirect_back(1);
                exit;
            }

            if ($letter['program_study'] !== $prodiKoor) {
                make_swal2('error', 'Access Denied', 'Letter belongs to another study program.');
                redirect_back(1);
                exit;
            }

            // === 2️⃣ Insert ke tabel approval ===
            $stmt = $pdo->prepare("
                INSERT INTO internship_letter_approval 
                    (id_internship_letter, role, role_username, role_name, status_approval, remark, created_at)
                VALUES
                    (:id, 'coor', :username, :user_name, :status, :remark, NOW())
            ");
            $stmt->execute([
                ':id' => $id,
                ':username' => $username,
                ':user_name' => $user_name,
                ':status' => $action,
                ':remark' => $remark
            ]);

            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $dateFormatted = $now->format('F j, Y \a\t H:i');

            if ($action === 'approved') {
                $historyMessage = "Internship Letter APPROVED by Internship Coordinator on {$dateFormatted}, Letter status changed to Waiting for CDC Approval";
            } else {
                $historyMessage = "Internship Letter REJECTED by Internship Coordinator on {$dateFormatted}, Letter status changed to REJECTED";
            }

            // Tambahkan remark jika ada
            if (!empty($remark)) {
                $historyMessage .= " with Remark: " . trim($remark);
            }

            $historyMessage .= " by {$user_id} | {$user_name}";



            // === 4️⃣ Insert ke tabel history ===
            $stmt = $pdo->prepare("
                INSERT INTO internship_letter_history 
                (id_internship_letter, status, created_at)
                VALUES (:id, :status, :created_at)
            ");
            $stmt->execute([
                ':id' => $id,
                ':status' => $historyMessage,
                ':created_at' => $now->format('Y-m-d H:i:s')
            ]);

            // === 5️⃣ Update status di tabel utama ===
            if ($action === 'approved') {
                // Jika disetujui oleh koordinator → lanjut ke CDC
                $stmt = $pdo->prepare("
                    UPDATE internship_letter
                    SET coor_approval = 'APPROVED',
                        status = 'WAITING',
                        cdc_approval = 'waiting',
                        updated_at = NOW()
                    WHERE id_internship_letter = :id
                ");
            } else {
                // Jika ditolak oleh koordinator → surat langsung REJECTED
                $stmt = $pdo->prepare("
                    UPDATE internship_letter
                    SET coor_approval = 'REJECTED',
                        status = 'REJECTED',
                        cdc_approval = 'pending',
                        updated_at = NOW()
                    WHERE id_internship_letter = :id
                ");
            }

            $stmt->execute([':id' => $id]);

            // === 6️⃣ Notifikasi dan redirect ===
            make_swal2('success', 'Success', "Letter has been successfully {$action} by Coordinator.");
            redirect( base_url("/coordinator/internship_letter") );
            exit;

        } catch (Exception $e) {
            var_dump($e->getMessage()); die;
            renderView('errors.500', [
                'message' => 'Unexpected error: ' . htmlspecialchars($e->getMessage())
            ]);
        }
    break;



endswitch;
