<?php


function getDataByLecturer($nik_dosen)
{
    $result = array();

    $query = $_SERVER['PDO']->prepare("SELECT * FROM lecturer WHERE nim_nik_unit = :nim_nik_unit LIMIT 1");
    $query->execute(['nim_nik_unit' => $nik_dosen]);
    $lecturer = $query->fetch(PDO::FETCH_OBJ);

    if (!$lecturer) {
        return null;
    } else {
        unset($lecturer->password);
    }

    $query = $_SERVER['PDO']->prepare("SELECT student_internship.nim, student_internship.name, attendance.* FROM attendance, internship, student_internship WHERE attendance.id_internship = internship.id_internship 
    AND internship.nim = student_internship.nim AND student_internship.nik_dospem = :nik_dospem");
    $query->execute(['nik_dospem' => $nik_dosen]);
    $attendance = $query->fetchAll(PDO::FETCH_OBJ);

    $query = $_SERVER['PDO']->prepare("SELECT DISTINCT(MONTH(attendance.date)) AS ATTENDANCE_DATE, YEAR(attendance.date) AS ATTENDANCE_YEAR FROM attendance, internship, student_internship 
    WHERE attendance.id_internship = internship.id_internship AND internship.nim = student_internship.nim 
    AND student_internship.nik_dospem = :nik_dospem ORDER BY YEAR(attendance.date) ASC");
    $query->execute(['nik_dospem' => $nik_dosen]);
    $filter = $query->fetchAll(PDO::FETCH_OBJ);


    $result['lecturer'] = $lecturer;
    $result['attendance'] = $attendance;
    $result['filter'] = $filter;

    return $result;
}

// Jika nilai nik belum didapatkan dari parameter GET atau POST, maka tetap menggunakan nilai asli
$nik = $_GET['nik'] ?? $_POST['nik'];

// Memanggil fungsi untuk mendapatkan data berdasarkan nik
$result = getDataByLecturer($nik);

if (!$result) {
    // Data tidak ditemukan, kembali dengan response error
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(array('message' => 'Attendance data not found.', 'error_code' => '400'));
    exit;
}

// Mengirimkan response dengan data yang berhasil ditemukan
header('Content-Type: application/json');
http_response_code(200);
echo json_encode( $result );