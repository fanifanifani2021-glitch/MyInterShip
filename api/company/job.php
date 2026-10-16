<?php


$nik = $_GET['nim'] ?? $_GET['nik'];
$data = array();
// $sql = $_SERVER['KONEKSI_DB']->prepare("SELECT * FROM internship, company WHERE internship.nim = ? AND internship.id_company = company.id_company ORDER BY internship.id_internship DESC");

$sql = $_SERVER['KONEKSI_DB']->prepare("SELECT * FROM internship, company WHERE internship.status IN ('Yes', 'yes' , 'Finish', 'finish') AND internship.nim = ? AND internship.id_company = company.id_company ORDER BY internship.id_internship DESC");

$sql->bind_param("i", $nik);
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_object()) {
    $data[] = $row;
}

if ($data) {
    $response =
        array(
            'welcome_message' => $_SERVER['API_WELCOME'],
            'status' => 1,
            'message' => 'Success',
            'data' => $data
        );
    echo json_encode($response, JSON_PRETTY_PRINT);
    http_response_code(200);
    exit;
} else {
    $response = array(
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 0,
        'message' => 'No Data Found'
    );
    echo json_encode($response);
    http_response_code(404);
    exit;
}
