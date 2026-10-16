<?php
// Koneksi Database
$pdo = $_SERVER['PDO'];

// Pagination
$limit = 5;
$page = isset($_POST['page']) && is_numeric($_POST['page']) && $_POST['page'] > 1 ? (int) $_POST['page'] : 1;
$start = ($page - 1) * $limit;

// Query utama
$query = "SELECT id_company, name, city, logo, facebook, twitter, instagram, type 
          FROM company WHERE status = 'verified'";

$conditions = [];

// Filter berdasarkan tipe perusahaan
if (!empty($_POST['check'])) {
    $check = $pdo->quote($_POST['check']);
    $conditions[] = "type = $check";
}

// Filter berdasarkan pencarian
if (!empty($_POST['query'])) {
    $search = $pdo->quote('%' . $_POST['query'] . '%');
    $conditions[] = "name LIKE $search";
}

// Jika ada kondisi tambahan, tambahkan ke query
if (!empty($conditions)) {
    $query .= ' AND ' . implode(' AND ', $conditions);
}

// Tambahkan ORDER BY dan LIMIT
$query .= " ORDER BY id_company DESC LIMIT $start, $limit";

// Jalankan query langsung tanpa prepared statement
$data = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Query total count (mempertimbangkan kondisi filter)
$query_total = "SELECT COUNT(*) AS total FROM company WHERE status = 'verified'";

if (!empty($conditions)) {
    $query_total .= ' AND ' . implode(' AND ', $conditions);
}

$total_data = $pdo->query($query_total)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Pastikan ada API_WELCOME di $_SERVER
$welcome_message = $_SERVER['API_WELCOME'] ?? 'Welcome to API';

// Response JSON
$response = [
    'welcome_message' => $welcome_message,
    'status' => !empty($data) ? 1 : 0,
    'message' => !empty($data) ? 'Success' : 'No Data Found',
    'data' => $data,
    'total' => $total_data
];

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
