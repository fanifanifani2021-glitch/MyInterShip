<?php

// set_rules('id_company' , 'ID Company' ,['required'] , ['required' => 'We need Company ID !']);

// $cek_form = form_validation();


// if ( $cek_form === false ){
//     echo json_encode(['status' => 0, 'message' => 'Please provide valid data !', 'form_validation_status' => $cek_form, 'form_notification' => $_SESSION['notifikasi']]);
//     http_response_code(400); exit;
// }

$id_company = escape_val($_GET['id']);


$sql = "SELECT * FROM `company` WHERE id_company = '$id_company'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec->num_rows == 0 ) {
    echo json_encode([
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 0, 'message' => 'Company data not found !', 'additional_message' => 'Maybe try different company ID ?']);
    http_response_code(404); exit;
} 

$sql = "SELECT * FROM `user_company` WHERE id_company = '$id_company'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec->num_rows == 0 ) {
    echo json_encode([
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 1, 'message' => 'No data found !', 'additional_message' => 'Maybe try different company ID ?', 'data' => [] ]);
    http_response_code(404); exit;
} 

$new_data = [];
foreach ($exec as $i => $dat) {

    $dat['password'] = NULL;
    $new_data[] = $dat;
}


    echo json_encode([
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 1, 'message' => 'Company User found !', 'data' => $new_data ]);
    http_response_code(404); exit;

