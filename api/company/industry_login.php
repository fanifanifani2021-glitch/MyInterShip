<?php


set_rules('username' , 'Username',['required'] , ['required' => 'This field is required !']);
set_rules('password' , 'Password',['required'] , ['required' => 'This field is required !']);


$cek_form = form_validation();


if ( $cek_form === false ){
    echo json_encode(['status' => '00', 'message' => 'Please provide valid credential !', 
    'form_validation_status' => $cek_form, 
    'form_notification' => $_SESSION['notifikasi'] ]);
    http_response_code(400); exit;
}

$username = escape_val( $_POST['username'] );
$password = escape_val( $_POST['password'] );

$sql = "SELECT * FROM user_company WHERE username = '$username'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec->num_rows == 0 ) {
    echo json_encode([
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 401, 'message' => 'User not found !']);
    http_response_code(400); exit;
} else if ( $exec->num_rows > 1 ) {
    echo json_encode([
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 402, 'message' => 'Multiple Username Found !']);
    http_response_code(400); exit;
}

$data = $exec->fetch_assoc();

if ( !password_verify( $password, $data['password'] ) ) {
    echo json_encode([
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 403, 'message' => 'Wrong password !']);
    http_response_code(400); exit;
} else {
    $data['password'] = NULL;
    echo json_encode([
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 1, 'message' => 'Login success !', 'data' => $data]);
    http_response_code(200); exit;
}