<?php
set_rules('username', 'Username', ['required']);
set_rules('password', 'Password', ['required']);

if ( form_validation(0) === false ){

    response_json(400, array(
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 0,
        'error_code' => 101,
        'message' => 'Form Validation Failed',
        'form_validation_status' => false,
        'form_notification' => $_SESSION['notifikasi']
    ));
    exit;
}

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://sid.polibatam.ac.id/api/v1.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'act=Login&username=' . $_POST['username'] . '&password=' . $_POST['password'] . '',
));

$response = curl_exec($curl);
$result = json_decode($response);
curl_close($curl);


$secretkey = $result->data->secretkey;

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://sid.polibatam.ac.id/api/v1.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'act=GetBiodata&secretkey='.$secretkey.'',
));
$response2 = curl_exec($curl); curl_close($curl);
$polibatam_data = json_decode($response2, true); 
$polibatam_data['data'] ['secretkey'] = $secretkey;

response_json(200, array(
    'welcome_message' => $_SERVER['API_WELCOME'],
    'status' => 1,
    'error_code' => 0,
    'message' => 'Success',
    'data' => $polibatam_data['data'] ?? [],
));
exit;