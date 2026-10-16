<?php
session_start();
$id_kampus = $_SESSION['CDC_Data']['id_kampus'];

$id_company = $_GET['id_company'] ?? $_POST['id_company'];
$start_date = $_GET['start_date'] ?? $_POST['start_date'];
$end_date   = $_GET['end_date']   ?? $_POST['end_date'];
$major      = $_GET['major']      ?? $_POST['major'];
$prodi      = $_GET['prodi']      ?? $_POST['prodi'];

// ==========================
//  Base SQL
// ==========================
$sql = "
SELECT
    si.nim,
    si.name AS student_name,
    ps.jurusan,
    ps.major,
    ps.jenjang,
    ps.prodi,
    ps.study_program,
    i.start_date,
    i.end_date,
    i.status
FROM internship i
JOIN student_internship si ON si.nim = i.nim
LEFT JOIN program_study ps ON si.program_study = ps.kode_prodi
WHERE
    si.id_kampus = :id_kampus
    AND i.id_company = :id_company
    AND i.start_date BETWEEN :start_date AND :end_date
    AND i.end_date   BETWEEN :start_date AND :end_date
    AND LOWER(i.status) IN ('yes','finish')
";

// ==========================
//  Filter Major
// ==========================
$bind = [
    ':id_kampus' => $id_kampus,
    ':id_company' => $id_company,
    ':start_date' => $start_date,
    ':end_date'   => $end_date
];

// Filter 1: Jika major tidak kosong
if (!empty($major)) {
    $sql .= " AND ps.major = :major ";
    $bind[':major'] = $major;
}

// Filter 2: Jika prodi dipilih
if (!empty($prodi)) {

    // Case: ALL program in a Major → value = "MAJOR:IF"
    if (strpos($prodi, 'MAJOR:') === 0) {
        $majorName = substr($prodi, 6);
        $sql .= " AND ps.major = :major_all ";
        $bind[':major_all'] = $majorName;
    }

    // Case: Specific Prodi → value = "PS:TI01"
    elseif (strpos($prodi, 'PS:') === 0) {
        $kodeProdi = substr($prodi, 3);
        $sql .= " AND ps.kode_prodi = :kode_prodi ";
        $bind[':kode_prodi'] = $kodeProdi;
    }
}

$sql .= " ORDER BY i.start_date ASC ";

try {
    $stmt = $_SERVER['PDO']->prepare($sql);
    $stmt->execute($bind);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================
//  Ubah status ke bahasa Indo
// ==========================
foreach ($data as &$row) {
    $status = strtolower($row['status']);
    $row['status'] = ($status === 'yes') ? 'Sedang Magang' : 'Selesai';
}
unset($row);

echo json_encode($data);
