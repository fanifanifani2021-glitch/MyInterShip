<?php
error_reporting( false ); session_start();
$title = "Proses Industry Feedback";
include "./controller/controller-init.php";

make_snackbar('You do not have permission to perform this action');
role_checker('user_type', ['HRD','Supervisor', 'SPV', 'spv'], 'notifikasi');


set_rules('catatan_utk_mahasiswa', 'catatan_utk_mahasiswa', ['required'], ['required' => 'This field is required']);
set_rules('catatan_utk_poltek', 'catatan_utk_poltek', ['required'], ['required' => 'This field is required']);
set_rules('layak_diterima', 'layak_diterima', ['required'], ['required' => 'This field is required']);
set_rules('langsung_diterima', 'langsung_diterima', ['required'], ['required' => 'This field is required']);


set_rules('nilai_akhir', 'nilai_akhir', ['required', 'numeric'], ['required' => 'This field is required', 'numeric' => 'Allowed values are 1 to 100']);

set_rules('pengembangan', 'pengembangan', ['required', 'allowed_values:1,2,3,4'], ['required' => 'This field is required', 'allowed_values' => 'Allowed values are 1 to 4']);
set_rules('bahasa', 'bahasa', ['required', 'allowed_values:1,2,3,4'], ['required' => 'This field is required', 'allowed_values' => 'Allowed values are 1 to 4']);
set_rules('it', 'it', ['required', 'allowed_values:1,2,3,4'], ['required' => 'This field is required', 'allowed_values' => 'Allowed values are 1 to 4']);
set_rules('komunikasi', 'komunikasi', ['required', 'allowed_values:1,2,3,4'], ['required' => 'This field is required', 'allowed_values' => 'Allowed values are 1 to 4']);
set_rules('kerjasama', 'kerjasama', ['required', 'allowed_values:1,2,3,4'], ['required' => 'This field is required', 'allowed_values' => 'Allowed values are 1 to 4']);

set_rules('etika', 'etika', ['required', 'allowed_values:1,2,3,4'], ['required' => 'This field is required', 'allowed_values' => 'Allowed values are 1 to 4' ]);
set_rules('kompetensi', 'kompetensi', ['required', 'allowed_values:1,2,3,4'], ['required' => 'This field is required', 'allowed_values' => 'Allowed values are 1 to 4' ]);



if ( form_validation() === false ){



    make_old('open-form', 'open');
    make_snackbar('(01) Check your submitted data and try again later');
    redirect_back(1); exit;
}  

$id = mysqli_real_escape_string($koneksi_db,$_POST['id_feedback']);
$catatan_utk_mahasiswa = mysqli_real_escape_string($koneksi_db,$_POST['catatan_utk_mahasiswa']);
$catatan_utk_poltek = mysqli_real_escape_string($koneksi_db,$_POST['catatan_utk_poltek']);
$etika = mysqli_real_escape_string($koneksi_db,$_POST['etika']);
$kompetensi = mysqli_real_escape_string($koneksi_db,$_POST['kompetensi']);
$bahasa = mysqli_real_escape_string($koneksi_db,$_POST['bahasa']);
$it = mysqli_real_escape_string($koneksi_db,$_POST['it']);
$komunikasi = mysqli_real_escape_string($koneksi_db,$_POST['komunikasi']);
$kerjasama = mysqli_real_escape_string($koneksi_db,$_POST['kerjasama']);
$pengembangan = mysqli_real_escape_string($koneksi_db,$_POST['pengembangan']);
$layak_diterima = mysqli_real_escape_string($koneksi_db,$_POST['layak_diterima']);
$langsung_diterima = mysqli_real_escape_string($koneksi_db,$_POST['langsung_diterima']);
$nilai_akhir = mysqli_real_escape_string($koneksi_db,$_POST['nilai_akhir']);

$id_intern = $_POST['id_internship'];
$nim = $_POST['nim'];
$name = $_POST['name'];

// Cek Internship Feedback
$sql = "SELECT * FROM industry_feedback WHERE id_internship = '".$id_intern."' LIMIT 1";
$exec = $_SERVER['DB']->query($sql);


if ( $exec->num_rows != 0 ) {

    $feedback_data = $exec->fetch_assoc();

    $where = array(
        'id_industry_feedback' => $feedback_data['id_industry_feedback'],
    );

    $data = array(
        'nim' => $nim, 
        'catatan_utk_mahasiswa' => $catatan_utk_mahasiswa, 
        'catatan_utk_poltek' => $catatan_utk_poltek, 
        'layak_diterima' => $layak_diterima, 
        'langsung_diterima' => $langsung_diterima, 
        'nilai_akhir' => $nilai_akhir, 
        'etika' => $etika, 
        'keahlian_kompetensi' => $kompetensi, 
        'keahlian_bahasa' => $bahasa, 
        'penggunaan_ti' => $it, 
        'komunikasi' => $komunikasi, 
        'kerjasama' => $kerjasama, 
        'pengembangan_diri' => $pengembangan, 'date' => date('Y-m-d'),

        'terisi' => 1,
    );

    $sql = table_update( 'industry_feedback', $data, $where );

    // $sql = mysqli_query($koneksi_db, "UPDATE `industry_feedback` SET
    // `catatan_utk_mahasiswa`= '$catatan_utk_mahasiswa', `catatan_utk_poltek`='$catatan_utk_poltek', 
    // `layak_diterima`= '$layak_diterima', `langsung_diterima`= '$langsung_diterima', `nilai_akhir`= '$nilai_akhir', `etika`= '$etika',  
    // `keahlian_kompetensi`= '$kompetensi', `keahlian_bahasa`= '$bahasa', `penggunaan_ti`= '$it', `komunikasi`= '$komunikasi', `kerjasama`= '$kerjasama', `pengembangan_diri`= '$pengembangan' 
    // WHERE `id_industry_feedback` = '$id' ;");


} else {

    $data = array(
        'id_industry_feedback' => NULL, 
        'id_internship' => $id_intern, 
        'nim' => $nim, 
        'catatan_utk_mahasiswa' => $catatan_utk_mahasiswa, 
        'catatan_utk_poltek' => $catatan_utk_poltek, 
        'layak_diterima' => $layak_diterima, 
        'langsung_diterima' => $langsung_diterima, 
        'nilai_akhir' => $nilai_akhir, 
        'etika' => $etika, 
        'keahlian_kompetensi' => $kompetensi, 
        'keahlian_bahasa' => $bahasa, 
        'penggunaan_ti' => $it, 
        'komunikasi' => $komunikasi, 
        'kerjasama' => $kerjasama, 
        'pengembangan_diri' => $pengembangan, 'date' => date('Y-m-d'),

        'terisi' => 1,

    );
    $sql = table_insert('industry_feedback', $data);


    
    // $sql = mysqli_query($koneksi_db, "INSERT INTO `industry_feedback` SET
    // `catatan_utk_mahasiswa`= '$catatan_utk_mahasiswa', `catatan_utk_poltek`='$catatan_utk_poltek', 
    // `layak_diterima`= '$layak_diterima', `langsung_diterima`= '$langsung_diterima', `nilai_akhir`= '$nilai_akhir', `etika`= '$etika',  
    // `keahlian_kompetensi`= '$kompetensi', `keahlian_bahasa`= '$bahasa', `penggunaan_ti`= '$it', `komunikasi`= '$komunikasi', `kerjasama`= '$kerjasama', `pengembangan_diri`= '$pengembangan' 
    // WHERE `id_industry_feedback` = '$id' ;");

}

// var_dump( $sql ); exit;


if ( $sql === TRUE) {
    reflash_all();
    make_snackbar('Industry Feedback Data has been saved');
    redirect_referrer(1);exit();
} 
else {
    make_old('open-form', 'open');

    make_snackbar('(02) Check your submitted data and try again later');
    redirect_back(1);exit;
}

?>