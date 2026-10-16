<?php
error_reporting( true ); session_start();

$title = 'Jobdesk Management';
include "./controller/controller-init.php";

$aksi = $_GET['aksi'];


// Add Jobdesc
if ( $aksi == "add" ){


    set_rules('id_jobdesc', 'id_jobdesc', ['required'], ['required' => 'This field is required !']); 
    set_rules('id_internsho', 'id_internship', ['required'], ['required' => 'This field is required !']); 
    set_rules('nim', 'nim', ['required'], ['required' => 'This field is required !']); 
    set_rules('jawaban_1', 'jawaban_1', ['required'], ['required' => 'This field is required !']); 
    set_rules('jawaban_2', 'jawaban_2', ['required'], ['required' => 'This field is required !']); 

    $cek_form = form_validation();


    if ( $cek_form === false ) {
        make_snackbar('Please check your submitted data !');
        make_old('open-form', 'open');
        redirect_back(1); exit();
    }


    $data = array(
        'id_jobdesc_intern' => NULL, 'id_jobdesc' => $_POST['id_jobdesc'],
        'id_internship' => $_POST['id_internship'], 'nim' => $_POST['nim'],
        'jawaban_1' => $_POST['jawaban_1'], 'jawaban_2' => $_POST['jawaban_2'],
        'timestamp_add' => date('Y-m-d H:i:s'), 'timestamp_update' => '-',
        'timestamp_approval' => date('Y-m-d H:i:s'),
    );


    $sql = table_insert('jobdesc_intern', $data);

    if ( $sql === TRUE) {
        reflash_all();
        make_swal2('success', 'Jobdesc Saved', 'Jobedesc data have been updated successfully !');
        // redirect('jobdesk_management', 1); exit();
        redirect_referrer(1); exit;
    }
    else {
        make_old('open-form', 'open');
        make_swal2('error', 'Jobdesc Not Saved', 'Please check your submitted data and try again later !');
        redirect_back(1); exit;
    }

} else


// Edit Jobdesc
if ( $aksi == "edit" ){


    set_rules('id_jobdesc_intern', 'id_jobdesc_intern', ['required'], ['required' => 'This field is required !']); 
    set_rules('id_jobdesc', 'id_jobdesc', ['required'], ['required' => 'This field is required !']); 
    set_rules('id_internsho', 'id_internship', ['required'], ['required' => 'This field is required !']); 
    set_rules('nim', 'nim', ['required'], ['required' => 'This field is required !']); 
    set_rules('jawaban_1', 'jawaban_1', ['required'], ['required' => 'This field is required !']); 
    set_rules('jawaban_2', 'jawaban_2', ['required'], ['required' => 'This field is required !']); 

    $cek_form = form_validation();


    if ( $cek_form === false ) {
        make_snackbar('Please check your submitted data !');
        make_old('open-form', 'open');
        redirect_back(1); exit();
    }

    $where = array(
        'id_jobdesc_intern' => $_POST['id_jobdesc_intern'], 'id_jobdesc' => $_POST['id_jobdesc'],
        'nim' => $_POST['nim'], 'id_internship' => $_POST['id_internship']
    );


    $data = array(
        'jawaban_1' => $_POST['jawaban_1'], 'jawaban_2' => $_POST['jawaban_2'],
        'timestamp_update' => date('Y-m-d H:i:s'), 'timestamp_approval' => date('Y-m-d H:i:s'),
    );

    $sql = table_update('jobdesc_intern', $data, $where);

    if ( $sql === TRUE) {
        reflash_all();
        make_swal2('success', 'Jobdesc Saved', 'Jobedesc data have been updated successfully !');
        // redirect('jobdesk_management', 1); exit();
        redirect_referrer(1); exit;
    }
    else {
        make_old('open-form', 'open');
        make_snackbar('Please check your submitted data and try again later !');
        
        // make_swal2('error', 'Jobdesc Not Saved', 'Please check your submitted data and try again later !');
        redirect_back(1); exit;
    }

} else


// Add Job Type
if ( $aksi == "add_job_type" ){


    set_rules('id_jobdesc_intern', 'id_jobdesc_intern', ['required'], ['required' => 'This field is required !']);
    set_rules('nim', 'nim', ['required'], ['required' => 'This field is required !']);
    set_rules('id_internship', 'id_internship', ['required'], ['required' => 'This field is required !']);
    set_rules('need_monitoring', 'need_monitoring', ['required'], ['required' => 'This field is required !']);
    set_rules('id_detail', 'id_detail', ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();


    // Cek

    $sql = "SELECT * FROM jobdesc_intern_ceklis WHERE id_jobdesc_intern = '".$_POST['id_jobdesc_intern']."' AND id_detail = '".$_POST['id_detail']."' ";

    if ( $_SERVER['KONEKSI_DB']->query($sql)->num_rows != 0 ) {
        $cek_form = false;
        error('id_detail', 'Selected job type already saved !');
    }


    if ( $cek_form === FALSE ){

        make_old('FocusAdd', true);
        make_snackbar('Please check your submitted data !');
        redirect_back(1); exit;
    }



    $data = array(
        'id_ceklis' => NULL, 'id_detail' => $_POST['id_detail'],
        'id_jobdesc_intern' => $_POST['id_jobdesc_intern'], 'ceklis' => 1,
        'timestamp_add' => date('Y-m-d H:i:s'), 'timestamp_update' => '-',
        'date_start' => '-', 'date_end' => '-',
    );

    $sql = table_insert('jobdesc_intern_ceklis', $data);

    if ( $sql === TRUE) {
        reflash_all();
        make_old('FocusAdd', true);
        make_snackbar('Jobdesk Type has been added !');
        // make_toast3('success', 'Job Type Saved', 'Jobdesk Type have been saved successfully !');
        redirect_referrer(1); exit;
    }
    else {
        make_snackbar('Please check your submitted data and try again later !');

        // make_toast3('error', 'Job Type Not Saved', 'Please check your submitted data and try again later !');
        redirect_back(1); exit;
    }







} else


// Add Monitoring
if ( $aksi == "add_monitoring_job" ){


    set_rules('id_jobdesc_intern', 'id_jobdesc_intern', ['required'], ['required' => 'This field is required !']);
    set_rules('nim', 'nim', ['required'], ['required' => 'This field is required !']);
    set_rules('id_internship', 'id_internship', ['required'], ['required' => 'This field is required !']);
    set_rules('need_monitoring', 'need_monitoring', ['required'], ['required' => 'This field is required !']);
    set_rules('id_detail', 'id_detail', ['required'], ['required' => 'This field is required !']);
    set_rules('start_date', 'start_date', ['required'], ['required' => 'This field is required !']);
    set_rules('end_date', 'end_date', ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();

    if ( $cek_form === FALSE ){
        make_toast3('error', 'Job Type Not Saved', 'Please check your submitted data and try again later !');
        redirect_back(1); exit;
    }



    $data = array(
        'id_ceklis' => NULL, 'id_detail' => $_POST['id_detail'],
        'id_jobdesc_intern' => $_POST['id_jobdesc_intern'], 'ceklis' => 1,
        'timestamp_add' => date('Y-m-d H:i:s'), 'timestamp_update' => '-',
        'date_start' => $_POST['start_date'], 'date_end' => $_POST['end_date'],
    );

    $sql = table_insert('jobdesc_intern_ceklis', $data);

    if ( $sql === TRUE) {
        reflash_all();
        make_old('FocusAdd', true);
        make_toast3('success', 'Job Type Saved', 'Jobdesk Type have been saved successfully !');
        redirect_referrer(1); exit;
    }
    else {
        make_toast3('error', 'Job Type Not Saved', 'Please check your submitted data and try again later !');
        redirect_back(1); exit;
    }







} else

// Edit Monitoring
if ( $aksi == "edit_monitoring_job" ){


    set_rules('id_detail', 'id_detail', ['required'], ['required' => 'This field is required !']);
    set_rules('loop', 'loop', ['required'], ['required' => 'This field is required !']);
    set_rules('id_jobdesc_intern', 'id_jobdesc_intern', ['required'], ['required' => 'This field is required !']);
    set_rules('nim', 'nim', ['required'], ['required' => 'This field is required !']);
    set_rules('id_internship', 'id_internship', ['required'], ['required' => 'This field is required !']);
    set_rules('need_monitoring', 'need_monitoring', ['required'], ['required' => 'This field is required !']);
    set_rules('edit_id_detail', 'id_detail', ['required'], ['required' => 'This field is required !']);
    set_rules('edit_start_date', 'start_date', ['required'], ['required' => 'This field is required !']);
    set_rules('edit_end_date', 'end_date', ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();


    if ( $cek_form === FALSE ){
        make_old('ModalEdit', TRUE);
        make_old('LoopEdit', $_POST['loop']);
        make_toast3('error', 'Job Type Not Edited', 'Please check your submitted data and try again later !');
        redirect_back(1); exit;
    }


    $where = array(
        'id_ceklis' => $_POST['id_ceklis'],
    );



    $data = array(
        'id_detail' => $_POST['edit_id_detail'], 'timestamp_update' => date('Y-m-d H:i:s'),
        'date_start' => $_POST['edit_start_date'], 'date_end' => $_POST['edit_end_date'],
    );

    $sql = table_update('jobdesc_intern_ceklis', $data, $where);

    if ( $sql === TRUE) {
        reflash_all();
        make_old('FocusTable', TRUE);

        make_toast3('success', 'Job Type Edited', 'Jobdesk Type have been saved successfully !');
        redirect_referrer(1); exit;
    }
    else {
        make_old('ModalEdit', TRUE);
        make_old('LoopEdit', $_POST['loop']);
        make_toast3('error', 'Job Type Not Edited', 'Please check your submitted data and try again later !');
        redirect_back(1); exit;
    }



} 

else {
    make_swal2('error', 'Not Allowed', 'You do not have permission to perform this action !');
    redirect_back(1); exit;
}





?>