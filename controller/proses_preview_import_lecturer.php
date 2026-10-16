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


    foreach ($sheet as $i => $row) {

        if ($numrow > 2) {

            $lecturer_nik = $row['B'];
            $lecturer_name = $row['C'];

            
            // Cek jika semua data tidak diisi
            if (empty($lecturer_nik) || empty($lecturer_name)) {
                $kosong++;
                continue; // Lewat data pada baris ini (masuk ke looping selanjutnya / baris selanjutnya)
            }

            $data_import[$i - 1]['lecturer_nik'] = $lecturer_nik;
            $data_import[$i - 1]['lecturer_name'] = $lecturer_name;
            $data_import[$i - 1]['import_status'] = NULL;
            $data_import[$i - 1]['import_review'] = 'Not Yet Imported';


        }

        $numrow++; // Tambah 1 setiap kali looping
    }

    if (($kosong) != 0) {
        $data_bermasalah = true;
    } else {
        $data_bermasalah = false;
    }

    $_SESSION['IMPORT'] ['LECTURER'] = $data_import;
    $_SESSION['IMPORT'] ['LECTURER_OVERALL_IMPORT'] = NULL;

    unlink($dir . $nama_file_baru);

    view('upkpk/lecturer_management/preview_data_lecturer', ['data_bermasalah' => $data_bermasalah ]);
}
