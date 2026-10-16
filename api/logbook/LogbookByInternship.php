<?php


function getDataByInternship($id_internship)
{
    $result = array();

    $query = $_SERVER['PDO']->prepare("SELECT student_internship.nim, student_internship.name, logbook.* FROM logbook, internship, student_internship WHERE logbook.id_internship = internship.id_internship 
    AND internship.nim = student_internship.nim AND internship.id_internship = :id_internship");
    $query->execute(['id_internship' => $id_internship]);
    $logbook = $query->fetchAll(PDO::FETCH_OBJ);

    if (!$logbook) {
        return null;
    }


    $query = $_SERVER['PDO']->prepare("SELECT student_internship.* FROM logbook, internship, student_internship WHERE logbook.id_internship = internship.id_internship 
    AND internship.nim = student_internship.nim AND internship.id_internship = :id_internship");
    $query->execute(['id_internship' => $id_internship]);
    $student = $query->fetchAll(PDO::FETCH_OBJ);

    if (!$student) {
        return null;
    }


    $result['student'] = $student;
    $result['logbook'] = $logbook;

    return $result;
}

// Jika nilai nik belum didapatkan dari parameter GET atau POST, maka tetap menggunakan nilai asli
$id_internship = $_GET['id_internship'] ?? $_POST['id_internship'];

// Memanggil fungsi untuk mendapatkan data berdasarkan nik
$result = getDataByInternship($id_internship);

if (!$result) {
    // Data tidak ditemukan, kembali dengan response error
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(array('message' => 'Logbook data not found.'));
    exit;
}

// Mengirimkan response dengan data yang berhasil ditemukan
header('Content-Type: application/json');
http_response_code(200);
echo json_encode( $result );