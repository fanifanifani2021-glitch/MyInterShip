<?php
session_start();
$lokasi = './upload/profile_picture/';
$default = 'profile.jpg';

$action = $_GET['action'];

if ($action == 'user_company') {


    // Get Data
    $sql = "SELECT profile_picture FROM user_company WHERE id_user_company = :id_user_company";
    $query = $_SERVER['PDO']->prepare($sql);
    $query->execute(['id_user_company' => $_SESSION['id_user_company']]);
    $old_data = $query->fetch(PDO::FETCH_ASSOC);


    $data = array(
        'profile_picture' => $default,
    );

    $where = array(
        'id_user_company' => $_SESSION['id_user_company']
    );

    $profile_remove = table_update('user_company', $data, $where);

    if (!in_array($old_data['profile_picture'], ['default.jpg', 'default.png', null])) {
        if (is_file($lokasi . $old_data['profile_picture'])) {
            unlink($lokasi . $old_data['profile_picture']);
        }
    }
} else if ($action == 'student') {

    $sql = "SELECT profile_picture FROM student_internship WHERE nim = :nim";
    $query = $_SERVER['PDO']->prepare($sql);
    $query->execute(['nim' => $_SESSION['nim']]);
    $old_data = $query->fetch(PDO::FETCH_ASSOC);


    $data = array(
        'profile_picture' => $default,
    );

    $where = array(
        'nim' => $_SESSION['nim']
    );

    $profile_remove = table_update('student_internship', $data, $where);

    if (!in_array($old_data['profile_picture'], ['default.jpg', 'default.png', null])) {
        if (is_file($lokasi . $old_data['profile_picture'])) {
            unlink($lokasi . $old_data['profile_picture']);
        }
    }
} else if ($action == 'lecturer') {

    // Get Data
    $sql = "SELECT profile_picture FROM lecturer WHERE nim_nik_unit = :nim_nik_unit";
    $query = $_SERVER['PDO']->prepare($sql);
    $query->execute(['nim_nik_unit' => $_SESSION['nim_nik_unit']]);
    $old_data = $query->fetch(PDO::FETCH_ASSOC);


    $data = array(
        'profile_picture' => $default,
    );

    $where = array(
        'nim_nik_unit' => $_SESSION['nim_nik_unit']
    );

    $profile_remove = table_update('lecturer', $data, $where);

    if (!in_array($old_data['profile_picture'], ['default.jpg', 'default.png', null])) {
        if (is_file($lokasi . $old_data['profile_picture'])) {
            unlink($lokasi . $old_data['profile_picture']);
        }
    }
} else {
    http_response_code(400);
    make_snackbar('Failed to remove Profile Picture');
}

$_SESSION['profile_picture'] = $default;

http_response_code(200);
make_snackbar('Profile Picture has been removed');

redirect_back(1);
exit;
