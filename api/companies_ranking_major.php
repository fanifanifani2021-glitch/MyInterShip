<?php
session_start();
$id_kampus = $_SESSION['CDC_Data']['id_kampus'];

$id_company = $_GET['id_company'] ?? $_POST['id_company'];
$year = $_GET['year'] ?? $_POST['year'];
$jurusan = $_GET['jurusan'] ?? $_POST['jurusan'];

$sql = "
SELECT
    si.nim,
    si.name AS student_name,
    ps.jurusan,
    ps.prodi,
    ps.jenjang,
    i.start_date,
    i.end_date,
    i.status
FROM internship i
JOIN student_internship si ON si.nim = i.nim
LEFT JOIN program_study ps ON si.program_study = ps.kode_prodi
WHERE
    si.id_kampus = :id_kampus AND
    i.id_company = :id_company AND
    ( ps.major = :jurusan OR ps.jurusan = :jurusan ) AND
    YEAR(i.start_date) = :year AND
    LOWER(i.status) IN ('yes', 'finish')
ORDER BY i.start_date
";

try {
    $stmt = $_SERVER['PDO']->prepare($sql);
    $stmt->execute([
        ':id_kampus' => $id_kampus,
        ':id_company' => $id_company,
        ':jurusan' => $jurusan,
        ':year' => $year
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Normalisasi status menjadi label readable
foreach ($data as &$row) {
    $status = strtolower($row['status'] ?? '');

    if ($status === 'yes') {
        $row['status'] = 'Sedang Magang';
    } elseif ($status === 'finish') {
        $row['status'] = 'Selesai';
    } else {
        $row['status'] = ucfirst($status);
    }
}
unset($row);

echo json_encode($data);
