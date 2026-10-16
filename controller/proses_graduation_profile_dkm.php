<?php
error_reporting( true ); session_start();

$title = 'Jobdesk Management';
include "./controller/controller-init.php";

make_snackbar('You do not have permission to perform this action');
role_checker( 'is_koor' , [true], 'notifikasi' );

$action = $_GET['action'];


// Add Jobdesc
if ( $action == "add" ){

    set_rules('kode_prodi', 'kode_prodi', ['required'], ['required' => 'This field is required !']); 
    set_rules('graduation_profile', 'graduation_profile', ['required'], ['required' => 'This field is required !']); 

    $cek_form = form_validation();

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        make_old('focus-add', 'open');
        redirect_back(1); exit();
    }

    $data = array(
        'id_profile_jobdesc' => NULL, 'kode_prodi' => $_POST['kode_prodi'],
        'nama_profile' => $_POST['graduation_profile'],
    );

    $sql = table_insert('profile_jobdesc', $data);

    if ( $sql === TRUE) {
        reflash_all(1);
        make_snackbar('Graduation Profile Data has been added');
        redirect_referrer(1); exit;
    }
    else {
        make_old('focus-add', 'open');
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }

} else

if ( $action == "edit" ){


    set_rules('id_edit', 'id_edit', ['required'], ['required' => 'This field is required !']); 
    set_rules('kode_prodi_edit', 'kode_prodi_edit', ['required'], ['required' => 'This field is required !']); 
    set_rules('graduation_profile_edit', 'graduation_profile', ['required'], ['required' => 'This field is required !']); 

    $cek_form = form_validation();

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');

        make_old('ModalEdit', 'open');
        redirect_back(1); exit();
    }

    $where = array(
        'id_profile_jobdesc' => $_POST['id_edit'],
        'kode_prodi' => $_POST['kode_prodi_edit'],
    );

    $data = array(
        'nama_profile' => $_POST['graduation_profile_edit'],
    );

    $sql = table_update( 'profile_jobdesc', $data, $where );

    if ( $sql === TRUE) {
        reflash_all(1);
        make_snackbar('Graduation Profile Data has been edited');
        redirect_referrer(1); exit;
    }
    else {
        make_old('ModalEdit', 'open');
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }

}

else {
    make_snackbar('You do not have permission to perform this action');
    redirect_back(1); exit;
}


?>