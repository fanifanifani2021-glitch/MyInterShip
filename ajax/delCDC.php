<?php
header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$id_upkpk = base64_decode($_POST['id_upkpk']);

$sql = mysqli_query( $koneksi_db , "DELETE FROM upkpk WHERE id_upkpk = $id_upkpk ");

if ( $sql === TRUE ) {

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'success';
    // $_SESSION['old']['swal2-title'] = 'Deleting Data Success';
    // $_SESSION['old']['swal2-message'] = 'UPKPK data have been succesfully deleted !';
    // $_SESSION['old']['swal2-timer'] = 2000;

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'CDC User has been deleted successfully !';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

    echo 'OK';
} else {

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'error';
    // $_SESSION['old']['swal2-title'] = 'Deleting Data Failed';
    // $_SESSION['old']['swal2-message'] = 'Please try again later !';
    // $_SESSION['old']['swal2-timer'] = 2000;

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Deleting data failed, Try again later';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';


    echo 'NOT OK';
}
