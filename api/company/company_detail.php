<?php

// set_rules('id_company' , 'ID Company' ,['required'] , ['required' => 'We need Company ID !']);

// $cek_form = form_validation();


// if ( $cek_form === false ){
//     echo json_encode(['status' => 0, 'message' => 'Please provide valid data !', 'form_validation_status' => $cek_form, 'form_notification' => $_SESSION['notifikasi']]);
//     http_response_code(400); exit;
// }

$id_company = escape_val($_GET['id_company']);

$sql = "SELECT * FROM company WHERE id_company = :id_company";
$exec = $_SERVER['KONEKSI_PDO']->prepare($sql);

$exec->execute( ['id_company' => $id_company] );

if ( $exec->rowCount() != 1 ) {
    echo json_encode([
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 0, 'message' => 'No data found !', 'additional_message' => 'Maybe try different company ID ?']);
    http_response_code(404); exit;
} else {
    echo json_encode([
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 1, 'message' => 'Yeyy, company found !', 'data' => $exec->fetch(PDO::FETCH_ASSOC) ]);
    http_response_code(200); exit;
}
