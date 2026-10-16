<?php

/**
 * Base Controller & Static Runner
 * Jembatan antara Controller gaya lama dan gaya baru.
 */
abstract class Controller
{
    private static $oldPath = '';
    private static $newPath = '';

    /**
     * Inisialisasi lokasi folder controller
     * @param string $new Folder untuk controller OOP (Application/Controllers)
     * @param string $old Folder untuk controller lama (controller/)
     */
    public static function init(string $new, string $old)
    {
        self::$newPath = rtrim($new, '/') . '/';
        self::$oldPath = rtrim($old, '/') . '/';
    }

    /**
     * Executor tunggal untuk menjalankan controller
     * @param string $path Path ke file controller (misal: Auth/LoginController)
     * @param array $params Parameter pendukung (berisi 'method', id, dll)
     */
    public static function run($handler, array $params = [])
    {
        $className = '';
        $method = '';

        // 1. Deteksi format Laravel: [ClassName::class, 'method']
        if (is_array($handler)) {
            $className = $handler[0];
            $method = $handler[1];
        } else {
            // 2. Format String lama: 'CDC/InternshipController'
            $method = $params['method'] ?? 'index';
            $className = 'Application\\Controllers\\' . str_replace('/', '\\', $handler);
        }

        // Eksekusi jika Class ada (OOP)
        if (class_exists($className)) {
            $instance = new $className();
            
            if (method_exists($instance, $method)) {
                // Gabungkan params dari router ke dalam method
                return $instance->$method($params);
            }
            die("Error: Method <b>{$method}</b> tidak ditemukan di kelas <b>{$className}</b>");
        }

        // Fallback Gaya Lama (Procedural) - Hanya jika handler adalah string
        if (!is_array($handler)) {
            $oldFile = self::$oldPath . $handler . '.php';
            if (file_exists($oldFile)) {
                extract($params); 
                return require $oldFile;
            }
        }

        die("Error: Controller tidak ditemukan untuk: " . (is_array($handler) ? $className : $handler));
    }

    /* --- Common Helpers --- */
    protected function view($path, $data = []) { return renderView($path, $data); }
    protected function json($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}