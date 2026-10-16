<?php

class Env
{
    /**
     * Cache Data Environment
     */
    private static $data = [];
    private static $isLoaded = false;
    private static $path = __DIR__ . '/../../.env'; // Sesuaikan path relative ke root

    /**
     * Load file .env ke Memory & putenv
     */
    public static function load()
    {
        // 1. Cek apakah sudah diload (Singleton pattern sederhana)
        if (self::$isLoaded) {
            return;
        }

        // 2. Cek file keberadaan file
        if (!file_exists(self::$path)) {
            // Jika tidak ada, kita inisiasi array kosong agar tidak error
            self::$isLoaded = true;
            return;
        }

        // 3. Baca File
        $lines = file(self::$path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip komentar
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse Key=Value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                $key = trim($key);
                $value = trim($value);

                // Hapus kutip " atau ' di awal/akhir value
                $value = self::sanitizeValue($value);

                // Simpan ke Static Variable
                self::$data[$key] = $value;

                // Simpan ke Environment Server (agar bisa dibaca getenv())
                putenv("$key=$value");
                $_SERVER[$key] = $value;
                $_ENV[$key] = $value;
            }
        }

        self::$isLoaded = true;
    }

    /**
     * Ambil Value ENV
     */
    public static function get($key, $default = null)
    {
        // Pastikan sudah di-load sebelum ambil
        if (!self::$isLoaded) {
            self::load();
        }

        return self::$data[$key] ?? $default;
    }

    /**
     * Tulis/Update Value ke file .env
     * (Menjaga komentar dan format asli tetap aman)
     */
    public static function set($key, $value)
    {
        if (!file_exists(self::$path)) {
            file_put_contents(self::$path, "");
        }

        // Baca konten mentah (string)
        $content = file_get_contents(self::$path);
        
        // Cek apakah key sudah ada di file?
        // Regex: Cari baris yang dimulai dengan KEY=...
        $pattern = "/^" . preg_quote($key, '/') . "=(.*)$/m";
        
        if (preg_match($pattern, $content)) {
            // UPDATE: Ganti baris yang ketemu dengan nilai baru
            $content = preg_replace($pattern, "$key=$value", $content);
        } else {
            // INSERT: Tambahkan di baris paling bawah
            $content = rtrim($content) . "\n$key=$value\n";
        }

        // Tulis ulang file
        file_put_contents(self::$path, $content);

        // Update memory runtime juga
        self::$data[$key] = $value;
        putenv("$key=$value");
    }

    /**
     * Helper: Bersihkan tanda kutip
     */
    private static function sanitizeValue($value)
    {
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            return $matches[1];
        } 
        if (preg_match("/^'(.*)'$/", $value, $matches)) {
            return $matches[1];
        }
        return $value;
    }
}