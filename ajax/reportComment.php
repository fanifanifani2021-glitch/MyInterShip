<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();

include 'ajax-init.php';

$id_comment = $_POST['id_comment'];
$report = mysqli_real_escape_string( $_SERVER['KONEKSI_DB'], $_POST['report']);
$timestamp_add = date('Y-m-d H:i:s'); $user_id = $_POST['user_id']; 
$user_type = $_POST['user_type']; $user_name = $_POST['user_name'];

$sql = mysqli_query( $_SERVER['KONEKSI_DB'] , "INSERT INTO `comment_report`(`id_report`, `id_comment`, `user_type`, `user_id`, `user_name`, `reason`, `timestamp_add`, `timestamp_update`) 
    VALUES ( NULL, '$id_comment', '$user_type', '$user_id', '$user_name', '$report', '$timestamp_add', '-' )");

if ( $sql === TRUE ) {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
    $_SESSION['old']['snackbar-message'] = 'Comment has been reported';

    // $_SESSION['old']['swal2-state'] = 'success';
    // $_SESSION['old']['swal2-message'] = 'Report data have been succesfully saved !';
    // $_SESSION['old']['swal2-timer'] = 2000;

    echo 'OK';
} else {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
    $_SESSION['old']['snackbar-message'] = 'Reporting Comment Success';

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'error';
    // $_SESSION['old']['swal2-title'] = 'Reporting Comment Failed ';
    // $_SESSION['old']['swal2-message'] = 'Please try again later !';
    // $_SESSION['old']['swal2-timer'] = 2000;
    
    echo 'NOT OK';
}
