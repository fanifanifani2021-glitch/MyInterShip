<?php

/**
 * Session Management Class
 * Menangani Session Native, Flash Data, Personifikasi, dan Pembersihan File Fisik.
 */
class Session
{
    /** @var int Masa berlaku session (7 Hari) */
    private const LIFETIME = 60 * 60 * 24 * 7;

    /** @var string Lokasi penyimpanan file session */
    private const SESSION_DIR = BASE_DIR . '/storage/sessions';

    /** @var array Key yang akan diperlakukan sebagai flash data (sekali baca) */
    private static $hybrid_keys = [
        'old', 'error', 'success', 'warning', 'info', 
        'notification', 'notifikasi', 'custom',
        'notification_queue', 'active_notif_libs', 'notif_custom_css', 'notif_custom_js'
    ];

    /**
     * Inisialisasi Session.
     * @param string $driver 'native' atau 'file'
     * @return void
     */
    public static function init($driver = 'native')
    {
        if (session_status() === PHP_SESSION_NONE) {
            
            if ($driver === 'file') {
                if (!file_exists(self::SESSION_DIR)) {
                    @mkdir(self::SESSION_DIR, 0777, true);
                    @file_put_contents(self::SESSION_DIR . '/.htaccess', "Order Deny,Allow\nDeny from all");
                }
                session_save_path(self::SESSION_DIR);
                ini_set('session.gc_maxlifetime', self::LIFETIME);
                ini_set('session.cookie_lifetime', self::LIFETIME);
                
                // Housekeeping manual (Peluang 5% tiap request)
                if (rand(1, 100) <= 5) {
                    self::collectGarbage();
                }
            }

            session_start();

            // Jalankan pembersihan flash data secara otomatis saat script selesai
            register_shutdown_function([self::class, 'manageHybridFlash']);
        }
    }

    /**
     * Simpan data ke session.
     */
    public static function set($key, $value) { $_SESSION[$key] = $value; }

    /**
     * Ambil data dari session dengan dukungan Mode Personifikasi.
     */
    public static function get($key, $default = null) {
        $isAdminArea = strpos($_SERVER['REQUEST_URI'], '/cdc') !== false || 
                       strpos($_SERVER['REQUEST_URI'], '/ajax/cdc') !== false;

        if (!$isAdminArea && isset($_SESSION['user_personification'][$key])) {
            return $_SESSION['user_personification'][$key];
        }

        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) { return isset($_SESSION[$key]); }

    public static function remove($key) { if(isset($_SESSION[$key])) unset($_SESSION[$key]); }

    public static function all() { return $_SESSION; }
    
    /**
     * Ambil data lalu hapus (Flash data manual).
     */
    public static function pull($key, $default = null) {
        $val = self::get($key, $default);
        self::remove($key);
        return $val;
    }

    public static function regenerate() { session_regenerate_id(true); }

    public static function id() { return session_id(); }
    
    /**
     * Hancurkan session dan hapus file fisik secara permanen.
     */
    public static function destroy() {
        $id = session_id();
        $path = self::SESSION_DIR . '/sess_' . $id;

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Hapus file fisik agar folder /storage/sessions tidak penuh
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Set data flash yang akan hilang pada request berikutnya.
     */
    public static function flash($key, $value)
    {
        self::set($key, $value);
        $_SESSION['__manual_flash'][] = $key;
    }

    /**
     * Pertahankan key flash agar tidak terhapus pada request ini.
     */
    public static function keep($key)
    {
        if (isset($_SESSION['__hybrid_cleanup'])) {
            $keyIndex = array_search($key, $_SESSION['__hybrid_cleanup']);
            if ($keyIndex !== false) {
                unset($_SESSION['__hybrid_cleanup'][$keyIndex]);
            }
        }
    }

    /**
     * Menghapus file session yang sudah kadaluwarsa secara fisik.
     */
    public static function collectGarbage()
    {
        if (!is_dir(self::SESSION_DIR)) return;
        $files = glob(self::SESSION_DIR . '/sess_*');
        $now = time();

        foreach ($files as $file) {
            if ($now - filemtime($file) > self::LIFETIME) {
                @unlink($file);
            }
        }
    }

    /**
     * Logic Inti Pembersihan Flashdata.
     * Tidak akan menghapus data jika terdeteksi HTTP Redirect (3xx).
     */
    public static function manageHybridFlash()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) return;

        // Cegah penghapusan data jika sedang dalam proses Redirect
        $statusCode = http_response_code();
        $isRedirect = ($statusCode >= 300 && $statusCode < 400);
        
        $headers = headers_list();
        foreach ($headers as $header) {
            if (stripos($header, 'Location:') === 0) {
                $isRedirect = true;
                break;
            }
        }

        if ($isRedirect) return;

        // 1. Hapus data yang ditandai dari request sebelumnya
        $cleanup_list = $_SESSION['__hybrid_cleanup'] ?? [];
        foreach ($cleanup_list as $key) {
            unset($_SESSION[$key]);
        }

        // 2. Tandai data untuk dihapus pada request berikutnya
        $next_cleanup = [];
        foreach (self::$hybrid_keys as $key) {
            if (isset($_SESSION[$key]) && !empty($_SESSION[$key])) {
                $next_cleanup[] = $key;
            }
        }

        if (isset($_SESSION['__manual_flash'])) {
            $next_cleanup = array_merge($next_cleanup, $_SESSION['__manual_flash']);
            unset($_SESSION['__manual_flash']);
        }

        $_SESSION['__hybrid_cleanup'] = array_unique($next_cleanup);
    }

    /**
     * Menghapus satu atau beberapa data dari session (Alias dari remove).
     * @param string|array $key
     * @return void
     */
    public static function forget($key)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                self::remove($k);
            }
        } else {
            self::remove($key);
        }
    }


    /**
     * Menghapus semua data session kecuali kunci tertentu.
     * @param array $except Kunci yang ingin dipertahankan (default: token CSRF)
     * @return void
     */
    public static function flush(array $except = ['_token', 'token'])
    {
        foreach (array_keys($_SESSION) as $key) {
            // Jangan hapus jika ada dalam daftar pengecualian atau kunci internal flash
            if (!in_array($key, $except) && strpos($key, '__') !== 0) {
                self::remove($key);
            }
        }
    }

}