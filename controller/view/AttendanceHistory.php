<?php
middleware('MahasiswaMiddleware');

$data = JWTCustomData($_GET['id_internship']);
$id_internship = $data['id_internship'];

// Pengecekan Internship
$nim = $_SESSION['nim'];

// Define the SQL query with a placeholder for the variable
$sql = "SELECT *
    FROM student_internship si
    JOIN internship i ON si.nim = i.nim
    WHERE si.nim = :nim AND i.id_internship = :id_internship
    LIMIT 1";

// Prepare the SQL statement
$stmt = $_SERVER['PDO']->prepare($sql);

// Bind the values to the placeholders
$stmt->execute([':nim' => $nim, ':id_internship' => $id_internship]);

// Check the number of rows returned
if ($stmt->rowCount() != 1) {
    make_swal2('warning', 'Not Allowed', 'You don`t have access to this page !');
    redirect_back(1);
    exit;
}


// You can fetch the result if needed
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// view('mahasiswa/attendance_internship/add_history', ['id_internship' => $id_internship, 'student_data' => $result]);
renderView('student.my_internship.attendance.add', ['id_internship' => $id_internship, 'student_data' => $result, 'title' => 'Add Attendance', 'routes' => 'my_internship']);