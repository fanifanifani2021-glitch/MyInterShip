<?php
error_reporting( false ); session_start();
$title = "Proses Student Feedback";
include "./controller/controller-init.php";


set_rules('id_feedback', 'id_feedback', ['required'], ['required' => 'This field is required !']);
set_rules('id_internship', 'id_internship', ['required'], ['required' => 'This field is required !']);
set_rules('kesan', 'kesan', ['required'], ['required' => 'This field is required !']);
set_rules('kendala', 'kendala', ['required'], ['required' => 'This field is required !']);
set_rules('masukan', 'masukan', ['required'], ['required' => 'This field is required !']);

set_rules('posisi', 'posisi', ['required'], ['required' => 'This field is required !']);
set_rules('ilmu_dari_poltek', 'ilmu_dari_poltek', ['required'], ['required' => 'This field is required !']);
set_rules('ilmu_baru', 'ilmu_baru', ['required'], ['required' => 'This field is required !']);
set_rules('data_magang', 'data_magang', ['required'], ['required' => 'This field is required !']);

if ( form_validation() === FALSE ){
    // var_dump( $_SESSION ); exit;
    make_old('open-form', true);
    make_snackbar('Please check your submitted data !');
    redirect_back(1); exit;
}

// echo "lolos bisa simpan"; exit;

$id = mysqli_real_escape_string($koneksi_db,$_POST['id_feedback']);
$posisi = mysqli_real_escape_string($koneksi_db,$_POST['posisi']);
$ilmu_dari_poltek = mysqli_real_escape_string($koneksi_db,$_POST['ilmu_dari_poltek']);
$ilmu_baru = mysqli_real_escape_string($koneksi_db,$_POST['ilmu_baru']);
$data_magang = mysqli_real_escape_string($koneksi_db,$_POST['data_magang']);
$kesan = mysqli_real_escape_string($koneksi_db,$_POST['kesan']);
$kendala = mysqli_real_escape_string($koneksi_db,$_POST['kendala']);
$masukan = mysqli_real_escape_string($koneksi_db,$_POST['masukan']);

$id_intern = base64_encode($_POST['id_internship']);

$sql = mysqli_query($koneksi_db, "UPDATE `student_feedback` SET
    `posisi`= '$posisi', `ilmu_dari_poltek`='$ilmu_dari_poltek', 
    `ilmu_baru`= '$ilmu_baru', `data_magang`= '$data_magang', `kesan`= '$kesan', `kendala`= '$kendala',  
    `masukan`= '$masukan' WHERE `id_student_feedback` = '$id' ;");

if ( $sql === TRUE) {
    reflash_all();
    make_swal2('success', 'Feedback Saved', 'Feedback data saved been updated successfully !');
    redirect_referrer(1); exit;
}
else {
    make_old('open-form', true);
    make_swal2('error', 'Feedback Not Saved', 'Please try again later !');
    redirect_back(1); exit;
}

?>