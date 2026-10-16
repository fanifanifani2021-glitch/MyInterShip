<?php
middleware('MahasiswaMiddleware');

$id_internship = base64_decode($_GET['id_internship']);
$id_attendance = base64_decode($_GET['id_attendance']);

// Get ID
$sql = "SELECT `attendance`.*, `attendance-signature`.`signature` 
        FROM attendance 
        LEFT JOIN `attendance-signature` ON attendance.id_attendance = `attendance-signature`.id_attendance 
        WHERE attendance.id_attendance = :id_attendance 
        AND attendance.id_internship = :id_internship 
        LIMIT 1";

$stmt = $_SERVER['PDO']->prepare($sql);

$stmt->execute(['id_attendance' => $id_attendance, 'id_internship' => $id_internship]);

if ($stmt->rowCount() != 1) {
    make_swal2('error', 'Not Found', 'Attendance data not found !');
    redirect_back(1);
    exit;
}

$attendance = $stmt->fetch(PDO::FETCH_ASSOC);
$attendance['SignatureAttendance'] = "index.php?viewer=internship_signature&id_internship=".$_GET['id_internship']. "&id_attendance=".$_GET['id_attendance'];

// view('mahasiswa/attendance_internship/detail_attendance', ['attendance' => $attendance, 'id_internship' => $id_internship, 'id_attendance' => $id_attendance]);

renderView('student.my_internship.attendance.detail', ['title' => 'Attendance Detail', 'routes' => 'my_internship', 'attendance' => $attendance, 'id_internship' => $id_internship, 'id_attendance' => $id_attendance]);
