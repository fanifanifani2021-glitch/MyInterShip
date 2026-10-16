<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();
include 'ajax-init.php';

$nim_nik_unit = $_POST['nim_nik_unit'];

$ch = curl_init('https://sid.polibatam.ac.id/apilogin/web/api/auth/cek-id?id='.$nim_nik_unit.'');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = json_decode(curl_exec($ch));

if( $result->status=="success" ) {

    $name = $result->data->name;
    $username = $result->data->username;
    $email = $result->data->email;
    $jabatan = $result->data->jabatan;


    if ( $result->data->jabatan == "Mahasiswa" ) { 
    
        $sql = "SELECT kode_prodi FROM program_study WHERE prodi = '".$result->data->prodi."' ";
        $kode_prodi = mysqli_query($koneksi_db , $sql);

        foreach ( $kode_prodi as $q => $hasil ) {
            $kode_prodi = $hasil['kode_prodi'];
        }


        $notelp = $result->data->notelp; $prodi = $result->data->prodi; $angkatan = $result->data->angkatan; $kode_prodi = $kode_prodi; 
    
    } else { $notelp = "--"; $prodi = '-'; $angkatan = '-'; $kode_prodi = '-'; }
    
    $ss[] = array("hasil_pencarian" => "Berhasil" ,"name" => $name, "username" => $username ,"email" => $email, "jabatan" => $jabatan, "notelp" => $notelp, 'prodi' => $prodi, 'kode_prodi' => $kode_prodi, 'angkatan' => $angkatan );

}else{
    $ss[] = array("hasil_pencarian" => "Gagal" , "name" => NULL, "username" => NULL, "email" => NULL, "jabatan" => NULL, "notelp" => NULL);
}

echo json_encode($ss);

?>