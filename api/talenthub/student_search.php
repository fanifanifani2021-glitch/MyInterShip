<?php

set_rules('secretkey', 'secretkey', ['required']);
set_rules('nik', 'nik', ['required']);

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
    CURLOPT_POSTFIELDS => 'act=GetToken&secretkey=' . $_POST['secretkey'],
));

$response = curl_exec($curl);
$result = json_decode($response, true);
curl_close($curl);

if ( $result['error_code'] == 0 ) {

    $token = $result['data']['token'];

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
        CURLOPT_POSTFIELDS => 'act=GetDataByID&token='.$token.'&filter=nik=' .$_POST['nik'],
    ));
    $response2 = curl_exec($curl); 
    curl_close($curl);
    $polibatam_data = json_decode($response2, true);
    
    if ( $polibatam_data['error_code'] == 0 ) {
        response_json(200, array(
            'welcome_message' => $_SERVER['API_WELCOME'],
            'status' => 0,
            'error_code' => 0,
            'message' => 'API Search Success',
            'name' => $polibatam_data['data'][0] ['NAMA'],
            'email' => $polibatam_data['data'][0] ['EMAIL']
        ));
        exit;
    } else {
        response_json(404, array(
            'welcome_message' => $_SERVER['API_WELCOME'],
            'status' => 0,
            'error_code' => 102,
            'message' => 'API Search Failed',
            'result' => $polibatam_data
        ));

        exit;
    }
} else {
    response_json(400, array(
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 400,
        'error_code' => 102,
        'message' => 'API Search Failed',
        'result' => $result
    ));
    exit;
}


exit;