<?php

switch ($method):
    default:
        abort(404, 'No Found');
        break;

    case 'list':
    if (!$id_kampus) {
        Router::json(400, 'id_kampus is required');
    }

    // cek dulu apakah kampus ada
    $stmtKampus = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM tb_kampus WHERE id_kampus = :id_kampus");
    $stmtKampus->execute(['id_kampus' => $id_kampus]);
    if ($stmtKampus->fetchColumn() == 0) {
        Router::json(404, 'Campus not found');
    }

    // ambil semua prodi
    $stmt = $_SERVER['PDO']->prepare("
        SELECT kode_prodi AS prodi_id, study_program AS en, prodi AS id, jurusan, major
        FROM program_study
        WHERE id_kampus = :id_kampus
        ORDER BY study_program ASC
    ");
    $stmt->execute(['id_kampus' => $id_kampus]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [
        'data' => $data,
        'recordsTotal' => count($data),
        'recordsFiltered' => count($data),
    ];

    Router::json(200, 'OK', $result);
    break;


    case 'detail':
    if (!$id_kampus) {
        Router::json(400, 'id_kampus is required');
    }

    // cek kampus
    $stmtKampus = $_SERVER['PDO']->prepare("SELECT COUNT(*) FROM tb_kampus WHERE id_kampus = :id_kampus");
    $stmtKampus->execute(['id_kampus' => $id_kampus]);
    if ($stmtKampus->fetchColumn() == 0) {
        Router::json(404, 'Campus not found');
    }

    // ambil data mahasiswa per prodi & angkatan
    $stmt = $_SERVER['PDO']->prepare("
        SELECT
            ps.kode_prodi AS id,
            ps.study_program AS en,
            ps.prodi,
            ps.jurusan,
            ps.major,
            SUBSTRING(si.nim, 4, 2) AS angkatan,
            SUM(CASE WHEN LOWER(i.status) IN ('yes','finish') THEN 1 ELSE 0 END) AS sudah_magang,
            SUM(CASE WHEN LOWER(i.status) NOT IN ('yes','finish') OR i.status IS NULL THEN 1 ELSE 0 END) AS belum_magang,
            COUNT(si.nim) AS total
        FROM program_study ps
        LEFT JOIN student_internship si
            ON si.program_study = ps.kode_prodi
            AND si.id_kampus = ps.id_kampus
        LEFT JOIN internship i
            ON i.nim = si.nim
        WHERE ps.id_kampus = :id_kampus AND ps.kode_prodi = :kode_prodi
          AND CAST(SUBSTRING(si.nim, 4, 2) AS UNSIGNED) >= 18
        GROUP BY ps.kode_prodi, SUBSTRING(si.nim, 4, 2)
        ORDER BY ps.prodi ASC, angkatan ASC
    ");
    $stmt->execute(['id_kampus' => $id_kampus, 'kode_prodi' => $kode_prodi]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = ['program_study' => []];

    foreach ($rows as $row) {
        $prodiId = $row['id'];

        if (!isset($result['program_study'][$prodiId])) {
            $result['program_study'][$prodiId] = [
                'id' => $row['id'],
                'en' => $row['en'],
                'prodi' => $row['prodi'],
                'jurusan' => $row['jurusan'],
                'major' => $row['major'],
                'student_stat' => []
            ];
        }

        $angkatan = $row['angkatan'] ?? 'unknown';
        $result['program_study'][$prodiId]['student_stat'][$angkatan] = [
            'belum_magang' => (int)$row['belum_magang'],
            'sudah_magang' => (int)$row['sudah_magang'],
            'total' => (int)$row['total']
        ];
    }

    // jika ingin index numeric
    $result['program_study'] = array_values($result['program_study']);

    Router::json(200, 'OK', $result);
    break;


endswitch;