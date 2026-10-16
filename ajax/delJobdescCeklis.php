<?php
header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$id_ceklis = $_POST['id_ceklis'];

// Cek Validasi
$sql = mysqli_query( $koneksi_db , "SELECT * FROM logbook WHERE id_logbook = '$id_logbook'");
$data = mysqli_fetch_array( $sql);


$sql = mysqli_query( $koneksi_db , "DELETE FROM jobdesc_intern_ceklis WHERE id_ceklis = '$id_ceklis' ");

if ( $sql === TRUE ) {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Jobdesk Type has been deleted !';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

    echo 'OK';
} else {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Failed to delete Jobdesk Type, please try again later !';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
    echo 'NOT OK';
}
