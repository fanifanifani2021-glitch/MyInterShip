<?php
header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$id_logbook = $_POST['id_logbook'];

// Cek Validasi
// $sql = mysqli_query( $koneksi_db , "SELECT * FROM logbook WHERE id_logbook = '$id_logbook'");
// $data = mysqli_fetch_array( $sql);


$sql = mysqli_query( $koneksi_db , "DELETE FROM logbook WHERE id_logbook = '$id_logbook' ");

if ( $sql === TRUE ) {

    $_SESSION['old']['have-swal2'] = true;
    $_SESSION['old']['swal2-state'] = 'success';
    $_SESSION['old']['swal2-title'] = 'Deleting Data Success';
    $_SESSION['old']['swal2-message'] = 'Logbook data have been succesfully deleted !';
    $_SESSION['old']['swal2-timer'] = 2000;

    echo 'OK';
} else {

    $_SESSION['old']['have-swal2'] = true;
    $_SESSION['old']['swal2-state'] = 'error';
    $_SESSION['old']['swal2-title'] = 'Deleting Data Failed';
    $_SESSION['old']['swal2-message'] = 'Please try again later !';
    $_SESSION['old']['swal2-timer'] = 2000;
    echo 'NOT OK';
}
