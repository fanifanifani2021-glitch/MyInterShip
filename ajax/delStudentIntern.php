<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();

include 'ajax-init.php';

$nim = $_POST['nim'];

$sql = mysqli_query( $koneksi_db , "DELETE FROM student_internship WHERE nim = $nim ");

if ( $sql === TRUE ) {
    echo 'OK';

    // $_SESSION['old']['have-swal2'] = true;
    // $_SESSION['old']['swal2-state'] = 'success';
    // $_SESSION['old']['swal2-title'] = 'Deleting Data Success';
    // $_SESSION['old']['swal2-message'] = 'Intern Student data have been succesfully deleted !';
    // $_SESSION['old']['swal2-timer'] = 2000;

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Student Internship has been removed';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
} else {
    echo 'NOT OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Failed to remove Student Internship, try again later';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

}
