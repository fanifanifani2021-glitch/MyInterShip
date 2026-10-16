<?php
session_start();
header('Content-Type: application/json');

$kode_prodi = $_POST['kode_program_study'] ?? null;
$data = $_POST['data'] ?? [];

if (!$kode_prodi || empty($data) || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input or no data provided.']);
    exit;
}

try {
    $pdo = $_SERVER['PDO'];
    $pdo->beginTransaction();

    $id_kampus = $_SESSION['CDC_Data']['id_kampus'] ?? $_SESSION['Lecturer-Data']['id_kampus'] ?? 1;
    $inserted = 0;

    // Statement insert/update mahasiswa
    $stmtStudent = $pdo->prepare("
        INSERT INTO student_internship (
            nim, name, program_study, email, other_email, phone, no_whatsapp,
            username, password, profile_picture, nik_dospem, id_kampus,
            account_status, local_account, password_reset
        ) VALUES (
            :nim, :name, :program_study, :email, :other_email, :phone, :no_whatsapp,
            :username, :password, :profile_picture, :nik_dospem, :id_kampus,
            :account_status, :local_account, :password_reset
        )
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            nik_dospem = VALUES(nik_dospem)
    ");

    // Statement select lecturer
    $stmtCheckLecturer = $pdo->prepare("SELECT COUNT(*) FROM lecturer WHERE nim_nik_unit = :nik");

    // Statement insert lecturer
    $stmtInsertLecturer = $pdo->prepare("
        INSERT INTO lecturer (
            nim_nik_unit, name, email_polibatam, is_koor, prodi_koor,
            status, password, profile_picture, id_kampus
        ) VALUES (
            :nim_nik_unit, :name, :email_polibatam, :is_koor, :prodi_koor,
            :status, :password, :profile_picture, :id_kampus
        )
    ");

    foreach ($data as $student) {
        $nim = trim($student['student_nim'] ?? '');
        $name = trim($student['student_name'] ?? '');
        $passwordRaw = $student['student_password'] ?? $nim;
        $nik_dospem = ($student['lecturer_nik'] === 'Unassigned' || empty($student['lecturer_nik'])) ? null : $student['lecturer_nik'];
        $lecturer_name = $student['lecturer_name'] ?? null;

        if (!$nim || !$name) continue;

        // Jika ada dospem, cek dosennya sudah ada atau belum
        if ($nik_dospem && $lecturer_name) {
            $stmtCheckLecturer->execute([':nik' => $nik_dospem]);
            $exists = $stmtCheckLecturer->fetchColumn();

            if (!$exists) {
                $stmtInsertLecturer->execute([
                    ':nim_nik_unit' => $nik_dospem,
                    ':name' => $lecturer_name,
                    ':email_polibatam' => '-',
                    ':is_koor' => 0,
                    ':prodi_koor' => '',
                    ':status' => 'Active',
                    ':password' => password_hash($nik_dospem, PASSWORD_DEFAULT),
                    ':profile_picture' => 'profile.jpg',
                    ':id_kampus' => $id_kampus
                ]);
            }
        }

        // Proses mahasiswa
        $stmtStudent->execute([
            ':nim' => $nim,
            ':name' => $name,
            ':program_study' => $kode_prodi,
            ':email' => '-',
            ':other_email' => '-',
            ':phone' => '-',
            ':no_whatsapp' => '-',
            ':username' => $nim,
            ':password' => password_hash($passwordRaw, PASSWORD_DEFAULT),
            ':profile_picture' => 'profile.jpg',
            ':nik_dospem' => $nik_dospem,
            ':id_kampus' => $id_kampus,
            ':account_status' => 1,
            ':local_account' => 1,
            ':password_reset' => 0
        ]);

        $inserted++;
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'inserted' => $inserted,
        'message' => "$inserted student(s) imported successfully."
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Import failed: ' . $e->getMessage()
    ]);
}
