<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();

include 'ajax-init.php';

$id_comment = $_POST['id_comment'];
$comment = mysqli_real_escape_string( $_SERVER['KONEKSI_DB'], $_POST['comment']);
$timestamp_update = date('Y-m-d H:i:s');


$sql    = mysqli_query( $koneksi_db , "UPDATE discussion_comment SET comment = '$comment' , timestamp_update = '$timestamp_update' WHERE id_comment = '$id_comment' ");
$sql2   = mysqli_query( $koneksi_db , "DELETE FROM comment_report WHERE id_comment = '$id_comment' ");

if ( $sql === TRUE ) {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Comment has been edited';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

    echo 'OK';
} else {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Failed to edit comment, try again later';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

    echo 'NOT OK';
}
