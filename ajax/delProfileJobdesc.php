<?php
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Origin: *');


error_reporting( false ); session_start();

include 'ajax-init.php';

$id_profile_jobdesc = $_POST['id_profile_jobdesc'];
$kode_prodi = $_POST['kode_prodi'];

// Cek Validasi
$sql = mysqli_query( $koneksi_db , "SELECT * FROM profile_jobdesc WHERE id_profile_jobdesc = '$id_profile_jobdesc' AND kode_prodi = '$kode_prodi' ");
if ( mysqli_num_rows($sql) != 1 ) {
    echo 'NOT OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Deleting Data Failed, try again later ';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

exit();
}


$sql = mysqli_query( $koneksi_db , "DELETE FROM profile_jobdesc WHERE id_profile_jobdesc = '$id_profile_jobdesc' ");

if ( $sql === TRUE ) {
    echo 'OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Graduation Profile Data has been deleted';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

} else {
    echo 'NOT OK';

    $_SESSION['old']['have-snackbar'] = true;
    $_SESSION['old']['snackbar-message'] = 'Deleting Data Failed, try again later ';
    $_SESSION['old']['snackbar-position'] = 'bottom-center';

}
