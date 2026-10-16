<?php
error_reporting(false);
session_start();

use Intervention\Image\ImageManager;

$title = "Proses Industry Profile";
include "./controller/controller-init.php";

make_snackbar('You do not have permission to perform this action');
role_checker('user_type', ['HRD', 'Supervisor'], 'notifikasi');


$action = $_GET['action'];


if ($action == 'password') {

    set_rules('id_user_company', 'id_user_company', ['required'], ['required' => 'This field is required !']);
    set_rules('old_password', 'Old Password', ['required'], ['required' => 'This field is required !']);
    set_rules('new_password', 'New Password', ['required', 'regex_match:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/u'], ['required' => 'This field is required !', 'regex_match' => 'Password did not match required format !']);
    set_rules('new_password2', 'Password Confirmation', ['required', 'matches:new_password'], ['required' => 'This field is required !', 'matches' => 'Password did not match !']);

    $cek_form = form_validation();

    if ($_POST['old_password'] != '') {

        // Cek User
        $sql = "SELECT `password` FROM user_company WHERE id_user_company = '" . $_POST['id_user_company'] . "'";
        $exec = $_SERVER['KONEKSI_DB']->query($sql);

        if ($exec->num_rows != 1) {
            error('old_password', 'Password is incorrect !');
            $cek_form = false;
        } else {

            // Cek Password
            $password_hash = $exec->fetch_assoc()['password'];

            if (!password_verify($_POST['old_password'], $password_hash)) {
                error('old_password', 'Password is incorrect !');
                $cek_form = false;
            } else {
                success('old_password');
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
        'id_user_company' => $_POST['id_user_company'],
    );

    $data = array(
        'password' => password_hash($_POST['new_password'], PASSWORD_DEFAULT),
    );


    $sql = table_update('user_company', $data, $where);

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


if ($action == 'identity') {

    // var_dump( $_POST ); exit();
    $id_user_company = mysqli_real_escape_string($koneksi_db, $_POST['id_user_company']);
    $user_fullname = mysqli_real_escape_string($koneksi_db, $_POST['user_fullname']);
    $user_phone = mysqli_real_escape_string($koneksi_db, $_POST['user_phone']);
    $user_email = mysqli_real_escape_string($koneksi_db, $_POST['user_email']);
    $username = mysqli_real_escape_string($koneksi_db, $_POST['username']);


    set_rules('id_user_company', 'id_user_company', ['required'], ['required' => 'Cannot be empty !']);

    set_rules('old_user_phone', 'old_user_phone', [''], ['required' => 'Cannot be empty !']);
    set_rules('old_user_email', 'old_user_email', [''], ['required' => 'Cannot be empty !']);
    set_rules('old_username', 'old_username', [''], ['required' => 'Cannot be empty !']);


    set_rules('user_fullname', 'User_fullname', ['required'], ['required' => 'Cannot be empty !']);
    set_rules('user_phone', 'User_phone', ['required'], ['required' => 'Cannot be empty !', 'unique_except' => 'Phone Number is already in use !']);
    set_rules('user_email', 'User_email', ['required'], ['required' => 'Cannot be empty !', 'unique_except' => 'E-Mail is already in use !']);
    set_rules('username', 'username', ['required', 'unique_except:' . $_POST['old_username'] . '|user_company.username'], ['required' => 'Cannot be empty !', 'unique_except' => 'Username already taken !']);

    $cek_form = form_validation();

    // if ( $_POST['username'] != '' ) {
    //     if ( $_POST['username'] != $_POST['old_username'] ) {
            
            
    //         $sql = "SELECT * FROM user_company WHERE username = :username LIMIT 1";
    //         $stmt = $PDO->prepare($sql);
    //         $stmt->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
    //         $stmt->execute();
            
    //         if ($stmt->rowCount() != 0) {
    //             error('username', 'Username already taken!');
    //             $cek_form = false;
                
    //         }
    //     }
    // }


    if ($cek_form === false) {
        
        // if ( $_SESSION['username'] == 'HRD.0421' ) {
        //   echo "<pre>" .var_dump( $_SESSION ). "</pre>";
        //   die;
        // }
        
        
        make_swal2('error' ,'Form Validation Failed', 'Check you submitted data and try again later (1)');
        // make_snackbar('Check you submitted data and try again later (1)');

        make_old('open-profile', 'open');
        redirect_back(1);
        exit;
    }



    $where = array(
        'id_user_company' => $_POST['id_user_company'],
    );

    $data = array(
        'user_fullname' => $user_fullname, 'user_phone' => $user_phone,
        'user_email' => $user_email, 'username' => $username
    );


    $sql = table_update('user_company', $data, $where);

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

if ($action == 'profile') {

    set_rules('new_profile', 'New Profile', ['required', 'allowed_types:png|jpg|jpeg'], ['required' => 'This field is required !', 'allowed_types' => 'This file type is not allowed !']);

    if (form_validation() === false) {
        make_snackbar('Check you submitted data and try again later');

        make_old('open-profile', 'open');
        redirect_back(1);
        exit;
    }

    // Get Data Profile
    $sql = "SELECT * FROM `user_company` WHERE id_user_company = '" . $_SESSION['id_user_company'] . "'";
    $exec = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

    if (!in_array(strtolower($exec['profile_picture']), ['profile.jpg', 'default.jpg'])) {
        unlink('upload/profile_picture/' . $exec['profile_picture']);
    }


    // Upload file
    // move_uploaded_file( $_FILES['new_profile'] ['tmp_name'] , );
    // $new_name = escape( uniqid(). random_strings(15). ".". strtolower(end(explode('.', $_FILES['new_profile']['name']))));

    //   if ( move_uploaded_file( $_FILES['new_profile'] ['tmp_name'], "upload/profile_picture/$new_name" ) === FALSE ) {
    //     make_snackbar('Check you submitted data and try again later');
    //     make_old('open-profile' , 'open');
    //     error('new_profile', 'File failed to upload !');
    //     redirect_back(1); exit;
    // }

    $new_name = escape_string(random_strings(15) . "-" . random_strings(15) . "-" . random_strings(15) . "-" . uniqid() . ".jpg");

    $image = imagecreatefromstring(file_get_contents($_FILES['new_profile']['tmp_name']));
    if (!imagejpeg($image, './upload/profile_picture/' . $new_name, 50)) {
        // if (move_uploaded_file($_FILES['new_profile']['tmp_name'], './upload/profile_picture/' . $new_name) === FALSE) {
        error('new_profile', 'Failed to upload Profile Picture');

        make_snackbar('Please check your submitted data and try again later');

        make_old('open-form', 'open');
        error('new_profile', 'File failed to upload !');
        redirect_back(1);
        exit;
    }


    // Update Profile Picture
    $manager = new ImageManager(['driver' => 'gd']);

    // Membuka gambar menggunakan library php-image-editor
    $image = $manager->make('./upload/profile_picture/' . $new_name);

    // Menghapus data EXIF geolokasi
    $image->iptc([]);

    // Simpan kembali gambar setelah menghapus geolokasi
    $image->save($imagePath);



    // Update Profile Picture


    $data = array(
        'profile_picture' => $new_name,
    );

    $where = array(
        'id_user_company' => $_SESSION['id_user_company']
    );

    $update = table_update('user_company', $data, $where);

    if ($update === false) {
        unlink("upload/profile_picture/$new_name");
        make_snackbar('Check you submitted data and try again later');

        make_old('open-profile', 'open');
        error('new_profile', 'File failed to upload !');
        redirect_back(1);
        exit;
    } else {
        reflash_all();
        $_SESSION['profile_picture'] = $new_name;
        make_snackbar('Profile Picture has been updated');
        redirect_referrer(1);
        exit;
    }
} else {
    make_snackbar('You do not have permission to perform this action');
    redirect_back(1);
    exit;
}
