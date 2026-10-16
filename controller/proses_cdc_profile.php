<?php
error_reporting(false);
session_start();
$title = "Proses CDC Profile";
include "./controller/controller-init.php";

make_snackbar('You do not have permission to perform this action');
role_checker('role', ['UPKPK', 'CDC'], 'notifikasi');


$action = $_GET['action'];

if ($action == 'identity') {

    set_rules('name', 'Name', ['required'], ['required' => 'Cannot be empty !']);
    set_rules('username', 'Username', ['required', 'unique_except:' . $_POST['old_username'] . '|upkpk.username'], ['required' => 'Cannot be empty !', 'unique_except' => 'Username already taken !']);

    $cek_form = form_validation();


    if ($cek_form === false) {
        make_snackbar('Check you submitted data and try again later');
        make_old('open-profile', 'open');
        redirect_back(1);
        exit;
    }


    $where = array(
        'username' => $_POST['old_username'],
    );

    $data = array(
        'username' => $_POST['username'],
        'name' => $_POST['name'],
    );


    $sql = table_update('upkpk', $data, $where);

    if ($sql === TRUE) {
        reflash_all();
        make_snackbar('Profile Data has been updated');
        redirect_referrer(1);
        exit;
    } else {
        make_snackbar('Check you submitted data and try again later');
        redirect_referrer(1);
        exit;
    }
} else

if ($action == 'password') {


    set_rules('username', 'Username CDC', ['required', 'exists:upkpk.username'], ['required' => 'This field is required !', 'exists' => 'CDC Username not found !']);
    set_rules('current_password', 'Current Password', ['required'], ['required' => 'This field is required !']);
    set_rules('new_password', 'New Password', ['required', 'regex_match:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/u'], ['required' => 'This field is required !', 'regex_match' => 'Password did not match required format !']);
    set_rules('new_password2', 'Password Confirmation', ['required', 'matches:new_password'], ['required' => 'This field is required !', 'matches' => 'Password did not match !']);

    $cek_form = form_validation();

    if ($_POST['current_password'] != '') {

        // Cek User
        $sql = "SELECT `password` FROM upkpk WHERE username = '" . $_POST['username']. "'";

        $exec = $_SERVER['KONEKSI_DB']->query($sql);


        if ($exec->num_rows != 1) {
            error('current_password', 'Password is incorrect !');
            $cek_form = false;
        } else {
            // Cek Password
            $password_hash = $exec->fetch_assoc()['password'];

            if (!password_verify($_POST['current_password'], $password_hash)) {
                error('current_password', 'Password is incorrect !');
                $cek_form = false;
            } else {
                success('current_password');
            }
        }
    }

    if ($cek_form === false) {
        make_snackbar('Check you submitted data and try again later');
        make_old('open-profile', 'open');
        redirect_back(1);
        exit();
    }


    $where = array(
        'username' => $_POST['username'],
    );

    $data = array(
        'password' => password_hash($_POST['new_password'], PASSWORD_DEFAULT),
    );


    $sql = table_update('upkpk', $data, $where);

    if ($sql === true) {
        reflash_all();

        make_snackbar('Password has been changed');
        redirect_referrer(1);
        exit();
    } else {
        make_snackbar('Check you submitted data and try again later');

        make_old('open-profile', 'open');
        redirect_back(1);
        exit();
    }
} else 




if ($action == 'profile') {

    set_rules('new_profile', 'New Profile', ['required', 'allowed_types:png|jpg|jpeg'], ['required' => 'This field is required !', 'allowed_types' => 'This file type is not allowed !']);

    if (form_validation() === false) {
        make_snackbar('Check you submitted data and try again later');

        make_old('open-profile', 'open');
        redirect_back(1);
        exit;
    }

    // Get Data Profile
    $sql = "SELECT * FROM `upkpk` WHERE username = '" . $_SESSION['username'] . "'";
    $exec = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

    if (!in_array(strtolower($exec['profile_picture']), ['profile.jpg', 'default.jpg'])) {
        unlink('upload/profile_picture/' . $exec['profile_picture']);
    }


    // Upload file
    // move_uploaded_file( $_FILES['new_profile'] ['tmp_name'] , );
    // $new_name = escape( uniqid(). random_strings(15). ".". strtolower(end(explode('.', $_FILES['new_profile']['name']))));

    $upload = upload('upload/profile_picture/', 'new_profile');


    if ($upload === FALSE) {
        make_snackbar('Check you submitted data and try again later');
        make_old('open-profile', 'open');
        error('new_profile', 'File failed to upload !');
        redirect_back(1);
        exit;
    }


    // Update Profile Picture
    $data = array(
        'profile_picture' => $upload,
    );

    $where = array(
        'username' => $_SESSION['username']
    );

    $update = table_update('upkpk', $data, $where);

    if ($update === false) {
        unlink("upload/profile_picture/$upload");
        make_snackbar('Check you submitted data and try again later');

        make_old('open-profile', 'open');
        error('new_profile', 'File failed to upload !');
        redirect_back(1);
        exit;
    } else {
        reflash_all();
        $_SESSION['profile_picture'] = $upload;
        make_snackbar('Profile Picture has been updated');
        redirect_referrer(1);
        exit;
    }
} else {
    make_snackbar('You do not have permission to perform this action');
    redirect_back(1);
    exit;
}
