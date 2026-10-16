<?php

$translate = [
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu',
];

$hariIni = date('l');
$hari = $translate[$hariIni] ?? $hariIni;

$jadwal = [
  [
    "day" => "Monday",
    "time" => "09.30 - 12.50",
    "course" => "RPL421 - Kecerdasan Buatan (Praktikum)",
    "class" => "TRPL4B",
    "room" => "TA.12.2"
  ],
  [
    "day" => "Monday",
    "time" => "18.00 - 18.50",
    "course" => "RPL211 - Pemrograman Berorientasi Objek",
    "class" => "TRPL2CK",
    "room" => "Online"
  ],
  [
    "day" => "Monday",
    "time" => "20.30 - 23.00",
    "course" => "RPL211 - Pemrograman Berorientasi Objek (Praktikum)",
    "class" => "TRPL2AK",
    "room" => "TA.12.4"
  ],
  [
    "day" => "Tuesday",
    "time" => "18.00 - 20.30",
    "course" => "RPL211 - Pemrograman Berorientasi Objek (Praktikum)",
    "class" => "TRPL2BK",
    "room" => "TA.12.4"
  ],
  [
    "day" => "Wednesday",
    "time" => "08.40 - 12.00",
    "course" => "RPL211 - Pemrograman Berorientasi Objek (Praktikum)",
    "class" => "TRPL2A",
    "room" => "TA.12.4"
  ],
  [
    "day" => "Wednesday",
    "time" => "13.40 - 17.00",
    "course" => "IF422 - Pengujian Perangkat Lunak (Praktikum)",
    "class" => "IF4C",
    "room" => "TA.11.4A"
  ],
  [
    "day" => "Wednesday",
    "time" => "19.40 - 20.30",
    "course" => "RPL211 - Pemrograman Berorientasi Objek",
    "class" => "TRPL2BK",
    "room" => "Online"
  ],
  [
    "day" => "Wednesday",
    "time" => "20.30 - 23.00",
    "course" => "RPL211 - Pemrograman Berorientasi Objek (Praktikum)",
    "class" => "TRPL2CK",
    "room" => "Online"
  ],
  [
    "day" => "Thursday",
    "time" => "12.00 - 15.20",
    "course" => "RPL211 - Pemrograman Berorientasi Objek (Praktikum)",
    "class" => "TRPL2B",
    "room" => "TA.12.4"
  ],
  [
    "day" => "Thursday",
    "time" => "18.00 - 18.50",
    "course" => "RPL211 - Pemrograman Berorientasi Objek",
    "class" => "TRPL2AK",
    "room" => "Online"
  ],
  [
    "day" => "Friday",
    "time" => "13.40 - 17.00",
    "course" => "RPL210 - Perancangan Perangkat Lunak (Praktikum)",
    "class" => "TRPL2C",
    "room" => "TA.12.2"
  ]
];

$jadwalHariIni = array_values(array_filter($jadwal, function ($item) use ($hariIni) {
    return $item['day'] === $hariIni;
}));

if (count($jadwalHariIni) > 0) {
    $pesan = $blade->render('whatsapp.jadwal_kuliah', [
        'hari' => $hari,
        'jadwalHariIni' => $jadwalHariIni
    ]);

    $token = 'wJaJJTar27GRZi7MD7m9';
    $target = '6285156861862';

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'target' => $target,
            'message' => $pesan,
            'delay' => 1,
        ],
        CURLOPT_HTTPHEADER => [
            "Authorization: $token"
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    log_message("Today Schedule : " .count($jadwalHariIni));
}

log_message("✅ Lecture Schedule running successfully. \n");
