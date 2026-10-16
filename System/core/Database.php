<?php

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            // Mengambil konfigurasi dari Konstanta atau Env
            $host    = defined('DB_HOST')     ? DB_HOST     : env('DB_HOST');
            $name    = defined('DB_NAME')     ? DB_NAME     : env('DB_NAME');
            $user    = defined('DB_USERNAME') ? DB_USERNAME : env('DB_USERNAME');
            $pass    = defined('DB_PASSWORD') ? DB_PASSWORD : env('DB_PASSWORD');
            $driver  = defined('DB_DRIVER')   ? DB_DRIVER   : env('DB_DRIVER', 'mysql');
            $port    = defined('DB_PORT')     ? DB_PORT     : env('DB_PORT', '3306');
            $charset = defined('DB_CHARSET')  ? DB_CHARSET  : 'utf8mb4';

            $dsn = "$driver:host=$host;port=$port;dbname=$name;charset=$charset";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_FOUND_ROWS   => true,
                PDO::ATTR_TIMEOUT            => 5, // Timeout 5 detik agar tidak hang lama
            ];

            $this->pdo = new PDO($dsn, $user, $pass, $options);

        } catch (PDOException $e) {
            // Jika koneksi gagal, langsung lempar ke handler static
            self::handleDatabaseDown($e);
        }
    }

    /**
     * Handler untuk menangani kondisi database mati secara elegan.
     */
    private static function handleDatabaseDown($exception) 
    {
        // 1. Logging internal
        if (class_exists('Logger')) {
            Logger::write("Database_Error", "Connection Failed: " . $exception->getMessage());
        }

        // 2. Set Status Code 503 (Service Unavailable)
        if (!headers_sent()) {
            http_response_code(503);
        }

        // 3. Response JSON jika request mengharapkan JSON (API/AJAX)
        // Gunakan try-catch kecil agar jika Request class error, tidak blank putih
        try {
            if (class_exists('Request') && Request::expectsJson()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Database connection failed. Please try again later.'
                ]);
                exit;
            }
        } catch (Exception $e) {
            // Fallback jika class Request belum siap
        }

        // 4. Render View Error
        // Pastikan fungsi renderView sudah di-load di index.php
        if (function_exists('renderView')) {
            try {
                renderView('errors.error_503', [
                    'title'   => 'Maintenance - Database Error',
                    'message' => (env('SERVER_MODE') === 'DEVELOPMENT') ? $exception->getMessage() : 'Kami sedang melakukan pemeliharaan database.'
                ]);
                exit;
            } catch (Exception $renderError) {
                // Jika renderView gagal (misal folder cache bermasalah), tampilkan pesan teks biasa
                self::fallbackErrorOutput($exception->getMessage());
            }
        }

        // 5. Hard Fallback jika renderView tidak tersedia
        self::fallbackErrorOutput($exception->getMessage());
    }

    /**
     * Output teks sederhana jika sistem view gagal total.
     */
    private static function fallbackErrorOutput($msg) 
    {
        echo "<h1>Database Connection Error</h1>";
        if (env('SERVER_MODE') === 'DEVELOPMENT') {
            echo "<p>Error: " . htmlspecialchars($msg) . "</p>";
        } else {
            echo "<p>Maaf, layanan sedang tidak tersedia. Silakan coba beberapa saat lagi.</p>";
        }
        exit;
    }

    /**
     * Mengambil Instance (Singleton)
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Mengambil Koneksi PDO langsung
     */
    public static function getConnection() 
    {
        // Memastikan instance dibuat melalui getInstance()
        return self::getInstance()->pdo;
    }
}