<?php


$id = $_GET['id_offer'] ?? $_GET['id'];

if ($id == '') {
    $response = array(
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 0,
        'message' => 'Please provide an Offer ID !'
    );

    echo json_encode($response);
    http_response_code(404);
    exit;
}

$sql = $_SERVER['KONEKSI_DB']->query("SELECT * FROM `offer` WHERE id_offer = '$id'");

if ( $sql->num_rows < 1 ) {
    $response = array(
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 0,
        'message' => 'No Data Found'
    );
    echo json_encode($response);
    http_response_code(404);
    exit;
} 

$data = $sql->fetch_assoc();

if ($data) {

    $sql = $_SERVER['KONEKSI_DB']->query("SELECT `company`.* FROM `company`, `offer` WHERE id_offer = '$id' AND offer.id_company = company.id_company");
    $data_company = $sql->fetch_assoc();
        
    $response =
        array(
            'welcome_message' => $_SERVER['API_WELCOME'],
            'status' => 1,
            'message' => 'Success',
            'data' => ['company' => $data_company, 'job' => $data ] 
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
