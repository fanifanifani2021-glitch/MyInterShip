<?php


// Get Data Student feedback
$id_student_feedback = base64_decode($_GET['id_student_feedback']);

$query = $_SERVER['PDO']->prepare("SELECT * FROM student_feedback WHERE id_student_feedback = :id_student_feedback LIMIT 1");
$query->execute(['id_student_feedback' => $id_student_feedback]);

$student_feedback = $query->fetch(PDO::FETCH_OBJ);

if (!$student_feedback) {
    // Data tidak ditemukan, kembali ke halaman sebelumnya
    make_swal2('error', 'Not Found', 'Student Feedback data is not found !');
    redirect_back();
    exit;
}


// Get Internship Data
$id_internship = $student_feedback->id_internship;
$query = $_SERVER['PDO']->prepare("SELECT * FROM internship WHERE id_internship = :id_internship LIMIT 1");
$query->execute(['id_internship' => $id_internship]);

$internship = $query->fetch(PDO::FETCH_OBJ);

if (!$internship) {
    // Data tidak ditemukan, kembali ke halaman sebelumnya
    make_swal2('error', 'Not Found', 'Logbook data is not found !');
    redirect_back();
    exit;
}

// Get Student Data
$nim = $internship->nim;
$query = $_SERVER['PDO']->prepare("SELECT student_internship.nim, student_internship.name, program_study.jenjang, program_study.prodi, program_study.study_program FROM student_internship, program_study WHERE program_study.kode_prodi = student_internship.program_study AND student_internship.nim = :nim LIMIT 1");
$query->execute(['nim' => $nim]);

$student = $query->fetch(PDO::FETCH_OBJ);

if (!$student) {
    // Data tidak ditemukan, kembali ke halaman sebelumnya
    make_swal2('error', 'Not Found', 'Logbook data is not found !');
    redirect_back();
    exit;
}

// Get Company Data
$id_company = $internship->id_company;
$query = $_SERVER['PDO']->prepare("SELECT * FROM company WHERE id_company = :id_company LIMIT 1");
$query->execute(['id_company' => $id_company]);

$company = $query->fetch(PDO::FETCH_OBJ);

if (!$company) {
    // Data tidak ditemukan, kembali ke halaman sebelumnya
    make_swal2('error', 'Not Found', 'Logbook data is not found !');
    redirect_back();
    exit;
}

use PhpOffice\PhpWord\Element\InlineImage;
use PhpOffice\PhpWord\TemplateProcessor;

require_once './vendor/autoload.php';

$templateProcessor = new TemplateProcessor('./word/student_feedback_template.docx');

$templateProcessor->setValues([
    'current_url' => base_url(),
    'current_time' => date('Y-m-d') . ' at ' . date('H:i:s'),
]);

$templateProcessor->setValues([
    'tahun' => date('Y', strtotime($student_feedback->date)) ?? date('Y'),
]);

$templateProcessor->setValues([
    'nama_mahasiswa' => $student->name,
    'nim_mahasiswa' => $student->nim,
    'prodi_mahasiswa' => $student->jenjang . ' ' . $student->prodi ?? 'NaN',
]);

$templateProcessor->setValues([
    'tempat_magang' => $company->name,
]);


$templateProcessor->setValues([
    'kesan' => escape_string(htmlspecialchars( $student_feedback->kesan )),
    'kendala' => escape_string(htmlspecialchars( $student_feedback->kendala )),
    'masukan' => escape_string(htmlspecialchars( $student_feedback->masukan )),
]);


$centang = [
    'posisi_1' => '',
    'posisi_2' => '',
    'posisi_3' => '',
    'posisi_4' => '',

    'ilmu_1' => '',
    'ilmu_2' => '',
    'ilmu_3' => '',
    'ilmu_4' => '',

    'baru_1' => '',
    'baru_2' => '',
    'baru_3' => '',
    'baru_4' => '',

    'data_1' => '',
    'data_2' => '',
    'data_3' => '',
    'data_4' => '',
];

$centang['posisi_' . $student_feedback->posisi] = '✓';

$centang['ilmu_' . $student_feedback->ilmu_dari_poltek] = '✓';

$centang['baru_' . $student_feedback->ilmu_baru] = '✓';

$centang['data_' . $student_feedback->data_magang] = '✓';

$templateProcessor->setValues($centang);



$new_name = uniqid() . '-generate.docx';
$path = './word/generate/' . $new_name;
$templateProcessor->saveAs($path);

$generated_name = "StudentFeedback_" . $student->nim . '.docx';

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $generated_name . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($path));

// Baca dan kirim file ke output
readfile($path);

// Hapus file sementara setelah selesai di-download
if ($_GET['bulk_download'] !== TRUE) {
    unlink($path);
}
