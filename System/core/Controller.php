<?php

abstract class Controller
{
    private static $oldPath = '';
    private static $newPath = '';

    protected $params = [];

    public static function init(string $new, string $old)
    {
        self::$newPath = rtrim($new, '/') . '/';
        self::$oldPath = rtrim($old, '/') . '/';
    }

    public static function run($handler, array $params = [])
    {
        $className = '';
        $method = '';

        if (is_array($handler)) {
            $className = $handler[0];
            $method = $handler[1];
        } else {
            $method = $params['method'] ?? 'index';
            $formattedPath = str_replace('/', '\\', $handler);
            $className = 'Application\\Controllers\\' . $formattedPath;
        }

        // A. CEK & EKSEKUSI CONTROLLER OOP (GAYA BARU)
        $oopFile = (defined('BASE_DIR') ? BASE_DIR : '') . '/Application/Controllers/' . str_replace('\\', '/', str_replace('Application\\Controllers\\', '', $className)) . '.php';

        if (file_exists($oopFile)) {
            try {
                require_once $oopFile;
            } catch (\Throwable $t) {
                self::showError("Syntax / Fatal Error di File Controller OOP", $t->getMessage(), $oopFile, $t->getLine());
            }

            if (class_exists($className)) {
                $instance = new $className();
                
                if (property_exists($instance, 'params')) {
                    $instance->params = $params;
                }

                if (method_exists($instance, $method)) {
                    try {
                        return $instance->$method($params);
                    } catch (\Throwable $t) {
                        self::showError("Runtime Error saat Eksekusi Method <b>{$method}()</b>", $t->getMessage(), $t->getFile(), $t->getLine());
                    }
                }

                self::showError("Method Not Found", "Method <b>{$method}()</b> tidak ditemukan di dalam kelas <b>{$className}</b>.", $oopFile);
            }
        }

        // B. FALLBACK CONTROLLER PROCEDURAL (GAYA LAMA)
        if (!is_array($handler)) {
            $oldFile = self::$oldPath . $handler . '.php';

            if (file_exists($oldFile)) {
                try {
                    extract($params);
                    if (!isset($method)) {
                        $method = $params['method'] ?? 'index';
                    }
                    return require $oldFile;
                } catch (\Throwable $t) {
                    self::showError("Error pada Controller Lama (Procedural)", $t->getMessage(), $oldFile, $t->getLine());
                }
            }
        }

        // C. JIKA CONTROLLER TIDAK DITEMUKAN
        $targetName = is_array($handler) ? $className : $handler;
        self::showError("Controller Not Found", "File Controller tidak ditemukan di folder OOP maupun Folder Lama.<br>Target: <b>{$targetName}</b>");
    }

    /**
     * Tampilan Debugger Error (Hanya Aktif di Mode DEVELOPMENT)
     */
    private static function showError($title, $message, $file = '', $line = '')
    {
        $serverMode = defined('SERVER_MODE') ? SERVER_MODE : (env('SERVER_MODE') ?? 'PRODUCTION');

        // Jika BUKAN mode DEVELOPMENT, sembunyikan detail error merah!
        if (strtoupper($serverMode) !== 'DEVELOPMENT') {
            if (class_exists('Router') && method_exists('Router', 'handleError')) {
                Router::handleError(500, "Internal Server Error");
            } else {
                http_response_code(500);
                echo "<h1>500 Internal Server Error</h1>";
            }
            exit;
        }

        // Jika Mode DEVELOPMENT -> Tampilkan kotak debugger merah
        if (!headers_sent()) {
            http_response_code(500);
        }

        echo "
        <div style='background: #fff0f0; border: 2px solid #ff4d4d; color: #900; padding: 20px; font-family: monospace; border-radius: 8px; margin: 20px;'>
            <h3 style='margin-top:0; color: #c00;'>⚠️ [Controller Runner Error] {$title}</h3>
            <p style='font-size: 15px;'>{$message}</p>";
            
        if ($file) {
            echo "<p style='font-size: 13px; color: #555; border-top: 1px solid #ffccd5; pt-2;'><b>File:</b> {$file} " . ($line ? "on line <b>{$line}</b>" : "") . "</p>";
        }

        echo "
            <hr style='border:0; border-top: 1px solid #ffccd5;'>
            <small style='color: #888;'>Framework Debugger • SERVER_MODE = DEVELOPMENT</small>
        </div>";
        exit;
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