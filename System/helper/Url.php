<?php

class Url
{
    // --- BASIC NAVIGATION ---

    public static function redirect($path = '/')
    {
        header("Location: " . self::base($path));
        exit;
    }

    public static function base($path = '')
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $baseUrl = $protocol . "://" . $host . $scriptDir;
        $baseUrl = rtrim($baseUrl, '/\\');

        return $baseUrl . '/' . ltrim($path, '/');
    }

    public static function current()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host     = $_SERVER['HTTP_HOST'];
        $uri      = $_SERVER['REQUEST_URI'];
        return $protocol . "://" . $host . $uri;
    }

    public static function previous($fallback = '/')
    {
        return $_SERVER['HTTP_REFERER'] ?? self::base($fallback);
    }

    // --- PARSING & MANIPULATION ---

    public static function path()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public static function segment($index = 1)
    {
        $path = trim(self::path(), '/');
        $segments = explode('/', $path);
        return $segments[$index - 1] ?? null;
    }

    public static function slug($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text);
    }

    // --- QUERY STRING HELPERS ---

    public static function addQuery(array $data)
    {
        $currentQuery = [];
        parse_str($_SERVER['QUERY_STRING'] ?? '', $currentQuery);
        $newQuery = array_merge($currentQuery, $data);
        return self::path() . '?' . http_build_query($newQuery);
    }

    /**
     * [BARU] Ambil data $_GET dengan aman dan default value
     * Contoh: $page = Url::query('page', 1);
     */
    public static function query($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    // --- UI/UX HELPERS (ASSETS & ACTIVE STATE) ---

    /**
     * [BARU] Load Asset dengan Cache Busting (Versioning otomatis)
     * Contoh Output: http://web.com/css/style.css?v=17089922
     * Pastikan konstanta BASE_PATH sudah didefinisikan di index.php
     */
    public static function asset($path)
    {
        $url = self::base($path);
        
        // Cek file fisik untuk ambil waktu modifikasi terakhir
        if (defined('BASE_PATH')) {
            $filePath = BASE_PATH . '/' . ltrim($path, '/');
            if (file_exists($filePath)) {
                $url .= '?v=' . filemtime($filePath);
            }
        }

        return $url;
    }

    /**
     * Cek URL dengan Pola Wildcard (*) 
     * Sekarang sudah cerdas menangani subfolder (seperti /myinternship/)
     */
    public static function match($pattern)
    {
        $path = self::path(); // Hasil: /myinternship/auth/login/cdc
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']); // Hasil: /myinternship

        // Bersihkan path dari subfolder agar hanya tersisa route-nya saja
        if ($scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
            $path = substr($path, strlen($scriptDir));
        }

        $currentPath = trim($path, '/'); // Hasil: auth/login/cdc
        $pattern = trim($pattern, '/');    // Pattern: auth/login/cdc

        // Ubah wildcard * menjadi Regex .*
        $pattern = str_replace('*', '.*', $pattern);
        
        return (bool) preg_match('#^' . $pattern . '$#i', $currentPath);
    }


    /**
     * [BARU] Set Header untuk Redirect 301 (Moved Permanently) dengan Delay
     * Digunakan untuk halaman pemberitahuan pindah alamat.
     * * @param string $path Path atau Full URL tujuan
     * @param int $seconds Detik penundaan sebelum redirect otomatis
     */
    public static function moved($path, $seconds = 5)
    {
        // Cek apakah $path adalah Full URL (http/https) atau relative path
        if (preg_match('#^https?://#i', $path)) {
            $url = $path;
        } else {
            $url = self::base($path);
        }

        http_response_code(301);
        header("Refresh: {$seconds}; url={$url}");
    }



    /**
     * [BARU] Mengambil Judul (<title>) dari sebuah URL atau Path
     * Logic: 
     * - Jika Internal: Format string URL menjadi Judul (Cepat, tanpa request)
     * - Jika External: Scrape HTML untuk ambil tag <title> (Lambat, request HTTP)
     */
    public static function getTitle($url)
    {
        // -----------------------------------------
        // A. HANDLER UNTUK URL INTERNAL (FORMATTING)
        // -----------------------------------------
        if (self::isInternal($url)) {
            // 1. Ambil path-nya saja (buang domain & query string)
            $path = parse_url($url, PHP_URL_PATH);
            
            // 2. Dapatkan folder root aplikasi untuk dibersihkan
            // Contoh: /myinternship/index.php -> sisa /myinternship
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']); 
            
            // 3. Bersihkan Path
            // Hapus folder aplikasi, ekstensi .php, dan prefix role umum
            $cleanup_list = [
                $scriptDir, 
                '.php', 
                'student/', 
                'lecturer/', 
                'cdc/', 
                'industry/', 
                'admin/'
            ];
            
            $cleanPath = str_replace($cleanup_list, '', $path);
            
            // 4. Ganti simbol pemisah URL (slash, dash, underscore) menjadi spasi
            $cleanPath = str_replace(['/', '-', '_'], ' ', $cleanPath);
            
            // 5. Rapikan string (trim spasi berlebih & kapitalisasi)
            $title = ucwords(trim($cleanPath));
            
            // 6. Return (Fallback ke "Dashboard" jika kosong/root)
            return !empty($title) ? $title : 'Dashboard';
        }
        
        // -----------------------------------------
        // B. HANDLER UNTUK URL EXTERNAL (SCRAPING)
        // -----------------------------------------
        
        // 1. Validasi: Pastikan URL External memiliki protokol (http/https)
        if (!preg_match('#^https?://#i', $url)) {
            // Jika user input "google.com", tambahkan "http://"
            $url = 'http://' . $url;
        }

        // 2. Validasi format URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return "Invalid URL";
        }

        // 3. Konfigurasi User-Agent (Penting agar tidak diblokir server tujuan)
        $options = [
            'http' => [
                'method' => "GET",
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n" .
                            "Accept-Language: en-US,en;q=0.9\r\n"
            ]
        ];
        $context = stream_context_create($options);

        // 4. Ambil HTML Content (Suppress warning dengan @)
        $html = @file_get_contents($url, false, $context);

        if ($html === false || empty($html)) {
            return "No Title (Unreachable)"; // Gagal koneksi
        }

        // 5. Parsing HTML
        $doc = new DOMDocument();
        libxml_use_internal_errors(true); // Matikan error HTML5 parsing
        $doc->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        // 6. Ambil tag <title>
        $nodes = $doc->getElementsByTagName('title');

        if ($nodes->length > 0) {
            return trim($nodes->item(0)->nodeValue);
        }

        return "No Title Found";
    }
    /**
     * [BARU] Cek apakah URL milik aplikasi kita (Internal) atau Luar (External)
     * * Contoh:
     * Url::isInternal('student/dashboard') -> true
     * Url::isInternal('http://localhost/myinternship/student') -> true (Jika base url sama)
     * Url::isInternal('https://google.com') -> false
     */
    public static function isInternal($url)
    {
        // 1. Jika URL kosong, anggap internal
        if (empty($url)) return true;

        // 2. Cek apakah URL adalah Relative Path (tidak ada http:// atau https://)
        // Regex ini mengecek skema protokol di awal string (http:, ftp:, mailto:, dll)
        if (!preg_match('#^[a-z][a-z0-9+.-]*:#i', $url)) {
            // Pengecualian: Protocol-relative URLs (misal //google.com) dianggap eksternal
            if (strpos($url, '//') === 0) return false;
            
            return true; // Relative path (misal: "student/home") adalah Internal
        }

        // 3. Jika URL Absolute (ada http://...), bandingkan dengan Base URL Aplikasi
        $appBaseUrl = self::base(); // Contoh: http://localhost/myinternship

        // Cek apakah $url DIAWALI dengan $appBaseUrl
        if (stripos($url, $appBaseUrl) === 0) {
            return true;
        }

        return false;
    }



    /**
     * [BARU] Validasi Keamanan URL untuk Redirect
     * Mencegah Open Redirect Vulnerability & XSS pada URL.
     * * @param string $path URL yang akan dicheck
     * @param bool $allowExternal (Default: false) Jika true, membolehkan redirect ke domain luar (misal google.com)
     * @return bool
     */
    public static function validatePath($path, $allowExternal = false)
    {
        // 1. Bersihkan path dari whitespace & karakter null
        $path = trim($path);
        
        // 2. Security Check Dasar (XSS & HTTP Response Splitting)
        // Mencegah karakter kontrol (\r, \n) dan karakter HTML berbahaya (<, >, ', ")
        if (preg_match('/[\r\n<>\'"]/', $path)) {
            return false;
        }

        // 3. Cek Skema Protokol (Mencegah javascript:, data:, vbscript:)
        // Kita hanya mengizinkan http/https atau relative path
        if (preg_match('/^([a-z0-9\+.-]+):/i', $path, $matches)) {
            $scheme = strtolower($matches[1]);
            if (!in_array($scheme, ['http', 'https'])) {
                return false; // Protocol berbahaya terdeteksi
            }
        }

        // 4. Deteksi apakah URL Absolute (ada http:// atau https://)
        $isAbsolute = preg_match('#^https?://#i', $path);

        // --- LOGIKA JIKA HANYA BOLEH INTERNAL (Default) ---
        if (!$allowExternal) {
            
            // A. Jika Absolute URL: WAJIB diawali dengan Base URL Aplikasi kita
            if ($isAbsolute) {
                $appBase = self::base(); // http://localhost/myinternship
                
                // Cek apakah $path dimulai dengan $appBase
                if (stripos($path, $appBase) !== 0) {
                    return false; // Domain berbeda, Tolak!
                }
            }
            // B. Jika Relative URL: 
            else {
                // Cegah "Protocol-Relative URL" (//google.com) yang bisa mem-bypass cek http
                if (strpos($path, '//') === 0 || strpos($path, '\\\\') === 0) {
                    return false;
                }

                // Cek karakter valid untuk path internal. 
                // Kita izinkan: Alphanumeric, Slash, Dash, Underscore, Dot, dan karakter Query String (?, =, &, %)
                if (!preg_match('/^[a-zA-Z0-9_\-\/\.\?=&%]+$/', $path)) {
                    return false; 
                }
            }
        }

        // 5. Jika $allowExternal = true, kita hanya validasi keamanan dasar (Langkah 2 & 3)
        // Jika sampai sini berarti lolos
        return true;
    }
}