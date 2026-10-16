<?php
header('Access-Control-Allow-Origin: *');

error_reporting(false);
session_start();


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

http_response_code(200);
make_snackbar('Logout success, Thank you for using our services !'); exit;
