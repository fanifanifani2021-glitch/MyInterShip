<?php

$nik_dosen = $_SESSION['nim_nik_unit'] ?? $_SESSION['Lecturer-Data'] ['nim_nik_unit'];
// Nilai nya '100017'

// URL target
$url = base_url('api.php?request=LogbookByLecturer');
// URL Target berhasil

// Inisialisasi CURL
$ch = curl_init();

$data = [
    'token' => env('APP_TOKEN'),
    'nik' => $nik_dosen
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

$response = json_decode( $response );

return view('logbook/logbook_by_lecturer', ['response' => $response ]); exit;