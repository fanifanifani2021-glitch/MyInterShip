<?php


function GetBiodata_v1($secretkey = '')
{
    
    $curl = curl_init();

    $secretkey = $result->data->secretkey;
    
    $secretkey = $secretkey;

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

    $response = curl_exec($curl);
    $result = json_decode($response);
    // CURLOPT_POSTFIELDS => 'act=GetBiodata&secretkey=' . $secretkey,

    curl_close($curl);

    // Mengubah JSON menjadi array asosiatif
    $response = $result;

    return $response;
}

$secretkey = $_POST['secretkey'];
dump_to_file('student_secretkey', "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n" . " Status : Login API Log \n" . "From : " 
. getBrowser() . " \n Payload : $secretkey \n\n");

// Memanggil fungsi untuk mendapatkan data berdasarkan nik
$result = GetBiodata_v1($secretkey);

if (!$result) {
    response_json(404, array('error_code' => '400', 'message' => 'Polibatam Biodata API is not found.'));
}

response_json(200, $result );