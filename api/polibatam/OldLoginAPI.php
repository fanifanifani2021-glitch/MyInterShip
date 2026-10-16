<?php


function polibatam_login_old($username = '', $password = '')
{

    $token = '9Kylnkoreo7zASjqMh4eEx0Hx9b4h5e2';
    $data = array(
        "username"        => $username,
        "password"        => $password,
        "token"           => $token,
    );

    $ch = curl_init('http://sid.polibatam.ac.id/apilogin/web/api/auth/login');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $jsonArray = json_decode($response, true);

    if ( $jsonArray['status'] == 'success') {
        $data2 = array(
            "nrp"        => $username,
            "token"           => $token,
        );

        $ch2 = curl_init('http://sid.polibatam.ac.id/apilogin/web/api/auth/encrypt');
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $data2);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

        $response2 = curl_exec($ch2);
        curl_close($ch2);

        // Mengubah JSON menjadi array asosiatif
        $jsonArray2 = json_decode($response2, true);
    }

    $jsonArray['data']['token'] = $jsonArray2['data'] ?? NULL;

    // Mengkonversi kembali ke JSON
    return $jsonArray;
}

$username = $_POST['username'];
$password = $_POST['password'];

// Memanggil fungsi untuk mendapatkan data berdasarkan nik
$result = polibatam_login_old($username, $password);

if (!$result) {
    response_json(404, array('error_code' => '400', 'message' => 'Old Polibatam Login API is not found.'));
}

response_json(200, $result );