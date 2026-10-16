<?php
error_reporting(true); 

// error_reporting(false); // Ubah ke E_ALL saat development
define('BASE_DIR', __DIR__);

date_default_timezone_set('Asia/Jakarta');

use GeniusTS\HijriDate\Hijri;
use Jenssegers\Blade\Blade;

// Auto Load Vendor
require_once('./vendor/autoload.php'); 

// 6. Setup Blade Engine
$viewsDir = BASE_DIR . '/views';
$cacheDir = BASE_DIR . '/storage/views/';
$blade = new Blade($viewsDir, $cacheDir);

// --- Custom Directives ---
$blade->directive('env', function ($expression) {
    return "<?php echo env($expression); ?>";
});

// Update: Pakai Session::get() agar konsisten
$blade->directive('csrf', function () {
    return "<?php echo '<input type=\"hidden\" name=\"token\" value=\"' . Session::get('token') . '\" />'; ?>";
});

$blade->directive('method', function ($expression) {
    return "<?php echo '<input type=\"hidden\" name=\"_method\" value=\"' . $expression . '\">'; ?>";
});


/**
 * Fungsi untuk merender tampilan menggunakan Blade.
 *
 * @param string $views Nama view dalam notasi dot (misalnya, 'home.index').
 * @param array $data Data yang akan dikirimkan ke tampilan (default kosong).
 * @param callable ...$customActions Fungsi tambahan yang bisa dijalankan sebelum rendering (opsional).
 */
function renderView($views, $data = [], callable ...$customActions)
{
    // convert dot notation to path
    $viewPath = str_replace('.', DIRECTORY_SEPARATOR, $views);
    $baseViewPath = __DIR__ . "/views/";
    $fullViewPath = $baseViewPath . $viewPath . '.blade.php';

    // run custom actions if any
    if (!empty($customActions)) {
        foreach ($customActions as $action) {
            if (is_callable($action)) {
                call_user_func($action);
            }
        }
    }

    // JSON fallback helper
    $jsonFallback = function($statusCode, $payload) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_PRETTY_PRINT);
        exit;
    };

    // base error info (can be expanded later)
    $errorInfo = [
        'time' => date('Y-m-d H:i:s'),
        'view_requested' => $views,
        'path' => $fullViewPath,
        'get' => $_GET,
        'post' => $_POST,
        'session' => isset($_SESSION) ? $_SESSION : [],
        'server' => $_SERVER,
        'input' => file_get_contents('php://input')
    ];

    $exceptionCaught = false;
    $caughtException = null;

    // 1) Try to render original view if exists
    if (file_exists($fullViewPath)) {

        try {
            global $blade;
            echo $blade->render($views, $data);
            return; // SUCCESS — stop here
        } catch (Exception $e) {
            // store the exception for later debug
            $exceptionCaught = true;
            $caughtException = $e;
            $errorInfo['render_error'] = $e->getMessage();
        }

    } else {
        // View file not found
        $exceptionCaught = true;
        $errorInfo['render_error'] = 'view_file_not_found';
    }

    // If exception was caught, collect file/line/snippet if exists
    if ($exceptionCaught && $caughtException instanceof Exception) {
        $errorInfo['file'] = $caughtException->getFile();
        $errorInfo['line'] = $caughtException->getLine();
        $errorInfo['snippet'] = getCodeSnippet(
            $caughtException->getFile(),
            $caughtException->getLine()
        );
    }

    // Determine environment
    $isProduction = false;
    try {
        $isProduction = (env('SERVER_MODE') === 'PRODUCTION');
    } catch (Exception $e) {
        $isProduction = false;
    }

    // PRODUCTION mode
    if ($isProduction) {
        $prodDebugView = __DIR__ . '/views/errors/error_404.blade.php';

        if (file_exists($prodDebugView)) {
            try {
                global $blade;
                echo $blade->render('errors.error_404', []);
                return;
            } catch (Exception $e) {
                http_response_code(404);
                echo "404 - Not Found";
                return;
            }
        }

        // Fallback JSON
        $jsonFallback(404, ['error' => '404 - Not Found']);
    }

    // DEVELOPMENT mode: use error_debug view
    $debugViewName = 'errors.error_debug';
    $debugViewPath = __DIR__ . '/views/errors/error_debug.blade.php';

    if (file_exists($debugViewPath)) {
        try {
            global $blade;
            $error = array_merge(['code' => 500, 'message' => 'View/render error'], $errorInfo);

            echo $blade->render($debugViewName, ['error' => $error]);
            return;

        } catch (Exception $e) {
            $errorInfo['debug_render_error'] = $e->getMessage();
        }
    }

    // If debug template fails: JSON DEBUG OUTPUT
    $jsonFallback(500, [
        'status' => 'error',
        'message' => 'View rendering failed and debug view not available',
        'details' => $errorInfo
    ]);
}



// -----------------------------------------------------------
// 1. LOAD HELPERS & GLOBAL FUNCTIONS DULU (Best Practice)
// -----------------------------------------------------------
// Alasannya: Kadang Core Class butuh fungsi helper (misal: env(), dd(), url())
// saat mereka diinisialisasi. Lebih aman load ini di awal.
require_once __DIR__ . "/System/core/Expression.php";
require_once __DIR__ . "/System/GlobalFunctions.php"; // Pindahkan ke atas

// Load Helpers
require_once __DIR__ . "/System/helper/Arr.php";
require_once __DIR__ . "/System/helper/Date.php";
require_once __DIR__ . "/System/helper/File.php";
require_once __DIR__ . "/System/helper/Num.php";
require_once __DIR__ . "/System/helper/Str.php";
require_once __DIR__ . "/System/helper/Url.php";
require_once __DIR__ . "/System/helper/Redirect.php";
require_once __DIR__ . "/System/helper/Hashid.php";

// -----------------------------------------------------------
// 2. LOAD CORE COMPONENTS
// -----------------------------------------------------------
require_once __DIR__ . "/System/core/Env.php";

// Load Environment Variable SEBELUM Database Logic
Env::load(); 

require_once __DIR__ . "/System/core/Router.php";
require_once __DIR__ . "/System/core/FileViewer.php";
require_once __DIR__ . "/System/core/Logger.php";
require_once __DIR__ . "/System/core/MimeTypes.php";
require_once __DIR__ . "/System/core/Session.php";
require_once __DIR__ . "/System/core/Controller.php";

require_once __DIR__ . "/System/core/Notification.php";
require_once __DIR__ . "/System/core/Validator.php";

// Database Stack (Urutan Wajib: Database -> QueryBuilder -> Model)
require_once __DIR__ . "/System/core/Database.php";
require_once __DIR__ . "/System/core/DB.php";
require_once __DIR__ . "/System/core/QueryBuilder.php";
require_once __DIR__ . "/System/core/Model.php";
require_once __DIR__ . "/System/core/Request.php";
require_once __DIR__ . "/System/core/Response.php";

require_once __DIR__ . "/System/core/Hash.php";
require_once __DIR__ . "/System/core/Encrypter.php";

Encrypter::init( Env::get('APP_KEY', 'MyInternship12379?!') );

// -----------------------------------------------------------
// 2.5 DATABASE PRE-FLIGHT CHECK (TAMBAHKAN DI SINI)
// -----------------------------------------------------------
// Memaksa inisialisasi koneksi di awal. 
// Jika gagal, handleDatabaseDown() di dalam class Database akan memutus eksekusi.
Database::getConnection();

// -----------------------------------------------------------
// 3. LOAD USER MODELS
// -----------------------------------------------------------
spl_autoload_register(function ($className) {
    // 1. Tentukan Folder Utama Model
    $baseDir = __DIR__ . '/Application/Models/';
    
    // 2. Cek di Root Folder Model dulu (Paling sering dipakai)
    $file = $baseDir . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    // 3. Jika tidak ada, cari di SEMUA Sub-folder (Recursive)
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        // Cek apakah file php dan namanya sesuai dengan Class
        if ($file->isFile() && $file->getExtension() === 'php') {
            // Bandingkan nama file (tanpa .php) dengan nama Class
            if ($file->getBasename('.php') === $className) {
                require_once $file->getPathname();
                return;
            }
        }
    }
});


spl_autoload_register(function ($className) {
    // 1. Tentukan Folder Utama
    $baseDir = __DIR__ . '/Application/Helpers/';
    
    // Ambil nama kelas murni tanpa namespace (misal: Application\Helpers\Student\StudentSidebarCounter -> StudentSidebarCounter)
    $classBasename = basename(str_replace('\\', '/', $className));

    // 2. Cek di Root Folder Helper dulu
    $file = $baseDir . $classBasename . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    // 3. Jika menggunakan struktur Namespace PSR-4 (Application/Helpers/Student/StudentSidebarCounter.php)
    $relativePath = ltrim($className, '\\');
    if (strpos($relativePath, 'Application\\Helpers\\') === 0) {
        $relativePath = substr($relativePath, strlen('Application\\Helpers\\'));
        $psr4File = $baseDir . str_replace('\\', '/', $relativePath) . '.php';
        if (file_exists($psr4File)) {
            require_once $psr4File;
            return;
        }
    }

    // 4. Jika tidak ada, cari di SEMUA Sub-folder (Recursive Fallback)
    if (is_dir($baseDir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $fileInfo) {
            // PERBAIKAN: Gunakan $fileInfo (bukan $file)
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                if ($fileInfo->getBasename('.php') === $classBasename) {
                    require_once $fileInfo->getPathname();
                    return;
                }
            }
        }
    }
});


// -----------------------------------------------------------
// 3.1 LOAD USER TRAIT
// -----------------------------------------------------------
spl_autoload_register(function ($className) {
    // 1. Tentukan Folder Utama Model
    $baseDir = __DIR__ . '/System/trait/';
    
    // 2. Cek di Root Folder Model dulu (Paling sering dipakai)
    $file = $baseDir . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    // 3. Jika tidak ada, cari di SEMUA Sub-folder (Recursive)
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        // Cek apakah file php dan namanya sesuai dengan Class
        if ($file->isFile() && $file->getExtension() === 'php') {
            // Bandingkan nama file (tanpa .php) dengan nama Class
            if ($file->getBasename('.php') === $className) {
                require_once $file->getPathname();
                return;
            }
        }
    }
});

// -----------------------------------------------------------
// 3.2 LOAD USER CONTROLLERS (Autoload untuk Inheritance)
// -----------------------------------------------------------
spl_autoload_register(function ($className) {
    // className akan berisi seperti: Application\Controllers\CDC\InternshipController
    
    // 1. Ubah backslash (\) menjadi forward slash (/)
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    
    // 2. Gabungkan dengan BASE_DIR
    $file = __DIR__ . DIRECTORY_SEPARATOR . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
// -----------------------------------------------------------
// 4. SYSTEM INIT
// -----------------------------------------------------------

// Inisiasi Session
Session::init('file');

// Set lokasi folder
Controller::init(
    BASE_DIR . '/Application/Controllers', // Lokasi Baru
    BASE_DIR . '/controller'               // Lokasi Lama
);

// Generate Session Token
if (!Session::has('_token')) {
    $token = bin2hex(random_bytes(32));
    Session::set('_token', $token);
    Session::set('token', $token); 
}

Notification::init();

// -----------------------------------------------------------
// MAINTENANCE MODE LOGIC (ENV + DB)
// -----------------------------------------------------------
$isMaintEnv = (env('MAINTENANCE_MODE') === 'true');
$dbMaint    = Maintenance::getActiveScheduled();

if ($isMaintEnv || $dbMaint != null ) {
    
    // Cek rute pengecualian menggunakan fungsi match yang baru
    $isExcludedRoute = (
        Url::match('auth/login/cdc') || 
        Url::match('auth/process/cdc') || 
        Url::match('cdc') || 
        Url::match('cdc/*')
    );

    // Jika sedang akses login CDC, abaikan maintenance
    if ($isExcludedRoute) {
        // Biarkan lanjut ke router
    } else {
        // Cek admin atau bypass IP
        $isAdmin = (Session::get('role') === 'UPKPK');
        $currentIp = Request::ip();
        $allowedIps = array_unique(array_merge(
            explode(',', env('MAINTENANCE_BYPASS_IPS', '')),
            $dbMaint ? explode(',', $dbMaint->allowed_ips) : []
        ));

        if (!$isAdmin && !in_array($currentIp, $allowedIps)) {
            http_response_code(503);
            renderView('errors.error_maintenance', [
                'title' => $isMaintEnv ? 'Emergency Maintenance' : $dbMaint->title,
                'end'   => $dbMaint ? date('d M Y, H:i', strtotime($dbMaint->end_date)) : 'Unspecified Time'
            ]);
            exit;
        }
    }
}

// -----------------------------------------------------------
// 5. LOAD ROUTES
// -----------------------------------------------------------
require_once __DIR__ . "/routes/router_redirect.php";
require_once __DIR__ . "/routes/router_web.php";
require_once __DIR__ . "/routes/router_api.php";
require_once __DIR__ . "/routes/router_viewer.php";
require_once __DIR__ . "/routes/router_ajax.php";
require_once __DIR__ . "/routes/router_manual.php";


// 5. MAINTENANCE: Pembersihan Excel (OPTIMIZED)
// Hanya jalan dengan peluang 2% (1 banding 50) agar tidak memberatkan server tiap request
if (rand(1, 50) === 1) {
    $folder = 'excel/generate/';
    // Cek dulu foldernya ada gak, biar gak error warning
    if (is_dir($folder)) {
        $expired_seconds = 24 * 60 * 60; // 1 hari
        foreach (glob($folder . '*.xlsx') as $file) {
            if (filemtime($file) < (time() - $expired_seconds)) {
                @unlink($file); // Pakai @ untuk suppress error jika file sedang dikunci sistem
            }
        }
    }
}


/**
 * Function abort() untuk menghentikan proses
 */
function abort($code = 500, $message = null)
{
    // Bersihkan output buffer jika ada
    if (ob_get_length()) {
        ob_end_clean();
    }

    // Set HTTP status code header
    http_response_code($code);

    // Cek AJAX request
    $isAjax = (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    );

    // ===============================
    // JIKA AJAX → RETURN JSON
    // ===============================
    if ($isAjax) {

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'error'   => true,
            'code'    => $code,
            'message' => $message ?: defaultAbortMessage($code)
        ]);

        exit();
    }

    // ===============================
    // JIKA BUKAN AJAX → TAMPILKAN VIEW
    // ===============================

    $views = [
        400 => 'errors.error_400',
        401 => 'errors.error_401',
        402 => 'errors.error_402',
        403 => 'errors.error_403',
        404 => 'errors.error_404',
        405 => 'errors.error_405',
        406 => 'errors.error_406',
        407 => 'errors.error_407',
        408 => 'errors.error_408',
        409 => 'errors.error_409',
        410 => 'errors.error_410',
        411 => 'errors.error_411',
        412 => 'errors.error_412',
        413 => 'errors.error_413',
        414 => 'errors.error_414',
        415 => 'errors.error_415',
        416 => 'errors.error_416',
        417 => 'errors.error_417',
        418 => 'errors.error_418',
        419 => 'errors.error_419',
        422 => 'errors.error_422',
        425 => 'errors.error_425',
        426 => 'errors.error_426',
        428 => 'errors.error_428',
        429 => 'errors.error_429',
        431 => 'errors.error_431',

        // SERVER ERRORS
        500 => 'errors.error_500',
        501 => 'errors.error_501',
        502 => 'errors.error_502',
        503 => 'errors.error_503',
        504 => 'errors.error_504',
        505 => 'errors.error_505',
    ];

    $view = isset($views[$code]) ? $views[$code] : null;

    if ($view !== null) {

        renderView($view, [
            'code'    => $code,
            'message' => $message ?: defaultAbortMessage($code)
        ]);

        exit();
    }

    // ======================
    // FALLBACK TANPA VIEW
    // ======================
    echo $message ?: defaultAbortMessage($code);
    exit();
}



/**
 * Default message lengkap untuk error 400 - 500 (versi ditingkatkan)
 */
function defaultAbortMessage($code) {
    $messages = [

        // --- CLIENT ERRORS (400 - 499) ---
        400 => "The request could not be understood by the server.",
        401 => "Please log in with a valid account or check your permission level.",
        402 => "Payment is required to access this resource.",
        403 => "You do not have permission to access this resource.",
        404 => "The page or resource you are looking for could not be found.",
        405 => "The HTTP method you used is not allowed for this endpoint.",
        406 => "The requested resource is not available in a suitable format.",
        407 => "Proxy authentication is required to proceed.",
        408 => "The request took too long and the server timed out.",
        409 => "There is a conflict with the current state of the resource.",
        410 => "This resource is no longer available and has been permanently removed.",
        411 => "A valid Content-Length header is required.",
        412 => "A precondition for this request has failed.",
        413 => "The submitted data is too large for the server to process.",
        414 => "The requested URL is too long for the server to interpret.",
        415 => "The server does not support the media type submitted.",
        416 => "The requested content range cannot be fulfilled.",
        417 => "The server cannot meet the expectation set by the request.",
        418 => "The server refuses to brew coffee because it is a teapot.",
        422 => "The server cannot process the contained instructions.",
        425 => "The server is unwilling to risk processing this request now.",
        426 => "The client must upgrade to a different protocol to continue.",
        428 => "A required precondition is missing in the request.",
        429 => "Too many requests — please slow down and try again later.",
        431 => "The request headers are too large for the server to process.",


        // --- SERVER ERRORS (500 - 599) ---
        500 => "An unexpected server error occurred.",
        501 => "The server does not support this functionality.",
        502 => "The server received an invalid response from an upstream service.",
        503 => "The server is currently unavailable or under maintenance.",
        504 => "The server timed out while waiting for a response.",
        505 => "The HTTP version used in this request is not supported.",
    ];

    return isset($messages[$code])
        ? $messages[$code]
        : "Error processing your request.";
}




function getCodeSnippet($file, $line, $padding = 7)
{
    if (!file_exists($file)) {
        return "File not found";
    }

    $lines = file($file);
    $start = max($line - $padding - 1, 0);
    $end   = min($line + $padding - 1, count($lines) - 1);

    $snippet = "";

    for ($i = $start; $i <= $end; $i++) {
        $currentLine = $i + 1;
        $prefix = ($currentLine == $line) ? ">> " : "   ";
        $snippet .= 
            $prefix . 
            str_pad($currentLine, 4, ' ', STR_PAD_LEFT) . 
            " | " . 
            $lines[$i];
    }

    return $snippet;
}



/**
 * Fungsi untuk menentukan apakah request dari ajax.
 */
function is_ajax_request()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
		strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

## Cron JOB / Schedule Task

// Global storage untuk menyimpan daftar cron yang didaftarkan
$GLOBALS['cron_tasks'] = [];


/**
 * Daftarkan cron job dengan parameter fleksibel.
 * 
 * Fungsi ini digunakan untuk menambahkan tugas cron dengan pengaturan yang fleksibel terkait dengan jadwal eksekusi,
 * batas eksekusi, interval, waktu tertentu dalam sehari, dan hari tertentu dalam seminggu atau sebulan.
 * 
 * @param string $taskName   Nama tugas cron (misalnya: 'MyInternshipStatsReport')
 * @param string $taskPath   Path ke file yang akan dieksekusi (misalnya: 'MyInternshipStat')
 * @param string $schedule   Jadwal eksekusi tugas cron ('everyMinute', 'hourly', 'daily', 'weekly', 'monthly')
 * @param int    $limit      Batas jumlah eksekusi tugas dalam periode tertentu (misalnya: 1 untuk sekali)
 * @param int    $interval   Interval waktu dalam detik yang membatasi eksekusi ulang tugas (misalnya: 3600 untuk 1 jam)
 * @param array  $times      Waktu tertentu dalam format 'HH:MM' (misalnya: ['07:00', '12:00']) untuk tugas yang berulang pada waktu tertentu dalam sehari (digunakan untuk 'daily', 'weekly', dan 'monthly')
 * @param array  $days       Hari tertentu yang dapat berisi nama hari dalam seminggu atau tanggal dalam sebulan (misalnya: ['Monday', 'Friday'] untuk weekly atau ['01', '15'] untuk monthly)
 */
function schedule_task($taskName, $taskPath, $schedule = 'everyMinute', $limit = 1, $interval = 60, $times = [], $days = [])
{
	
    $GLOBALS['cron_tasks'][] = [
        'taskName' => $taskName,
        'taskPath' => $taskPath,
        'schedule' => $schedule,
        'limit'    => $limit,
        'interval' => $interval,
        'times'    => $times, // Menambahkan waktu khusus (jam) yang diinginkan
        'days'     => $days,  // Menambahkan hari tertentu (misalnya: Senin, Jumat, atau tanggal tertentu dalam sebulan)
    ];
}


/**
 * Fungsi untuk membatasi metode HTTP yang diizinkan dalam suatu request.
 *
 * @param string $method_type Jenis metode yang diizinkan (default, ALL, POST, GET, DEL, PUT).
 */
function allowed_method($method_type = 'default')
{
    switch ($method_type) {
        default:
            // Jika metode yang digunakan tidak tersedia, kembalikan respons JSON dengan HTTP 405.
            echo json_encode([
                'welcome_message' => $_SERVER['API_WELCOME'],
                'status' => 0,
                'message' => 'Method not available !'
            ], JSON_PRETTY_PRINT);
            http_response_code(405);
            exit;
            break;

        case 'ALL':
        case 'ANY':
            // Jika 'ALL' atau 'ANY', maka semua metode HTTP diizinkan.
            break;

        case 'POST':
            // Memeriksa apakah request menggunakan metode POST.
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                // Periksa apakah request berasal dari AJAX.
                if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                    if (strtolower(env('SERVER_MODE')) == 'production') {
                        // Jika mode produksi, tampilkan halaman error 405.
                        renderView('errors.error_405', ['title' => '405 - Method Not Allowed', 'method' => 'POST']);
                    } else {
                        // Jika mode pengembangan, tampilkan error 404.
                        renderView('errors.error_404', ['title' => '404 - Page Not Found']);
                    }
                } else {
                    // Jika request berasal dari AJAX, kirim respons JSON dengan error 405.
                    response_json(405, ['error_code' => 405, 'status' => 0, 'message' => 'Only POST Method is allowed !']);
                }
                die;
            }
            break;

        case 'GET':
            // Memeriksa apakah request menggunakan metode GET.
            if ($_SERVER['REQUEST_METHOD'] != 'GET') {
                if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                    if (strtolower(env('SERVER_MODE')) == 'production') {
                        renderView('errors.error_405', ['title' => '405 - Method Not Allowed', 'method' => 'GET or HEAD']);
                    } else {
                        renderView('errors.error_404', ['title' => '404 - Page Not Found']);
                    }
                } else {
                    response_json(405, ['error_code' => 405, 'status' => 0, 'message' => 'Only GET Method is allowed !']);
                }
                die;
            }
            break;

        case 'DEL':
            // Memeriksa apakah request menggunakan metode DELETE (dikirim sebagai POST dengan parameter 'DEL').
            if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['DEL'])) {
                if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                    if (strtolower(env('SERVER_MODE')) == 'production') {
                        renderView('errors.error_405', ['title' => '405 - Method Not Allowed', 'method' => "DELETE"]);
                    } else {
                        renderView('errors.error_404', ['title' => '404 - Page Not Found']);
                    }
                } else {
                    response_json(405, ['error_code' => 405, 'status' => 0, 'message' => 'Only DELETE Method is allowed !']);
                }
                die;
            }
            break;

        case 'PUT':
            // Memeriksa apakah request menggunakan metode PUT (dikirim sebagai POST dengan parameter 'PUT').
            if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['PUT'])) {
                if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                    if (strtolower(env('SERVER_MODE')) == 'production') {
                        renderView('errors.error_405', ['title' => '405 - Method Not Allowed', 'method' => 'PUT']);
                    } else {
                        renderView('errors.error_404', ['title' => '404 - Page Not Found']);
                    }
                } else {
                    response_json(405, ['error_code' => 405, 'status' => 0, 'message' => 'Only PUT Method is allowed !']);
                }
                die;
            }
            break;
    }
}



// Tentukan path file log
$logFilePath = __DIR__ . '/logs/polibatam_api-log.txt';
$backupDir = __DIR__ . '/logs/backup/';

// Cek jika direktori backup belum ada, maka buat direktori tersebut
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// Fungsi untuk mendapatkan log terakhir
function getLastLogStatus($logFilePath) {
    if (!file_exists($logFilePath)) {
        return null;
    }
    
    $lines = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false || count($lines) === 0) {
        return null;
    }
    
    $lastLine = end($lines);
    
    if (strpos($lastLine, 'gagal') !== false) {
        return false; // API gagal
    } elseif (strpos($lastLine, 'berhasil') !== false) {
        return true; // API berhasil
    }
    
    return null; // Tidak ditemukan status yang valid
}


if (!isset($_GET['api'])) {
	// Cek status log terakhir dan set nilai $_SERVER['API_PROBLEM']
	$lastLogStatus = getLastLogStatus($logFilePath);
	$_SERVER['API_PROBLEM'] = ($lastLogStatus === false) ? true : false;
}



// Fungsi untuk mendapatkan timestamp dari log terakhir
function getLastLogTimestamp($logFilePath)
{
	if (!file_exists($logFilePath)) {
		return null;
	}

	$lines = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if ($lines === false || count($lines) === 0) {
		return null;
	}

	$lastLine = end($lines);
	$timestamp = substr($lastLine, 0, 19); // Ambil bagian timestamp dari format log

	return DateTime::createFromFormat('Y-m-d H:i:s', $timestamp);
}




/**
* Note: This file may contain artifacts of previous malicious infection.
* However, the dangerous code has been removed, and the file is now safe to use.
*/


function clearOldCache($cacheDir, $currentDate)
{
	// Loop melalui semua direktori di dalam $cacheDir
	foreach (glob("$cacheDir/*", GLOB_ONLYDIR) as $dir) {
		// Ambil nama direktori (yang adalah tanggal)
		$dirName = basename($dir);

		// Cek apakah nama direktori adalah tanggal yang valid dan bukan tanggal hari ini
		if (strtotime($dirName) && $dirName !== $currentDate) {
			// Hapus semua file dalam direktori yang lama
			foreach (glob("$dir/*") as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}

			// Hapus direktori jika kosong
			if (count(glob("$dir/*")) === 0) {
				rmdir($dir);
			}
		}
	}
}

$currentCookieParams = session_get_cookie_params();
$sidvalue = session_id();
setcookie('PHPSESSID', $sidvalue, 0, $currentCookieParams['path'], $currentCookieParams['domain'], true);
header("Content-Type: text/html; charset=utf-8");
header("X-Content-Type-Options: nosniff;");
$url = $_SERVER['REQUEST_URI'];
$break = explode('/', $url);
if (!defined('APP_KEY')) {
	define('APP_KEY', openssl_random_pseudo_bytes(32));
}
if (!defined('SERVER_MODE')) {
	define('SERVER_MODE', env('SERVER_MODE') ?? 'DEVELOPMENT');
}

$_SESSION['SERVER_MODE'] = SERVER_MODE;
if (env('SERVER_MODE') === NULL) {
	writeEnv('SERVER_MODE', SERVER_MODE);
}
if (env('APP_KEY') === NULL) {
	writeEnv('APP_KEY', APP_KEY);
}

include "database/config.php";
include "database/database_connection.php";


if ($_SERVER['ON_MAINTENANCE']) {
	if (!$_SESSION['ADMIN_MAINTENANCE']) {
		view('exception/maintenance');
		exit;
	}
}

// if (isset($_SERVER['SECURE_ACCESS'])) {
// 	if ($_SERVER['SECURE_ACCESS'] === TRUE) {
// 		if (stripos($_SERVER['HTTP_HOST'], 'localhost') && $_SERVER['REQUEST_SCHEME'] != 'https') {
// 			view('exception/secure_access');
// 			exit;
// 		}
// 	}
// }

function failed_login()
{
    $_SESSION['failed_login']['multiplier'] = isset($_SESSION['failed_login']['multiplier']) ? $_SESSION['failed_login']['multiplier']  + 1 : 1;

    // Increment the failed login attempts and update the last failed login time
    $_SESSION['failed_login']['attempts'] = isset($_SESSION['failed_login']['attempts']) ? $_SESSION['failed_login']['attempts']  + 1 : 1;
    $_SESSION['failed_login']['last_login_time'] = time();


    $remaining_attempt = env('FAILED_ATTEMPT') - $_SESSION['failed_login']['attempts'] ?? env('FAILED_ATTEMPT') ?? 5;

    return true;
}

function generateUUIDv4()
{
	// Menghasilkan 16 byte random
	$data = random_bytes(16);

	// Set versi ke 0100
	$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
	// Set varian ke 10xxxxxx
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

	// Formatkan UUID
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


function isRepeatedRequest()
{
	// Check if the client sent an ETag or Last-Modified header
	if (isset($_SERVER['HTTP_IF_NONE_MATCH']) || isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
		return true;
	}
	return false;
}


function throttle($page, $action = 'index', $maxAttempts = 2, $decayAmount = 120, $decayUnit = 'minutes')
{


	$key = 'throttle_' . $page . '_' . $action;

	// Get the current attempts count, last attempt timestamp, first access time, page, and action from session
	$attempts = ($_SESSION[$key]['attempts'] ?? 0);
	$lastAttempt = ($_SESSION[$key]['last_attempt'] ?? null);
	$firstAccess = ($_SESSION[$key]['first_access'] ?? null);
	$currentPage = ($_SESSION[$key]['page'] ?? null);
	$currentAction = ($_SESSION[$key]['action'] ?? null);

	// Calculate the time elapsed since the last attempt
	$currentTime = time();
	$timeElapsed = $lastAttempt ? ($currentTime - $lastAttempt) : ($decayAmount * getTimeMultiplier($decayUnit));

	// Reset attempts count if the decay time has passed
	if ($timeElapsed >= $decayAmount) {
		$attempts = 0;
	}

	// Check if the attempts exceed the maximum allowed
	if ($attempts >= $maxAttempts) {
		if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'XMLHttpRequest') {
			renderView('errors.error_429', ['title' => '429 - Too Many Requests']);
		} else {
			response_json(404, [
				'error_code' => 429,
				'status' => 0, 'message' => 'Too Many Requests'
			]);
		}
		die; // Throttle limit exceeded
	}

	// Update attempts count and last attempt timestamp
	$_SESSION[$key]['attempts'] = $attempts + 1;
	$_SESSION[$key]['last_attempt'] = $currentTime;

	// Update page and action accessed by user if it's the first time accessing this page and action
	if (!$firstAccess) {
		$_SESSION[$key]['first_access'] = $currentTime;
	}
	if (!$currentPage) {
		$_SESSION[$key]['page'] = $page;
	}
	if (!$currentAction) {
		$_SESSION[$key]['action'] = $action;
	}

	return true; // Allowed to proceed
}

function getTimeMultiplier($unit = 'seconds')
{
	$multipliers = [
		'seconds' => 1,
		'minutes' => 60,
		'hours' => 3600,
		'days' => 86400
		// Add more units if needed
	];

	return $multipliers[$unit] ?? 1;
}

function getIndonesianDayName($dayNumber)
{
	$dayNames = [
		'Ahad',
		'Isnaini',
		'Tsulaatsaai',
		'Arbi’aai',
		'Khomiis',
		'Jumu’ati',
		'Sabtu'
	];

	return $dayNames[$dayNumber];
}

// Fungsi untuk mendapatkan tanggal hijriah dan nama hari dalam bahasa Indonesia
function getHijriDateWithDayName()
{
	// Ambil tanggal hari ini
	$todayGregorianDate = date('Y-m-d');

	// Konversi tanggal Gregorian ke Hijriah menggunakan library HijriDate
	$hijriDate = Hijri::convertToHijri($todayGregorianDate);

	// Formatkan tanggal hijriah sesuai kebutuhan Anda (misalnya: "d M Y")
	$formattedHijriDate = $hijriDate->format('d M Y');

	// Dapatkan angka hari (0 untuk Minggu, 1 untuk Senin, dst.)
	$dayNumber = $hijriDate->format('w');

	// Dapatkan nama hari dalam bahasa Indonesia berdasarkan angka hari
	$dayName = getIndonesianDayName($dayNumber);

	return $dayName . ', ' . $formattedHijriDate . 'H';
}



function whenAjaxRequest(callable ...$customActions)
{
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {


		if (!empty($customActions)) {
			foreach ($customActions as $action) {
				if (is_callable($action)) {
					call_user_func($action);
				}
			}
		}

		return null;
	}

	return null;
}


function debug_only(bool $bool = true)
{

	if ($bool) {
		if ($_SESSION['FLOGIN'] !== TRUE) {
			view('exception/error_404');
			die;
		}
	}
}



function getDataTableData(string $sql = "SELECT * FROM tb_customer", callable ...$customActions)
{

	global $orm;

	$PDO = $_SERVER['PDO'];

	$draw = isset($_POST['draw']) ? $_POST['draw'] : 1;

	$start = isset($_POST['start']) ? $_POST['start'] : 1;
	$length = isset($_POST['length']) ? $_POST['length'] : 10;
	$search_val = isset($_POST['search']) && $_POST['search']['value'] != '' ? $_POST['search']['value'] : '';

	$result = $PDO->query($sql)->fetchAll(PDO::FETCH_ASSOC);

	$column_order = array(); // Inisialisasi array untuk menyimpan nama-nama kolom

	if ($result) {
		// Mendapatkan nama-nama kolom dari baris pertama
		$column_order = array_keys($result[0]);
	}

	$where = '';
	if (!empty($search_val)) {
		$search_conditions = array();
		foreach ($column_order as $column) {
			$search_conditions[] = "$column LIKE '%$search_val%'";
		}
		$where .= " WHERE " . implode(' OR ', $search_conditions);
	}

	$sql .= $where;

	$countStmt = $PDO->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$total_records = count($countStmt);

	$total_records = intval($total_records);

	$order = '';
	if (isset($_POST['order'])) {
		$order_column_index = $_POST['order'][0]['column'];
		$order_dir = $_POST['order'][0]['dir'];
		$order_column = $column_order[$order_column_index];
		$order = " ORDER BY $order_column $order_dir";
	}

	$sql .= $order;

	$limit = " LIMIT $start, $length";
	$sql .= $limit;

	$stmt = $PDO->query($sql);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$data = array();

	foreach ($result as $row) {

		array_push($data, $row);

		$lastIndex = count($data) - 1;

		if (!empty($customActions)) {
			foreach ($customActions as $action) {
				if (is_callable($action)) {
					$new_custom = $action($row);

					$data[$lastIndex][array_key_first($new_custom)] = $new_custom[array_key_first($new_custom)];
				}
			}
		} else {
			// Kode aksi default
			$data[$lastIndex]['aksi'] = '<button class="btn btn-primary btn-sm" onclick="editRow(\'' . encrypt($row['id']) . '\')">Edit</button> <button class="btn btn-danger btn-sm btn-delete" data-id="' . encrypt($row['id']) . '">Hapus</button>';
		}
	}


	$response = array(
		'draw' => intval($draw),
		'recordsTotal' => intval($total_records),
		'recordsFiltered' => intval($total_records),
		'data' => $data,
	);

	return array('result' => $result, 'data' => $data, 'total_records' => $total_records, 'draw' => $draw, 'response' => $response);
}


function getDataTableData2($sql = "SELECT * FROM tb_customer", callable ...$customActions)
{

	$koneksi = $_SERVER['KONEKSI_DB'];

	$id_company = $_SESSION['id_company'];

	$sql =     "SELECT * FROM offer WHERE id_company = '$id_company'";

	$start = isset($_POST['start']) ? $_POST['start'] : 1;
	$length = isset($_POST['length']) ? $_POST['length'] : 10;
	$total_records = mysqli_num_rows(mysqli_query($koneksi, $sql));


	$id_company = $_SESSION['id_company'];


	$column_order = array('id', 'name', 'email', 'phone', 'city', 'country');
	$search = "";
	$search_query = "";
	if (isset($_POST['search']) && $_POST['search']['value'] != '') {
		$search_val = $_POST['search']['value'];
		$where = " WHERE ( name LIKE '%$search_val%' OR email LIKE '%$search_val%' OR phone LIKE '%$search_val%' OR city LIKE '%$search_val%' OR country LIKE '%$search_val%' )";
		$sql .= $where;
	}

	$order = ' ';
	if (isset($_POST['order'])) {
		$order_column_index = $_POST['order'][0]['column'];
		$order_dir = $_POST['order'][0]['dir'];
		$order_column = $column_order[$order_column_index];

		$order .= " ORDER BY $order_column $order_dir";
	}

	$sql .= " $order LIMIT $start, $length";


	$result = mysqli_query($koneksi, $sql);


	$sql_count = "SELECT COUNT(*) AS count FROM offer WHERE id_company = '$id_company'";
	if (isset($_POST['search']) && $_POST['search']['value'] != '') {
		$search_val = $_POST['search']['value'];
		$sql_count .= $where;
	}
	$result_count = mysqli_query($koneksi, $sql_count);
	$row_count = mysqli_fetch_assoc($result_count)['count'];


	$data = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$data[] = array("id" => $row['id'], "name" => $row['name'], "email" => $row['email'], "phone" => $row['phone'], "city" => $row['city'], "country" => $row['country'], "aksi" => '<button class="btn btn-primary btn-sm" onclick="editRow(\'' . encrypt($row['id']) . '\')">Edit</button> <button class="btn btn-danger btn-sm btn-delete" data-id="' . encrypt($row['id']) . '">Hapus</button>', 'draw' => intval($_POST['draw']), 'search_val' => $search_val);
	}


	$output = array('welcome_message' => $_SERVER['API_WELCOME'], "draw" => intval($_POST['draw']), "recordsTotal" => intval($total_records), "recordsFiltered" => intval($row_count), "sql" => $sql, "data" => $data);

	response_json(200, $output);
	// 	$conn->close();
}


function escape_val($val = '')
{
	$escape = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $val);
	return $escape;
}


function escape_string($string = '')
{
	$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
	$replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");
	return str_replace($search, $replace, $string);
}


function response_json(int $http_code = 200, array $data = [], array $headers = [])
{

	$default = array(
		'welcome_message' => $_SERVER['API_WELCOME'],
		'additional_welcome_message' => $_SERVER['API_ADDITIONAL_WELCOME'],
		'timestamp' => date('Y-m-d H:i:s'),
	);

	$new_data = array_merge($default, $data);

	http_response_code($http_code);

	if ($headers = []) {
		header('Content-type: application/json');
	} else {
		foreach ($headers as $key => $value) {
			header($key . ' : ' . $value);
		}
	}

	dump_to_file('api_log', " Status Code :  $http_code " . ' ; ' . getBrowser() . ' ; \n' . 'Payload : ' . $data);

	echo json_encode($new_data, JSON_PRETTY_PRINT);
	exit;
}




function createCustomJWT($payload)
{
	$secret = APP_KEY ?? 'aaaa';


	$header_data = array('alg' => 'HS256', 'typ' => 'JWT', 'created_at' => time());

	$header = base64_encode(json_encode($header_data));
	$payload = base64_encode(json_encode($payload));
	$signature = hash_hmac('sha256', "$header.$payload", $secret, true);
	$signature = base64_encode($signature);
	return "$header.$payload.$signature";
}



function verifyCustomJWT($jwt, $secret = APP_KEY)
{
	list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);
	$header = json_decode(base64_decode($headerEncoded));
	$payload = json_decode(base64_decode($payloadEncoded));
	$signature = base64_decode($signatureEncoded);
	$expectedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
	return hash_equals($signature, $expectedSignature);
}



function JWTCustomData($payload = '')
{

	$payload = json_decode(base64_decode(explode('.', $payload)[1]), true);
	return $payload;
}





if (!function_exists('createJWT')) {
	function createJWT($payload)
	{
		var_dump(APP_KEY);
		exit;

		$secret = APP_KEY ?? 'aaaa';


		$header_data = array('alg' => 'HS256', 'typ' => 'JWT', 'created_at' => time());

		$header = base64_encode(json_encode($header_data));
		$payload = base64_encode(json_encode($payload));
		set_userdata($payload);
		$signature = hash_hmac('sha256', "$header.$payload", $secret, true);
		$signature = base64_encode($signature);
		return "$header.$payload.$signature";
	}
}

if (!function_exists('verifyJWT')) {

	function verifyJWT($jwt, $secret = APP_KEY)
	{
		list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);
		$header = json_decode(base64_decode($headerEncoded));
		$payload = json_decode(base64_decode($payloadEncoded));
		$signature = base64_decode($signatureEncoded);
		$expectedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
		return hash_equals($signature, $expectedSignature);
	}
}

if (!function_exists('getJWT')) {

	function getJWT($object = false)
	{
		if (!verifyJWT($_COOKIE['X-SESS'])) {
			return false;
		} else {

			if ($object) {
				$payload = json_decode(base64_decode(explode('.', $_COOKIE['X-SESS'])[1]));
			} else {
				$payload = json_decode(base64_decode(explode('.', $_COOKIE['X-SESS'])[1]), true);
			}
			return $payload;
		}
	}
}


if (!function_exists('JWTdata')) {

	function JWTdata($name = '', $object = false)
	{
		if (!verifyJWT($_COOKIE[$name])) {
			return [];
		} else {

			if ($object) {
				$payload = json_decode(base64_decode(explode('.', $_COOKIE[$name])[1]));
			} else {
				$payload = json_decode(base64_decode(explode('.', $_COOKIE[$name])[1]), true);
			}
			return $payload;
		}
	}
}


if (!function_exists('addJWT')) {

	function addJWT($new_data)
	{

		if (!verifyJWT($_COOKIE['X-SESS'])) {
			return null;
		} else {

			$currentCookieParams = session_get_cookie_params();

			$data = getJWT();
			$timeout = $dataJWT['timeout'] ?? $_SERVER['TIMEOUT'];

			$timeout_time = time() + $timeout;

			$data = array_merge($data, $new_data, ['last_connection' => time(), 'timeout' => $timeout, 'timeout_time' => $timeout_time]);

			// unsetJWT();

			$jwt = createJWT($data);

			createJWT($data);

			set_userdata($data);

			setcookie('X-SESS', $jwt, $timeout_time, $currentCookieParams['path'], $currentCookieParams['domain'], true, true);

			return true;
		}
	}
}


if (!function_exists('unsetJWT')) {

	function unsetJWT()
	{

		$_COOKIE['X-SESS'] = null;

		$currentCookieParams = session_get_cookie_params();

		unset_userdata();

		setcookie('X-SESS', '', time() - 3600, $currentCookieParams['path'], $currentCookieParams['domain'], true, true);

		return true;
	}
}


if (!function_exists('verifikasiJWT')) {

	function verifikasiJWT()
	{
		verifyJWT($_COOKIE['X-SESS']);
	}
}

if (!function_exists('unset_userdata')) {

	function unset_userdata(string $nama = '')
	{

		if ($nama == '') {
			unset($_SESSION['USERDATA']);
		} else {
			unset($_SESSION['USERDATA'][$nama]);
		}
	}
}


function writeEnv($key, $value)
{
    if (!isset($_SERVER['ENV']['LOAD']) || $_SERVER['ENV']['LOAD'] !== true) {
        loadEnv();
    }

    $_SERVER['ENV']['DATA'][$key] = $value;

    $envPath = __DIR__ . '/.env';
    $handle  = fopen($envPath, 'w');

    foreach ($_SERVER['ENV']['DATA'] as $k => $v) {
        fwrite($handle, $k . '="' . $v . '"' . PHP_EOL);
    }

    fclose($handle);
    return true;
}



function loadEnv()
{
    if (!isset($_SERVER['ENV'])) {
        $_SERVER['ENV'] = [
            'LOAD' => false,
            'DATA' => [],
        ];
    }

    if ($_SERVER['ENV']['LOAD'] === true) {
        return true;
    }

    $envPath = __DIR__ . '/.env';
    if (!file_exists($envPath)) {
        return false;
    }

    $envFile = file_get_contents($envPath);
    $lines   = preg_split("/\r\n|\n|\r/", $envFile);

    foreach ($lines as $line) {
        $line = trim($line);

        // skip empty & comment
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        // must contain "="
        if (strpos($line, '=') === false) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // strip quotes (PHP 7 safe)
        if (
            (substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
        ) {
            $value = substr($value, 1, -1);
        }

        putenv($key . '=' . $value);
        $_SERVER['ENV']['DATA'][$key] = $value;
    }

    $_SERVER['ENV']['LOAD'] = true;
    return true;
}





function env($key, $default = null)
{
    // pastikan env sudah di-load
    if (!isset($_SERVER['ENV']['LOAD']) || $_SERVER['ENV']['LOAD'] !== true) {
        loadEnv();
    }

    if (array_key_exists($key, $_SERVER['ENV']['DATA'])) {
        return $_SERVER['ENV']['DATA'][$key];
    }

    // fallback TERKONTROL (bukan liar)
    $fallback = getloadEnv($key);

    return $fallback !== null ? $fallback : $default;
}




function encrypt($data)
{
	$key = env('APP_KEY') ?? APP_KEY;
	$key = substr(hash('sha256', $key), 0, 32);
	$iv = openssl_random_pseudo_bytes(16);
	$encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
	return base64_encode($iv . $encrypted);
}
function decrypt($data)
{
	$key = env('APP_KEY') ?? APP_KEY;
	$key = substr(hash('sha256', $key), 0, 32);
	$data = base64_decode($data);
	$iv = substr($data, 0, 16);
	$encrypted = substr($data, 16);
	return openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}
function route(string $nama_controller = "", $method_type = 'POST', array $data = [])
{
	error_reporting(false);
	$currentCookieParams = session_get_cookie_params();
	$sidvalue = session_id();
	setcookie('PHPSESSID', $sidvalue, 0, $currentCookieParams['path'], $currentCookieParams['domain'], true);
	switch ($method_type) {
		default:
			echo json_encode(['welcome_message' => $_SERVER['API_WELCOME'], 'status' => 0, 'message' => 'Method not available !'], JSON_PRETTY_PRINT);
			http_response_code(405);
			exit;
			break;
		case 'ALL':
			break;
		case 'POST':
			if ($_SERVER['REQUEST_METHOD'] != 'POST') {
				echo json_encode(['welcome_message' => $_SERVER['API_WELCOME'], 'status' => 0, 'message' => 'Only POST Method is supported !'], JSON_PRETTY_PRINT);
				http_response_code(405);
				exit;
			}
			break;
		case 'GET':
			if ($_SERVER['REQUEST_METHOD'] != 'GET') {
				echo json_encode(['welcome_message' => $_SERVER['API_WELCOME'], 'status' => 0, 'message' => 'Only GET Method is supported !'], JSON_PRETTY_PRINT);
				http_response_code(405);
				exit;
			}
			break;
		case 'DEL':
			if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
			} else {
				if (isset($_POST['_method']) && $_POST['_method'] == 'DELETE') {
				} else if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['DEL'])) {
					echo json_encode(['welcome_message' => $_SERVER['API_WELCOME'], 'status' => 0, 'message' => 'Only DELETE Method is supported !'], JSON_PRETTY_PRINT);
					http_response_code(405);
					exit;
				}
			}
			break;
		case 'PUT':
			if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
			} else {
				if (isset($_POST['_method']) && $_POST['_method'] == 'PUT') {
				} else if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['PUT'])) {
					echo json_encode(['welcome_message' => $_SERVER['API_WELCOME'], 'status' => 0, 'message' => 'Only PUT Method is supported !'], JSON_PRETTY_PRINT);
					http_response_code(405);
					exit;
				}
			}
			break;
	}
	if (!in_array($method_type, ['GET', 'ALL'])) {
		if (cek_token() === false) {
			http_response_code(419);
			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'XMLHttpRequest') {
				view('exception/error_419');
				exit;
			} else {
				echo json_encode(['welcome_message' => $_SERVER['API_WELCOME'], 'status' => 0, 'message' => 'Invalid request !'], JSON_PRETTY_PRINT);
				exit();
			}
		}
	}
	if (!is_file("./controller/" . $nama_controller . ".php")) {
		http_response_code(404);
		if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'XMLHttpRequest') {
			view('exception/error_404');
			exit;
		} else {
			echo json_encode(['welcome_message' => $_SERVER['API_WELCOME'], 'status' => 0, 'message' => 'Resources is not available !'], JSON_PRETTY_PRINT);
			exit();
		}
	} else {

		$db = $_SERVER['KONEKSI_DB'];
		$DB = $_SERVER['KONEKSI_DB'];

		$pdo = $_SERVER['PDO'];
		$PDO = $_SERVER['PDO'];

		if (count($data) > 0) {
			foreach ($data as $first => $second) {
				${$first} = $second;
			}
		}

		include "./controller/" . $nama_controller . ".php";
	}
}


function ff_own_grp(string $lokasi, string $owner, string $jenis = "both")
{
	if ($jenis == "group" || $jenis == "grp") {
		chgrp($lokasi, $owner);
	} else if ($jenis == "owner" || $jenis == "own") {
		chown($lokasi, $owner);
	} else {
		chgrp($lokasi, $owner);
		chown($lokasi, $owner);
	}
}
function ff_permission(string $lokasi, string $permission_mod = '0655')
{
	if (!is_dir($lokasi)) {
		mkdir($lokasi, $permission_mod, true);
	} else {
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($lokasi));
		foreach ($iterator as $item) {
			chmod($item, $permission_mod);
		}
		chmod($lokasi, $permission_mod);
	}
}
function back(int $previous = 1, $timer = 1)
{
	echo "<script> window.setTimeout(function(){ window.location.replace = history.go(-$previous); }, $timer); </script>";
}
function close(int $timer = 2000)
{
	echo "<script> window.setTimeout(function(){ window.close(); }, $timer);</script>";
}
function persiapan_upload(int $maks_upload = 3, string $lokasi = "")
{
	$lokasi_upload = "upload/" . $lokasi;
	$maksimal_upload = $maks_upload * pow(1024, 2);
	foreach ($_FILES as $name => $value) {
		if ($_FILES[$name]['name'] == "") {
			error($name, 'Wajib upload file !');
			$cek_form = false;
		}
		if ($_FILES[$name]['size'] > $maksimal_upload) {
			error($name, 'Maksimal ukuran file ' . $maks_upload . ' MB !');
			$cek_form = false;
		}
	}
	if ($lokasi != "") {
		if (!is_dir($lokasi_upload)) {
			mkdir($lokasi, 0775, true);
		}
	}
	if ($cek_form === false) {
		return false;
	} else {
		return true;
	}
}
function dev_mode(string $jenis = "")
{
	if (stripos($jenis, 'GET') !== FALSE) {
		highlight_string("<?php \$_GET : \r\n  " . var_export($_GET, true) . " \r\n \r\n ");
	} else if (stripos($jenis, 'FILES') !== FALSE) {
		highlight_string("<?php \$_FILES : \r\n  " . var_export($_FILES, true) . " \r\n \r\n ");
	} else if (stripos($jenis, 'POST') !== FALSE) {
		highlight_string("<?php \$_POST : \r\n " . var_export($_POST, true) . "\r\n \r\n ");
	} else if (stripos($jenis, 'ERROR') !== FALSE) {
		highlight_string("<?php \LAST_ERROR : \r\n " . var_export(error_get_last(), true) . "\r\n \r\n \r\n");
	} else {
		highlight_string("<?php GET : \r\n  " . var_export($_GET, true) . " \r\n \r\n ");
		highlight_string("<?php POST | \$_POST : \r\n " . var_export($_POST, true) . " \r\n \r\n \r\n");
		highlight_string("<?php FILES | \$_FILES : \r\n  " . var_export($_FILES, true) . " \r\n \r\n \r\n");
		highlight_string("<?php SESSION | \$_SESSION : \r\n  " . var_export($_SESSION, true) . " \r\n \r\n \r\n");
		highlight_string("<?php PHP LAST_ERROR | LAST_ERROR : \r\n " . var_export(error_get_last(), true) . "\r\n \r\n \r\n");
		highlight_string("<?php ENV | \$_ENV : \r\n  " . var_export($_ENV, true) . " \r\n \r\n \r\n");
		highlight_string("<?php SERVER | \$_SERVER : \r\n  " . var_export($_SERVER, true) . " \r\n \r\n \r\n");
		highlight_string("<?php ALL | GET ALL VAR DEFINED BY USER : \r\n  " . var_export(get_defined_vars(), true) . " \r\n \r\n \r\n");
	}
}
function dev_db(object $koneksi_db, string $mode = "ERROR")
{
	if ($mode == "ERROR") {
		return var_dump(mysqli_error($koneksi_db));
	} else {
		return var_dump($koneksi_db);
	}
}
function hapus_upload(string $lokasi, string $nama_file)
{
	$lokasi_file = "upload/" . $lokasi . "/" . $nama_file;
	chmod($lokasi_file, 0655);
	$hapus_file = unlink($lokasi_file);
	if ($hapus_file === TRUE) {
		return true;
	} else {
		return false;
	}
}
function upload(string $lokasi, string $nama_input, string $nama_file = 'random', int $maksimal_upload = 3)
{
	$name = substr(escape(hash("sha512", random_strings(10)) . "." . pathinfo($_FILES[$nama_input]['name'], PATHINFO_EXTENSION)), 100, 32);
	$temp = $_FILES[$nama_input]['tmp_name'];
	$folder = $lokasi;
	if (!is_dir($folder)) {
		if (mkdir($folder) === false) {
			error($nama_input, "Gagal Upload File mkdir !");
			return false;
		}
		chmod($folder, 0755);
	}
	success($nama_input);
	if (move_uploaded_file($temp, $folder . $name) === TRUE) {
		success($nama_input);
		return $name;
	} else {
		error($nama_input, 'File gagal di upload uploaded file !');
		error($nama_input, $folder . $name);
		return false;
	}
}
function csrf()
{
	$token = $_SESSION['token'];
	echo '<input type="hidden" name="token" value="' . $token . '" />';
}
function dev()
{
	echo '<input type="hidden" name="DEVELOPMENT" value="DEVELOPMENT" />';
}
function input(string $nama_input = '', $value_input = '', array $attributes = [])
{
	$input = '';
	$input .= '<input type="';
	$input .= (array_key_exists('type', $attributes)) ? $attributes['type'] : 'text';
	$input .= '"';
	$input .= 'id="' . (array_key_exists('id', $attributes)) ? $attributes['id'] : $nama_input;
	'"';
	$input .= 'name="' . $nama_input . '"';
	$input .= 'value="' . $value_input ?? '' . '"';
	$input .= 'class="' . (array_key_exists('class', $attributes)) ? $attributes['class'] : 'form-control';
	' ' . (array_key_exists('form_validation', $attributes)) ? show_error($nama_input) : '';
	'"';
	$input .= '/>';
	$input .= "Heloow world !";
	return $input;
}
function input_hidden(string $nama_input = '', $value_input = '', string $id_input = '')
{
	if ($id_input == '') {
		$id_input = $nama_input;
	} else {
		$id_input = $id_input;
	}
	echo '<input type="hidden" id="' . $id_input . '" name="' . $nama_input . '" value="' . $value_input . '" />';
}

function middleware(string $lokasi = "", array $data = [], callable ...$customActions)
{
	if (count($data) > 0) {
		foreach ($data as $first => $second) {
			${$first} = $second;
		}
	}

	$DB = $_SERVER['DB'];
	$db = $_SERVER['DB'];

	$PDO = $_SERVER['PDO'];
	$pdo = $_SERVER['PDO'];

	global $orm;

	if (!is_file("./middleware/" . $lokasi . ".php")) {
		include "./view/exception/error_middleware.php";
		exit;
	} else {

		if (!empty($customActions)) {
			foreach ($customActions as $action) {
				if (is_callable($action)) {
					call_user_func($action);
				}
			}
		}


		include "./middleware/" . $lokasi . ".php";
	}
}

function view(string $lokasi = "", array $data = [], callable ...$customActions)
{
	if (count($data) > 0) {
		foreach ($data as $first => $second) {
			${$first} = $second;
		}
	}

	$DB = $_SERVER['DB'];
	$db = $_SERVER['DB'];

	$PDO = $_SERVER['PDO'];
	$pdo = $_SERVER['PDO'];

	global $orm;

	if (!is_file("./view/" . $lokasi . ".php")) {
		include "./view/exception/error_view.php";
	} else {

		if (!empty($customActions)) {
			foreach ($customActions as $action) {
				if (is_callable($action)) {
					call_user_func($action);
				}
			}
		}


		include "./view/" . $lokasi . ".php";
	}
}

function controller(string $nama_controller = "", array $data = [], callable ...$customActions)
{

	if (count($data) > 0) {
		foreach ($data as $first => $second) {
			${$first} = $second;
		}
	}


	$DB = $_SERVER['DB'];
	$db = $_SERVER['DB'];

	$PDO = $_SERVER['PDO'];
	$pdo = $_SERVER['PDO'];

	global $orm;

    $baseControllerpath = __DIR__ . "/controller/" . $nama_controller . ".php"; // Sesuaikan dengan path ke direktori views Anda.
	if (!is_file( $baseControllerpath )) {

		if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
			echo json_encode(['welcome_message' => $_SERVER['API_WELCOME'], 'status' => 0, 'message' => 'Invalid request !'], JSON_PRETTY_PRINT);
			exit();
		} else {
			return include "./view/exception/error_controller.php";
		}
	} else {

		if (!empty($customActions)) {
			foreach ($customActions as $action) {
				if (is_callable($action)) {
					call_user_func($action);
				}
			}
		}

		return include "./controller/" . $nama_controller . ".php";
	}
}
function ajax(string $nama_ajax = "")
{


	$DB = $_SERVER['DB'];
	$db = $_SERVER['DB'];

	$PDO = $_SERVER['PDO'];
	$pdo = $_SERVER['PDO'];

	global $orm;


	if (!is_file("./ajax/" . $nama_ajax . ".php")) {
		return include "./view/exception/error_controller.php";
	} else {
		return include "./ajax/" . $nama_ajax . ".php";
	}
}
function cek_login(string $role = "")
{
	if ($_SESSION['role'] != $role) {
		header("location:javascript://history.go(-1)");
		exit;
	}
}
function no_flash()
{
	return $_SESSION['no_flash'] = TRUE;
}
function flash_all()
{
	return $_SESSION['no_flash'] = FALSE;
}
function cek_token()
{
	if ($_POST['token'] != $_SESSION['token'] && $_POST['_token'] != $_SESSION['token']) {
		return false;
	} else {
		return true;
	}
}
function go_old(string $val = "")
{
	return $_SESSION['old'][$val] = $_SESSION['old'][$val];
}
function old(string $val = "", $new_val = "")
{
	go_old($val);
	if ($_SESSION['old'][$val] === NULL) {
		if ($new_val != '') {
			return $new_val;
		}
	} else {
		return $_SESSION['old'][$val];
	}
}
function cek_old($val, $new_val)
{
	if ($_SESSION['old'][$val] === NULL) {
		echo $new_val;
	} else {
		echo $_SESSION['old'][$val];
	}
}
function reflash(string $val = "")
{
	return $_SESSION[$val] = [];
}

function show_notif(string $nama_val = "")
{
	echo $_SESSION['notifikasi'][$nama_val];
}
function is_error(string $nama_val = "")
{
	return isset($_SESSION['error'][$nama_val]);
}
function make_error_notif(string $error = "")
{
	$_SESSION['notifikasi']['error'] = $error;
}
function is_notif()
{
	return isset($_SESSION['notifikasi']['error']);
}
function is_notif_show()
{
	echo $_SESSION['notifikasi']['error'];
}
function error(string $nama_val = "", string $error = "")
{
	$_SESSION['notifikasi'][$nama_val] = $error;
	$_SESSION['error'][$nama_val] = 'is-invalid border-danger';
}
function success(string $nama_val = "", string $notif = "")
{
	$_SESSION['notifikasi'][$nama_val] = $notif;
	$_SESSION['error'][$nama_val] = 'is-valid border-success';
}
function make_old(string $nama_val = "", $val = '')
{
	return $_SESSION['old'][$nama_val] = $val;
}
function to_old()
{
    // 1. Pastikan session sudah start (Opsional, defensive coding)
    session_start();

    // 2. Reset data old sebelumnya agar bersih
    $_SESSION['old'] = [];

    // 3. Simpan data POST
    if (!empty($_POST)) {
        foreach ($_POST as $name => $value) {
            // Simpan ke session.
            // Disarankan tidak melakukan sanitasi di sini (simpan raw data),
            // tapi sanitasi lah saat MENAMPILKAN di view (echo htmlspecialchars($val)).
            $_SESSION['old'][$name] = $value;
        }
    }

    // Catatan: $_FILES tidak diproses karena input file tidak bisa di-repopulate value-nya.
}
function random_strings(int $panjang_karakter = 10)
{
	$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	return substr(str_shuffle($str_result), 0, $panjang_karakter);
}
function print_error_database(object $koneksi_db, bool $bool = true)
{
	if ($bool === TRUE) {
		$file = "./logs/error_database.txt";
		if (!file_exists($file)) {
			$myfile = fopen($file, "w");
		}
		$current = file_get_contents($file);
		$current .= "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n" . "Error : " . $koneksi_db->connect_error . " \n";
		$current .= "Server : " . $_SERVER['SERVER_NAME'] . " \n\n";
		file_put_contents($file, $current);
		return true;
	} else {
		return true;
	}
}

function dump_to_file($nama = '', $isi = '')
{

	$file = "./logs/$nama.txt";

	$maxFileSize = 300000; // Batas ukuran file dalam byte (contoh: 1 MB)
	$backupDir = "./logs/backup/";

	if (file_exists($file) && filesize($file) > $maxFileSize) {
		$nama_baru = date("YmdHis") . "_" . $nama;
		$fileBaru = $backupDir . $nama_baru . ".txt";

		if (!file_exists($backupDir)) {
			mkdir($backupDir, 0755, true);
		}

		if (rename($file, $fileBaru)) {
		}
	}

	if (is_array($isi)) {
		$long_text = implode('/n', $isi);
	} else if (is_string($isi)) {
		$long_text = $isi;
	} else {
		$long_text = 'no_data';
	}


	if (!file_exists($file)) {
		$myfile = fopen($file, "w");
	}
	$current = file_get_contents($file);

	$current .= "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n";
	$current .= "Data : \n " . $long_text . " \n\n";
	file_put_contents($file, $current);
	return true;
}

function dump_error($isi = '')
{

	if (is_array($isi)) {
		$long_text = implode('/n', $isi);
	} else if (is_string($isi)) {
		$long_text = $isi;
	} else {
		$long_text = 'no_data';
	}


	$file = "./logs/dump_error.txt";
	if (!file_exists($file)) {
		$myfile = fopen($file, "w");
	}
	$current = file_get_contents($file);

	$current .= "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n";
	$current .= "Data : \n " . $long_text . " \n\n";
	file_put_contents($file, $current);
	return true;
}


function escape(string $value = "")
{
	$return = '';
	for ($i = 0; $i < strlen($value); ++$i) {
		$char = $value[$i];
		$ord = ord($char);
		if ($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126) $return .= $char;
		else $return .= '\\x' . dechex($ord);
	}
	return $return;
}
function getBrowser()
{
	$location = $_SERVER['REMOTE_ADDR'];
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$bname = 'Unknown';
	$platform = 'Unknown';
	$version = "";
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'linux';
	} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'mac';
	} elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'windows';
	}
	if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
		$bname = 'Internet Explorer';
		$ub = "MSIE";
	} elseif (preg_match('/Firefox/i', $u_agent)) {
		$bname = 'Mozilla Firefox';
		$ub = "Firefox";
	} elseif (preg_match('/OPR/i', $u_agent)) {
		$bname = 'Opera';
		$ub = "Opera";
	} elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
		$bname = 'Google Chrome';
		$ub = "Chrome";
	} elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
		$bname = 'Apple Safari';
		$ub = "Safari";
	} elseif (preg_match('/Netscape/i', $u_agent)) {
		$bname = 'Netscape';
		$ub = "Netscape";
	} elseif (preg_match('/Edge/i', $u_agent)) {
		$bname = 'Edge';
		$ub = "Edge";
	} elseif (preg_match('/Trident/i', $u_agent)) {
		$bname = 'Internet Explorer';
		$ub = "MSIE";
	}
	$known = array('Version', $ub, 'other');
	$pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if (!preg_match_all($pattern, $u_agent, $matches)) {
	}
	$i = count($matches['browser']);
	if ($i != 1) {
		if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
			$version = $matches['version'][0];
		} else {
			$version = $matches['version'][1];
		}
	} else {
		$version = $matches['version'][0];
	}
	if ($version == null || $version == "") {
		$version = "?";
	}
	return $u_agent . " " . $version . " di " . $platform . " pada " . $location;
}
function reflash_all()
{
	reflash('old');
	reflash('error');
	reflash('notifikasi');
	reflash('custom');
	return true;
}
function toast2(string $tipe = "error", string $judul = "")
{
	echo "

	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>

	
	<script type='text/javascript'>

		const Toast = swal.mixin({
			toast: true,
			position: 'top-right',
			timer: 2000,
			showConfirmButton: false,
			timerProgressBar: true,
		})
	setTimeout(function () { 

		Toast.fire({
			icon: '$tipe',
			title: '$judul'
		});

	},10); 


	</script>";
}


function redirect(string $page = "", int $timer = 1000)
{
    $base = rtrim(base_url(), '/');

    // 1. Default → kembali ke index
    if ($page === "") {
        echo "<script>
            window.setTimeout(function(){ window.location = '{$base}/index.php'; }, $timer);
        </script>";
        return;
    }

    // 2. "back"
    if (strtolower($page) === "back") {
        echo "<script>
            window.setTimeout(function(){ history.go(-1); }, $timer);
        </script>";
        return;
    }

    // 3. Jika URL dimulai dengan base_url → INTERNAL FULL URL
    if (strpos($page, $base) === 0) {
        echo "<script>
            window.setTimeout(function(){ window.location = '$page'; }, $timer);
        </script>";
        return;
    }

    // 4. Jika URL adalah http(s) full → EXTERNAL URL
    if (preg_match('#^https?://#i', $page)) {
        echo "<script>
            window.setTimeout(function(){ window.location = '$page'; }, $timer);
        </script>";
        return;
    }

    // 5. Jika diawali "/" → RELATIVE PATH (langsung redirect)
    if (substr($page, 0, 1) === "/") {
        $url = $base . $page;
        echo "<script>
            window.setTimeout(function(){ window.location = '$url'; }, $timer);
        </script>";
        return;
    }

    // 6. Jika bukan URL → route biasa
    echo "<script>
        window.setTimeout(function(){ window.location = '{$base}/index.php?page=$page'; }, $timer);
    </script>";
}

function redirect_back(int $timer = 1000, int $previous = 1)
{
	echo "<script> window.setTimeout( function(){ window.location.replace = history.go( -$previous ) ; }, $timer); </script>";
}
function redirect_referrer(int $timer = 1000, int $previous = 1)
{
	echo "<script> window.setTimeout( function(){ window.location = document.referrer; }, $timer); </script>";
}
function close_current_window(int $timer = 2000)
{
	echo "<script>window.setTimeout(function(){ window.close();}, $timer )</script>";
}
function active_nav_pill(string $nama_atribut, array $value_atribut)
{
	foreach ($value_atribut as $data_atribut) {
		$val_atribut = $data_atribut;
		$cek_atribut = $_GET[$nama_atribut];
		if ($cek_atribut == "") {
			$cek_atribut = "tata_cara";
		}
		if ($val_atribut == $cek_atribut) {
			echo "active";
		}
	}
}
function make_custom_notif(string $konteks = "", string $error = "")
{
	$_SESSION['custom'][$konteks] = $error;
}
function koma(string $value = "", string $rp = "")
{
	if ($value) {
		if ($rp == "") {
			$formated = str_replace(',', '.', number_format($value));
			return $formated;
		} else {
			$formated = 'Rp. ' . str_replace(',', '.', number_format($value)) . ",00";
			return $formated;
		}
	} else {
		return '-';
	}
}
function table_delete(string $table_name = "", string $attribute = "", string $value = "", string $mode = 'auto')
{
	$sql = "DELETE FROM `$table_name` WHERE `$attribute` = '$value' ";
	if ($mode != 'auto') {
		return $sql;
	}
	$table_delete = mysqli_query($_SERVER['KONEKSI_DB'], $sql);
	return $table_delete;
}
function table_delete2(string $table_name = "", $where_delete = '', string $mode = 'auto')
{
	if (is_array($where_delete)) {
		$arr_where = array_keys($where_delete);
		$where = "";
		foreach ($arr_where as $arr_where2) {
			$atr_where2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $arr_where2);
			$val_where2 = $where_delete[$arr_where2];
			if (array_key_last($where_delete) == $arr_where2) {
				$where .= "`$atr_where2` = '$val_where2' ";
			} else {
				$where .= "`$atr_where2` = '$val_where2' AND ";
			}
		}
	} else {
		$where = $where_delete;
	}
	$sql = "DELETE FROM `$table_name` WHERE $where ";
	if ($mode != "auto") {
		echo "QUERY : " . $sql;
		exit;
	}
	$table_delete = mysqli_query($_SERVER['KONEKSI_DB'], $sql);
	return $table_delete;
}


function table_insert(string $nama_tabel = "", $value_insert = '', string $mode = 'auto')
{
	$nama_tabel = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $nama_tabel);
	if (is_array($value_insert)) {
		$atr = array_keys($value_insert);
		$attribute = "";
		$value = "";
		foreach ($atr as $atr2) {
			$atr2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $atr2);
			if (array_key_last($value_insert) == $atr2) {
				$attribute .= "`$atr2`";

				if ($value_insert[$atr2] !== NULL) {
					$val2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $value_insert[$atr2] ?? '');
					$value .= "'$val2'";
				} else {
					$value .= "NULL";
				}
			} else {
				$attribute .= "`$atr2`" . ",";

				if ($value_insert[$atr2] !== NULL) {

					$val2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $value_insert[$atr2] ?? '');
					$val2 = $value_insert[$atr2];
					$value .= "'$val2'" . ",";
				} else {
					$value .= "NULL,";
				}
			}
		}
		$sql = "INSERT INTO `$nama_tabel` ($attribute) VALUES ($value);";
	} else {
		$value_insert = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $value_insert);
		$sql = "INSERT INTO `$nama_tabel` VALUES $value_insert";
	}
	if ($mode != 'auto') {
		echo "Query : " . $sql;
		exit;
	}

	$aql = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $sql);

	$table_insert = mysqli_query($_SERVER['KONEKSI_DB'], $sql);
	return $table_insert;
}

function table_insert2(string $nama_tabel = "", $value_insert = '', string $mode = 'auto')
{
	if (is_array($value_insert)) {
		$attributes = [];
		$placeholders = [];
		$bind_params = [];

		foreach ($value_insert as $attribute => $value) {
			// Sanitasi untuk mencegah SQL Injection
			$attribute = mysqli_real_escape_string($_SERVER['DB'], $attribute);

			// Sanitasi nilai untuk mencegah XSS (Cross-Site Scripting)
			if (is_string($value)) {
				$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			}

			// Tambahkan ke array attribut dan placeholder
			$attributes[] = "`$attribute`";

			if ($value !== NULL) {
				$placeholders[] = ":$attribute";
				$bind_params[$attribute] = $value;
			} else {
				$placeholders[] = "NULL";
			}
		}

		$sql = "INSERT INTO `$nama_tabel` (" . implode(", ", $attributes) . ") VALUES (" . implode(", ", $placeholders) . ");";
	} else {
		// Sanitasi untuk mencegah SQL Injection
		$value_insert = mysqli_real_escape_string($_SERVER['DB'], $value_insert);

		// Sanitasi nilai untuk mencegah XSS (Cross-Site Scripting)
		if (is_string($value_insert)) {
			$value_insert = htmlspecialchars($value_insert, ENT_QUOTES, 'UTF-8');
		}

		$sql = "INSERT INTO `$nama_tabel` VALUES $value_insert";
	}

	if ($mode != 'auto') {
		echo "Query : " . $sql;
		exit;
	}

	$table_insert = $_SERVER['PDO']->prepare($sql);

	try {
		$table_insert->execute($bind_params);

		// Jika ingin mengembalikan ID dari data yang baru saja dimasukkan
		// $inserted_id = $_SERVER['PDO']->lastInsertId();
		// return $inserted_id;

		// Jika Anda hanya ingin mengembalikan true jika berhasil
		return true;
	} catch (PDOException $e) {
		// Jika terjadi kesalahan, Anda bisa menampilkan pesan kesalahan atau melakukan logging
		// echo "Error: " . $e->getMessage();
		// Atau logging error
		// error_log("Error in table_insert2: " . $e->getMessage());
		return false;
	}
}



function table_update2(string $nama_tabel = "", array $value_update = [], $where_update = '', string $mode = 'auto')
{
	$bind_params = [];
	$set = "";

	foreach ($value_update as $attribute => $value) {
		// Sanitasi untuk mencegah SQL Injection
		$attribute = mysqli_real_escape_string($_SERVER['DB'], $attribute);

		// Sanitasi nilai untuk mencegah XSS (Cross-Site Scripting)
		if (is_string($value)) {
			$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		}

		if ($value !== NULL) {
			$set .= "`$attribute` = :$attribute, ";
			$bind_params[$attribute] = $value;
		} else {
			$set .= "`$attribute` = NULL, ";
		}
	}

	// Hapus koma terakhir dari bagian SET
	$set = rtrim($set, ', ');

	if (is_array($where_update)) {
		$where = "";
		foreach ($where_update as $attribute => $value) {
			// Sanitasi untuk mencegah SQL Injection
			// $attribute = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $attribute);
			// $value = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $value);

			// Tambahkan ke bagian WHERE dalam query UPDATE
			$where .= "`$attribute` = :$attribute AND ";


			if ($value !== NULL) {

				$where .= "`$attribute` = :$attribute, ";
				$bind_params[$attribute] = $value;
			} else {
				$where .= "`$attribute` = NULL, ";
			}
		}

		// Hapus "AND" terakhir dari bagian WHERE
		$where = rtrim($where, ' AND ');
		$where = rtrim($where, ', ');
	} else {
		// Sanitasi untuk mencegah SQL Injection
		$where_update = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $where_update);

		// Gunakan langsung dalam bagian WHERE
		$where = htmlspecialchars($where_update);
	}

	$sql = "UPDATE `$nama_tabel` SET $set WHERE $where";

	if ($mode != 'auto') {
		echo "Query : " . $sql;
		exit;
	}

	$table_update = $_SERVER['PDO']->prepare($sql);

	try {
		$table_update->execute($bind_params);

		// Add success handling here if necessary
		return true;
	} catch (PDOException $e) {
		make_old('DatabaseException', ['Exception' => $e, 'Message' => $e->getMessage()]);
		return false;
		// return $e->getMessage();
		die("Error: " . $e->getMessage());
	}
}

function table_update(string $nama_tabel = "", array $value_update = [], $where_update = '', string $mode = 'auto')
{
	$atr = array_keys($value_update);
	$set = "";
	foreach ($atr as $atr2) {
		if (array_key_last($value_update) == $atr2) {
			$atr2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $atr2 ?? '');

			$val2 = $value_update[$atr2];
			$val2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $val2 ?? '');

			$val2 = htmlspecialchars($val2);

			$set .= "`$atr2` = \"$val2\" ";
		} else {
			$atr2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $atr2 ?? '');

			$val2 = $value_update[$atr2];
			$val2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $val2 ?? '');

			$val2 = htmlspecialchars($val2);

			$set .= "`$atr2` = \"$val2\", ";
		}
	}
	if (is_array($where_update)) {
		$arr_where = array_keys($where_update);
		$where = "";
		foreach ($arr_where as $arr_where2) {
			if (array_key_last($where_update) == $arr_where2) {
				$atr_where2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $arr_where2 ?? '');
				$atr_where2 = htmlspecialchars($atr_where2);


				$val_where2 = $where_update[$arr_where2];
				$val_where2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $val_where2 ?? '');
				$val_where2 = htmlspecialchars($val_where2);

				$where .= "`$atr_where2` = \"$val_where2\" ";
			} else {
				$atr_where2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $arr_where2 ?? '');
				$atr_where2 = htmlspecialchars($atr_where2);

				$val_where2 = $where_update[$arr_where2];
				$val_where2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $val_where2 ?? '');
				$val_where2 = htmlspecialchars($val_where2);

				$where .= "`$atr_where2` = \"$val_where2\" AND ";
			}
		}
	} else {
		$where_update = $where_update;
		$where_update = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $where_update);
		$where_update = htmlspecialchars($where_update);

		$where = $where_update;
	}
	$sql = "UPDATE `$nama_tabel` SET $set WHERE $where";

	if ($mode != 'auto') {
		echo "Query :" . $sql;
		exit;
	}
	$table_update = mysqli_query($_SERVER['KONEKSI_DB'], $sql);
	return $table_update;
}


function table_search(string $nama_tabel = "", $where = '', string $mode = 'auto')
{
	if (is_array($where)) {
		$atr = array_keys($where);
		$where2 = "";
		foreach ($atr as $atr2) {
			$val2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $where[$atr2]);
			$atr2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $atr2);
			if (array_key_last($where) == $atr2) {
				if ($where[$atr2] !== NULL) {
					$where2 .= "$atr2 = '$val2'";
				} else {
					$val2 = 'NULL';
					$where2 .= "$atr2 = $val2";
				}
			} else {
				if ($where[$atr2] !== NULL) {
					$where2 .= "$atr2 = '$val2' AND";
				} else {
					$val2 = 'NULL';
					$where2 .= "$atr2 = $val2 AND";
				}
			}
		}
		if (count($where) < 1) {
			$sql = "SELECT * FROM `$nama_tabel` ;";
		} else {
			$sql = "SELECT * FROM `$nama_tabel` WHERE $where2 ;";
		}
	} else {
		if ($where == '') {
			$sql = "SELECT * FROM `$nama_tabel` ;";
		} else {
			$sql = "SELECT * FROM `$nama_tabel` WHERE $where ;";
		}
	}
	if ($mode != 'auto') {
		echo "Query : " . $sql;
		exit;
	}
	$table_search = mysqli_query($_SERVER['KONEKSI_DB'], $sql);
	return $table_search;
}


function set_rules(string $nama_atribut = '', string $panggilan_atribut = '', array $rules = [], array $custom_message = [], string $lang = 'ID')
{
	$_SESSION['set_rules'][$nama_atribut] = array('panggilan' => $panggilan_atribut, 'rules' => $rules, 'custom-message' => $custom_message, 'lang' => $lang,);
	return true;
}
function size_converter(int $bytes)
{
	if ($bytes >= 1073741824) {
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	} elseif ($bytes >= 1048576) {
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	} elseif ($bytes >= 1024) {
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	} elseif ($bytes > 1) {
		$bytes = $bytes . ' B';
	} elseif ($bytes == 1) {
		$bytes = $bytes . ' B';
	} else {
		$bytes = '0 B';
	}
	return $bytes;
}


function form_validation(int $with = 1)
{
	$_SESSION['error'] = [];
	$hasil_validasi = [];

	foreach ($_GET as $name => $value) {
		if ($_SESSION['set_rules'][$name] !== NULL) {
			foreach ($_SESSION['set_rules'][$name]['rules'] as $data_rule) {
				$rule_check = reset(explode(':', $data_rule));


				if ($rule_check == "allow_not_required") {
					$hasil_validasi[$name][] = input_allow_not_required($name);
				}
				if ($rule_check == "not_required") {
					$hasil_validasi[$name][] = input_not_required($name);
				}
				if ($rule_check == 'required') {
					$hasil_validasi[$name][] = input_required($name);
				}
				if ($rule_check == 'min_length') {
					$length = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_min_length($name, $length);
				}
				if ($rule_check == 'exact_length') {
					$length = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_exact_length($name, $length);
				}
				if ($rule_check == 'max_length') {
					$length = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_max($name, $length);
				}
				if ($rule_check == 'matches') {
					$new = end(explode(':', $data_rule));
					$second_variable = reset(explode('|', $new));
					$nama_attribute = end(explode('|', $new));
					$hasil_validasi[$name][] = input_matches($name, $new, $second_variable, $nama_attribute);
				}
				if ($rule_check == 'greater_than') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_greater_than($name, $comparison);
				}
				if ($rule_check == 'greater_than_equal_to') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_greater_than_equal_to($name, $comparison);
				}
				if ($rule_check == 'less_than') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_less_than($name, $comparison);
				}
				if ($rule_check == 'less_than_equal_to') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_less_than_equal_to($name, $comparison);
				}
				if ($rule_check == 'alpha') {
					$hasil_validasi[$name][] = input_alpha($name);
				}
				if ($rule_check == 'alpha_numeric') {
					$hasil_validasi[$name][] = input_alpha_numeric($name);
				}
				if ($rule_check == 'numeric') {
					$hasil_validasi[$name][] = input_numeric($name);
				}
				if ($rule_check == 'integer') {
					$hasil_validasi[$name][] = input_integer($name);
				}
				if ($rule_check == 'decimal') {
					$hasil_validasi[$name][] = input_decimal($name);
				}
				if ($rule_check == 'valid_phone') {
					$hasil_validasi[$name][] = input_valid_phone($name);
				}
				if ($rule_check == 'valid_email') {
					$hasil_validasi[$name][] = input_valid_email($name);
				}
				if ($rule_check == 'valid_url') {
					$hasil_validasi[$name][] = input_valid_url($name);
				}
				if ($rule_check == 'valid_time') {
					$hasil_validasi[$name][] = input_valid_time($name);
				}
				if ($rule_check == 'after_date') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_after_date($name, $comparison);
				}

				if ($rule_check == 'before_date') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_before_date($name, $comparison);
				}
				if ($rule_check == 'after_or_equal_date') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_after_or_equal_date($name, $comparison);
				}
				if ($rule_check == 'valid_date') {
					$hasil_validasi[$name][] = input_valid_date($name);
				}
				if ($rule_check == 'is_unique') {
					$nama_tabel = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_is_unique($name, $nama_tabel);
				}
				if ($rule_check == 'unique_except') {
					$new_data = end(explode(':', $data_rule));
					$except = reset(explode('|', $new_data));
					$nama_tabel = end(explode('|', $new_data));
					$hasil_validasi[$name][] = input_unique_except($name, $except, $nama_tabel);
				}
				if ($rule_check == 'except_unique') {
					$arr = end(explode(':', $data_rule));
					$previous_value = reset(explode('|', $arr));
					$nama_tabel = end(explode('|', $arr));
					$hasil_validasi[$name][] = input_except_unique($name, $nama_tabel, $previous_value);
				}
				if ($rule_check == 'exists') {
					$nama_tabel = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_exists($name, $nama_tabel);
				}
				if ($rule_check == 'regex_match') {
					$matches = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_regex_match($name, $matches);
				}
				if ($rule_check == 'allowed_values') {
					$allowed_values = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_allowed_values($name, $allowed_values);
				}
				if ($rule_check == 'exact_value') {
					$rule = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_exact_value($name, $rule);
				}
			}
		}
	}

	foreach ($_POST as $name => $value) {
		if ($_SESSION['set_rules'][$name] !== NULL) {
			foreach ($_SESSION['set_rules'][$name]['rules'] as $data_rule) {
				$rule_check = reset(explode(':', $data_rule));


				if ($rule_check == "allow_not_required") {
					$hasil_validasi[$name][] = input_allow_not_required($name);
				}
				if ($rule_check == "not_required") {
					$hasil_validasi[$name][] = input_not_required($name);
				}
				if ($rule_check == 'required') {
					$hasil_validasi[$name][] = input_required($name);
				}
				if ($rule_check == 'min_length') {
					$length = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_min_length($name, $length);
				}
				if ($rule_check == 'exact_length') {
					$length = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_exact_length($name, $length);
				}
				if ($rule_check == 'max_length') {
					$length = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_max($name, $length);
				}
				if ($rule_check == 'matches') {
					$new = end(explode(':', $data_rule));
					$second_variable = reset(explode('|', $new));
					$nama_attribute = end(explode('|', $new));
					$hasil_validasi[$name][] = input_matches($name, $new, $second_variable, $nama_attribute);
				}
				if ($rule_check == 'greater_than') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_greater_than($name, $comparison);
				}
				if ($rule_check == 'greater_than_equal_to') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_greater_than_equal_to($name, $comparison);
				}
				if ($rule_check == 'less_than') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_less_than($name, $comparison);
				}
				if ($rule_check == 'less_than_equal_to') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_less_than_equal_to($name, $comparison);
				}
				if ($rule_check == 'alpha') {
					$hasil_validasi[$name][] = input_alpha($name);
				}
				if ($rule_check == 'alpha_numeric') {
					$hasil_validasi[$name][] = input_alpha_numeric($name);
				}
				if ($rule_check == 'numeric') {
					$hasil_validasi[$name][] = input_numeric($name);
				}
				if ($rule_check == 'integer') {
					$hasil_validasi[$name][] = input_integer($name);
				}
				if ($rule_check == 'decimal') {
					$hasil_validasi[$name][] = input_decimal($name);
				}
				if ($rule_check == 'valid_phone') {
					$hasil_validasi[$name][] = input_valid_phone($name);
				}
				if ($rule_check == 'valid_email') {
					$hasil_validasi[$name][] = input_valid_email($name);
				}
				if ($rule_check == 'valid_url') {
					$hasil_validasi[$name][] = input_valid_url($name);
				}
				if ($rule_check == 'valid_time') {
					$hasil_validasi[$name][] = input_valid_time($name);
				}
				if ($rule_check == 'after_date') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_after_date($name, $comparison);
				}


				if ($rule_check == 'before_date') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_before_date($name, $comparison);
				}


				if ($rule_check == 'after_or_equal_date') {
					$comparison = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_after_or_equal_date($name, $comparison);
				}
				if ($rule_check == 'valid_date') {
					$hasil_validasi[$name][] = input_valid_date($name);
				}
				if ($rule_check == 'is_unique') {
					$nama_tabel = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_is_unique($name, $nama_tabel);
				}
				if ($rule_check == 'unique_except') {
					$new_data = end(explode(':', $data_rule));
					$except = reset(explode('|', $new_data));
					$nama_tabel = end(explode('|', $new_data));
					$hasil_validasi[$name][] = input_unique_except($name, $except, $nama_tabel);
				}
				if ($rule_check == 'except_unique') {
					$arr = end(explode(':', $data_rule));
					$previous_value = reset(explode('|', $arr));
					$nama_tabel = end(explode('|', $arr));
					$hasil_validasi[$name][] = input_except_unique($name, $nama_tabel, $previous_value);
				}
				if ($rule_check == 'exists') {
					$nama_tabel = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_exists($name, $nama_tabel);
				}
				if ($rule_check == 'regex_match') {
					$matches = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_regex_match($name, $matches);
				}
				if ($rule_check == 'allowed_values') {
					$allowed_values = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_allowed_values($name, $allowed_values);
				}
				if ($rule_check == 'exact_value') {
					$rule = end(explode(':', $data_rule));
					$hasil_validasi[$name][] = input_exact_value($name, $rule);
				}
			}
		}
	}

	foreach ($_FILES as $name => $value) {
		if ($_SESSION['set_rules'][$name] !== NULL) {
			foreach ($_SESSION['set_rules'][$name]['rules'] as $rule) {
				$rule_check = reset(explode(':', $rule));
				if ($rule_check == 'allow_not_required') {
					$hasil_validasi[$name][] = files_allow_not_required($name);
				}
				if ($rule_check == 'required') {
					$hasil_validasi[$name][] = files_required($name);
				}
				if ($rule_check == 'max_size') {
					$max_size = end(explode(':', $rule));
					$hasil_validasi[$name][] = files_max_size($name, $max_size);
				}
				if ($rule_check == 'min_size') {
					$min_size = end(explode(':', $rule));
					$hasil_validasi[$name][] = files_min_size($name, $min_size);
				}
				if ($rule_check == 'allowed_types') {
					$allowed_types = end(explode(':', $rule));
					$hasil_validasi[$name][] = files_allowed_types($name, $allowed_types);
				}
			}
		}
	}


	foreach (array_keys($hasil_validasi) as $nama_atribut) {
		foreach ($hasil_validasi[$nama_atribut] as $hasil_validasi2) {
			if ($hasil_validasi2 === FALSE) {
				$hasil_validasi3[$nama_atribut] = FALSE;
				$cek_hasil2 = FALSE;
			}
		}
		if ($hasil_validasi3[$nama_atribut] === NULL && $with == 1 || $cek_hasil2 !== FALSE && $with == 1) {
			success($nama_atribut);
		}
	}
	foreach ($hasil_validasi3 as $dat) {
		if ($dat === false) {
			$cek_form = false;
		}
	}
	reset_rules();
	$_SESSION['cek_form']['form_validation'] = $cek_form;
	return $cek_form;
}
function validate_all(int $with = 1)
{
	$_SESSION['error'] = [];
	$hasil_validasi = [];
	foreach ($_POST as $name => $value) {
		if ($_POST[$name] == "") {
			if ($_SESSION['set_rules'][$name]['custom-message']) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']);
			} else {
				error($name, 'Tidak boleh kosong !');
			}
			$hasil_validasi[$name][] = false;
		} else {
			if ($with == 1) {
				success($name);
			}
		}
	}
	foreach ($_FILES as $name => $value) {
		if ($_FILES[$name]['error'] == 4) {
			if ($_SESSION['set_rules'][$name]['custom-message']) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']);
			} else {
				error($name, 'Tidak boleh kosong !');
			}
			$hasil_validasi[$name][] = false;
		} else {
			if ($with == 1) {
				success($name);
			}
		}
	}
	foreach (array_keys($hasil_validasi) as $nama_atribut) {
		foreach ($hasil_validasi[$nama_atribut] as $hasil_validasi2) {
			if ($hasil_validasi2 === FALSE) {
				$hasil_validasi3[$nama_atribut] = FALSE;
				$cek_hasil2 = FALSE;
			}
		}
		if ($hasil_validasi3[$nama_atribut] === NULL && $with == 1 || $cek_hasil2 !== FALSE && $with == 1) {
			success($nama_atribut);
		}
	}
	foreach ($hasil_validasi3 as $dat) {
		if ($dat === false) {
			$cek_form = false;
		}
	}
	reset_rules();
	$_SESSION['cek_form']['form_validation'] = $cek_form;
	return $cek_form;
}
function set_custom_message(string $nama_atribut = '', string $custom_message = '', string $lang = 'ID')
{
	$_SESSION['set_rules'][$nama_atribut] = array('custom-message' => $custom_message, 'lang' => $lang,);
	return true;
}
function files_required($name)
{
	if ($_FILES[$name]['error'] != 0) {
		if ($_SESSION['set_rules'][$name]['custom-message']['required'] !== NULL) {
			error($name, $_SESSION['set_rules'][$name]['custom-message']['required']);
		} else {
			error($name, 'Wajib upload file !');
		}
		return false;
	} else {
		success($name);
	}
}
function files_allow_not_required($name)
{
	if ($_FILES[$name]['error'] != 0) {
		return true;
	} else {
		return true;
	}
}
function files_max_size($name, $max_size)
{
	if (stripos($max_size, 'kb') !== false) {
		$max_size = $max_size * 1024;
	} else if (stripos($max_size, 'mb') !== false) {
		$max_size = $max_size * 1024 * 1024;
	} else if (stripos($max_size, 'gb') !== false) {
		$max_size = $max_size * 1024 * 1024 * 1024;
	} else {
		if (!is_numeric($max_size)) {
			$max_size = 0;
		} else {
			$max_size = $max_size;
		}
	}
	if ($_FILES[$name]['error'] == 0) {
		$new_size = size_converter($max_size);
		if ($_FILES[$name]['size'] > $max_size) {
			if ($_SESSION['set_rules'][$name]['custom-message']['max_size'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['max_size']);
			} else {
				error($name, 'Maksimal ukuran file ' . $new_size . ' !');
			}
			return false;
		}
	}
}
function files_min_size($name, $min_size)
{
	if (stripos($min_size, 'kb') !== false) {
		$min_size = $min_size * 1024;
	} else if (stripos($min_size, 'mb') !== false) {
		$min_size = $min_size * 1024 * 1024;
	} else if (stripos($min_size, 'gb') !== false) {
		$min_size = $min_size * 1024 * 1024 * 1024;
	} else {
		if (!is_numeric($min_size)) {
			$min_size = 0;
		} else {
			$min_size = $min_size;
		}
	}
	if ($_FILES[$name]['error'] == 0) {
		$new_size = size_converter($min_size);
		if ($_FILES[$name]['size'] < $min_size) {
			if ($_SESSION['set_rules'][$name]['custom-message']['min_size'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['max_size']);
			} else {
				error($name, 'Minimal ukuran file ' . $new_size . ' !');
			}
			return false;
		}
	}
}
function files_allowed_types($name, $allowed_types)
{
	if (!is_uploaded_file($_FILES[$name]['tmp_name'])) {
		return false;
	}
	if ($_FILES[$name]['error'] == 0) {
		$mime_type = mime_content_type($_FILES[$name]['tmp_name']);
		$fileExtension = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
		$fileType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES[$name]['tmp_name']);
		$allowed = explode('|', $allowed_types);
		$tipe = '';
		$allowedExtentions = [];
		$allowedFileTypes = [];
		foreach ($allowed as $allow) {
			if ($allow != '') {
				$tipe = '.' . $allow;
				$mimes = mimes_search($allow);
				array_push($allowedExtentions, $allow);
				if (is_array($mimes)) {
					if (!in_array($mime_type, $mimes) || !in_array($fileExtension, $allowedExtentions)) {
						$cek[] = FALSE;
					} else {
						$cek[] = TRUE;
					}
				} else {
					if ($mime_type != $mimes || !in_array($fileExtension, $allowedExtentions)) {
						$cek[] = FALSE;
					} else {
						$cek[] = TRUE;
					}
				}
			}
		}
		if (in_array(TRUE, $cek)) {
			$cek = TRUE;
		} else {
			$cek = FALSE;
		}
		if ($cek === false) {
			if ($_SESSION['set_rules'][$name]['custom-message']['allowed_types'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['allowed_types']);
			} else {
				error($name, 'File yang diperbolehkan berupa ' . $tipe . " atau file tidak valid !");
			}
			return false;
		} else {
			success($name);
		}
	} else {
		return false;
	}
}
function input_is_unique($name, $nama_table)
{
// 	if ($_POST[$name] != "") {
// 		$tabel = reset(explode('.', $nama_table));
// 		$cek = "SELECT $nama_table FROM $tabel WHERE $nama_table = '$_POST[$name]'";
// 		$cek_db = $_SERVER['KONEKSI_DB']->query($cek);
// 		if ($_SERVER['KONEKSI_DB']->connect_errno || mysqli_num_rows($cek_db) != 0) {
// 			if ($_SESSION['set_rules'][$name]['custom-message']['is_unique'] !== NULL) {
// 				error($name, $_SESSION['set_rules'][$name]['custom-message']['is_unique']);
// 			} else {
// 				error($name, 'Nilai sudah digunakan, silahkan memasukkan nilai yang lain !');
// 			}
// 			return false;
// 		}
// 	} else {
// 		return false;
// 	}

	if ($_POST[$name] != "") {
		$tabel = reset(explode('.', $nama_table));
		$cek_query = "SELECT COUNT(*) FROM $tabel WHERE $nama_table = :value";
		
		try {
			$stmt = $_SERVER['PDO']->prepare($cek_query);
			$stmt->bindParam(':value', $_POST[$name], PDO::PARAM_STR);
			$stmt->execute();
			$count = $stmt->fetchColumn();
		
			if ($count > 0) {
				if (!empty($_SESSION['set_rules'][$name]['custom-message']['is_unique'])) {
					error($name, $_SESSION['set_rules'][$name]['custom-message']['is_unique']);
				} else {
					error($name, 'Nilai sudah digunakan, silahkan memasukkan nilai yang lain !');
				}
				return false;
			}
		} catch (PDOException $e) {
			error($name, 'Terjadi kesalahan saat memvalidasi data.');
			return false;
		}
		
	} else {
		return false;
	}
}
function input_exists($name, $nama_table)
{
	if ($_POST[$name] != "") {
		$tabel = reset(explode('.', $nama_table));
		$cek = "SELECT $nama_table FROM $tabel WHERE $nama_table = '$_POST[$name]'";
		$cek_db = $_SERVER['DB']->query($cek);
		if ($_SERVER['DB']->connect_errno || mysqli_num_rows($cek_db) != 1) {
			if ($_SESSION['set_rules'][$name]['custom-message']['exists'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['exists']);
			} else {
				error($name, 'Nilai tidak valid, silahkan memasukkan nilai yang lain !');
			}
			return false;
		}
	} else {
		return false;
	}
}


function input_before_date($name, $comparison)
{
	if ($_POST[$name] != "") {
		$date_1 = strtotime($_POST[$name]);
		$date_2 = isset($_POST[$comparison]) ? strtotime($_POST[$comparison]) : strtotime($comparison);

		if ((bool) strtotime($_POST[$name]) === false || $date_1 >= $date_2) {
			if ($_SESSION['set_rules'][$name]['custom-message']['before_date'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['before_date']);
			} else {
				error($name, 'Tanggal harus sebelum dari ' . date('d M Y', $date_2) . ' ! ');
			}
			return false;
		}
	} else {
		return false;
	}
}

function input_after_date($name, $comparison)
{
	if ($_POST[$name] != "") {
		$date_1 = strtotime($_POST[$name]);
		$date_2 = isset($_POST[$comparison]) ? strtotime($_POST[$comparison]) : strtotime($comparison);

		if ((bool) strtotime($_POST[$name]) === false || $date_1 < $date_2) {
			if ($_SESSION['set_rules'][$name]['custom-message']['after_date'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['after_date']);
			} else {
				error($name, 'Tanggal harus lebih besar dari ' . date('d M Y', $date_2) . ' ! ');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_after_or_equal_date($name, $comparison)
{
	if ($_POST[$name] != "") {
		$date_1 = strtotime($_POST[$name]);
		$date_2 = isset($_POST[$comparison]) ? strtotime($_POST[$comparison]) : strtotime($comparison);

		if ((bool) strtotime($_POST[$name]) === false || $date_1 <= $date_2) {
			if ($_SESSION['set_rules'][$name]['custom-message']['after_or_equal_date'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['after_or_equal_date']);
			} else {
				error($name, 'Tanggal harus lebih besar atau sama dengan ' . date('d M Y', $date_2) . ' ! ');
			}
			return false;
		}
	} else {
		return false;
	}
}

function input_valid_date($name)
{
	if ($_POST[$name] != "") {
		if ((bool)strtotime($_POST[$name]) === false) {
			if ($_SESSION['set_rules'][$name]['custom-message']['valid_date'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['valid_date']);
			} else {
				error($name, 'Masukkan tanggal yang valid !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_valid_time($name)
{
	if ($_POST[$name] != "") {
		if ((bool)strtotime($_POST[$name]) === false) {
			if ($_SESSION['set_rules'][$name]['custom-message']['valid_time'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['valid_time']);
			} else {
				error($name, 'Masukkan waktu yang valid !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_unique_except($name, $except, $nama_table)
{
	if ($_POST[$name] != "") {
		if ($_POST[$name] != $except) {
			$tabel = reset(explode('.', $nama_table));
			$val2 = $_POST[$name];
			$cek = "SELECT $nama_table FROM $tabel WHERE $nama_table = '$val2'";
			$cek_db = $_SERVER['KONEKSI_DB']->query($cek);
			if ($_SERVER['KONEKSI_DB']->connect_errno || mysqli_num_rows($cek_db) != 0) {
				if ($_SESSION['set_rules'][$name]['custom-message']['unique_except'] !== NULL) {
					error($name, $_SESSION['set_rules'][$name]['custom-message']['unique_except']);
				} else {
					error($name, 'Nilai sudah digunakan, silahkan memasukkan nilai yang lain !');
				}
				return false;
			}
		}
	} else {
		return false;
	}
}
function input_except_unique($name, $nama_table, $previous_value)
{
	if ($_POST[$name] != "") {
		$tabel = reset(explode('.', $nama_table));
		$cek = "SELECT $nama_table FROM $tabel WHERE $nama_table NOT IN ('$previous_value') AND $nama_table = '$_POST[$name]' ";
		$cek_db = $_SERVER['KONEKSI_DB']->query($cek);
		if ($_SERVER['KONEKSI_DB']->connect_errno || $_POST[$name] != $previous_value && $cek_db->num_rows > 0) {
			if ($_SESSION['set_rules'][$name]['custom-message']['except_unique'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['except_unique']);
			} else {
				error($name, 'Nilai sudah digunakan, silahkan memasukkan nilai yang lain !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_allowed_values($name, $value_rules)
{
	if ($_POST[$name] != "") {
		$array = array_map('trim', explode(',', $value_rules));
		if (in_array($_POST[$name], $array) === FALSE) {
			if ($_SESSION['set_rules'][$name]['custom-message']['allowed_values'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['allowed_values']);
			} else {
				error($name, 'Nilai yang diterima adalah ' . $value_rules . ' !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_not_required($name)
{
	if ($_POST[$name] != "") {
		if ($_SESSION['set_rules'][$name]['custom-message']['not_required'] !== NULL) {
			error($name, $_SESSION['set_rules'][$name]['custom-message']['not_required']);
		} else {
			error($name, 'Tidak boleh diisi !');
		}
		return false;
	} else {
		success($name);
	}
}
function input_allow_not_required($name)
{
	if ($_POST[$name] != "") {
		success($name);
	} else {
		success($name);
	}
}
function input_required($name)
{
	if ($_POST[$name] == "") {
		if ($_SESSION['set_rules'][$name]['custom-message']['required'] !== NULL) {
			error($name, $_SESSION['set_rules'][$name]['custom-message']['required']);
		} else {
			error($name, 'Tidak boleh kosong !');
		}
		return false;
	} else {
		return true;
	}
}
function input_min_length($name, $length)
{
	if (!is_numeric($length)) {
		$length = 0;
	}
	if ($_POST[$name] != '') {
		if (strlen($_POST[$name]) < $length) {
			if ($_SESSION['set_rules'][$name]['custom-message']['min_length'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['min_length']);
			} else {
				error($name, 'Minimal panjang karakter adalah ' . $length . " karakter !");
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_max($name, $length)
{
	if (!is_numeric($length)) {
		$length = 0;
	}
	if ($_POST[$name] != '') {
		if (strlen($_POST[$name]) > $length) {
			if ($_SESSION['set_rules'][$name]['custom-message']['max_length'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['max_length']);
			} else {
				error($name, 'Maksimal panjang karakter adalah ' . $length . " karakter !");
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_matches($name, $second_variable, $nama_attribute = '')
{
	if ($nama_attribute == "") {
		$nama_attribute = $second_variable;
	}
	if ($_POST[$name] != '') {
		if ($_POST[$name] != $_POST[$second_variable]) {
			if ($_SESSION['set_rules'][$name]['custom-message']['matches'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['matches']);
			} else {
				error($name, 'Harus sama dengan kolom ' . $_SESSION['set_rules'][$second_variable]['panggilan']);
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_exact_value($name, $value)
{
	if ($_POST[$name] != '') {
		if ($_POST[$name] != $value) {
			if ($_SESSION['set_rules'][$name]['custom-message']['exact_value'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['exact_value']);
			} else {
				error($name, 'Kolom harus bernilai  ' . $value . ' !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_exact_length($name, $length)
{
	if (!is_numeric($length)) {
		$length = 0;
	}
	if ($_POST[$name] != '') {
		if (strlen($_POST[$name]) != $length) {
			if ($_SESSION['set_rules'][$name]['custom-message']['exact_length'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['exact_length']);
			} else {
				error($name, 'Panjang karakter harus ' . $length . " karakter !");
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_greater_than($name, $comparison)
{
	if (!is_numeric($comparison)) {
		$comparison = 0;
	}
	if ($_POST[$name] != '') {
		if (!is_numeric($_POST[$name]) || $_POST[$name] <= $comparison) {
			if ($_SESSION['set_rules'][$name]['custom-message']['greater_than'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['greater_than']);
			} else {
				error($name, 'Nilai harus lebih besar dari ' . $comparison);
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_greater_than_equal_to($name, $comparison)
{
	if (!is_numeric($comparison)) {
		$comparison = 0;
	}
	if ($_POST[$name] != '') {
		if (!is_numeric($_POST[$name]) || $_POST[$name] < $comparison) {
			if ($_SESSION['set_rules'][$name]['custom-message']['greater_than_equal_to'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['greater_than_equal_to']);
			} else {
				error($name, 'Nilai harus lebih besar atau sama dengan ' . $comparison);
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_less_than($name, $comparison)
{
	if (!is_numeric($comparison)) {
		$comparison = 0;
	}
	if ($_POST[$name] != '') {
		if (!is_numeric($_POST[$name]) || $_POST[$name] >= $comparison) {
			if ($_SESSION['set_rules'][$name]['custom-message']['less_than'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['less_than']);
			} else {
				error($name, 'Nilai harus lebih kecil dari ' . $comparison);
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_less_than_equal_to($name, $comparison)
{
	if (!is_numeric($comparison)) {
		$comparison = 0;
	}
	if ($_POST[$name] != '') {
		if (!is_numeric($_POST[$name]) || $_POST[$name] > $comparison) {
			if ($_SESSION['set_rules'][$name]['custom-message']['less_than_equal_to'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['less_than_equal_to']);
			} else {
				error($name, 'Nilai harus lebih kecil atau sama dengan ' . $comparison);
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_alpha($name)
{
	if ($_POST[$name] != '') {
		if (ctype_alnum($_POST[$name]) === true) {
			if ($_SESSION['set_rules'][$name]['custom-message']['alpha'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['alpha']);
			} else {
				error($name, 'Nilai hanya boleh terdiri dari Huruf !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_alpha_numeric($name)
{
	if ($_POST[$name] != '') {
		if (ctype_alnum($_POST[$name]) === false) {
			if ($_SESSION['set_rules'][$name]['custom-message']['alpha_numeric'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['alpha_numeric']);
			} else {
				error($name, 'Nilai hanya boleh terdiri dari Huruf dan Angka !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_numeric($name)
{
	if ($_POST[$name] != '') {
		if (is_numeric($_POST[$name]) === false) {
			if ($_SESSION['set_rules'][$name]['custom-message']['numeric'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['numeric']);
			} else {
				error($name, 'Nilai hanya boleh terdiri dari Angka !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_integer($name)
{
	if ($_POST[$name] != '') {
		if (is_int($_POST[$name]) === false) {
			if ($_SESSION['set_rules'][$name]['custom-message']['integer'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['integer']);
			} else {
				error($name, 'Nilai hanya boleh terdiri dari Angka Genap !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_decimal($name)
{
	if ($_POST[$name] != '') {
		if (is_float($_POST[$name]) === false) {
			if ($_SESSION['set_rules'][$name]['custom-message']['decimal'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['decimal']);
			} else {
				error($name, 'Nilai hanya boleh terdiri dari Angka Desimal !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_valid_phone($name)
{
	if ($_POST[$name] != '') {
		$regex = "/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}/u";
		if (preg_match_all($regex, $_POST[$name]) === 0) {
			if ($_SESSION['set_rules'][$name]['custom-message']['valid_phone'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['valid_phone']);
			} else {
				error($name, 'Masukkan alamat e-mail yang valid !');
			}
			return false;
		}
	} else {
		return false;
	}
}
function input_valid_email($name)
{
	if ($_POST[$name] != '') {
		if (filter_var($_POST[$name], FILTER_VALIDATE_EMAIL) === false) {
			if ($_SESSION['set_rules'][$name]['custom-message']['valid_email'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['valid_email']);
			} else {
				error($name, 'Masukkan alamat e-mail yang valid !');
			}
			return false;
		} else {
			$_POST[$name] = filter_var($_POST[$name], FILTER_SANITIZE_EMAIL);
		}
	} else {
		return false;
	}
}
function input_valid_url($name)
{
	if ($_POST[$name] != '') {
		$regex = "((https?|ftp)\:\/\/)?";
		$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";
		$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})";
		$regex .= "(\:[0-9]{2,5})?";
		$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";
		$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?";
		$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?";
		if (preg_match("/^$regex$/i", $_POST[$name]) === FALSE) {
			if ($_SESSION['set_rules'][$name]['custom-message']['valid_url'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['valid_url']);
			} else {
				error($name, 'Masukkan URL yang valid !');
			}
			return false;
		} else {
			$_POST[$name] = filter_var($_POST[$name], FILTER_SANITIZE_URL);
		}
	} else {
		return false;
	}
}
function input_regex_match($name, $matches)
{
	if ($_POST[$name] != '') {
		if (preg_match_all($matches, $_POST[$name], $hasil) === 0) {
			if ($_SESSION['set_rules'][$name]['custom-message']['regex_match'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['regex_match']);
			} else {
				error($name, 'Tidak sesuai dengan format yang diminta !');
			}
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}
function reset_rules()
{
	$_SESSION['set_rules'] = [];
	$_SESSION['cek_form'] = [];
	$_SESSION['type'] = [];
}


function form_notification(string $tags = '')
{
	if ($tags != '') {
		$tags;
	}
	if (isset($_SESSION['notifikasi']['error'])) {
		echo '<div class="alert alert-warning">' . $_SESSION['notifikasi']['error'] . '</div>';
	}
	if ($tags != '') {
		closed_tag($tags);
	}
}
function closed_tag(string $tags = '')
{
	$closed_tag = (array) simplexml_load_string($tags)[0];
	echo '</' . $closed_tag . '>';
}
function make_toast($state = 'primary', $title = 'Not Set', $message = '', $icon = '')
{

	make_old('have-notification', true);
	make_old('have-toast', true);
	make_old('toast-state', $state);
	make_old('toast-title', $title);
	make_old('toast-message', $message);
	make_old('toast-icon', $icon);
	return true;
}
function make_toast2($state = 'success', $title = 'Not Set', $message = '', $icon = '')
{
	make_old('have-notification', true);
	make_old('have-toast2', true);
	make_old('toast2-state', $state);
	make_old('toast2-title', $title);
	make_old('toast2-message', $message);
	make_old('toast2-icon', $icon);
	return true;
}
function make_toast3($state = 'success', $title = 'Not Set', $message = '', $timer = 3000)
{
	make_old('have-notification', true);
	make_old('have-toast3', true);
	make_old('toast3-state', $state);
	make_old('toast3-title', $title);
	make_old('toast3-message', $message);
	make_old('toast3-timer', $timer);
	return true;
}
function make_toast5($state = 'success', $title = 'Not Set', $message = '', $timer = '5000')
{
	make_old('have-notification', true);
	make_old('have-toast5', true);
	make_old('toast5-state', $state);
	make_old('toast5-title', $title);
	make_old('toast5-message', $message);
	make_old('toast5-timer', $timer);
	return true;
}
function make_snackbar($message = 'Not Set', $position = 'bottom-center')
{
	make_old('have-notification', true);
	make_old('have-snackbar', true);
	make_old('snackbar-message', $message);
	make_old('snackbar-position', $position);
	return true;
}
function make_soloalert($state = 'success', $title = 'Not Set', $message = '', $transparency = false)
{
	make_old('have-notification', true);
	make_old('have-soloalert', true);
	make_old('soloalert-state', $state);
	make_old('soloalert-title', $title);
	make_old('soloalert-message', $message);
	make_old('soloalert-transparency', $transparency);
	return true;
}
function make_cutealert($state = 'success', $title = 'Not Set', $message = '')
{
	make_old('have-notification', true);
	make_old('have-cutealert', true);
	make_old('cutealert-state', $state);
	make_old('cutealert-title', $title);
	make_old('cutealert-message', $message);
	return true;
}
function make_swal2($state = 'success', $title = 'Not Set', $message = '', $timer = 2000)
{
	make_old('have-notification', true);
	make_old('have-swal2', true);
	make_old('swal2-state', $state);
	make_old('swal2-title', $title);
	make_old('swal2-message', $message);
	make_old('swal2-timer', $timer);
	return true;
}
function make_sound($state = 'chime', $timer = 1000)
{
	make_old('have-notification', true);
	if (!in_array(strtolower($state), ['error', 'warning', 'success', 'bye', 'close', 'welcome'])) {
		$state = 'chime';
	} else {
		$state = strtolower($state);
	}
	make_old('have-sound', true);
	make_old('sound-state', $state);
	make_old('sound-timer', $timer);
	return true;
}
function swal2(string $tipe = "error", string $judul = "Not Set", string $notifikasi = "", int $timer = 2000)
{
	$time = "( Elapsed Time : " . number_format((float)(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 2, '.', '') . " Seconds )";
	echo "
	<link rel='stylesheet' href='./core/component/sweetalert2.css'>
    <script src='./core/component/sweetalert2.all.js'></script>


	<script type='text/javascript'>
	setTimeout(function () { 
		swal.fire({
			
			title               : '$judul',
			text                :  '$notifikasi',
			//footer              :  '$time',
			icon                : '$tipe',
			timer               : $timer,
			showConfirmButton   : true
		});  
	},10); 
	</script>";
}
function show_toast()
{
	show_notification();
}
function reset_notification()
{
	make_old('have-notification', false);
	make_old('have-sound', false);
	make_old('sound-state', NULL);
	make_old('sound-timer', NULL);
	make_old('have-snackbar', false);
	make_old('snackbar-message', NULL);
	make_old('snackbar-position', NULL);
	make_old('have-toast', false);
	make_old('toast-state', NULL);
	make_old('toast-title', NULL);
	make_old('toast-message', NULL);
	make_old('toast-icon', NULL);
	make_old('have-toast2', false);
	make_old('toast2-state', NULL);
	make_old('toast2-title', NULL);
	make_old('toast2-message', NULL);
	make_old('toast2-icon', NULL);
	make_old('have-toast3', false);
	make_old('toast3-state', NULL);
	make_old('toast3-title', NULL);
	make_old('toast3-message', NULL);
	make_old('toast3-timer', NULL);
	make_old('have-cutealert', false);
	make_old('cutealert-state', NULL);
	make_old('cutealert-title', NULL);
	make_old('cutealert-message', NULL);
	make_old('have-toast5', false);
	make_old('toast5-state', NULL);
	make_old('toast5-title', NULL);
	make_old('toast5-message', NULL);
	make_old('toast5-timer', NULL);
	make_old('have-swal2', false);
	make_old('swal2-state', NULL);
	make_old('swal2-title', NULL);
	make_old('swal2-message', NULL);
	make_old('swal2-icon', NULL);
	make_old('have-soloalert', false);
	make_old('soloalert-state', NULL);
	make_old('soloalert-title', NULL);
	make_old('soloalert-message', NULL);
	make_old('soloalert-transparency', NULL);
}
function show_notification($without_jquery = 0)
{
	$komponen = '';
	$notifikasi = '';

	if ($without_jquery == 0) {
		echo "<script src='./core/component/jquery.min.js'></script>";
	}

	if ($_SESSION['old']['have-toast'] === true) {
		echo "<script src='./core/component/bootstrap-notify.min.js'></script>";
	}
	if ($_SESSION['old']['have-toast2'] === true) {
		echo "<link rel='stylesheet' href='./core/component/sweetalert2.min.css'>";
		echo "<script src='./core/component/sweetalert2.all.min.js'></script>";
	}
	if ($_SESSION['old']['have-toast3'] === true) {
		echo "<link rel='stylesheet' href='./core/component/toastr.css'>";
		echo "<script src='./core/component/toastr.min.js'></script>";
	}
	if ($_SESSION['old']['have-toast4'] === true || $_SESSION['old']['have-toast5'] === true) {
		echo "<link rel='stylesheet' href='./core/component/cute-alert-style.css' />";
		echo "<script src='./core/component/cute-alert.js'></script>";
	}
	if ($_SESSION['old']['have-swal2'] === true) {
		echo "<link rel='stylesheet' href='./core/component/sweetalert2.css'>";
		echo "<script src='./core/component/sweetalert2.all.min.js'></script>";
	}
	if ($_SESSION['old']['have-soloalert'] === true) {
		echo "<script src='./core/component/soloalert.min.js'></script>";
	}
	if ($_SESSION['old']['have-snackbar'] === true) {
		echo "<link rel='stylesheet' href='./core/component/snackbar/dist/snackbar.min.css' media='screen' title='no title' charset='utf-8'>";
		echo "<link rel='stylesheet' href='./core/component/snackbar/src/prism.css' media='screen' title='no title' charset='utf-8'>";
		echo "<script src='./core/component/snackbar/src/modernizr.js' charset='utf-8'></script>";
		echo "<script src='./core/component/snackbar/src/materialize.min.js' charset='utf-8'></script>";
		echo "<script src='./core/component/snackbar/src/prism.js' charset='utf-8'></script>";
		echo "<script src='./core/component/snackbar/dist/snackbar.min.js' charset='utf-8'></script>";
		echo "<script src='./core/component/snackbar/src/init.js' charset='utf-8'></script>";
	}

	if ($_SESSION['old']['have-cutealert'] === true) {
		echo "<link rel='stylesheet' href='./core/component/cute-alert-style.css' media='screen' title='no title' charset='utf-8'>";
		echo "<script src='./core/component/cute-alert.js' charset='utf-8'></script>";
	}

	echo "<script>";
	echo "$(function() {";
	// echo  "$(document).ready(function() {";
	if ($_SESSION['old']['have-sound'] === true) {
		$state = $_SESSION['old']['sound-state'];
		$timer = $_SESSION['old']['sound-timer'];
		echo "play_sound( '$state', $timer );";
		make_old('have-sound', false);
		make_old('sound-state', NULL);
		make_old('sound-timer', NULL);
	}
	if ($_SESSION['old']['have-snackbar'] === true) {
		$message = $_SESSION['old']['snackbar-message'];
		$position = $_SESSION['old']['snackbar-position'];
		echo "Snackbar.show({
				text: '$message',
				pos: '$position',
			});";
		make_old('have-snackbar', false);
		make_old('snackbar-message', NULL);
		make_old('snackbar-position', NULL);
	}
	if ($_SESSION['old']['have-toast'] === true) {
		$state = $_SESSION['old']['toast-state'];
		$title = $_SESSION['old']['toast-title'];
		$message = $_SESSION['old']['toast-message'];
		$icon = $_SESSION['old']['toast-icon'];
		echo "toast ( '$state', '$title', '$message', '$icon' );";
		make_old('have-toast', false);
		make_old('toast-state', NULL);
		make_old('toast-title', NULL);
		make_old('toast-message', NULL);
		make_old('toast-icon', NULL);
	}
	if ($_SESSION['old']['have-toast2'] === true) {
		$state = $_SESSION['old']['toast2-state'];
		$title = $_SESSION['old']['toast2-title'];
		$message = $_SESSION['old']['toast2-message'];
		$icon = $_SESSION['old']['toast2-icon'];
		echo "toast2 ( '$state', '$title', '$message', '$icon' );";
		make_old('have-toast2', false);
		make_old('toast2-state', NULL);
		make_old('toast2-title', NULL);
		make_old('toast2-message', NULL);
		make_old('toast2-icon', NULL);
	}
	if ($_SESSION['old']['have-toast3'] === true) {
		$state = $_SESSION['old']['toast3-state'];
		$title = $_SESSION['old']['toast3-title'];
		$message = $_SESSION['old']['toast3-message'];
		$timer = $_SESSION['old']['toast3-timer'];
		echo "toast3 ( '$state', '$title', '$message', '$timer' );";
		make_old('have-toast3', false);
		make_old('toast3-state', NULL);
		make_old('toast3-title', NULL);
		make_old('toast3-message', NULL);
		make_old('toast3-timer', NULL);
	}
	if ($_SESSION['old']['have-cutealert'] === true) {
		$state = $_SESSION['old']['cutealert-state'];
		$title = $_SESSION['old']['cutealert-title'];
		$message = $_SESSION['old']['cutealert-message'];
		echo "cutealert ( '$state', '$title', '$message' );";
		make_old('have-cutealert', false);
		make_old('cutealert-state', NULL);
		make_old('cutealert-title', NULL);
		make_old('cutealert-message', NULL);
	}
	if ($_SESSION['old']['have-toast5'] === true) {
		$state = $_SESSION['old']['toast5-state'];
		$title = $_SESSION['old']['toast5-title'];
		$message = $_SESSION['old']['toast5-message'];
		$timer = $_SESSION['old']['toast5-timer'];
		echo "toast5( '$state', '$title', '$message', '$timer' );";
		make_old('have-toast5', false);
		make_old('toast5-state', NULL);
		make_old('toast5-title', NULL);
		make_old('toast5-message', NULL);
		make_old('toast5-timer', NULL);
	}
	if ($_SESSION['old']['have-swal2'] === true) {
		$state = $_SESSION['old']['swal2-state'];
		$title = $_SESSION['old']['swal2-title'];
		$message = $_SESSION['old']['swal2-message'];
		$timer = $_SESSION['old']['swal2-timer'];
		echo "swal2 ( '$state', '$title', '$message' , $timer )";
		make_old('have-swal2', false);
		make_old('swal2-state', NULL);
		make_old('swal2-title', NULL);
		make_old('swal2-message', NULL);
		make_old('swal2-icon', NULL);
	}
	if ($_SESSION['old']['have-soloalert'] === true) {
		$state = $_SESSION['old']['soloalert-state'];
		$title = $_SESSION['old']['soloalert-title'];
		$message = $_SESSION['old']['soloalert-message'];
		$transparency = $_SESSION['old']['soloalert-transparency'];
		echo "soloalert ( '$state', '$title', '$message', $transparency );";
		make_old('have-soloalert', false);
		make_old('soloalert-state', NULL);
		make_old('soloalert-title', NULL);
		make_old('soloalert-message', NULL);
		make_old('soloalert-transparency', NULL);
	}
	echo "});";
	echo "</script>";
	reset_notification();
}
function alert(string $msg = "")
{
	echo "<script type='text/javascript'>alert('$msg');</script>";
}
function delete_dir(string $lokasi)
{
	$dir = $lokasi;
	$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
	foreach ($files as $file) {
		if ($file->isDir()) {
			rmdir($file->getRealPath());
		} else {
			unlink($file->getRealPath());
		}
	}
	rmdir($dir);
}
function base_url2(string $location = '')
{
	$root = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
	$root .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
	$root .= $location;
	return $root;
}

function base_url(string $path = '')
{
    // Mengambil skema (http atau https)
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    
    // Mengambil host (misal: localhost:3000)
    $host = $_SERVER['HTTP_HOST'];
    
    // Mengambil root path dari script (misal: /)
    $scriptName = str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
    
    // Menyusun URL lengkap
    $baseUrl = $scheme . '://' . $host . $scriptName;
    
    // Menggabungkan dengan path yang diberikan
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}
function flash_session()
{
	reflash('old');
	reflash('error');
	reflash('notifikasi');
	reflash('custom');
	reflash('type');
}
function role_checker(string $attribut = 'role', array $value = [], string $tipe = 'halaman')
{
	if (!in_array($_SESSION[$attribut], $value)) {
		if ($tipe == 'halaman') {
			view('exception/error_role');
			exit;
		} else {
			redirect_referrer(1);
			exit;
		}
	} else {
		if ($tipe == 'notifikasi') {
			reset_notification();
		}
	}
}
function checker(string $found, array $check, string $what = 'active', string $else = '')
{
	if ($found == '') {
		$found = $_GET['page'];
	}
	if (in_array($found, $check)) {
		echo $what;
	} else {
		echo $else;
	}
}
if (!function_exists('set_userdata')) {

	function set_userdata($nama, $value = '')
	{
		session_start();

		if (is_array($nama)) {
			$_SESSION['USERDATA'] = $nama;
		} else if (is_string($nama)) {
			$_SESSION['USERDATA'][$nama] = $value;
		}
	}
}

function userdata(string $nama)
{
	return $_SESSION['userdata'][$nama];
}
function unset_userdata(string $nama)
{
	unset($_SESSION['userdata'][$nama]);
}



function get_client_ip()
{
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP']))
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_X_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if (isset($_SERVER['REMOTE_ADDR']))
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}

function api(string $nama_controller = "", $method_type = 'POST', callable ...$customActions)
{

	error_reporting(false);
	session_start();
	$currentCookieParams = session_get_cookie_params();
	$sidvalue = session_id();
	setcookie(
		'PHPSESSID', //name  
		$sidvalue, //value  
		0, //expires at end of session  
		$currentCookieParams['path'], //path  
		$currentCookieParams['domain'], //domain  
		true //secure  
	);
	
	header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    $valid_methods = [
        'ALL' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'POST' => 'POST',
        'GET' => 'GET',
        'DEL' => 'POST',
        'PUT' => 'POST'
    ];
    
    // Pastikan metode yang digunakan valid
    if (!in_array($method_type, array_keys($valid_methods))) {
        echo json_encode([
            'welcome_message' => $_SERVER['API_WELCOME'],
            'status' => 0,
            'message' => 'Method not available !'
        ], JSON_PRETTY_PRINT);
        http_response_code(405);
        exit;
    }

    // Cek apakah request method sesuai dengan yang diinginkan
    if ($_SERVER['REQUEST_METHOD'] !== $valid_methods[$method_type] && 
        !($method_type === 'DEL' && isset($_POST['DEL'])) && 
        !($method_type === 'PUT' && isset($_POST['PUT']))		
		) {
        $method_message = 'Only ' . $method_type . ' Method is supported !';
        echo json_encode([
            'error_code' => 405,
            'status' => 0,
            'message' => $method_message
        ], JSON_PRETTY_PRINT);
        http_response_code(405);
        exit;
    }

    if (!in_array($method_type, ['GET', 'ALL'])) {
    
        // Mengambil token dari POST dan melindunginya
        $token = escape_val($_POST['token']);
    
        // Pastikan token ada jika menggunakan metode POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($token)) {
            response_json(401, [
                'error_code' => 401,
                'status' => 0,
                'message' => 'Token Not Found !',
                'additional_message' => 'Excuse me ! Who are you ?'
            ]);
            exit;
        }
    
        // Menyiapkan query untuk memeriksa token di database
        $sql = "SELECT * FROM access_token WHERE token = '$token'"; // Menggunakan token yang sudah di-escape
        $sql_token = $_SERVER['DB']->query($sql);
        $cek_token = $sql_token->num_rows;
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $cek_token != 1) {
            response_json(401, [
                'error_code' => 401,
                'status' => 0,
                'message' => 'Invalid Token !',
                'additional_message' => 'Excuse me ! Who are you ?'
            ]);
            exit;
        }
    
        $token_data = $sql_token->fetch_assoc();
    
        // Update jumlah koneksi
        $update_sql = "UPDATE access_token SET connection_count = connection_count + 1 WHERE token = '$token'"; // Token sudah di-escape
        $_SERVER['DB']->query($update_sql);
    
        // Jika token telah di-revoke, hentikan eksekusi
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $token_data['token_revoke'] != 0) {
            $last_connection = getBrowser() . ' [Revoke Access]';
            $update_revoke_sql = "UPDATE access_token SET last_connection = '$last_connection' WHERE token = '$token'"; // Token sudah di-escape
            $_SERVER['DB']->query($update_revoke_sql);
    
            echo json_encode([
                'welcome_message' => $_SERVER['API_WELCOME'],
                'status' => 0,
                'message' => 'Token has been revoked!',
                'additional_message' => 'Ask for a new one at helpdesk'
            ], JSON_PRETTY_PRINT);
            http_response_code(401);
            exit;
        }
    
        // Ambil alamat yang diizinkan
		// Query sudah dilakukan
        $allowed_address = explode(';', $token_data['allowed_address']);
        $client_ip = get_client_ip();
		$client_host = gethostbyaddr($client_ip);
        
        // Memeriksa apakah alamat IP atau DNS diizinkan
        if ($client_ip != '::1' && $_SERVER['REQUEST_METHOD'] == 'POST' 
            && !in_array($token_data['allowed_address'], ['Any', 'any', 'ANY']) 
            && !(in_array($client_host, $allowed_address))) {
    
            $last_connection = getBrowser() . ' .Timestamp : ' . date('Y-m-d H:i:s') . ' [Unwanted Access]';
            $update_access_sql = "UPDATE access_token SET last_connection = '$last_connection' WHERE token = '$token'"; // Token sudah di-escape
            $_SERVER['DB']->query($update_access_sql);
    
            echo json_encode([
                'welcome_message' => $_SERVER['API_WELCOME'],
                'status' => 0,
                'message' => 'Access from this address is not allowed!',
                'additional_message' => 'Ask for a new token at helpdesk',
            ], JSON_PRETTY_PRINT);
            http_response_code(401);
            exit;
        }
    }

	// Tentukan path ke file controller
    $controller_file = "./api/" . $nama_controller . ".php";
    
    // Cek apakah file controller benar-benar ada
    if (!file_exists($controller_file)) {
        echo json_encode([
            'welcome_message' => $_SERVER['API_WELCOME'],
            'status' => 0,
            'message' => 'API not available!'
        ], JSON_PRETTY_PRINT);
        http_response_code(404);
        exit;
    } else {
        // Jika ada aksi kustom yang disediakan, pastikan aksi tersebut valid dan callable
        if (!empty($customActions) && is_array($customActions)) {
            foreach ($customActions as $action) {
                // Periksa apakah aksi tersebut adalah callable
                if (is_callable($action)) {
                    call_user_func($action);
                }
            }
        }
    
        // Sertakan file controller setelah melakukan pengecekan
        include $controller_file;
    }

	
}

function mimes_search(string $pencarian)
{
	$mimes = array('hqx' => array('application/mac-binhex40', 'application/mac-binhex', 'application/x-binhex40', 'application/x-mac-binhex40'), 'cpt' => 'application/mac-compactpro', 'csv' => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'), 'bin' => array('application/macbinary', 'application/mac-binary', 'application/octet-stream', 'application/x-binary', 'application/x-macbinary'), 'dms' => 'application/octet-stream', 'lha' => 'application/octet-stream', 'lzh' => 'application/octet-stream', 'exe' => array('application/octet-stream', 'application/x-msdownload'), 'class' => 'application/octet-stream', 'psd' => array('application/x-photoshop', 'image/vnd.adobe.photoshop'), 'so' => 'application/octet-stream', 'sea' => 'application/octet-stream', 'dll' => 'application/octet-stream', 'oda' => 'application/oda', 'pdf' => array('application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream'), 'ai' => array('application/pdf', 'application/postscript'), 'eps' => 'application/postscript', 'ps' => 'application/postscript', 'smi' => 'application/smil', 'smil' => 'application/smil', 'mif' => 'application/vnd.mif', 'xls' => array('application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'application/x-ms-excel', 'application/x-excel', 'application/x-dos_ms_excel', 'application/xls', 'application/x-xls', 'application/excel', 'application/download', 'application/vnd.ms-office', 'application/msword'), 'ppt' => array('application/powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.ms-office', 'application/msword'), 'pptx' => array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/x-zip', 'application/zip'), 'wbxml' => 'application/wbxml', 'wmlc' => 'application/wmlc', 'dcr' => 'application/x-director', 'dir' => 'application/x-director', 'dxr' => 'application/x-director', 'dvi' => 'application/x-dvi', 'gtar' => 'application/x-gtar', 'gz' => 'application/x-gzip', 'gzip' => 'application/x-gzip', 'php' => array('application/x-httpd-php', 'application/php', 'application/x-php', 'text/php', 'text/x-php', 'application/x-httpd-php-source'), 'php4' => 'application/x-httpd-php', 'php3' => 'application/x-httpd-php', 'phtml' => 'application/x-httpd-php', 'phps' => 'application/x-httpd-php-source', 'js' => array('application/x-javascript', 'text/plain'), 'swf' => 'application/x-shockwave-flash', 'sit' => 'application/x-stuffit', 'tar' => 'application/x-tar', 'tgz' => array('application/x-tar', 'application/x-gzip-compressed'), 'z' => 'application/x-compress', 'xhtml' => 'application/xhtml+xml', 'xht' => 'application/xhtml+xml', 'zip' => array('application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/s-compressed', 'multipart/x-zip'), 'rar' => array('application/x-rar', 'application/rar', 'application/x-rar-compressed'), 'mid' => 'audio/midi', 'midi' => 'audio/midi', 'mpga' => 'audio/mpeg', 'mp2' => 'audio/mpeg', 'mp3' => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'), 'aif' => array('audio/x-aiff', 'audio/aiff'), 'aiff' => array('audio/x-aiff', 'audio/aiff'), 'aifc' => 'audio/x-aiff', 'ram' => 'audio/x-pn-realaudio', 'rm' => 'audio/x-pn-realaudio', 'rpm' => 'audio/x-pn-realaudio-plugin', 'ra' => 'audio/x-realaudio', 'rv' => 'video/vnd.rn-realvideo', 'wav' => array('audio/x-wav', 'audio/wave', 'audio/wav'), 'bmp' => array('image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'), 'gif' => 'image/gif', 'jpeg' => array('image/jpeg', 'image/pjpeg'), 'jpg' => array('image/jpeg', 'image/pjpeg'), 'jpe' => array('image/jpeg', 'image/pjpeg'), 'jp2' => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'), 'j2k' => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'), 'jpf' => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'), 'jpg2' => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'), 'jpx' => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'), 'jpm' => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'), 'mj2' => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'), 'mjp2' => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'), 'png' => array('image/png', 'image/x-png'), 'tiff' => 'image/tiff', 'tif' => 'image/tiff', 'css' => array('text/css', 'text/plain'), 'html' => array('text/html', 'text/plain'), 'htm' => array('text/html', 'text/plain'), 'shtml' => array('text/html', 'text/plain'), 'txt' => 'text/plain', 'text' => 'text/plain', 'log' => array('text/plain', 'text/x-log'), 'rtx' => 'text/richtext', 'rtf' => 'text/rtf', 'xml' => array('application/xml', 'text/xml', 'text/plain'), 'xsl' => array('application/xml', 'text/xsl', 'text/xml'), 'mpeg' => 'video/mpeg', 'mpg' => 'video/mpeg', 'mpe' => 'video/mpeg', 'qt' => 'video/quicktime', 'mov' => 'video/quicktime', 'avi' => array('video/x-msvideo', 'video/msvideo', 'video/avi', 'application/x-troff-msvideo'), 'movie' => 'video/x-sgi-movie', 'doc' => array('application/msword', 'application/vnd.ms-office'), 'docx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword', 'application/x-zip'), 'dot' => array('application/msword', 'application/vnd.ms-office'), 'dotx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword'), 'xlsx' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/vnd.ms-excel', 'application/msword', 'application/x-zip'), 'word' => array('application/msword', 'application/octet-stream'), 'xl' => 'application/excel', 'eml' => 'message/rfc822', 'json' => array('application/json', 'text/json'), 'pem' => array('application/x-x509-user-cert', 'application/x-pem-file', 'application/octet-stream'), 'p10' => array('application/x-pkcs10', 'application/pkcs10'), 'p12' => 'application/x-pkcs12', 'p7a' => 'application/x-pkcs7-signature', 'p7c' => array('application/pkcs7-mime', 'application/x-pkcs7-mime'), 'p7m' => array('application/pkcs7-mime', 'application/x-pkcs7-mime'), 'p7r' => 'application/x-pkcs7-certreqresp', 'p7s' => 'application/pkcs7-signature', 'crt' => array('application/x-x509-ca-cert', 'application/x-x509-user-cert', 'application/pkix-cert'), 'crl' => array('application/pkix-crl', 'application/pkcs-crl'), 'der' => 'application/x-x509-ca-cert', 'kdb' => 'application/octet-stream', 'pgp' => 'application/pgp', 'gpg' => 'application/gpg-keys', 'sst' => 'application/octet-stream', 'csr' => 'application/octet-stream', 'rsa' => 'application/x-pkcs7', 'cer' => array('application/pkix-cert', 'application/x-x509-ca-cert'), '3g2' => 'video/3gpp2', '3gp' => array('video/3gp', 'video/3gpp'), 'mp4' => 'video/mp4', 'm4a' => 'audio/x-m4a', 'f4v' => array('video/mp4', 'video/x-f4v'), 'flv' => 'video/x-flv', 'webm' => 'video/webm', 'aac' => 'audio/x-acc', 'm4u' => 'application/vnd.mpegurl', 'm3u' => 'text/plain', 'xspf' => 'application/xspf+xml', 'vlc' => 'application/videolan', 'wmv' => array('video/x-ms-wmv', 'video/x-ms-asf'), 'au' => 'audio/x-au', 'ac3' => 'audio/ac3', 'flac' => 'audio/x-flac', 'ogg' => array('audio/ogg', 'video/ogg', 'application/ogg'), 'kmz' => array('application/vnd.google-earth.kmz', 'application/zip', 'application/x-zip'), 'kml' => array('application/vnd.google-earth.kml+xml', 'application/xml', 'text/xml'), 'ics' => 'text/calendar', 'ical' => 'text/calendar', 'zsh' => 'text/x-scriptzsh', '7z' => array('application/x-7z-compressed', 'application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'), '7zip' => array('application/x-7z-compressed', 'application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'), 'cdr' => array('application/cdr', 'application/coreldraw', 'application/x-cdr', 'application/x-coreldraw', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr'), 'wma' => array('audio/x-ms-wma', 'video/x-ms-asf'), 'jar' => array('application/java-archive', 'application/x-java-application', 'application/x-jar', 'application/x-compressed'), 'svg' => array('image/svg+xml', 'application/xml', 'text/xml'), 'vcf' => 'text/x-vcard', 'srt' => array('text/srt', 'text/plain'), 'vtt' => array('text/vtt', 'text/plain'), 'ico' => array('image/x-icon', 'image/x-ico', 'image/vnd.microsoft.icon'), 'odc' => 'application/vnd.oasis.opendocument.chart', 'otc' => 'application/vnd.oasis.opendocument.chart-template', 'odf' => 'application/vnd.oasis.opendocument.formula', 'otf' => 'application/vnd.oasis.opendocument.formula-template', 'odg' => 'application/vnd.oasis.opendocument.graphics', 'otg' => 'application/vnd.oasis.opendocument.graphics-template',
	'odi' =>  'application/vnd.oasis.opendocument.image', 'oti' => 'application/vnd.oasis.opendocument.image-template', 'odp' => 'application/vnd.oasis.opendocument.presentation', 'otp' => 'application/vnd.oasis.opendocument.presentation-template', 'ods' => 'application/vnd.oasis.opendocument.spreadsheet', 'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template', 'odt' => 'application/vnd.oasis.opendocument.text', 'odm' => 'application/vnd.oasis.opendocument.text-master', 'ott' => 'application/vnd.oasis.opendocument.text-template', 'oth' => 'application/vnd.oasis.opendocument.text-web');
	return $mimes[strtolower($pencarian)];
}
function put()
{
	input_hidden('put', 'put');
}
function del()
{
	input_hidden('del', 'del');
}
function this_get()
{
	if ($_SERVER['REQUEST_METHOD'] != 'GET') {
		if ($_SESSION['SERVER_MODE'] == 'DEVELOPMENT') {
			echo "Hanya mendukung method GET";
		} else {
			view('exception/404.php');
		}
		exit;
	}
}
function this_post()
{
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		if ($_SESSION['SERVER_MODE'] == 'DEVELOPMENT') {
			echo "Hanya mendukung method POST";
		} else {
			view('exception/404.php');
		}
		exit;
	}
}
function this_put()
{
	if ($_SERVER['REQUEST_METHOD'] != 'POST' || $_POST['put'] === NULL) {
		if ($_SESSION['SERVER_MODE'] == 'DEVELOPMENT') {
			echo "Hanya mendukung method PUT";
		} else {
			view('exception/404.php');
		}
		exit;
	}
}
function this_del()
{
	if ($_SERVER['REQUEST_METHOD'] != 'POST' || $_POST['del'] === NULL) {
		if ($_SESSION['SERVER_MODE'] == 'DEVELOPMENT') {
			echo "Hanya mendukung method DELETE";
		} else {
			view('exception/404.php');
		}
		exit;
	}
}
function range_time_elapsed_string($datetime, $datetime2, $full = false)
{
    $now = new DateTime($datetime2);
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Menghitung minggu secara manual
    $totalDays = $diff->days;
    $weeks = floor($totalDays / 7);
    $days = $totalDays % 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    // Memperbarui array string untuk menampilkan minggu dan hari
    $output = array();
    if ($diff->y) {
        $output[] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
    }
    if ($diff->m) {
        $output[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
    }
    if ($weeks) {
        $output[] = $weeks . ' week' . ($weeks > 1 ? 's' : '');
    }
    if ($days) {
        $output[] = $days . ' day' . ($days > 1 ? 's' : '');
    }
    if ($diff->h) {
        $output[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
    }
    if ($diff->i) {
        $output[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    }
    if ($diff->s) {
        $output[] = $diff->s . ' second' . ($diff->s > 1 ? 's' : '');
    }

    if (!$full) {
        $output = array_slice($output, 0, 1);
    }

    return $output ? implode(', ', $output) : 'just now';
}



function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Menghitung minggu secara manual
    $totalDays = $diff->days;
    $weeks = floor($totalDays / 7);
    $days = $totalDays % 7;

    // Menggunakan array untuk menentukan format waktu
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    $output = array();

    // Menyusun string output berdasarkan perhitungan waktu
    if ($diff->y) {
        $output[] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
    }
    if ($diff->m) {
        $output[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
    }
    if ($weeks) {
        $output[] = $weeks . ' week' . ($weeks > 1 ? 's' : '');
    }
    if ($days) {
        $output[] = $days . ' day' . ($days > 1 ? 's' : '');
    }
    if ($diff->h) {
        $output[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
    }
    if ($diff->i) {
        $output[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    }
    if ($diff->s) {
        $output[] = $diff->s . ' second' . ($diff->s > 1 ? 's' : '');
    }

    // Jika $full adalah false, hanya menampilkan bagian pertama
    if (!$full) {
        $output = array_slice($output, 0, 1);
    }

    return $output ? implode(', ', $output) . ' ago' : 'just now';
}
function csrf_token()
{
	return $_SESSION['token'];
}

function sanitize_filename($filename)
{
    // Karakter yang tidak diperbolehkan dalam penamaan file di berbagai sistem operasi
    $invalid_chars = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
    
    // Gantikan karakter yang tidak valid dengan underscore
    return str_replace($invalid_chars, '_', $filename);
}


function file_checker($filepath = './core/404.png', $thumbnail = false, $filename = 'viewer', $download = true)
{
	$image = '';
	if ($thumbnail === true) {
		$compress = true;
	} else {
		$compress = false;
	}


	if (file_exists($filepath)) {
		touch($filepath, filemtime($filepath), time()); // this will just record the time of access in file inode. you can write your own code to do whatever
		$path_parts = pathinfo($filepath);
		switch (strtolower($path_parts['extension'])) {

			case 'mp4':
				header('Content-type: video/mp4');
				break;

			case 'mp3':
				header('Content-type: audio/mp3');
				break;

			case 'wav':
				header('Content-type: audio/wav');
				break;

			case "gif":
				header("Content-type: image/gif");
				($compress) ? $image = imagecreatefromgif($filepath) : '';

				break;

			case "jpg":
				header("Content-type: image/jpeg");
				($compress) ? $image = imagecreatefromjpeg($filepath) : '';
				break;

			case "jpeg":
				header("Content-type: image/jpeg");
				($compress) ? $image = imagecreatefromjpeg($filepath) : '';
				break;

			case "png":
				header("Content-type: image/png");
				($compress) ? $image = imagecreatefrompng($filepath) : '';
				break;

			case "bmp":
				header("Content-type: image/bmp");
				break;
			case "pdf":
				header("Content-type: application/pdf");
				break;

			case 'xlsx':
				header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				// header('Content-type: application/zip');
				// header('Content-type: application/vnd.ms-excel');
				// header('Content-type: application/msword');
				// header('Content-type: application/x-zip');
				break;

			case 'xls':
				header('Content-type: application/vnd.ms-excel');
				// header('Content-type: application/msexcel');
				// header('Content-type: application/x-msexcel');
				// header('Content-type: application/x-ms-excel');
				// header('Content-type: application/application/xls');
				break;
		}

		if ($image != '' && $compress === true) {
			$destination = './tmp/img/' . microtime() . '.jpg';
			imagejpeg($image, $destination, 10);
			$filepath = $destination;
		}

		$info = '.' . pathinfo($filepath)['extension'];
		if ( isset( $_GET['prefname'] ) ) {
            $new_filename = sanitize_filename( base64_decode($_GET['prefname'])). $info;
        } else {
            $new_filename = ($filename != 'viewer') ? $filename . $info : basename($filepath);
        }
        $file_name = $new_filename;

		header('Content-Description: File Transfer');
		header("Accept-Ranges: bytes");
		header('Content-Length: ' . filesize($filepath));
		// header('filename="'.$file_name.'"');

		if ($download) {
			header('Content-Disposition: attachment; filename="' . $file_name . '"');
		}

		header("Last-Modified: Fri, 03 Mar 2004 06:32:31 GMT");
		ob_clean();
		flush();

		if (readfile($filepath)) {
			($image != '' && $compress) ? unlink($destination) : '';
		}
	} else {

		header("HTTP/1.0 404 Not Found");
		header("Content-type: image/png");
		header('Content-Length: ' . filesize("./core/404.png"));
		header('Content-Disposition: attachment; filename="404_notfound.png"');
		header("Accept-Ranges: bytes");
		header("Last-Modified: Fri, 03 Mar 2004 06:32:31 GMT");
		ob_clean();
		flush();
		readfile("./core/404.png");
	}
}


function file_remover($filepath)
{
	if (!file_exists($filepath)) {
		return false;
	}

	if (unlink($filepath)) {
		return true;
	} else {
		return false;
	}
}

function allowed_types($filepath, $allowed_types = [])
{

	if (!file_exists($filepath)) {
		file_checker();
		exit;
	}


	touch($filepath, filemtime($filepath), time()); // this will just record the time of access in file inode. you can write your own code to do whatever
	$path_parts = pathinfo($filepath);
	$fileExtension = $path_parts['extension'];




	$mime_type = mime_content_type($filepath);
	$fileType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filepath);


	$tipe = '';

	$allowedExtentions = [];
	$allowedFileTypes = [];



	foreach ($allowed_types as $allow) {
		if ($allow != '') {

			$tipe = '.' . $allow;

			$mimes = mimes_search($allow);

			array_push($allowedExtentions, $allow);


			if (is_array($mimes)) {
				if (!in_array($mime_type, $mimes) || !in_array($fileExtension, $allowedExtentions)) {
					$cek[] = FALSE;
				} else {
					$cek[] = TRUE;
				}
			} else {
				if ($mime_type != $mimes || !in_array($fileExtension, $allowedExtentions)) {
					$cek[] = FALSE;
				} else {
					$cek[] = TRUE;
				}
			}
		}
	}


	if (in_array(TRUE, $cek)) {
		$cek = TRUE;
	} else {
		$cek = FALSE;
	}


	if ($cek === true) {
		return true;
	} else {
		// return false;
		file_checker();
		exit;
	}
}

try {
	if (isset($_GET['request'])) {
		include "web/request.php";
	} else if (isset($_GET['viewer'])) {
		include "routes/viewer.php";
	} else if (isset($_GET['api'])) {
		include "routes/api.php";
	} else if (isset($_GET['cron'])) {
		$currentTime = date('H:i'); // Format HH:MM
		$currentDay = date('d');
		$currentWeekday = date('N'); // 1 = Senin, 7 = Minggu

		$logFile = __DIR__ . '/logs/schedule/cron.log';


		include "routes/schedule.php";

		/**
		 * Simpan log ke file
		 */
		function log_message($message)
		{
			global $logFile;
			file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
		}

		/**
		 * Fungsi untuk throttle eksekusi
		 */
		function throttleCron($taskName, $limit, $interval)
		{
			$cacheFile = __DIR__ . "/storage/cache/{$taskName}.json";

			// Cek apakah file cache ada
			$cache = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];

			// Filter data yang masih dalam batas waktu
			$cache = array_filter($cache, function ($timestamp) use ($interval) {
				return $timestamp >= time() - $interval;
			});

			// Jika melebihi limit, hentikan eksekusi
			if (count($cache) >= $limit) {
				log_message("⛔ Task '{$taskName}' throttled, skipped.");
				return false;
			}

			// Tambahkan timestamp baru
			$cache[] = time();
			file_put_contents($cacheFile, json_encode($cache));

			return true;
		}

		/**
		 * Jalankan cron sesuai jadwal dan throttle
		 */
		foreach ($GLOBALS['cron_tasks'] as $task) {
			$taskName = $task['taskName'];
			$currentTime = date('H:i');         // Misal: "08:00"
			$currentWeekday = date('l');        // Misal: "Monday"
			$currentDay = date('d');            // Misal: "01"
			$currentDayInt = date('j');         // Misal: 1 (tanpa leading zero)
		
			switch ($task['schedule']) {
				case 'everyMinute':
					$run = true;
					break;
		
				case 'hourly':
					$run = (date('i') == '00');
					break;
		
				case 'daily':
					$run = in_array($currentTime, $task['times']);
					break;
		
				case 'weekly':
					$run = (in_array($currentWeekday, $task['days']) || in_array($currentDay, $task['days']))
						&& in_array($currentTime, $task['times']);
					break;
		
				case 'monthly':
					$isFirstWeek = ($currentDayInt <= 7); // hanya tanggal 1–7
					$run = $isFirstWeek &&
						(in_array($currentWeekday, $task['days']) || in_array($currentDay, $task['days'])) &&
						in_array($currentTime, $task['times']);
					break;
		
				default:
					$run = false;
			}
			
		// 	if ($run && throttleCron($taskName, $task['limit'], $task['interval'])) {
		// 	    require __DIR__ . "/controller/schedule/{$task['taskPath']}.php";
		// 	    log_message("✅ Task '{$taskName}' executed.");
		// 	}
		
			if ($run) {
				require __DIR__ . "/controller/schedule/{$task['taskPath']}.php";
				log_message("✅ Task '{$taskName}' executed.");
			}
		}

		

		
	// 	log_message("Cron Job Running");
		echo json_encode(["status" => "success", "message" => "Cron executed successfully."]);
	} else if (isset($_GET['route'])) {
	
			// Dispatch router
			Router::dispatch($_GET['route'], $_SERVER['REQUEST_METHOD']);

			// Render core HTML jika bukan AJAX request
			if (
				!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
				strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
			) {
				include "core/core.html";
			}

	} else {
		include "web/routes.php";
		// Render core HTML jika bukan AJAX request
		if (
			!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
		) {
			include "core/core.html";
		}

	}

} catch (Exception $e) {
	if (env('SERVER_MODE') == 'PRODUCTION') {
		renderView('errors.error_500', ['title' => '500 - Internal Server Error']);
	} else {
		echo "Error: " . $e->getMessage();
	}
}