<?php
header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$id_discussion = $_POST['id_discussion'];




$sql = mysqli_query( $koneksi_db , "DELETE FROM discussion WHERE id_discussion = '$id_discussion' ");

if ( $sql === TRUE ) {
    echo 'OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Discussion Topic has been removed';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

} else {
    echo 'NOT OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Failed to remove Discussion Topic, please try again later';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
}
