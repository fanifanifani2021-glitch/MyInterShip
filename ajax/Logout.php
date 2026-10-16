<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();

// Fungsi untuk menghapus file dalam folder cache berdasarkan daftar VIEWER_REQUEST
function clearCacheFiles()
{
    if (isset($_SESSION['VIEWER_REQUEST']) && is_array($_SESSION['VIEWER_REQUEST'])) {
        foreach ($_SESSION['VIEWER_REQUEST'] as $viewer) {
            if (isset($viewer['CACHE']) && file_exists($viewer['CACHE'])) {
                unlink($viewer['CACHE']);
            }
        }
    }
}

// Panggil fungsi untuk menghapus file cache sebelum session di-destroy
clearCacheFiles();


session_destroy();
session_start();
// $_SESSION['old'] = array(
//     'have-swal2' => TRUE,
//     'swal2-state' => 'success',
//     'swal2-title' => 'Logout Success',
//     'swal2-message' => 'Thank you for using our services !',
//     'swal2-timer' => 2000,
// );

$_SESSION['old'] = array(
    'have-snackbar' => TRUE,
    'snackbar-message' => 'Logout success, Thank you for using our services !',
    'snackbar-position' => 'bottom-center');

// $_SESSION['old'] += array(
//     'have-sound' => true,
// 	'sound-state' => 'close',
// 	'sound-timer' => 1000,
// );

echo "OK";