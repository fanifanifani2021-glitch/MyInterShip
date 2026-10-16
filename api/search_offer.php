<?php

set_rules('id_offer', 'ID Company', ['required'], ['required' => 'We need Offer ID !']);

$cek_form = form_validation();


if ($cek_form === false) {
    echo json_encode(['status' => 0, 'message' => 'Please provide valid data !', 'form_validation_status' => $cek_form, 'form_notification' => $_SESSION['notifikasi']]);
    http_response_code(400);
    exit;
}

$id_offer = $_GET['id_offer'];

$sql = "SELECT * FROM offer INNER JOIN company ON offer.id_company = company.id_company WHERE id_offer = '$id_offer'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec->num_rows != 1 ) {

    echo json_encode(['status' => 0, 'message' => 'Failed, no offer found !', 'data' => [] ]);
    http_response_code(400);
    exit;
} else {

    $new_data = [];
    foreach ($exec as $index => $data) {
        $new_data[] = $data;
    }

    echo json_encode(['status' => 1, 'message' => 'Success, here is all offer !', 'data' => $new_data]);
    http_response_code(200);
    exit;
}
