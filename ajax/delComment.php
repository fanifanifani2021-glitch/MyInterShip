<?php
header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$id_comment = $_POST['id_comment'];




$sql = mysqli_query( $koneksi_db , "DELETE FROM discussion_comment WHERE id_comment = '$id_comment' ");

if ( $sql === TRUE ) {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Comment has been removed';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

    echo 'OK';
} else {

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Failed to delete comment, try again later';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

    echo 'NOT OK';
}
