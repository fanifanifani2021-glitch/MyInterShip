<?php


// Get Data Student feedback
$id = decrypt($_GET['id']);

$query = $_SERVER['PDO']->prepare("SELECT * FROM industry_feedback WHERE id_internship = :id_internship AND terisi = 1 LIMIT 1");
$query->execute(['id_internship' => $id]);

$industry_feedback = $query->fetch(PDO::FETCH_OBJ);

if (!$industry_feedback) {
    // Data tidak ditemukan, kembali ke halaman sebelumnya
    make_error_notif('Industry feedback not found or your supervisor has not yet filled it!');

    make_swal2('error', 'Not Found', '(1) Industry Feedback is not found !');
    redirect_back(1);
    exit;
}


// Get Internship Data
$query = $_SERVER['PDO']->prepare("SELECT * FROM internship WHERE id_internship = :id_internship LIMIT 1");
$query->execute(['id_internship' => $id]);

$internship = $query->fetch(PDO::FETCH_OBJ);

if (!$internship) {
    // Data tidak ditemukan, kembali ke halaman sebelumnya
    make_swal2('error', 'Not Found', '(2) Internship data is not found !');
    redirect_back(1);
    exit;
}

// Get Student Data
$nim = $internship->nim;
$query = $_SERVER['PDO']->prepare("SELECT student_internship.nim, student_internship.name, program_study.jenjang, program_study.prodi, program_study.study_program FROM student_internship, program_study WHERE program_study.kode_prodi = student_internship.program_study AND student_internship.nim = :nim LIMIT 1");
$query->execute(['nim' => $nim]);

$student = $query->fetch(PDO::FETCH_OBJ);

if (!$student) {
    // Data tidak ditemukan, kembali ke halaman sebelumnya
    make_swal2('error', 'Not Found', '(3) Student data is not found !');
    redirect_back(1);
    exit;
}

// Get Company Data
$id_company = $internship->id_company;
$query = $_SERVER['PDO']->prepare("SELECT * FROM company WHERE id_company = :id_company LIMIT 1");
$query->execute(['id_company' => $id_company]);

$company = $query->fetch(PDO::FETCH_OBJ);

if (!$company) {
    // Data tidak ditemukan, kembali ke halaman sebelumnya
    make_swal2('error', 'Not Found', '(4) Internship data is not found !');
    redirect_back(1);
    exit;
}


$id_user_company = $internship->id_user_company;

$query = $_SERVER['PDO']->prepare("SELECT * FROM user_company WHERE id_user_company = :id_user_company LIMIT 1");
$query->execute(['id_user_company' => $id_user_company]);

$Usercompany = $query->fetch(PDO::FETCH_OBJ);

if (!$Usercompany) {
    $Usercompany = NULL;
}


$exportLanguage = $_GET['exportLanguage'];

switch ($exportLanguage):

    default:
        make_swal2('error', 'Not Supported', 'Export Language is not available');
        redirect_back(1);
        break;

    case 'ID':
        $template = 'industry_feedback_template_ID.docx';
        break;

    case 'EN':

        // make_swal2('error', 'Not Supported', 'Export Language is not available');
        // redirect_back();
        $template = 'industry_feedback_template_EN.docx';
        break;

endswitch;



use PhpOffice\PhpWord\Element\InlineImage;
use PhpOffice\PhpWord\TemplateProcessor;

require_once './vendor/autoload.php';

$templateProcessor = new TemplateProcessor('./word/' . $template);

$templateProcessor->setValues([
    'current_url' => base_url(),
    'current_time' => date('Y-m-d') . ' at ' . date('H:i:s'),
    'current_date' => date('d M Y', strtotime($industry_feedback->date)) ?? date('Y-m-d'),
]);

$templateProcessor->setValues([
    'tahun' => date('Y', strtotime($industry_feedback->date)) ?? date('Y'),
]);

$templateProcessor->setValues([
    'nama_mahasiswa' => $student->name,
    'nim_mahasiswa' => $student->nim,
    'prodi_mahasiswa' => $student->jenjang . ' ' . $student->prodi ?? 'NaN',
]);

$templateProcessor->setValues([
    'nama_perusahaan' => $company->name,

    'nama_supervisor' => $Usercompany->user_fullname ?? 'NaN',
]);


$templateProcessor->setValues([
    'nilai_akhir' => escape_string(htmlspecialchars($industry_feedback->nilai_akhir)),
    'catatan_utk_mahasiswa' => escape_string(htmlspecialchars($industry_feedback->catatan_utk_mahasiswa)),
    'catatan_utk_poltek' => escape_string(htmlspecialchars($industry_feedback->catatan_utk_poltek)),
    'layak_diterima' => escape_string(htmlspecialchars($industry_feedback->layak_diterima)),
    'langsung_diterima' => escape_string(htmlspecialchars($industry_feedback->langsung_diterima)),
]);


$centang = [
    'etika_1' => '',
    'etika_2' => '',
    'etika_3' => '',
    'etika_4' => '',

    'keahlian_kompetensi_1' => '',
    'keahlian_kompetensi_2' => '',
    'keahlian_kompetensi_3' => '',
    'keahlian_kompetensi_4' => '',

    'keahlian_bahasa_1' => '',
    'keahlian_bahasa_2' => '',
    'keahlian_bahasa_3' => '',
    'keahlian_bahasa_4' => '',

    'penggunaan_ti_1' => '',
    'penggunaan_ti_2' => '',
    'penggunaan_ti_3' => '',
    'penggunaan_ti_4' => '',

    'komunikasi_1' => '',
    'komunikasi_2' => '',
    'komunikasi_3' => '',
    'komunikasi_4' => '',

    'kerjasama_1' => '',
    'kerjasama_2' => '',
    'kerjasama_3' => '',
    'kerjasama_4' => '',

    'pengembangan_diri_1' => '',
    'pengembangan_diri_2' => '',
    'pengembangan_diri_3' => '',
    'pengembangan_diri_4' => '',
];

$centang['etika_' . $industry_feedback->etika] = '✓';

$centang['keahlian_kompetensi_' . $industry_feedback->keahlian_kompetensi] = '✓';

$centang['keahlian_bahasa_' . $industry_feedback->keahlian_bahasa] = '✓';

$centang['penggunaan_ti_' . $industry_feedback->penggunaan_ti] = '✓';

$centang['komunikasi_' . $industry_feedback->komunikasi] = '✓';

$centang['kerjasama_' . $industry_feedback->kerjasama] = '✓';

$centang['pengembangan_diri_' . $industry_feedback->pengembangan_diri] = '✓';

$templateProcessor->setValues($centang);


// $generated_name = "IndustryFeedback_" . $exportLanguage . '_' . $student->nim . '.docx';
$generated_name = "IndustryFeedback_" . $student->nim . '.docx';
$new_name = $generated_name;
// $new_name = uniqid() . '-generate.docx';
$path = './word/generate/' . $new_name;
$templateProcessor->saveAs($path);

if ($_GET['bulk_download'] != 1) {

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
    unlink($path);
} else {
    http_response_code(200);
}
