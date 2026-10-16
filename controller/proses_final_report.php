<?php
error_reporting( true ); session_start();
$title = "Proses Final Report";
include "./controller/controller-init.php";

// make_snackbar('You do not have permission to perform this action');
// role_checker( 'type' , ['Mahasiswa'], 'notifikasi');

    set_rules( 'id_internship', '', ['required',], ['required' => 'This field is required !'] );
    set_rules( 'final_report', '', ['required', 'allowed_types:pdf', 'max_size: 10MB'], ['required' => 'This field is required !', 'allowed_types' => 'Allowed file type is pdf !', 'max_size' => 'Maximum file size is upto 10MB'] );

    $cek_form = form_validation(); 

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }    

    $id = mysqli_real_escape_string($koneksi_db,$_POST['id_internship']);
    $final = '-';
    $timestamp = date('Y-m-d H:i:s');

    $cek = mysqli_query($koneksi_db, "SELECT final_report FROM internship WHERE id_internship = $id");
    $count = mysqli_fetch_assoc($cek);

    // if($count['final_report'] != "-"){
    //     make_swal2('error', 'Final Report Not Saved', 'You have upload final report file !');
    //     redirect_back(1); exit;
    // }

    $sql = mysqli_query($koneksi_db, "UPDATE `internship` SET
        `final_report`= '$final', `date_finalreport`='$timestamp' WHERE `id_internship` = '$id' ;");

    if ( $sql === TRUE) {
        // Move Uploaded file
        $direkori = "upload/lampiran_internship/";

        $upload_final = upload( $direkori, 'final_report');

        if ( $upload_final === false) {
            make_snackbar('Failed when uploading final report, please try again later');
            redirect_back(1); exit;
        }

        $update_internship = array(
            'final_report' => $upload_final,
        );
        $update_internship = table_update( 'internship', $update_internship, ['id_internship' => $id]);
    
        if ( $update_internship === false ) {
            make_snackbar('Failed when uploading final report, please try again later');
            redirect_back(1); exit;
        }

        reflash_all();

        make_snackbar('Final Report Uploaded');
        redirect_referrer(1); exit();
    }
    
    else {
        make_snackbar('Failed when saving final report, try again later');
        redirect_back(1);exit;
    }


?>