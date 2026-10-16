<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

// semua API otomatis pakai token middleware
Router::group([
    'prefix'     => 'viewer'
], function () {

    Router::get('/main/logo', function () {
		$file = $_SERVER['MAIN_LOGO'];
        FileViewer::serve(
            BASE_DIR. '/assets',
            BASE_DIR. '/' .$file,
            [
                'jpg'  => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png'  => ['image/png'],
            ]
        );
    })->name('main.logo');


    Router::get('/profile-picture', function () {
        
        $baseDir = BASE_DIR . '/upload';
        $profileFile = $_SESSION['profile_picture'] ?? null;

        $filePath = null;

        // Jika ada foto profil khusus (bukan default)
        if ($profileFile && $profileFile !== 'default.jpg') {
            $filePath = $baseDir . '/profile_picture/' . basename($profileFile);
        }

        // Fallback jika file tidak ada / null / default.jpg
        if (empty($filePath) || !file_exists($filePath) || !is_readable($filePath)) {
            if (file_exists($baseDir . '/default/default.jpg')) {
                $filePath = $baseDir . '/default/default.jpg';
            } else {
                $filePath = $baseDir . '/default.jpg';
            }
        }

        FileViewer::serve(
            $baseDir,
            $filePath,
            [
                'jpg'  => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png'  => ['image/png'],
            ]
        );

    })->name('user.profile-picture');


    Router::get('/manual/attendance_location/{step}', function ( $params ) {
        
        $baseDir = BASE_DIR . '/storage/public';

        if ( $params['step']) {
            $filePath = $baseDir . '/manual/industry-attendance_location/' . $params['step']. ".jpeg";
        }

        // fallback default (MASIH DI DALAM baseDir)
        if (empty($filePath) || !file_exists($filePath) || !is_readable($filePath)) {
            $filePath = $baseDir . '/404.png';
        }

        FileViewer::serve(
            $baseDir,
            $filePath,
            [
                'jpg'  => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png'  => ['image/png'],
            ]
        );

    })->name('manual.attendance_location');


    Router::get('/excel/template/lecturer', function () {
        controller('ImportController/LecturerImport', ['method' => 'template_import']);
    })->name('excel.template.import_lecturer');


    Router::get('/excel/template/{filename}', function ($params) {
        $location = base64_decode($params['filename']) ?? NULL;

        $baseDir = BASE_DIR . '/excel/template/';

        $filePath = $baseDir . basename($location);

        FileViewer::serve(
            $baseDir,
            $filePath,
            [
                'xls'  => ['application/vnd.ms-excel'],
                'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            ]
        );
    })->name('excel.template');


    // Rute Viewer Logo Perusahaan (Flexible: URL atau Local File)
    Router::get('/company/logo/{filename}', function ($params) {
        // 1. Decode parameter
        $source = base64_decode($params['filename']);
        $baseDir = BASE_DIR . '/upload/company';
        $fallbackPath = BASE_DIR . '/upload/default/default_company.png';

        // 2. Cek apakah ini Link URL Eksternal (http/https)
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            // Jika URL, kita langsung redirect ke link tersebut
            // Catatan: Jika ingin fallback untuk link mati, diperlukan pengecekan header (slow)
            header("Location: " . $source);
            exit;
        }

        // 3. Jika bukan URL, anggap sebagai File Lokal di folder /upload/company
        $fileName = basename($source);
        $filePath = $baseDir . '/' . $fileName;

        // Fallback jika file lokal tidak ditemukan atau tidak terbaca
        if (empty($fileName) || !file_exists($filePath) || !is_readable($filePath)) {
            $filePath = $fallbackPath;
        }

        // Sajikan file menggunakan FileViewer
        FileViewer::serve(
            BASE_DIR . '/upload', 
            $filePath,
            [
                'jpg'  => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png'  => ['image/png'],
                'webp' => ['image/webp'],
            ]
        );
    })->name('viewer.company.logo');


    Router::get('/student/avatar/{filename}', function($params) {
        
        $filename = $params['filename'] ?? null;

        // 1. Decode nama file
        $file = base64_decode($filename);

        // 2. Tentukan path folder (sesuaikan dengan struktur folder root kamu)
        $path = BASE_DIR . '/upload/profile_picture/' . $file;
        $defaultPath = BASE_DIR . '/upload/default/default.jpg';

        // 3. Cek apakah file ada, jika tidak gunakan default
        if (empty($file) || !file_exists($path)) {
            $path = $defaultPath;
        }

        // 4. Ambil ekstensi untuk Header
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $mimeType = ($ext == 'png') ? 'image/png' : 'image/jpeg';

        // 5. Kirim Header dan Output Gambar
        header("Content-type: $mimeType");
        readfile($path);
        exit;

    })->name('viewer.student.avatar');

});