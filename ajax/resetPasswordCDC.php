<?php
header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$id_upkpk = base64_decode($_POST['id_upkpk']);
$password = password_hash('12345678', PASSWORD_DEFAULT);

$sql = mysqli_query( $koneksi_db , "UPDATE upkpk SET `password` = '$password' WHERE id_upkpk = $id_upkpk ");

if ( $sql === TRUE ) {

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'success';
    // $_SESSION['old']['swal2-title'] = 'Deleting Data Success';
    // $_SESSION['old']['swal2-message'] = 'UPKPK data have been succesfully deleted !';
    // $_SESSION['old']['swal2-timer'] = 2000;

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Password has been reset successfully !';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

    echo 'OK';
} else {

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'error';
    // $_SESSION['old']['swal2-title'] = 'Deleting Data Failed';
    // $_SESSION['old']['swal2-message'] = 'Please try again later !';
    // $_SESSION['old']['swal2-timer'] = 2000;

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Reset Password failed, Try again later';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';


    echo 'NOT OK';
}
