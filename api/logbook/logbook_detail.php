<?php

function getDataByIdLogbook($id_logbook)
{
    $pdo = $_SERVER['PDO'];
    $result = [];

    // Logbook
    $query = $pdo->prepare("SELECT * FROM logbook WHERE id_logbook = :id_logbook LIMIT 1");
    $query->execute(['id_logbook' => $id_logbook]);
    $logbook = $query->fetch(PDO::FETCH_OBJ);
    if (!$logbook) return null;

    // Internship
    $query = $pdo->prepare("SELECT * FROM internship WHERE id_internship = :id_internship LIMIT 1");
    $query->execute(['id_internship' => $logbook->id_internship]);
    $internship = $query->fetch(PDO::FETCH_OBJ);
    if (!$internship) return null;

    // Student
    $query = $pdo->prepare("SELECT * FROM student_internship WHERE nim = :nim LIMIT 1");
    $query->execute(['nim' => $internship->nim]);
    $student = $query->fetch(PDO::FETCH_OBJ);
    if (!$student) return null;

    // Supervisor
    $query = $pdo->prepare("SELECT * FROM user_company WHERE id_user_company = :id_supervisor LIMIT 1");
    $query->execute(['id_supervisor' => $internship->id_user_company]);
    $supervisor = $query->fetch(PDO::FETCH_OBJ);

    // Company
    $query = $pdo->prepare("SELECT * FROM company WHERE id_company = :id_company LIMIT 1");
    $query->execute(['id_company' => $internship->id_company]);
    $company = $query->fetch(PDO::FETCH_OBJ);

    // Dospem (bisa null)
    $dospem = null;
    if (!empty($student->nik_dospem)) {
        $query = $pdo->prepare("SELECT * FROM lecturer WHERE nim_nik_unit = :nik_dospem LIMIT 1");
        $query->execute(['nik_dospem' => $student->nik_dospem]);
        $dospem = $query->fetch(PDO::FETCH_OBJ);
    }

    $result['logbook'] = $logbook;
    $result['internship'] = $internship;
    $result['student'] = $student;
    $result['supervisor'] = $supervisor;
    $result['company'] = $company;
    $result['dospem'] = $dospem;

    return $result;
}

// Ambil id_logbook dari GET/POST dan coba decode base64 jika valid
$id_logbook_raw = $_GET['id_logbook'] ?? $_POST['id_logbook'] ?? null;
if ($id_logbook_raw && base64_encode(base64_decode($id_logbook_raw, true)) === $id_logbook_raw) {
    $id_logbook = base64_decode($id_logbook_raw);
} else {
    $id_logbook = $id_logbook_raw;
}

$result = $id_logbook ? getDataByIdLogbook($id_logbook) : null;

header('Content-Type: application/json');

if (!$result) {
    http_response_code(404);
    echo json_encode(['message' => 'Logbook data not found.']);
    exit;
}

http_response_code(200);
echo json_encode($result);
