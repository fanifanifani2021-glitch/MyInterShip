<?php


function polibatam_login_v1($user = '', $pass = '')
{
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
        CURLOPT_POSTFIELDS => 'act=Login&username=' . $user . '&password=' . $pass . '',
    ));

    $response = curl_exec($curl);
    $result = json_decode($response);
    curl_close($curl);

    // Mengubah JSON menjadi array asosiatif
    $response = $result;

    return $response;
}

$username = $_POST['username'];
$password = $_POST['password'];

// Memanggil fungsi untuk mendapatkan data berdasarkan nik
$result = polibatam_login_v1($username, $password);
    if (is_string($response)) {
        $payload .= $response;
    } else if (is_null($response)) {
        $payload .= "Response is null";
    } else if (is_array($response)) {
        foreach ($response as $key => $value) {
            $payload .= $key . " => " . $value . ", ";
        }
    } else {
        $payload .= " Reponse nya invalid ";
    }
    dump_to_file('login_api', "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n" . " Status : Login API Log \n" . "From : " . getBrowser() . " \n Payload : $payload \n\n");


if (!$result) {
    // response_json(404, array('error_code' => '400', 'message' => 'New Polibatam Login API is not found.'));
    // foreach ($_GET as $key => $value) {
    //     $payload .= $key . " => " . $value . ", ";
    // }

    // foreach ($_POST as $key => $value) {
    //     $payload .= $key . " => " . $value . ", ";
    // }
    
    if (is_string($response)) {
        $payload .= $response;
    } else if (is_null($response)) {
        $payload .= "Response is null";
    } else if (is_array($response)) {
        foreach ($response as $key => $value) {
            $payload .= $key . " => " . $value . ", ";
        }
    } else {
        $payload .= " Reponse nya invalid ";
    }



    dump_to_file('login_api', "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n" . " Status : Login API Log \n" . "From : " . getBrowser() . " \n Payload : $payload \n\n");

    response_json(400, array('error_code' => '400', 'message' => 'API Invalid Response !'));
}

response_json(200, $result );