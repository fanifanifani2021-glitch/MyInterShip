<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();

include 'ajax-init.php';

$id_comment = $_POST['id_comment'];
$user_id = $_POST['user_id']; 

$sql = mysqli_query( $_SERVER['KONEKSI_DB'] , "DELETE FROM `comment_report` WHERE `id_comment` = '$id_comment' AND `user_id` = '$user_id'");

if ( $sql === TRUE ) {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
    $_SESSION['old']['snackbar-message'] = 'Report has been canceled';

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'success';
    // $_SESSION['old']['swal2-title'] = 'Cancel Report Success';
    // $_SESSION['old']['swal2-message'] = 'Report have been succesfully canceled !';
    // $_SESSION['old']['swal2-timer'] = 2000;

    echo 'OK';
} else {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
    $_SESSION['old']['snackbar-message'] = 'Comment Report has not been canceled, try again later';

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'error';
    // $_SESSION['old']['swal2-title'] = 'Cancel Report Failed';
    // $_SESSION['old']['swal2-message'] = 'Please try again later !';
    // $_SESSION['old']['swal2-timer'] = 2000;
    
    echo 'NOT OK';
}
