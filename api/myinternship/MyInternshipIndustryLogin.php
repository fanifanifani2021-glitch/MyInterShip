<?php

$username = escape_string( $_POST['username'] );
$password = $_POST['password'];



// Query menggunakan prepared statement
$sql = "SELECT user_company.*, company.*
FROM user_company
LEFT JOIN company ON user_company.id_company = company.id_company
WHERE user_company.username = :username
LIMIT 1";


// response_json(400, array('error_code' => '100', 'message' => ['username' => $username, 'password' => $password, 'error_message' => $_SERVER['PDO']]));

try {

    $query = $_SERVER['PDO']->prepare($sql);
    $query->execute(array(':username' => $username));

    // Count Siswa
    if ($query->rowCount() != 1) {
        response_json(400, array('error_code' => '108', 'message' => 'Account not registered'));
        exit;
    }

    // Ambil hasil query
    $data = $query->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($password, $data['password'])) {
        response_json(400, array('error_code' => '102', 'message' => 'Password is incorrect'));
        exit;
    }


    unset($data['password']);

    // Buat session baru.
    $data['username'] = $data['username'];
    $data['id_user_company'] = $data['id_user_company'];
    $data['id_company'] = $data['id_company'];
    $data['user_fullname'] =  $data['user_fullname'];
    $data['user_email'] = $data['user_email'];
    $data['user_type'] = $data['user_type'];
    $data['role'] = "industri";
    $data['company_name'] = $data['name'];
    $data['profile_picture'] = $data['profile_picture'];


    $data['LOGIN'] = TRUE;

    response_json(200, array('message' => 'Login success', 'error_code' => '200', 'data' => $data));
} catch (PDOException $e) {

response_json(400, array('error_code' => '100', 'message' => ['username' => $username, 'password' => $password, 'error_message' => $e->getMessage()]));

    response_json(400, array('error_code' => '110', 'message' => 'Internal Error !'));
    exit;
}
