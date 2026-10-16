<?php

// Daftar kelurahan yang dilewati saat perjalanan
$rutePerjalanan = [
    '21.71.04.1002', // Batu Besar
    '21.71.04.1003', // Kabil
    '21.71.10.1004', // Belian
    '21.71.10.1003', // Teluk Tering
];

// Lokasi rumah dan kampus (cuaca harian penuh)
$lokasiCuacaLengkap = [
    '21.71.04.1002', // Batu Besar
    '21.71.10.1003', // Teluk Tering
];


function getEmoji($desc)
{
    $desc = strtolower($desc);

    if (strpos($desc, 'cerah') !== false) return '☀️';
    elseif (strpos($desc, 'berawan') !== false) return '⛅';
    elseif (strpos($desc, 'hujan ringan') !== false) return '🌧️';
    elseif (strpos($desc, 'hujan sedang') !== false) return '🌧️';
    elseif (strpos($desc, 'hujan lebat') !== false) return '⛈️';
    elseif (strpos($desc, 'kabut') !== false) return '🌫️';
    else return '☁️';
}

function getIconUrl($desc)
{
    $desc = strtolower($desc);

    if (strpos($desc, 'cerah') !== false) return 'https://cdn.bmkg.go.id/Web/cuaca-cerah.png';
    elseif (strpos($desc, 'berawan') !== false) return 'https://cdn.bmkg.go.id/Web/cuaca-berawan.png';
    elseif (strpos($desc, 'hujan ringan') !== false) return 'https://cdn.bmkg.go.id/Web/hujan-ringan.png';
    elseif (strpos($desc, 'hujan sedang') !== false) return 'https://cdn.bmkg.go.id/Web/hujan-sedang.png';
    elseif (strpos($desc, 'hujan lebat') !== false) return 'https://cdn.bmkg.go.id/Web/hujan-lebat.png';
    elseif (strpos($desc, 'kabut') !== false) return 'https://cdn.bmkg.go.id/Web/kabut.png';
    else return 'https://cdn.bmkg.go.id/Web/berawan.png';
}

// ======= Data Wilayah =======
$rutePerjalanan = [ // Lokasi dilewati saat berangkat
    '21.71.04.1002', // Batu Besar
    '21.71.04.1003', // Kabil
    '21.71.10.1004', // Belian
    '21.71.10.1003', // Teluk Tering
];

$lokasiCuacaLengkap = [ // Lokasi untuk cuaca seharian penuh
    '21.71.04.1002', // Batu Besar
    '21.71.10.1003', // Teluk Tering
];

// ======= Helper Fungsi =======
function ambilCuacaWilayah($kodeWilayah)
{
    $url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4={$kodeWilayah}";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
    ]);
    $response = curl_exec($curl);
    curl_close($curl);

    if (!$response) return null;

    $data = json_decode($response, true);
    if (!$data) return null;

    return [
        'lokasi' => $data['lokasi'] ?? [],
        'cuaca' => $data['data'][0]['cuaca'] ?? [],
    ];
}

function filterCuacaJam(array $cuacaData, $jam = '07:00')
{
    $hasil = [];

    foreach ($cuacaData as $kelompokCuaca) {
        foreach ($kelompokCuaca as $item) {
            if (!isset($item['local_datetime'])) continue;

            $tanggalItem = date('Y-m-d', strtotime($item['local_datetime']));
            $jamItem = date('H:i', strtotime($item['local_datetime']));

            if ($tanggalItem === date('Y-m-d') && strpos($jamItem, $jam) === 0) {
                $hasil[] = $item;
            }
        }
    }

    return $hasil;
}


// ======= Ambil Data Cuaca =======
// Cuaca saat perjalanan (jam 07:00)
// Cuaca lengkap harian
$cuacaLengkap = array_filter(array_map(function ($kode) {
    return ambilCuacaWilayah($kode);
}, $lokasiCuacaLengkap));

// Cuaca saat perjalanan jam 07:00
$perjalananPergi = array_filter(array_map(function ($kode) {
    $data = ambilCuacaWilayah($kode);
    if (!$data) return null;

    return [
        'lokasi' => $data['lokasi'],
        'cuaca' => filterCuacaJam($data['cuaca'], '07:00'),
    ];
}, $rutePerjalanan));



// Cuaca lengkap harian

// Format tanggal
$tanggal = (new IntlDateFormatter(
    'id_ID',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Asia/Jakarta',
    IntlDateFormatter::GREGORIAN,
    'EEEE, dd MMMM yyyy'
))->format(new DateTime());
$tanggal = str_replace('Jumat', "Jum'at", $tanggal);

// ======= Render Blade Template =======
$message = $blade->render('whatsapp.weather_report', [
    'nama' => 'Gilang',
    'tanggal' => $tanggal,
    'perjalananPergi' => $perjalananPergi,
    'cuacaLengkap' => $cuacaLengkap,
    'getEmoji' => function ($desc) {
                            return getEmoji($desc);
                        },
]);

// ======= Kirim via Fonnte =======
$apiKey = 'wJaJJTar27GRZi7MD7m9';
$to = '6285156861862'; // Ganti dengan nomor kamu

$payload = [
    'target' => $to,
    'message' => $message,
    'countryCode' => '62',
];

$ch = curl_init('https://api.fonnte.com/send');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($payload),
    CURLOPT_HTTPHEADER => ['Authorization: ' . $apiKey],
]);

$response = curl_exec($ch);
curl_close($ch);

// Tampilkan hasil response API Fonnte
$responseData = json_decode($response, true);
print_r($responseData);

// Log berhasil
log_message("✅ Weather Report sent successfully.");
