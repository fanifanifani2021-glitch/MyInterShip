<?php
header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$nim = $_POST['nim'];
$password = password_hash( $_SERVER['STUDENT_DEFAULT_PASSWORD'] , PASSWORD_DEFAULT);

$sql = mysqli_query( $koneksi_db , "UPDATE student_internship SET `password` = '$password' WHERE nim = '$nim'");

if ( $sql === TRUE ) {
    http_response_code(200); exit;

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Password has been reset successfully !';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

    echo 'OK';
} else {
    echo $nim;
    http_response_code(400); exit;


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
