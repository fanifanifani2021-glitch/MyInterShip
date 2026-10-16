<?php

error_reporting(true);

// Fungsi untuk mengecek status server
function checkServerStatus($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return ($contentSize > 0) ? "Online - Home Page Online (received {$contentSize} bytes)" : "Online - System Down";
    }
    return "Offline";
}

// Fungsi untuk mendapatkan statistik server
function getServerStats() {
    // CPU Load (1 menit terakhir)
    $load = sys_getloadavg();
    $cpuUsage = round($load[0], 2); // Mengambil nilai CPU usage dalam persen (dengan dua angka desimal)

    // Membaca informasi memori dari /proc/meminfo
    $memInfo = file_get_contents("/proc/meminfo");
    preg_match('/MemTotal:\s+(\d+)/', $memInfo, $totalMatch);
    preg_match('/MemFree:\s+(\d+)/', $memInfo, $freeMatch);
    preg_match('/Buffers:\s+(\d+)/', $memInfo, $buffersMatch);
    preg_match('/Cached:\s+(\d+)/', $memInfo, $cachedMatch);

    // Konversi nilai RAM dari KB ke MB (membagi dua kali dengan 1024)
    $totalRam = isset($totalMatch[1]) ? intval($totalMatch[1]) / 1024 : 0; // Total RAM dalam MB
    $freeRam = isset($freeMatch[1]) ? intval($freeMatch[1]) / 1024 : 0; // Free RAM dalam MB
    $buffers = isset($buffersMatch[1]) ? intval($buffersMatch[1]) / 1024 : 0; // Buffer dalam MB
    $cached = isset($cachedMatch[1]) ? intval($cachedMatch[1]) / 1024 : 0; // Cached dalam MB

    // Menghitung RAM yang digunakan (dalam MB)
    $usedRam = $totalRam - ($freeRam + $buffers + $cached);
    $ramUsagePercent = ($totalRam > 0) ? round(($usedRam / $totalRam) * 100, 2) : 0;

    // Kembalikan data statistik dalam format yang diinginkan
    return [
        'cpu_usage' => "{$cpuUsage} %", // CPU Usage tanpa koma
        'total_ram' => round($totalRam) . ' MB', // Total RAM dalam MB (dibulatkan ke integer)
        'used_ram' => round($usedRam) . ' MB (' . round($ramUsagePercent) . '%)', // RAM digunakan (dibulatkan ke integer)
        'free_ram' => round($freeRam) . ' MB', // Free RAM dalam MB (dibulatkan ke integer)
    ];
}



// Ambil data jurusan di Politeknik Batam
$sql = "SELECT DISTINCT major FROM program_study WHERE id_kampus = 1";
$stmt = $_SERVER['PDO']->query($sql);
$dataStatistikJurusan = [];

foreach ($stmt as $jurusanData) {
    $major = $jurusanData['major'];
    $sql = "SELECT * FROM program_study WHERE id_kampus = 1 AND major = :major ORDER BY jenjang";
    $query = $_SERVER['PDO']->prepare($sql);
    $query->execute([':major' => $major]);
    $prodiList = $query->fetchAll(PDO::FETCH_ASSOC);

    $dataProdi = [];
    foreach ($prodiList as $data) {
        $kodeProdi = $data['kode_prodi'];
        $angkatan_1 = substr(date('Y') - 2, -2); // Tahun lalu, ambil 2 digit terakhir
        $angkatan_2 = substr(date('Y') - 3, -2); // Dua tahun lalu, ambil 2 digit terakhir


        $query = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM student_internship WHERE program_study = :kode_prodi");
        $query->execute([':kode_prodi' => $kodeProdi]);
        $totalMahasiswa = $query->fetchColumn();

        $query = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM student_internship WHERE program_study = :kode_prodi AND nim LIKE :angkatan_1");
        $query->execute([':kode_prodi' => $kodeProdi, ':angkatan_1' => "%$angkatan_1"]);
        $angkatan1Count = $query->fetchColumn();

        $query = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM student_internship WHERE program_study = :kode_prodi AND nim LIKE :angkatan_2");
        $query->execute([':kode_prodi' => $kodeProdi, ':angkatan_2' => "%$angkatan_2%"]);
        $angkatan2Count = $query->fetchColumn();

        $dataProdi[] = [
            'kode_prodi' => $kodeProdi,
            'jenjang' => $data['jenjang'],
            'prodi' => $data['prodi'],
            'study_program' => $data['study_program'],
            'total_mahasiswa' => $totalMahasiswa,
            'tahun_angkatan_1' => $angkatan_1,
            'tahun_angkatan_2' => $angkatan_2,
            'angkatan_1' => $angkatan1Count,
            'angkatan_2' => $angkatan2Count,
        ];
    }

    $dataStatistikJurusan[] = [
        'major' => $major,
        'prodi_list' => $dataProdi
    ];
}

// URL server MyInternship
$serverUrl = 'https://myinternship.id';
$status_server = checkServerStatus($serverUrl);
$serverStats = getServerStats();

$apiKey = 'wJaJJTar27GRZi7MD7m9';
$to = '6285156861862';

$message = $blade->render('whatsapp.myinternship_stats', [
    'status_server' => $status_server,
    'api_polibatam' => $_SERVER['API_PROBLEM'],

    'cpu_usage' => $serverStats['cpu_usage'],
    'total_ram' => $serverStats['total_ram'],
    'used_ram' => $serverStats['used_ram'],
    'free_ram' => $serverStats['free_ram'],
    'dataStatistikJurusan' => $dataStatistikJurusan,
]);

$payload = [
    'target' => $to,
    'message' => $message,
    'countryCode' => '62',
];

$ch = curl_init('https://api.fonnte.com/send');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $apiKey,
]);

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
print_r($responseData);
echo "MyInternship Stats report sent successfully!\n";
log_message("✅ MyInternship Stats report sent successfully. \n");