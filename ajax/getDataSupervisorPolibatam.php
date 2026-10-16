<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();
include 'ajax-init.php';


$nim_nik_unit = $_POST['nim_nik_unit'];

$sql = mysqli_query( $_SERVER['KONEKSI_DB'] , "SELECT * FROM lecturer WHERE nim_nik_unit = '$nim_nik_unit' ");
$array = [];

if ( mysqli_num_rows( $sql ) == 1 ) {
    // echo ">0";
    while($row = mysqli_fetch_assoc($sql)) {

        $sql2 = mysqli_query( $_SERVER['KONEKSI_DB'], "SELECT COUNT(*) AS total_mahasiswa FROM internship WHERE nik = '$nim_nik_unit'");
        $mhs_supervisor = mysqli_fetch_assoc($sql2) ['total_mahasiswa'];
        $data = array(
            'hasil_pencarian' => 'Berhasil',
            'name' => $row['name'],
            'email' => $row['email_polibatam'],
            'status' => $row['status'],
            'program_study' => $row['program_study'],
            'total_student' => $mhs_supervisor,
        );
        array_push($array, $data);
    }


}else{
    $array[] = array("hasil_pencarian" => "Gagal");
}

echo json_encode($array);

?>