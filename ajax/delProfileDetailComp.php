<?php
header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$id_detail_profile = $_POST['id_detail_profile'];

// Cek Validasi
// $sql = mysqli_query( $koneksi_db , "SELECT * FROM profile_jobdesc WHERE id_profile_jobdesc = '$id_profile_jobdesc' AND kode_prodi = '$kode_prodi' ");
// if ( mysqli_num_rows($sql) != 1 ) {
    
//     $_SESSION['old']['have-swal2'] = true;
//     $_SESSION['old']['swal2-state'] = 'error';
//     $_SESSION['old']['swal2-title'] = 'Deleting Data Failed';
//     $_SESSION['old']['swal2-message'] = 'Please try again later !';
//     $_SESSION['old']['swal2-timer'] = 2000;

//     echo 'NOT OK';

// exit();
// }


$sql = mysqli_query( $koneksi_db , "DELETE FROM profile_detail_comp WHERE id_detail_profile = '$id_detail_profile' ");

if ( $sql === TRUE ) {
    echo 'OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Competence Unit has been removed';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
} else {
    echo 'NOT OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Failed to remove Competence Unit, try again later';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';
}
