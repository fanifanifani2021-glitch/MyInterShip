<?php

/**
 * Logout Controller - Gaya Manual (Procedural Fallback)
 * Lokasi: controller/AuthController/LogoutController.php
 */

// --- Fungsi Internal untuk Membersihkan Cache Viewer ---
if (!function_exists('clearViewerCache')) {
    function clearViewerCache() {
        // Menggunakan Session::get() untuk akses yang lebih aman
        $viewerRequests = Session::get('VIEWER_REQUEST');
        
        if (is_array($viewerRequests)) {
            foreach ($viewerRequests as $viewer) {
                if (isset($viewer['CACHE']) && file_exists($viewer['CACHE'])) {
                    @unlink($viewer['CACHE']);
                }
            }
        }
    }
}

switch ($method):
    default:
        echo "Method Not Found";
    break;

    case 'logout_action':
        // 1. Jalankan pembersihan file fisik cache viewer
        clearViewerCache();

        // 2. Ambil nama user untuk pesan perpisahan (opsional)
        $userName = Session::get('name') ?? 'User';

        // 3. Bersihkan data session pengguna
        // Gunakan flush() agar CSRF Token (_token) tetap ada untuk mencegah error Page Expired
        Session::flush(['_token', 'token']);

        // 4. Berikan notifikasi
        Notification::swal2('Logout Success', "Thank you for using our services, $userName!", 'success');

        // 5. Redirect ke halaman utama
        Response::redirect(Url::base(''))->send();
    break;
endswitch;