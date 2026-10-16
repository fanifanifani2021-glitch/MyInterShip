<?php

// URL target
$url1 = base_url('index.php?api=count_company');
$url2 = base_url('index.php?api=count_job');
$url3 = base_url('index.php?api=count_internship');
// URL Target berhasil

// Inisialisasi CURL
$ch = curl_init();

// Inisialisasi CURL
$ch1 = curl_init();
$ch2 = curl_init();
$ch3 = curl_init();

// Pengaturan CURL untuk setiap URL
$options = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $url1,
];
curl_setopt_array($ch1, $options);

$options[CURLOPT_URL] = $url2;
curl_setopt_array($ch2, $options);

$options[CURLOPT_URL] = $url3;
curl_setopt_array($ch3, $options);

// Eksekusi CURL secara paralel menggunakan multi-CURL
$mh = curl_multi_init();
curl_multi_add_handle($mh, $ch1);
curl_multi_add_handle($mh, $ch2);
curl_multi_add_handle($mh, $ch3);


$active = null;
do {
    $mrc = curl_multi_exec($mh, $active);
} while ($mrc == CURLM_CALL_MULTI_PERFORM);

while ($active && $mrc == CURLM_OK) {
    if (curl_multi_select($mh) != -1) {
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }
}

// Ambil respons dari setiap CURL
$response1 = curl_multi_getcontent($ch1);
$response2 = curl_multi_getcontent($ch2);
$response3 = curl_multi_getcontent($ch3);

// Tutup koneksi CURL
curl_multi_remove_handle($mh, $ch1);
curl_multi_remove_handle($mh, $ch2);
curl_multi_remove_handle($mh, $ch3);
curl_multi_close($mh);


// Ubah respons JSON menjadi array asosiatif
$data1 = json_decode($response1, true);
$data2 = json_decode($response2, true);
$data3 = json_decode($response3, true);

// Ambil nilai CountCompany, CountJob, dan CountInternship
$countCompany = $data1['CountCompany'];
$countJob = $data2['CountJob'];
$countInternship = $data3['CountInternship'];

$data = array (
    'CountCompany' => $countCompany ?? 0,
    'CountJob' => $countJob ?? 0,
    'CountInternship' => $countInternship ?? 0,
);


response_json( 200, array('message' => 'success', 'error_code' => '200', 'status' => 1, 'data' => $data));