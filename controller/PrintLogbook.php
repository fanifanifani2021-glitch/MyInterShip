<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id_logbook = $_GET['id_logbook'];
// URL target
$url = base_url('api.php?request=logbook_detail');

// Inisialisasi CURL
$ch = curl_init();

$data = [
    'token' => env('APP_TOKEN'),
    'id_logbook' => $id_logbook
];

// Set URL
curl_setopt($ch, CURLOPT_URL, $url);

// Set untuk mengembalikan respons sebagai string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Set metode permintaan menjadi POST
curl_setopt($ch, CURLOPT_POST, true);

// Set data POST yang akan dikirim
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


// Eksekusi CURL dan ambil respons
$response = curl_exec($ch);

$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Cek apakah ada kesalahan CURL
if (curl_errno($ch) || $code != 200) {
    make_swal2('error', 'Not Found', 'Failed to fetch Logbook Data !');
    redirect_back(1);
    exit;
}

// Tutup CURL
curl_close($ch);

$response = json_decode($response);

if (!function_exists('createCustomJWT')) {
    function createCustomJWT($payload)
    {
        $secret = APP_KEY ?? 'aaaa';


        $header_data = array('alg' => 'HS256', 'typ' => 'JWT', 'created_at' => time());

        $header = base64_encode(json_encode($header_data));
        $payload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$header.$payload", $secret, true);
        $signature = base64_encode($signature);
        return "$header.$payload.$signature";
    }
}


function generate_qr($id_logbook = 00, $role = 'Supervisor')
{


    $PNG_TEMP_DIR = './tmp/qr/';

    if (!is_dir($PNG_TEMP_DIR)) {
        mkdir($PNG_TEMP_DIR);
    }

    $logopath = "./assets/icon-qr.png";

    $payload = array('id_logbook' => $id_logbook, 'role' => $role);
    $payload = createCustomJWT($payload);
    $data = base_url("index.php?page=logbook_approval&data=" . $payload);


    $filename = $PNG_TEMP_DIR . uniqid() . ".png";

    // Call Function QR Code
    QRcode::png($data, $filename, "H", 10, 4);

    $QR = imagecreatefrompng($filename);

    $logo = imagecreatefromstring(file_get_contents($logopath));

    imagecolortransparent($logo, imagecolorallocatealpha($logo, 0, 0, 0, 127));
    imagealphablending($logo, false);
    imagesavealpha($logo, true);

    $QR_width = imagesx($QR);
    $QR_height = imagesy($QR);

    $logo_width = imagesx($logo);
    $logo_height = imagesy($logo);

    $logo_qr_width = $QR_width / 4;
    $scale = $logo_width / $logo_qr_width;
    $logo_qr_height = $logo_height / $scale;

    imagecopyresampled($QR, $logo, $QR_width / 2.7, $QR_height / 2.7, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

    // Simpan kode QR lagi, dengan logo di atasnya
    imagepng($QR, $filename);

    return $filename;
    // return $data;
}

use PhpOffice\PhpWord\Element\InlineImage;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\ExtendedTemplateProcessor;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Section;

use PhpOffice\PhpWord\Shared\Html;

// Set kata sandi
$password = 'your_password';

require_once './vendor/autoload.php';
include "./library/phpqrcode/qrlib.php";

$templateProcessor = new TemplateProcessor('./word/logbook_template.docx');

$templateProcessor->setValues([
    'current_url' => base_url(),
    'current_time' => date('Y-m-d') . ' at ' . date('H:i:s'),
]);

$templateProcessor->setValues([
    'tanggal_generate' => date('d M Y', strtotime($response->logbook->end_date)) ?? date('d M Y'),
    'minggu' => $response->logbook->week_num,
    'tanggal_mulai' => date('d M Y', strtotime($response->logbook->start_date)),
    'tanggal_selesai' => date('d M Y', strtotime($response->logbook->end_date)),
]);

$templateProcessor->setValues([
    'nama_mahasiswa' => $response->student->name,
    'nim_mahasiswa' => $response->student->nim,
    'telp_email_mahasiswa' => $response->student->email ?? 'NaN',
]);

$templateProcessor->setValues([
    'nama_perusahaan' => $response->company->name,
    'alamat_perusahaan' => $response->company->address,
]);

// Dari database, description masih mengandung HTML
$desc = $response->logbook->description ?? '-';

// Bersihkan semua tag HTML agar hanya teks murni
$desc_clean = strip_tags($desc);

// Hilangkan entitas HTML seperti &nbsp; atau &amp;
$desc_clean = html_entity_decode($desc_clean, ENT_QUOTES, 'UTF-8');

// Set ke template Word
$templateProcessor->setValue('isi_logbook', $desc_clean);

// Isi dan Dokumentasi Logbook
// $templateProcessor->setValue('isi_logbook', escape_string(htmlspecialchars($response->logbook->description)) ?? '-');
// $templateProcessor->setValue('isi_logbook', '[[ISI_LOGBOOK_HTML]]');
$templateProcessor->setValue('dokumentasi_logbook',  escape_string(htmlspecialchars($response->logbook->documentation))  ?? '-');



// Validasi Supervisor dan Dosoem
$templateProcessor->setValues([
    'nama_supervisor' => isset($response->supervisor) && isset($response->supervisor->user_fullname) ? $response->supervisor->user_fullname : 'Belum diset',
    'telp_email_supervisor' => isset($response->supervisor) && isset($response->supervisor->user_phone) ? $response->supervisor->user_phone : 'Belum diset',
]);


$templateProcessor->setValues([
    'nama_dospem' => isset($response->dospem) && isset($response->dospem->name) ? $response->dospem->name : 'Belum diset',
    'nik_dospem' => isset($response->dospem) && isset($response->dospem->nim_nik_unit) ? $response->dospem->nim_nik_unit : 'Belum diset',
]);



$generated_qr = array();

// Generate QR Code SPV
if ($response->logbook->approval_spv != 'Waiting') {
    $qr_supervisor = generate_qr($response->logbook->id_logbook);
    $templateProcessor->setImageValue('validasi_supervisor', array('path' => $qr_supervisor, 'width' => 100, 'height' => 100));
    array_push($generated_qr, $qr_supervisor);
} else {
    $templateProcessor->setValue('validasi_supervisor', 'Waiting for Approval');
}


// Generate QR Code SPV
if ($response->logbook->approval_dpm != 'Waiting') {
    $qr_supervisor = generate_qr($response->logbook->id_logbook, 'Lecturer');
    $templateProcessor->setImageValue('validasi_dospem', array('path' => $qr_supervisor, 'width' => 100, 'height' => 100));
    array_push($generated_qr, $qr_supervisor);
} else {
    $templateProcessor->setValue('validasi_dospem', 'Waiting for Approval');
}


$new_name = uniqid() . '-generate.docx';
$path = './word/generate/' . $new_name;
$templateProcessor->saveAs($path);

$generated_name = "Logbook_" . $response->student->nim . "_W" . $response->logbook->week_num . "-" . date('MY', strtotime($response->logbook->start_date)) . '.docx';

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

foreach ($generated_qr as $value) {
    unlink($value);
}
