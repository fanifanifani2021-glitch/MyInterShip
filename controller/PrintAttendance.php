<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id_internship = $_GET['id_internship'];
$filter = $_GET['filter'];
// URL target
$url = base_url('api.php?request=attendance_detail');

// Inisialisasi CURL
$ch = curl_init();

$data = [
    'token' => env('APP_TOKEN'),
    'id_internship' => $id_internship,
    'filter' => $filter,
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
    make_swal2('error', 'Not Found', 'Failed to fetch Attendance Data !');
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


function generate_qr($id_attendance = 00, $role = 'Supervisor')
{


    $PNG_TEMP_DIR = './tmp/qr/';

    if (!is_dir($PNG_TEMP_DIR)) {
        mkdir($PNG_TEMP_DIR);
    }

    $logopath = "./assets/icon-qr.png";

    $payload = array('id_attendance' => $id_attendance, 'role' => $role);
    $payload = createCustomJWT($payload);
    $data = base_url("index.php?page=attendance_approval&data=" . $payload);


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


// Set kata sandi
$password = 'your_password';


require_once './vendor/autoload.php';
include "./library/phpqrcode/qrlib.php";


$templateProcessor = new TemplateProcessor('./word/attendance_template-2.docx');
// $templateProcessor = new ExtendedTemplateProcessor('./word/attendance_template-2.docx');




$templateProcessor->setValues([
    'current_url' => base_url(),
    'current_time' => date('Y-m-d') . ' at ' . date('H:i:s'),
    'tanggal_cetak' => date('d M Y'),

]);

$templateProcessor->setValues([
    'nama_mahasiswa' => $response->student->name,
    'nim_mahasiswa' => $response->student->nim,
]);



$templateProcessor->setValues([
    'nama_perusahaan' => $response->company->name,
    'bulan_presensi' => date('M Y', strtotime($filter)),
]);

$avail_days = [
    '1_1', '1_2', '1_3', '1_4', '1_5', '2_1', '2_2', '2_3', '2_4', '2_5',
    '3_1', '3_2', '3_3', '3_4', '3_5', '4_1', '4_2', '4_3', '4_4', '4_5',
    '5_1', '5_2', '5_3', '5_4', '5_5'
];

$weekdays = json_decode(json_encode($response->weekdays), true);
$new_attendance = json_decode(json_encode($response->new_attendance), true);

$generated_qr = array();


foreach ($new_attendance as $key => $value) {
    $tanggal = date('d M Y', strtotime($key));

    $list = $value['week_num'] . "_" . $value['dateOrder'];

    if ($value['data'] != null) {

        $supervisor = 'Not Yet Available';
        $pembimbing = 'Nanti Cetak QR Code';


        $tanggal .= " " . $value['data']['attendance_type'] ?? 'Abstaint';

        if (strtotime(date('Y-m-d')) < strtotime($key)) {
            $masuk = 'Not Yet Available';
            $pulang = 'Not Yet Available';
        } else {

            if ($value['data']['attendance_type'] == 'National Holiday') {
                $masuk = 'Holiday';
                $pulang = 'Holiday';
            } else if ($value['data']['attendance_type'] == 'Present') {
                $masuk = date('H:i', strtotime($value['data']['check_in'])) ?? 'NaN';
                $pulang = date('H:i', strtotime($value['data']['check_out'])) ?? 'NaN';
            } else {
                $masuk = 'NaN';
                $pulang = 'NaN';
            }
        }



        // Generate QR Code SPV
        if ($value['data']['approval_spv'] != 'Waiting') {
            $qr_supervisor = generate_qr($value['data']['id_attendance']);
            $templateProcessor->setImageValue('supervisor_' . $list, array('path' => $qr_supervisor, 'width' => 100, 'height' => 100));
            array_push($generated_qr, $qr_supervisor);
        } else {
            $templateProcessor->setValue('supervisor_' . $list, 'Waiting for Approval');
        }

        // Generate QR Code Lecturer
        if ($value['data']['approval_dpm'] != 'Waiting') {
            $qr_pembimbing = generate_qr($value['data']['id_attendance'], 'Lecturer');
            $templateProcessor->setImageValue('pembimbing_' . $list, array('path' => $qr_pembimbing, 'width' => 100, 'height' => 100));
            array_push($generated_qr, $qr_pembimbing);
        } else {
            $templateProcessor->setValue('pembimbing_' . $list, 'Waiting for Approval');
        }
    } else {
        $tanggal .= " Not Available";
        $masuk = 'Not Yet Available';
        $pulang = 'Not Yet Available';

        $templateProcessor->setValue('supervisor_' . $list, '-');
        $templateProcessor->setValue('pembimbing_' . $list, '-');
    }

    $list = $value['week_num'] . "_" . $value['dateOrder'];

    $templateProcessor->setValues([
        'tanggal_' . $list => $tanggal,
        'masuk_' .  $list => $masuk,
        'pulang_' .  $list => $pulang,
    ]);


    unset($avail_days[array_search($list, $avail_days)]);
}


foreach ($avail_days as $value) {

    $templateProcessor->setValues([
        'tanggal_' . $value => '',
        'masuk_' .  $value => '',
        'pulang_' .  $value => '',
        'supervisor_' . $value => '',
        'pembimbing_' . $value => '',
    ]);
}



// $templateProcessor->setValues([
//     'nama_supervisor' => isset($response->supervisor) && isset($response->supervisor->user_fullname) ? $response->supervisor->user_fullname : 'Belum diset',
// ]);

// // Path ke file gambar yang akan dimasukkan
$imagePath = './word/tes.png';

// Menyisipkan gambar ke dalam template
$templateProcessor->setImageValue('nama_supervisor', array('path' => $imagePath, 'width' => 100, 'height' => 100));


$new_name = uniqid() . '-generate.docx';
$path = './word/generate/' . $new_name;
$templateProcessor->saveAs($path);

// // Load the Word document
// $phpWord = IOFactory::load($path);

// // Set Password Protection For editing 
// $documentProtection = $phpWord->getSettings()->getDocumentProtection();
// $documentProtection->setEditing(\PhpOffice\PhpWord\SimpleType\DocProtect::READ_ONLY);
// $documentProtection->setPassword('your_password');

// // Save the modified document
// $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

// $new_name = "New-".uniqid() . '-generate.docx';
// $path = './word/generate/' . $new_name;

// $templateProcessor->saveAs($path);

// $objWriter->save($path);

// $zip = new ZipArchive;

// // Open the Word document
// if ($zip->open($path) === true) {
//     // Locate and retrieve the content of 'word/document.xml'
//     $index = $zip->locateName("word/document.xml");

//     if ($index !== false) {
//         $text = $zip->getFromIndex($index);

//         // Create a DOMDocument object
//         $dom = new DOMDocument;
//         $dom->loadXML($text);
//         // Mengambil namespace yang benar
//         $namespaceURI = $dom->lookupNamespaceURI('w');

//         // Mendapatkan elemen root
//         $root = $dom->documentElement;

//         // Menambahkan atribut "w:read-only" ke elemen root
//         $root->setAttributeNS($namespaceURI, 'w:read-only', 'true');
//         // Simpan konten yang telah dimodifikasi kembali ke 'word/document.xml'
//         $zip->deleteName('word/document.xml');
//         $zip->addFromString('word/document.xml', $dom->saveXML());
//     }

//     // Close the Word document
//     $zip->close();
// }


$generated_name = "Attendance_" . $response->student->nim .  "-" . date('MY', strtotime($filter)) . '.docx';

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
