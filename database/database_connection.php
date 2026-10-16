<?php
error_reporting(false); session_start();

if (!function_exists('loadEnv')) {
    function loadEnv()
    {
    	// Inisialisasi array ENV jika belum ada
    	if (!isset($_SERVER['ENV'])) {
    		$_SERVER['ENV'] = [
    			'LOAD' => false,
    			'DATA' => [],
    		];
    	}
    
    	// Jika ENV sudah dimuat, kembalikan true
    	if ($_SERVER['ENV']['LOAD']) {
    		return true;
    	}
    
    	// Baca file .env
    	$envFile = file_get_contents('./.env');
    	$lines = explode("\n", $envFile);
    
    	// Filter dan proses baris .env
    	foreach ($lines as $key => $line) {
    		$line = trim($line);
    
    		// Hapus baris kosong dan komentar
    		if ($line === '' || strpos($line, '#') === 0) {
    			unset($lines[$key]);
    			continue;
    		}
    	}
    
    	// Jika jumlah baris sama dengan jumlah data ENV yang dimuat, kembalikan true
    	if (count($lines) == count($_SERVER['ENV']['DATA'] ?? [])) {
    		return true;
    	}
    
    	// Proses setiap baris yang tersisa
    	foreach ($lines as $line) {
    		$line = trim($line);
    
    		if ($line === '' || strpos($line, '#') === 0) {
    			continue;
    		}
    
    		list($key, $value) = explode('=', $line, 2);
    		$value = trim($value);
    
    		// Hapus tanda kutip dari nilai
    		if (preg_match('/^"(.*)"$/', $value, $matches)) {
    			$value = $matches[1];
    		} elseif (preg_match("/^'(.*)'\$/", $value, $matches)) {
    			$value = $matches[1];
    		}
    
    		// Simpan ke ENV
    		putenv("$key=$value");
    		$_SERVER['ENV']['DATA'][$key] = $value;
    	}
    
    	// Tandai bahwa ENV sudah dimuat
    	$_SERVER['ENV']['LOAD'] = true;
    
    	return true;
    }
}

if (!function_exists('writeEnv')) {
    function writeEnv($key, $value)
    {
        $env_file = './.env';

        // load existing env variables
        $env_variables = $_SERVER['ENV']['DATA'];



        // check if key already exists
        if (isset($_SERVER['ENV']['DATA'][$key])) {


            // update existing value
            $env_variables[$key] = $value;
        } else {
            // add new key-value pair
            $env_variables[$key] = $value;
        }

        // write updated env variables to env file
        $handle = fopen($env_file, 'w');
        foreach ($env_variables as $key => $value) {
            fwrite($handle, "$key=$value\n");
        }
        fclose($handle);

        // update $_SERVER variable
        $_SERVER['ENV']['DATA'] = $env_variables;

        return true;
    }
}

if (!function_exists('getloadEnv')) {
    function getloadEnv($key = '')
    {

        // Load the contents of the .env file
        $envFile = file_get_contents('./.env');

        // Parse each line of the .env file
        $lines = explode("\n", $envFile);
        foreach ($lines as $key => $line) {
            // Ignore comments and empty lines
            if (trim($line) == '' || strpos(trim($line), '#') === 0) {
                unset($lines[$key]);
                continue;
            }
        }


        if ($key == '') {
            return $lines;
        } else {
            return $lines[$key] ?? null;
        }
    }
}

if (!function_exists('env')) {
    function env($key)
    {
        return $_SERVER['ENV']['DATA'][$key] ?? getloadEnv($key) ?? NULL;
    }
}


if (!function_exists('dump_to_file')) {
    function dump_to_file($nama, $isi)
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
}


if (function_exists('loadEnv')) {
    loadEnv();
}


define('BASE_PATH', env('BASE_PATH') ?? 'localhost');

define('DB_HOST', env('DB_HOST') ?? 'localhost');
define('DB_DRIVER', env('DB_DRIVER') ?? 'mysql');
define('DB_PORT', env('DB_PORT') ?? 3306);
define('DB_USERNAME', env('DB_USERNAME') ?? 'root');
define('DB_NAME', env('DB_NAME') ?? 'my_internship');
define('DB_PASSWORD', env('DB_PASSWORD') ?? '');
define('DB_CHARSET', env('DB_CHARSET') ?? 'utf8mb4');
define('DB_COLLATION', env('DB_COLLATION') ?? 'utf8mb4_general_ci');
define('DB_PREFIX', env('DB_PREFIX') ?? '');

try {
    if (version_compare(PHP_VERSION, '8.0.0') < 0) {
        $koneksi_db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
        if ($koneksi_db->connect_errno) {
            throw new Exception($koneksi_db->connect_error);
        } else {
            $koneksi_pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $koneksi_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $koneksi_pdo->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
        }
    } else {
        $koneksi_pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $koneksi_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $koneksi_pdo->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
    }
    
    $_SERVER['PDO'] = $koneksi_pdo ?? NULL;
    $_SERVER['KONEKSI_PDO'] = $koneksi_pdo ?? NULL;
    
    $_SERVER['KONEKSI_DB'] = $koneksi_db;
    $_SERVER['DB'] = $koneksi_db;

} catch (Exception $e) {
    dump_to_file('database_log', "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n" . " Status : Error Access \n" .
    "Error : " . $e->getMessage() . " \n Server : " . $_SERVER['SERVER_NAME'] . " \n\n");
    view('exception/error_503');
    exit;
}

// if ($_SERVER['SCRIPT_NAME'] == '/index.php') {
//     foreach ($_GET as $key => $value) {
//         $payload .= $key . " => " . $value . ", ";
//     }
//     dump_to_file('access_log', "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n" . " Status : Access Log \n" . "From : " . getBrowser() . " \n Payload : $payload \n\n" );
// }

// if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
//     $_SERVER['USER_AGENT'] = 'Mobile';
// }
