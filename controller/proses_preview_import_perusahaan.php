<?php
error_reporting( true ); session_start();

$_SESSION['IMPORT'] = [];

$dir = 'tmp/';
$nama_file_baru = 'data.xlsx';

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// Cek apakah terdapat file data.xlsx pada folder tmp
if (is_file($dir . $nama_file_baru)) {
    unlink($dir . $nama_file_baru); // Hapus file tersebut
}

$tipe_file = $_FILES['file']['type']; // Ambil tipe file yang akan diupload
$tmp_file = $_FILES['file']['tmp_name'];

if ($tipe_file != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") { // Jika file yang diupload bukan File Excel 2007 (.xlsx)
    echo "<div class='alert alert-danger'>Hanya File Excel 2007 (.xlsx) yang diperbolehkan
        </div>"; exit;
}


$_SESSION['IMPORT'] ['COMPANY'] = [];
$_SESSION['IMPORT'] ['COMPANY_OVERALL_IMPORT'] = NULL;


// Cek apakah file yang diupload adalah file Excel 2007 (.xlsx)
if ($tipe_file == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {


    move_uploaded_file($tmp_file, $dir . $nama_file_baru);

    // Load librari PHPExcel nya
    require_once './library/PHPExcel/PHPExcel.php';

    $excelreader = new PHPExcel_Reader_Excel2007();

    $loadexcel = $excelreader->load($dir . $nama_file_baru); // Load file yang tadi diupload ke folder tmp
    $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

    $data_import = [];
    $numrow = 1;
    $kosong = 0;

    $sql = "SELECT MAX(id_company) as max_company FROM company";
    $query = $_SERVER['DB']->query($sql)->fetch_assoc();
    $max_company = (int) $query['max_company'] + 1;

    $sql = "SELECT COUNT(id_user_company) as max_user_company FROM user_company";
    $query = $_SERVER['DB']->query($sql)->fetch_assoc();
    $max_user_company = (int) $query['max_user_company'] + 30;


    // var_dump($sheet); exit;
    foreach ($sheet as $i => $row) {
        if ($numrow > 2) {
            // Ambil data pada excel sesuai Kolom
            $nama_perusahaan = $row['B'];
            $email = $row['C'];
            $no_telp = $row['D'];
            $alamat = $row['E'];
            $province = $row['F'] ?? 'KEPULAUAN RIAU';
            $city = $row['G'] ?? 'KOTA BATAM';

            $username = 'HRD.' .$nama_perusahaan;

            $username = 'HRD.' .sprintf( '%04s', $max_user_company++);

            $password = '123456' ;
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Cek jika semua data tidak diisi
            if (empty($nama_perusahaan)) {
                $kosong++;
                continue; // Lewat data pada baris ini (masuk ke looping selanjutnya / baris selanjutnya)
            }

            $data_import[$i - 1]['nama_perusahaan'] = $nama_perusahaan;
            $data_import[$i - 1]['email'] = $email;
            $data_import[$i - 1]['no_telp'] = $no_telp;
            $data_import[$i - 1]['alamat'] = $alamat;
            $data_import[$i - 1]['province'] = $province;
            $data_import[$i - 1]['city'] = $city;
            $data_import[$i - 1]['access_type'] = '1';
            $data_import[$i - 1]['company_status'] = '1';
            $data_import[$i - 1]['username'] = $username;
            $data_import[$i - 1]['password'] = $password;
            $data_import[$i - 1]['password_hash'] = $password_hash;


            $data_import[$i - 1]['import_status'] = null;
            $data_import[$i - 1]['import_review'] = 'Not Yet Imported';

        }

        $numrow++; // Tambah 1 setiap kali looping
    }

    if (($kosong) != 0) {
        $data_bermasalah = true;
    } else {
        $data_bermasalah = false;
    }

    $_SESSION['IMPORT'] ['COMPANY'] = $data_import;
    $_SESSION['IMPORT'] ['COMPANY_OVERALL_IMPORT'] = NULL;

    unlink($dir . $nama_file_baru);

    view('upkpk/kelola_perusahaan/preview_data_perusahaan', ['data_import' => $data_import, 'data_bermasalah' => $data_bermasalah, 'total_kosong' => $kosong]);
}
