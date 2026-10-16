<?php
error_reporting( true ); session_start();

$title = 'Jobdesk Management';
include "./controller/controller-init.php";

$action = $_GET['action'];


make_snackbar('You do not have permission to perform this action');
role_checker( 'user_type' , ['Koor'], 'notifikasi' );

// Add Jobdesc
if ( $action == "add" ){


    set_rules('id_profile_jobdesc', 'id_profile', ['required'], ['required' => 'This field is required !']); 
    set_rules('unit_kompetensi', 'unit_kompetensi', ['required'], ['required' => 'This field is required !']); 
    set_rules('deskripsi_unit', 'deskripsi_unit', ['required'], ['required' => 'This field is required !']); 
    set_rules('kode_matkul', 'kode_matkul', ['required'], ['required' => 'This field is required !']); 
    set_rules('nama_matkul', 'nama_matkul', ['required'], ['required' => 'This field is required !']); 

    $cek_form = form_validation();

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');

        make_old('focus-add', 'open');
        redirect_back(1); exit();
    }

    $data = array(
        'id_detail_profile' => NULL, 'id_profile_jobdesc' => $_POST['id_profile_jobdesc'],
        'unit_kompetensi' => $_POST['unit_kompetensi'], 'deskripsi_unit' => $_POST['deskripsi_unit'],
        'kode_matkul' => $_POST['kode_matkul'], 'matkul' => $_POST['nama_matkul'],
    );

    $sql = table_insert('profile_detail_comp', $data);


    if ( $sql === TRUE) {
        reflash_all();
        make_old('focus-add', 'open');

        make_snackbar('Competence Unit has been saved');
        redirect_referrer(1); exit;
    }
    else {
        make_snackbar('Check your submitted data and try again later');

        make_old('focus-add', 'open');
        redirect_back(1); exit;
    }

} else

if ( $action == "edit" ){


    set_rules('id_edit', 'id_edit', ['required'], ['required' => 'This field is required !']); 
    set_rules('unit_kompetensi_edit', 'unit_kompetensi_edit', ['required'], ['required' => 'This field is required !']); 
    set_rules('deskripsi_unit_edit', 'deskripsi_unit_edit', ['required'], ['required' => 'This field is required !']); 
    set_rules('kode_matkul_edit', 'kode_matkul_edit', ['required'], ['required' => 'This field is required !']); 
    set_rules('nama_matkul_edit', 'nama_matkul_edit', ['required'], ['required' => 'This field is required !']); 

    $cek_form = form_validation();

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');

        make_old('ModalEdit', 'open');
        redirect_back(1); exit();
    }


    $where = array(
        'id_detail_profile' => $_POST['id_edit'],
    );

    $data = array(
        'unit_kompetensi' => $_POST['unit_kompetensi_edit'], 'deskripsi_unit' => $_POST['deskripsi_unit_edit'],
        'kode_matkul' => $_POST['kode_matkul_edit'], 'matkul' => $_POST['nama_matkul_edit'],
    );

    $sql = table_update( 'profile_detail_comp', $data, $where );

    if ( $sql === TRUE) {
        reflash_all(1);

        make_snackbar('Competence Unit has been edited');
        redirect_referrer(1); exit;
    }
    else {
        make_snackbar('Check your submitted data and try again later');

        make_old('ModalEdit', 'open');
        redirect_back(1); exit;
    }

}

else {
    make_snackbar('You do not have permission to perform this action');
    redirect_back(1); exit;
}


?>