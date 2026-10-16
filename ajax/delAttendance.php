<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();

include 'ajax-init.php';

$id_attendance = $_POST['id_attendance'];

// Cek Validasi
$sql = mysqli_query( $koneksi_db , "SELECT * FROM attendance WHERE id_attendance = '$id_attendance'");
$data = mysqli_fetch_array( $sql);


$sql = mysqli_query( $koneksi_db , "DELETE FROM attendance WHERE id_attendance = '$id_attendance' ");

if ( $sql === TRUE ) {
    echo 'OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Attendance has been deleted';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

} else {
    echo 'NOT OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Failed to delete Attendance, try again later';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
}
