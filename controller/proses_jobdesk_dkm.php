<?php
error_reporting( true ); session_start();

$title = 'Jobdesk Management';
include "./controller/controller-init.php";

make_snackbar('You do not have permission to perform this action');
role_checker( 'is_koor' , [TRUE], 'notifikasi' );

$aksi = $_GET['aksi'];


// Add Jobdesc
if ( $aksi == "add" ){

    set_rules('prodi', 'prodi', ['required'], ['required' => 'This field is required !']); 
    set_rules('pertanyaan_1', 'pertanyaan_1', ['required'], ['required' => 'This field is required !']); 
    set_rules('pertanyaan_2', 'pertanyaan_2', ['required'], ['required' => 'This field is required !']); 
    set_rules('need_monitoring', 'need_monitoring', ['required'], ['required' => 'This field is required !']); 
    set_rules('additional_information', 'additional_information', ['required'], ['required' => 'This field is required !']); 

    $cek_form = form_validation();

    if ( $cek_form === false ) {
        make_snackbar('Please check your submitted data and try again later');
        make_old('open-form', 'open');

        redirect_back(1); exit();
    }

    $data = array(
        'id_jobdesc' => NULL, 'kode_prodi' => $_POST['prodi'],
        'pertanyaan_1' => $_POST['pertanyaan_1'], 'pertanyaan_2' => $_POST['pertanyaan_2'],
        'need_monitoring' => $_POST['need_monitoring'], 'additional_information' => $_POST['additional_information'],
    );

    $sql = table_insert('jobdesc', $data);


    if ( $sql === TRUE) {
        reflash_all(1);
        make_swal2('success', 'Jobdesc Saved', 'Jobdesc data have been successfully saved !');
        redirect('jobdesk_management', 1); exit;
    }
    else {
        make_old('open-form', 'open');
        make_swal2('error', 'Jobdesc Not Saved', 'Please check your submitted data and try again later !');
        redirect_back(1); exit;
    }

} else


// Edit Jobdesc
if ( $aksi == "edit" ){

    set_rules('id_jobdesc', 'id_jobdesc', ['required'], ['required' => 'This field is required !']); 
    set_rules('prodi', 'prodi', ['required'], ['required' => 'This field is required !']); 
    set_rules('pertanyaan_1', 'pertanyaan_1', ['required'], ['required' => 'This field is required !']); 
    set_rules('pertanyaan_2', 'pertanyaan_2', ['required'], ['required' => 'This field is required !']); 
    set_rules('need_monitoring', 'need_monitoring', ['required'], ['required' => 'This field is required !']); 
    set_rules('additional_information', 'additional_information', ['required'], ['required' => 'This field is required !']); 

    $cek_form = form_validation();

    if ( $cek_form === false ) {
        make_snackbar('Please check your submitted data and try again later');

        make_old('open-form', 'open');
        redirect_back(1); exit();
    }


    $where = array(
        'id_jobdesc' => $_POST['id_jobdesc'],
        'kode_prodi' => $_POST['prodi'],
    );

    $data = array(
        'pertanyaan_1' => $_POST['pertanyaan_1'], 'pertanyaan_2' => $_POST['pertanyaan_2'],
        'need_monitoring' => $_POST['need_monitoring'], 'additional_information' => $_POST['additional_information'],
    );

    $sql = table_update('jobdesc', $data, $where);


    if ( $sql === TRUE) {
        reflash_all();
        make_snackbar('Jobdesc Data has been updated');

        redirect_referrer(1); exit;
    }
    else {
        make_old('open-form', 'open');
        make_snackbar('Please check your submitted data and try again later');
        redirect_back(1); exit;
    }

} else


// Add Job Type
if ( $aksi == "addjob" ){

    set_rules('id_jobdesc', 'id_jobdesc', ['required'], ['required' => 'This field is required !']);
    set_rules('jenis_pekerjaan', 'jenis_pekerjaan', ['required'], ['required' => 'This field is required !']);
    set_rules('job_description', 'job_description', ['required'], ['required' => 'This field is required !']);

    if ( form_validation() === FALSE ){
        make_snackbar('Please check your submitted data and try again later');
        redirect_back(1); exit;
    }

    $data = array(
        'id_detail' => NULL,
        'id_jobdesc' => $_POST['id_jobdesc'],
        'jenis_pekerjaan' => $_POST['jenis_pekerjaan'],
        'job_description' => $_POST['job_description'],
    );


    $sql = table_insert('detail_jobdesc', $data);

    // var_dump( $_SERVER['KONEKSI_DB'] ); exit;


    if ( $sql === TRUE) {
        make_snackbar('Jobtype has been added');

        make_old('scroll', 'true');
        redirect_referrer(1); exit();
    }
    else {
        make_snackbar('Please check your submitted data and try again later');
        redirect_back(1); exit;
    }

} else


// Delete Job Type
if ( $aksi == "deljob" ){
    $id = mysqli_real_escape_string($koneksi_db,$_POST['id_hapus']);

    $sql = mysqli_query($koneksi_db, "DELETE FROM detail_jobdesc WHERE id_detail = '$id' ;");

    if ( $sql === TRUE) {
        make_snackbar('Jobtype has been deleted');
        make_old('scroll', 'true');
        redirect_referrer(1); 
        exit();
    }
    else {
        make_snackbar('Failed to delete Job Type, try again later');
        redirect_back(1); exit;
    }

}


// Edit Job Type
if ( $aksi == "editjob" ){
    $id = mysqli_real_escape_string($koneksi_db,$_POST['id_detail_edit']);
    $jenis = mysqli_real_escape_string($koneksi_db,$_POST['jenis_pekerjaan_edit']);
    $job_description = mysqli_real_escape_string($koneksi_db,$_POST['job_description_edit']);

    $sql = mysqli_query($koneksi_db, "UPDATE `detail_jobdesc` SET
    `jenis_pekerjaan`= '$jenis', `job_description` = '$job_description' WHERE `id_detail` = '$id' ;");

    if ( $sql === TRUE) {
        reflash_all();

        make_snackbar('Jobtype has been edited');
        make_old('scroll', 'true');
        redirect_referrer(1); exit();
    }
    else {
        make_old('ModalEdit', 'open');
        make_snackbar('Please check your submitted data and try again later');

        redirect_back(1); exit;
    }

}


?>