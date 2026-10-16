<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();

include 'ajax-init.php';

$id_profile_student_jobdesc = $_POST['id_profile_student_jobdesc'];

// Cek Validasi


$sql = mysqli_query( $koneksi_db , "DELETE FROM profile_student_jobdesc WHERE id_profile_student_jobdesc = '$id_profile_student_jobdesc' ");

if ( $sql === TRUE ) {
    echo 'OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Student Competency has been deleted';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

} else {
    echo 'NOT OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Deleting Data Failed, try again later ';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
}
