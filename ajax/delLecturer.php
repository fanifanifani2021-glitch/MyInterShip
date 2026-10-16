<?php
header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$nim_nik_unit = $_POST['nim_nik_unit'];

$sql = mysqli_query( $koneksi_db , "DELETE FROM lecturer WHERE nim_nik_unit = $nim_nik_unit ");

if ( $sql === TRUE ) {

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'success';
    // $_SESSION['old']['swal2-title'] = 'Deleting Data Success';
    // $_SESSION['old']['swal2-message'] = 'Lecturer data have been succesfully deleted !';
    // $_SESSION['old']['swal2-timer'] = 2000;

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
    $_SESSION['old']['snackbar-message'] = 'Lecturer data have been succesfully deleted';

    echo 'OK';
} else {

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'error';
    // $_SESSION['old']['swal2-title'] = 'Deleting Data Failed';
    // $_SESSION['old']['swal2-message'] = 'Please try again later !';
    // $_SESSION['old']['swal2-timer'] = 2000;

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
    $_SESSION['old']['snackbar-message'] = 'Deleting data failed, try again later';
    echo 'NOT OK';
}
