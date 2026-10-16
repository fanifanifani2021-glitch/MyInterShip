<?php
error_reporting( false ); session_start();
$title = "Proses Perusahaan";
include "./controller/controller-init.php";
include "./controller/template/cek_proses_hrd.php";

$aksi = $_GET['aksi'];

make_snackbar('You do not have permission to perform this action');
role_checker('user_type', ['HRD'], 'notifikasi');


if ( $aksi == "assign_supervisor" ) {


    set_rules( 'id_internship', 'id_internship' , ['required'], ['required' => 'This field is required !'] );
    set_rules( 'id_supervisor', 'id_supervisor' , ['required'], ['required' => 'This field is required !'] );
    set_rules( 'start_date', 'id_supervisor' , ['required'], ['required' => 'This field is required !'] );
    set_rules( 'end_date', 'id_supervisor' , ['required'], ['required' => 'This field is required !'] );

    if ( form_validation() === false ) {
        make_snackbar('Check your submitted data and try again later');
        make_old('ModalAssignSupervisor', 'open');
        redirect_back(1); exit;
    }


    $where = array( 'id_internship' => $_POST['id_internship'] );

    $data = array( 
        'id_user_company' => $_POST['id_supervisor'],
        'start_date' => date( 'Y-m-d', strtotime($_POST['start_date'])),
        'end_date' => date( 'Y-m-d', strtotime($_POST['end_date'])),
    );

    $assign = table_update( 'internship', $data, $where );

    if ( $assign === TRUE ) {
        reflash_all();

        make_snackbar('Supervisor has been assigned to current Student Intern');
        // make_swal2('success', ' Assign Supervisor Success', 'Supervisor have been assigned successfully to current internship !');
        redirect_referrer(1); exit();
    } else {
        make_old('ModalAssignSupervisor', 'open');
        
        make_snackbar('Check your submitted data and try again later');
        // make_swal2('error', 'Assign Supervisor Failed', 'Please try again later !');
        redirect_back(1); exit();
    }

} else

if ($aksi == "tambah_spv"){

    set_rules( 'user_fullname', 'User_fullname' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'user_phone', 'User_phone' , ['required', 'is_unique:user_company.user_phone'], ['required' => 'Cannot be empty !', 'is_unique' => 'Phone Number is already in use !'] );
    set_rules( 'user_email', 'User_email' , ['required', 'is_unique:user_company.user_email'], ['required' => 'Cannot be empty !', 'is_unique' => 'E-Mail is already in use !'] );
    set_rules( 'user_type', 'user_type' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'username', 'username' , ['required', 'is_unique:user_company.username'], ['required' => 'Cannot be empty !', 'is_unique' => 'Username already taken !'] );
    set_rules( 'password', 'password' , ['required', ], ['required' => 'Cannot be empty !'] );

    $cek_form = form_validation();  

    if ( $_POST['user_type'] == 'HRD') {

        $sql = "SELECT * FROM user_company WHERE user_type = 'HRD' AND id_company = '".$_POST['id_company']."'";
        $exec = $_SERVER['KONEKSI_DB']->query($sql);

        if ( $exec->num_rows != 0 ){
            $cek_form = false;
            error('user_type', 'You can only have one HRD per company !');
        }

    }



    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        make_old('ModalTambah', 'open');
        redirect_back(1); exit;
    }

    $data = array(
        'id_user_company' => NULL, 'id_company' => $_POST['id_company'], 
        'user_fullname' => $_POST['user_fullname'], 'user_phone' => $_POST['user_phone'], 
        'user_email' => $_POST['user_email'], 'username' => $_POST['username'], 
        'password' => password_hash( $_POST['password'], PASSWORD_DEFAULT), 'user_type' => 'Supervisor', 
        'profile_picture' => 'profile.jpg'
    );


    $sql = table_insert('user_company', $data);


    if ( $sql === TRUE) {
        reflash_all();
        make_snackbar('New Supervisor has been added');
        redirect_referrer(1); exit;

    } else {
        make_old('ModalTambah', 'open');

        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }


} else


if ($aksi == "edit_spv"){


    set_rules( 'id_user_company', 'id_user_company' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'loop', 'loop' , ['required'], ['required' => 'Cannot be empty !'] );

    set_rules( 'old_user_phone', 'old_user_phone' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'old_user_email', 'old_user_email' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'old_username', 'old_username' , ['required'], ['required' => 'Cannot be empty !'] );


    set_rules( 'edit_user_fullname', 'User_fullname' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'edit_user_phone', 'User_phone' , ['required', 'unique_except:' .$_POST['old_user_phone'].'|user_company.user_phone'], ['required' => 'Cannot be empty !', 'unique_except' => 'Phone Number is already in use !'] );
    set_rules( 'edit_user_email', 'User_email' , ['required', 'unique_except:'.$_POST['old_user_email']. '|user_company.user_email'], ['required' => 'Cannot be empty !', 'unique_except' => 'E-Mail is already in use !'] );
    set_rules( 'edit_username', 'Username' , ['required', 'unique_except:'.$_POST['old_username']. '|user_company.username'], ['required' => 'Cannot be empty !', 'unique_except' => 'Username already taken !'] );

    $cek_form = form_validation(); 



    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');

        make_old('ModalEdit', 'open');
        make_old('LoopEdit', $_POST['loop']);
        redirect_back(1); exit;
    }




    $id_user_company = mysqli_real_escape_string($koneksi_db,$_POST['id_user_company']);
    $loop = mysqli_real_escape_string($koneksi_db,$_POST['loop']);
    $user_fullname = mysqli_real_escape_string($koneksi_db,$_POST['edit_user_fullname']);
    $user_phone = mysqli_real_escape_string($koneksi_db,$_POST['edit_user_phone']);
    $user_email = mysqli_real_escape_string($koneksi_db,$_POST['edit_user_email']);
    $user_type = mysqli_real_escape_string($koneksi_db, 'Supervisor');
    $username = mysqli_real_escape_string($koneksi_db,$_POST['edit_username']);
    $password = "password";
    $hash = password_hash($password, PASSWORD_DEFAULT);



    $where = array(
        'id_user_company' => $_POST['id_user_company'],
    );

    $data = array(
        'user_fullname' => $user_fullname, 
        'user_phone' => $user_phone, 'user_email' => $user_email, 
        'username' => $username, 'user_type' => $user_type);


    $sql = table_update( 'user_company', $data, $where);


    if ( $sql === TRUE) {
        reflash_all();
        make_sound('success');

        make_snackbar('Supervisor has been edited');
        redirect_referrer(1); exit;

    } else {
        reflash_all();

        make_old('ModalEdit', 'open');
        make_old('LoopEdit', $_POST['loop']);

        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }

} else


if ( $aksi == "hapus_spv" ) {

    $id = mysqli_real_escape_string($koneksi_db,$_POST['id_hapus']);

    $sql = mysqli_query($koneksi_db, "DELETE FROM user_company WHERE id_user_company = '$id';" );
    
    if ( $sql === TRUE ) {
        reflash_all();
        make_snackbar('Supervisor has been deleted');
        redirect_referrer(1); exit();
    } else {

        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }
exit;
}


if ( $aksi == "reset_spv" ) {
    $password = 'password';
    $password = password_hash($password, PASSWORD_DEFAULT);

    $where = array(
        'id_user_company' => $_POST['id_reset'],
    );

    $data = array(
        'password' => $password,
    );


    $sql = table_update( 'user_company', $data, $where);


    if ( $sql === TRUE) {
        reflash_all();

        make_snackbar('Supervisor Password has been resetted');
        redirect_referrer(1); exit;

    } else {
        reflash_all();
        make_snackbar('Failed to reset password, try again later');

        redirect_back(1); exit;
    }
exit;
}


else {
    make_snackbar('You do not have permission to perform this action');
    redirect_back(1); exit;
}

