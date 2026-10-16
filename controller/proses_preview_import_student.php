<?php
error_reporting(true);
session_start();

header('Content-Type: application/json');

require_once './library/PHPExcel/PHPExcel.php';

$dir = 'tmp/';
$nama_file_baru = 'data.xlsx';
$tmp_file = $_FILES['file']['tmp_name'];
$tipe_file = $_FILES['file']['type'];

$response = [
    'success' => false,
    'data' => [],
    'data_bermasalah' => false,
    'total_kosong' => 0,
    'message' => ''
];

// Validasi file
if ($tipe_file !== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
    $response['message'] = 'Only .xlsx files are allowed';
    echo json_encode($response);
    exit;
}

// Siapkan direktori
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
if (file_exists($dir . $nama_file_baru)) {
    unlink($dir . $nama_file_baru);
}
move_uploaded_file($tmp_file, $dir . $nama_file_baru);

// Load Excel
$excelreader = new PHPExcel_Reader_Excel2007();
$loadexcel = $excelreader->load($dir . $nama_file_baru);
$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

// Ambil semua NIM yang sudah ada di database SEKALI SAJA
$stmt_all = $_SERVER['PDO']->prepare("SELECT nim FROM student_internship");
$stmt_all->execute();
$existing_students = $stmt_all->fetchAll(PDO::FETCH_COLUMN, 0);
$existing_lookup = array_flip($existing_students); // pencarian cepat

$data_import = [];
$numrow = 1;
$kosong = 0;

foreach ($sheet as $i => $row) {
    if ($numrow > 2) {
        $student_nim = (int) $row['B'];
        $student_name = trim($row['C']);
        $lecturer_nik = trim($row['D']);
        $lecturer_name = trim($row['E']);

        if (empty($student_nim) || empty($student_name)) {
            $kosong++;
            continue;
        }

        $data_row = [
            'student_nim' => $student_nim,
            'student_name' => $student_name,
            'student_password' => $student_nim,
            'lecturer_nik' => $lecturer_nik,
            'lecturer_name' => $lecturer_name,
            'import_status' => null,
            'import_review' => 'Not Yet Imported',
            'lecturer_status' => 0,
            'lecturer_status_message' => 'Not Checked'
        ];

        // Cek apakah mahasiswa sudah terdaftar
        if (isset($existing_lookup[$student_nim])) {
            $data_row['import_status'] = true;
            $data_row['import_review'] = 'Student has been registered';
        }

        // Cek dosen
        if ($lecturer_nik !== '') {
            $stmt = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM lecturer WHERE nim_nik_unit = :nik");
            $stmt->execute([':nik' => $lecturer_nik]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $data_row['lecturer_status'] = 1;
                $data_row['lecturer_status_message'] = 'Lecturer is registered';
            } else {
                $data_row['lecturer_status'] = 0;
                $data_row['lecturer_status_message'] = 'Lecturer is not registered';
            }
        }

        $data_import[] = $data_row;
    }
    $numrow++;
}

unlink($dir . $nama_file_baru);

// Output sebagai JSON
echo json_encode([
    'success' => true,
    'data' => $data_import,
    'data_bermasalah' => $kosong > 0,
    'total_kosong' => $kosong
]);
exit;
