<?php
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

header("Content-Type: text/html; charset=utf-8");
header("X-Content-Type-Options: nosniff;");

include "database/config.php";
include "database/database_connection.php";

if ($_SERVER['ON_MAINTENANCE']) {
	if (!$_SESSION['ADMIN_MAINTENANCE']) {
		view('exception/maintenance');
		exit;
	}
}

if (isset($_SERVER['SECURE_ACCESS'])) {

	if ($_SERVER['SECURE_ACCESS'] === TRUE) {

		if (stripos($_SERVER['HTTP_HOST'], 'localhost') && $_SERVER['REQUEST_SCHEME'] != 'https') {
			// if ( $_SERVER['REQUEST_SCHEME'] != 'https' ) {
			view('exception/secure_access');
			exit;
		}
	}
}





include "web/api_routes.php";

/**
 * To clear SESSION['old'], SESSION['error'], SESSION['notifikasi']
 * and SESSION['custom'] value.
 */
if (!isset($_SESSION['no_flash']) || $_SESSION['no_flash'] === FALSE) {
	reflash('old');
	reflash('error');
	reflash('notifikasi');
	reflash('custom');
}


function escape_val($val = '')
{
	$escape = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $val);
	return $escape;
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

    // dump_to_file('api_log', " Status Code :  $http_code " . ' ; ' . getBrowser() . ' ; ');
    
    
        dump_to_file('api_log', " Status Code :  $http_code " . ' ; ' . getBrowser() . ' ; \n' . 'Payload : ' .$data );



	echo json_encode($new_data, JSON_PRETTY_PRINT);
	exit;
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

/**
 * To change Owner or Group from file or folder.
 * 
 * @param	string		$lokasi		Lokasi Folder.
 * @param	string		$owner		Owner dari folder.
 * @param	string		$jenis		Menentukan apakah yang diganti group ataukah owner, default value adalah "both".
 * @return	bool
 */
function ff_own_grp(string $lokasi, string $owner, string $jenis = "both")
{

	if ($jenis == "group" || $jenis == "grp") {
		chgrp($lokasi, $owner);
	} else

	if ($jenis == "owner" || $jenis == "own") {
		chown($lokasi, $owner);
	} else {
		chgrp($lokasi, $owner);
		chown($lokasi, $owner);
	}
}


/**
 * To change permission from a folder.
 * 
 * @param	string		$lokasi				Lokasi Folder.
 * @param	string		$permission_mod		Permission Mod yang diinginkan.
 * @return	bool
 */
function ff_permission(string $lokasi, string $permission_mod)
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


/**
 * Go back to previous page.
 * 
 * Meminta value "previous" untuk kembali ke halaman sebelumnya, sesuai dengan 
 * jumlah halaman sebelumnya yang diinginkan.
 * 
 * @param	int		$previous	Jumlah Previous Page yang diinginkan.
 * @return	void	Javascript-History.go
 */
function back(int $previous = 1, $timer = 1)
{
	// if ( $_SESSION['old']['have-toast'] === TRUE ) : $timer = 1; endif;
	echo "<script> window.setTimeout(function(){ window.location.replace = history.go(-$previous); }, $timer); </script>";
}


/**
 * Close current window.
 * 
 * Meminta value "previous" untuk kembali ke halaman sebelumnya, sesuai dengan 
 * jumlah halaman sebelumnya yang diinginkan.
 * 
 * @param	int		$timer	Jumlah waktu yang diinginkan, default timer adalah 2000.
 * @return	void	Javascript-Windows.close
 */
function close(int $timer = 2000)
{
	echo "<script> window.setTimeout(function(){ window.close(); }, $timer);</script>";
}




/**
 * Check uploaded file for maximum file size.
 * 
 * Meminta value "maks_upload" untuk melakukan pengecekan, apakah 
 * file yang diupload sesuai dengan batas maksimum upload file.
 * 
 * Meminta value "lokasi" untuk melakukan pengecekan, apakah
 * folder yang dituju sudah ada atau belum. Jika belum ada maka akan 
 * membuat folder baru dengan permission mod 0775
 * 
 * @param	int			$maks_upload	Maksimal ukuran file yang diperbolehkan, default adalah 3 MB.
 * @param	string		$lokasi			Lokasi upload file yang baru.
 * @return	bool
 */
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


/**
 * Get PHP last error.
	
 * Meminta value $jenis sebagai parameter yang
 * akan menentukan dev_mode() apa yang akan ditampilkan
 * 
 * @param	string		$jenis		FILES | POST | ERROR | NULL
 * @return	array
 */
function dev_mode(string $jenis = "")
{

	if (stripos($jenis, 'FILES') !== FALSE) {
		highlight_string("<?php \$_FILES : \r\n  " . var_export($_FILES, true) . " \r\n \r\n ");
	} else

	if (stripos($jenis, 'POST') !== FALSE) {
		highlight_string("<?php \$_POST : \r\n " . var_export($_POST, true) . "\r\n \r\n ");
	} else

	if (stripos($jenis, 'ERROR') !== FALSE) {
		highlight_string("<?php \LAST_ERROR : \r\n " . var_export(error_get_last(), true) . "\r\n \r\n \r\n");
	} else {
		highlight_string("<?php POST | \$_POST : \r\n " . var_export($_POST, true) . " \r\n \r\n \r\n");
		highlight_string("<?php FILES | \$_FILES : \r\n  " . var_export($_FILES, true) . " \r\n \r\n \r\n");

		highlight_string("<?php SESSION | \$_SESSION : \r\n  " . var_export($_SESSION, true) . " \r\n \r\n \r\n");
		highlight_string("<?php PHP LAST_ERROR | LAST_ERROR : \r\n " . var_export(error_get_last(), true) . "\r\n \r\n \r\n");
		highlight_string("<?php ENV | \$_ENV : \r\n  " . var_export($_ENV, true) . " \r\n \r\n \r\n");
		highlight_string("<?php SERVER | \$_SERVER : \r\n  " . var_export($_SERVER, true) . " \r\n \r\n \r\n");
		highlight_string("<?php ALL | GET ALL VAR DEFINED BY USER : \r\n  " . var_export(get_defined_vars(), true) . " \r\n \r\n \r\n");
	}
}


/**
 * Get Database last error.
	
 * Meminta variabel koneksi database $koneksi_db sebagai parameter 
 * untuk membuat koneksi ke database dan mengambil error yang terjadi.
 * Meminta variabel mode untuk menentukan mode yang akan ditampilkan,
 * default mode nya adalah ERROR
 * 
 * @param	object		$koneksi_database		Object Database Connection.
 * @param	string		$mode					Mode yang akan ditampilkan, ERROR | ALL.
 * @return	array
 */
function dev_db(object $koneksi_db, string $mode = "ERROR")
{

	if ($mode == "ERROR") {
		return var_dump(mysqli_error($koneksi_db));
	} else {
		return var_dump($koneksi_db);
	}
}


/**
 * Delete file from /upload folder.
	
 * Meminta "lokasi file" serta "nama file" yang akan dihapus
 * dari folder /upload
 * 
 * @param	string		$lokasi			Folder tempat file diupload kecuali /upload.
 * @param	string		$nama_file		Nama File yang akan dihapus lengkap dengan ekstensi file.
 * @return	bool
 */
function hapus_upload(string $lokasi, string $nama_file)
{

	$root = $_SERVER['DOCUMENT_ROOT'];
	$root = "";

	$lokasi_file = $root . "upload/" . $lokasi . "/" . $nama_file;

	chmod($lokasi_file, 0777);

	$hapus_file = unlink($lokasi_file);


	if ($hapus_file === TRUE) {
		return true;
	} else {
		return false;
	}
}


/**
 * Move sanitize uploaded file and move it to /upload folder.
 * Only work with pdf file !!
	
 * Meminta "lokasi", "nama input", "nama file baru" serta "maksimal upload" 
 * dari file yang akan diupload kemudian mengganti nama file dengan yang baru, melakukan 
 * pengecekan error saat upload dan memindahkan file ke lokadi baru yang ditunjuk. 
 * 
 * @param	string		$lokasi				Folder tempat file diupload kecuali /upload.
 * @param	string		$nama_input			Name dari file upload.
 * @param	string		$nama_file			Nama File yang baru. Default value adalah random.
 * @param	int			$maksimal_upload	Maksimal ukuran file yang diperbolehkan, default 3MB.
 * @return	bool
 */
function upload(string $lokasi, string $nama_input, string $nama_file = 'random', int $maksimal_upload = 3)
{

	$owner = "webadmin";


	if ($nama_file == 'random') {
		// $name = rand(0,9999).$_FILES[$nama_input]['name'];
		$nama_file = escape(uniqid() . random_strings(15) . ".pdf");
		$name = $nama_file;
	} else {
		$nama_file = preg_replace('/\s+/', '_', escape($nama_file));
		$name = escape($nama_file . random_strings(10) . ".pdf");
	}
	$size = $_FILES[$nama_input]['size'];
	$type = $_FILES[$nama_input]['type'];
	$temp = $_FILES[$nama_input]['tmp_name'];

	// $lokasi_server
	// $root = $_SERVER['DOCUMENT_ROOT'];
	// $root = "";
	$folder = "upload/$lokasi/";
	// $folder = "/mnt/data/www/my_internship/upload/" .$lokasi. "/";



	if (!is_dir($folder)) {

		if (mkdir($folder) === false) {
			// var_dump( $folder ); exit;
			// dev_mode( 'error'); exit;
			error($nama_input, "Gagal Upload File mkdir !");
			return false;
		}
		chmod($folder, 0777);
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

/**
 * Give you hidden token in #html form tag.
 * Always declare it when you submitted any form !!
 * 
 * @return	void	html-hidden-csrf
 */
function csrf()
{
	$token = $_SESSION['token'];
	echo '<input type="hidden" name="token" value="' . $token . '" />';
}


function dev()
{
	echo '<input type="hidden" name="DEVELOPMENT" value="DEVELOPMENT" />';
}



/**
 * Simple way to give you any html input hidden atribut.
 * 
 * @param	string				$nama_input		Input Name yang diinginkan.
 * @param	string | array		$value_input	Input Value yang diinginkan.
 * @return	void				html-hidden-$nama_input
 */
function input_hidden(string $nama_input = '', $value_input = '', string $id_input = '')
{

	if ($id_input == '') {
		$id_input = $nama_input;
	} else {
		$id_input = $id_input;
	}

	echo '<input type="hidden" id="' . $id_input . '" name="' . $nama_input . '" value="' . $value_input . '" />';
}



/**
 * Primarily to give you file in /view folder, Check if file exist, 
 * then give you include file in /view folder if exist.
 * If doesnt exit, give you error view message.
 * 
 * @param	string		$lokasi		Lokasi file view tanpa ekstensi (.php).
 * @return	void		include
 */
function view(string $lokasi = "")
{

	if (!is_file("./view/" . $lokasi . ".php")) {
		include "./view/exception/error_view.php";
	} else {
		include "./view/" . $lokasi . ".php";
	}
}



/**
 * Primarily to give you file in /controller folder, Check if file exist, 
 * then give you include file in /controller folder if exist.
 * If doesnt exit, give you error controller message.
 * 
 * @param	string		$lokasi		Lokasi file controller tanpa ekstensi (.php).
 * @return	void		include
 */
function controller(string $nama_controller = "")
{

	if (!is_file("./controller/" . $nama_controller . ".php")) {
		return include "./view/exception/error_controller.php";
	} else {
		return include "./controller/" . $nama_controller . ".php";
	}
}


// Function to get the client IP address
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


function api(string $nama_controller = "", $method_type = 'POST')
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


	switch ($method_type) {
		default:
			echo json_encode([
				'welcome_message' => $_SERVER['API_WELCOME'],

				'status' => 0, 'message' => 'Method not available !'
			], JSON_PRETTY_PRINT);
			http_response_code(405);
			exit;
			break;

		case 'ALL':
			break;

		case 'POST':
			if ($_SERVER['REQUEST_METHOD'] != 'POST') {
				response_json( 405 , ['error_code' => 405, 'status' => 0, 'message' => 'Only POST Method is supported !']  );
			}

			break;

		case 'GET':
			if ($_SERVER['REQUEST_METHOD'] != 'GET') {
				response_json( 405 , ['error_code' => 405, 'status' => 0, 'message' => 'Only GET Method is supported !']  );
			}
			break;

		case 'DEL':
			if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['DEL'])) {
				response_json( 405 , ['error_code' => 405, 'status' => 0, 'message' => 'Only DELETE Method is supported !']  );
			}
			break;

		case 'PUT':
			if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['PUT'])) {
				response_json( 405 , ['error_code' => 405, 'status' => 0, 'message' => 'Only PUT Method is supported !']  );
			}
			break;
	}

	// var_dump( get_client_ip() ); exit;

	if (!in_array($method_type, ['GET', 'ALL'])) {

		$token  = escape_val($_POST['token']);

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $token == '') {
			response_json( 401 , ['error_code' => 401, 'status' => 0, 'message' => 'Token Not Found !', 'additional_message' => 'Excuse me ! Who are you ?' ]  );
		}

		$sql = "SELECT * FROM access_token WHERE token = '$token'";
		$sql_token = $_SERVER['DB']->query($sql);
		$cek_token = $sql_token->num_rows;

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $cek_token != 1) {
			response_json( 401 , ['error_code' => 401, 'status' => 0, 'message' => 'Invalid Token !', 'additional_message' => 'Excuse me ! Who are you ?' ]  );
		}

		$token_data = $sql_token->fetch_assoc();

		$sql = "UPDATE access_token SET connection_count = connection_count+1 WHERE token = '$token'";
		$exec = $_SERVER['DB']->query($sql);

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $token_data['token_revoke'] != 0) {

			$last_connection = getBrowser() . ' [Revoke Access]';
			$sql = "UPDATE access_token SET last_connection = '$last_connection' WHERE token = '$token'";
			$exec = $_SERVER['DB']->query($sql);

			echo json_encode([
				'welcome_message' => $_SERVER['API_WELCOME'],

				'status' => 0, 'message' => 'Token have been revoke !', 'additional_message' => 'Ask for a new one at helpdesk'
			], JSON_PRETTY_PRINT);
			http_response_code(401);
			exit;
		}

		$allowed_address = explode(';', $token_data['allowed_address']);

		if (get_client_ip() != '::1' && $_SERVER['REQUEST_METHOD'] == 'POST' && !in_array($token_data['allowed_address'], ['Any', 'any', 'ANY']) && !in_array(get_client_ip(), $allowed_address)) {
			$last_connection = getBrowser() . ' [Unwanted Access]';
			$sql = "UPDATE access_token SET last_connection = '$last_connection' WHERE token = '$token'";
			$exec = $_SERVER['DB']->query($sql);

			echo json_encode([
				'welcome_message' => $_SERVER['API_WELCOME'],

				'status' => 0, 'message' => 'Access from this address is not allowed !', 'additional_message' => 'Ask for a new token at helpdesk'
			], JSON_PRETTY_PRINT);
			http_response_code(401);
			exit;
		}
	}



	if (!is_file("./api/" . $nama_controller . ".php")) {
		echo json_encode([
			'welcome_message' => $_SERVER['API_WELCOME'],

			'status' => 0, 'message' => 'API not available !'
		], JSON_PRETTY_PRINT);
		http_response_code(404);
		exit;
		// return include "./view/exception/error_api.php";
	} else {
		include "./api/" . $nama_controller . ".php";
	}
}

/**
 * Primarily to give you file in /controller folder, Check if file exist, 
 * then give you include file in /controller folder if exist.
 * If doesnt exit, give you error controller message.
 * 
 * @param	string		$lokasi		Lokasi file controller tanpa ekstensi (.php).
 * @return	void		include
 */
function ajax(string $nama_ajax = "")
{

	if (!is_file("./ajax/" . $nama_ajax . ".php")) {
		return include "./view/exception/error_controller.php";
	} else {
		return include "./ajax/" . $nama_ajax . ".php";
	}
}




/**
 * To check if user had login and had role.
 * If user doesn't, return to back to last opened page.
 *  
 * @param	string		$role		Lokasi file controller tanpa ekstensi (.php).
 * @return	void		include
 */
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


/**
 * To emptied all S_SESSION message.
 */
function flash_all()
{
	return $_SESSION['no_flash'] = FALSE;
}


/**
 * To check if token exits on submitted form.
 *  
 * @return	bool
 */
function cek_token()
{

	if ($_POST['token'] != $_SESSION['token']) {
		return false;
	} else {
		return true;
	}
}



/**
 * Make $_SESSION['old'] ['variable_name'].
 *  
 * @param	string		$val				Value old().
 * @return	void		$_SESSION['old'] [$val]
 */
function go_old(string $val = "")
{
	return $_SESSION['old'][$val] = $_SESSION['old'][$val];
}



/**
 * Call $_SESSION['old'] ['variable_name'].
 *  
 * @param	string		$val				Value old().
 * @return	void		$_SESSION['old'] [$val]
 */
function old(string $val = "", $new_val = "")
{
	go_old($val);

	if (in_array($_SESSION['old'][$val], [NULL, ''])) {

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


/**
 * To emptied $_SESSION ['variable_name'].
 */
function reflash(string $val = "")
{
	return $_SESSION[$val] = [];
}


/**
 * To show #html input error message.
 * It give you $_SESSION['error'] ['variable_name'] message.
 * If #html input is valid then it will return is-valid border-success.
 * If #html input is invalid then it will return is-invalid border-danger.
 */
function show_error(string $nama_val = "")
{
	echo $_SESSION['error'][$nama_val];
}


/**
 * To show #html input additional error messages.  
 * It give you $_SESSION['notifikasi'] ['variable_name'] message.
 * You can make notifikasi by utilizing success() or error() function.
 */
function show_notif(string $nama_val = "")
{
	echo $_SESSION['notifikasi'][$nama_val];
}


/**
 * To check if #html input is valid or invalid. 
 */
function is_error(string $nama_val = "")
{
	return isset($_SESSION['error'][$nama_val]);
}


/**
 * Make form error notification.
 * e.g. Register Gagal !
 */
function make_error_notif(string $error = "")
{
	$_SESSION['notifikasi']['error'] = $error;
}


/**
 * To show form overall notification. 
 */
function is_notif()
{
	return isset($_SESSION['notifikasi']['error']);
}


/**
 * To print form overall notification. 
 */
function is_notif_show()
{
	echo $_SESSION['notifikasi']['error'];
}


/**
 * To make #html input has error messages.
 * It based on #html input name
 */
function error(string $nama_val = "", string $error = "")
{

	$_SESSION['notifikasi'][$nama_val] = $error;
	$_SESSION['error'][$nama_val] = 'is-invalid border-danger';
}


/**
 * To make #html input has success messages.
 * It based on #html input name
 */
function success(string $nama_val = "", string $notif = "")
{

	$_SESSION['notifikasi'][$nama_val] = $notif;
	$_SESSION['error'][$nama_val] = 'is-valid border-success';
}


/**
 * To make $_SESSION ['old'] ['variable_name']
 */
function make_old(string $nama_val = "", $val = '')
{
    

	return $_SESSION['old'][$nama_val] = $val;
}


/**
 * Fastest way to make all #html input to $_SESSION ['old'] ['variable_name]
 */
function to_old()
{

	$_SESSION['old'] = [];
	$_SESSION['error'] = [];

	foreach ($_POST as $name => $value) {
		$_SESSION['old'][$name] = $value;
	}

	foreach ($_FILES as $name => $value) {
	}
}


/**
 * Give you ?? length random character from A to 0.
 */
function random_strings(int $panjang_karakter = 10)
{
	$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	return substr(
		str_shuffle($str_result),
		0,
		$panjang_karakter
	);
}


/**
 * Give you database error logs.
 * Error log can be found in /logs/error_database.txt
 */
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


/**
 * Sanitize file name from file name error.
 * Always use this when you uploading any file.
 */
function escape(string $value = "")
{
	$return = '';
	for ($i = 0; $i < strlen($value); ++$i) {
		$char = $value[$i];
		$ord = ord($char);
		if ($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126)
			$return .= $char;
		else
			$return .= '\\x' . dechex($ord);
	}
	return $return;
}


/**
 * Give you current user browser.
 */
function getBrowser()
{
	$location = $_SERVER['REMOTE_ADDR'];
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$bname = 'Unknown';
	$platform = 'Unknown';
	$version = "";

	//First get the platform?
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'linux';
	} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'mac';
	} elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'windows';
	}

	// Next get the name of the useragent yes seperately and for good reason
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

	// finally get the correct version number
	$known = array('Version', $ub, 'other');
	$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if (!preg_match_all($pattern, $u_agent, $matches)) {
		// we have no matching number just continue
	}
	// see how many we have
	$i = count($matches['browser']);
	if ($i != 1) {
		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
			$version = $matches['version'][0];
		} else {
			$version = $matches['version'][1];
		}
	} else {
		$version = $matches['version'][0];
	}

	// check if we have a number
	if ($version == null || $version == "") {
		$version = "?";
	}

	// return array(
	//   'userAgent' => $u_agent,
	//   'name'      => $bname,
	//   'version'   => $version,
	//   'platform'  => $platform,
	//   'pattern'   => $pattern,
	//   'location'  => $location,
	// );

	return $u_agent . " " . $version . " di " . $platform . " pada " . $location;
}


/**
 * Emptied all $_SESSION => ['old] , ['error'] ['notifikasi'] , ['custom'] value.
 */
function reflash_all()
{
	reflash('old');
	reflash('error');
	reflash('notifikasi');
	reflash('custom');

	return true;
}


/**
 * Give you Sweetalert 2 Toast.
 */
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




/**
 * Redirect you to given page.
 * Give your redirect location to redirect ( $page ); 
 */
function redirect(string $page = "", int $timer = 1000)
{

	// /if ( $_SESSION['old']['have-toast'] === TRUE ) : $timer = 1; endif;


	if ($page == "") {
		echo "<script> window.setTimeout(function(){ window.location = 'index.php'; }, $timer); </script>";
	} else 
	if ($page == "back" || $page == "BACK") {
		echo "<script> window.setTimeout(function(){ window.location.replace = history.go( -1 ); }, $timer); </script>";
	} else {
		echo "<script> window.setTimeout(function(){ window.location = 'index.php?page=$page'; }, $timer); </script>";
	}
}


/**
 * To redirect you back to previous page.
 * Equivalent with back() function.
 */
function redirect_back(int $timer = 1000, int $previous = 1)
{

	// if ( $_SESSION['old']['have-toast'] === TRUE ) : $timer = 1; endif;
	echo "<script> window.setTimeout( function(){ window.location.replace = history.go( -$previous ) ; }, $timer); </script>";
}

function redirect_referrer(int $timer = 1000, int $previous = 1)
{

	// if ( $_SESSION['old']['have-toast'] === TRUE ) : $timer = 1; endif;
	echo "<script> window.setTimeout( function(){ window.location = document.referrer ; }, $timer); </script>";
}



/**
 * To close current open window.
 */
function close_current_window(int $timer = 2000)
{
	echo "<script>window.setTimeout(function(){ window.close();}, $timer )</script>";
}



/**
 * Redirect you to given page.
 * Give your redirect location to redirect ( $page ); 
 */
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



/**
 * Make Custom Session Atribut.
 * Can be accessed with $_SESSION['custom'] [$konteks] 
 */
function make_custom_notif(string $konteks = "", string $error = "")
{
	$_SESSION['custom'][$konteks] = $error;
}


/**
 * Make Formatted Number.
 */
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



/**
 * Delete from table.
 *  
 * @param	mysqli		$koneksi_db				Parameter koneksi database.
 * @param	string		$table_name				Nama tabel yang akan di insert.
 * @param	string		$attribute				Atribut yang dicari.
 * @param	string		$value					Value dari atribut yang dicari.
 * @param	string		$mode					auto | NULL untuk melihat query yang akan dijalankan.
 * @return	bool
 */
function table_delete(string $table_name = "", string $attribute = "", string $value = "", string $mode = 'auto')
{

	$sql = "DELETE FROM `$table_name` WHERE `$attribute` = '$value' ";

	if ($mode != 'auto') {
		return $sql;
	}

	$table_delete = mysqli_query($_SERVER['KONEKSI_DB'], $sql);


	return $table_delete;
}


/**
 * Delete from table.
 *  
 * @param	mysqli			$koneksi_db					Parameter koneksi database.
 * @param	string			$table_name					Nama tabel yang akan di insert.
 * @param	string,array	$where_delete				Array atau String Where to delete.
 * @param	string			$mode						auto | NULL untuk melihat query yang akan dijalankan.
 * @return	bool
 */
function table_delete2(string $table_name = "", $where_delete = '', string $mode = 'auto')
{


	if (is_array($where_delete)) {
		$arr_where = array_keys($where_delete);
		$where = "";
		foreach ($arr_where as $arr_where2) {

			$atr_where2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $arr_where2);
			$val_where2 = $where_delete[$arr_where2];
			// $val_where2 = mysqli_real_escape_string( $koneksi_db, $where_delete[$arr_where2] );


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



/**
 * Insert data to table.
 *  
 * @param	string			$nama_tabel				Nama tabel yang akan di insert.
 * @param	array,string	$value_insert			Array Atribut dan Value yang akan di insert atau String Values yang akan diinput.
 * @param	string			$mode					auto | null untuk melihat query yang akan dijalankan.
 * @return	bool
 */
function table_insert(string $nama_tabel = "", $value_insert = '', string $mode = 'auto')
{


	if (is_array($value_insert)) {

		// Memecah Value dan Atribut dari array.
		$atr = array_keys($value_insert);
		$attribute = "";
		$value = "";
		foreach ($atr as $atr2) {

			$atr2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $atr2);


			if (array_key_last($value_insert) == $atr2) {
				$attribute .= "`$atr2`";

				if ($value_insert[$atr2] !== NULL) {
					$val2 = $value_insert[$atr2];
					// $val2 = mysqli_real_escape_string( $koneksi_db, $value_insert[$atr2] );

					$value .= "'$val2'";
				} else {
					$value .= "NULL";
				}
			} else {

				$attribute .= "`$atr2`" . ",";

				if ($value_insert[$atr2] !== NULL) {
					// $val2 = mysqli_real_escape_string( $koneksi_db, $value_insert[$atr2] );
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
		$sql = "INSERT INTO `$nama_tabel` $value_insert";
	}


	// Mode dev
	if ($mode != 'auto') {
		echo "Query : " . $sql;
		exit;
	}

	$table_insert = mysqli_query($_SERVER['KONEKSI_DB'], $sql);


	return $table_insert;
}



/**
 * Update data from table.
 *  
 * @param	string			$nama_tabel				Nama tabel yang akan di insert.
 * @param	array			$value_update			Array dari Atribut dan Value yang akan di insert.
 * @param	array,string	$where_update			Array dari Where atau String Where yang akan di update.
 * @param	string			$mode					auto | null untuk melihat query yang akan dijalankan.
 * @return	bool
 */
function table_update(string $nama_tabel = "", array $value_update = [], $where_update = '',  string $mode = 'auto')
{

	$atr = array_keys($value_update);
	$set = "";
	foreach ($atr as $atr2) {

		if (array_key_last($value_update) == $atr2) {

			$atr2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $atr2);
			$val2 = $value_update[$atr2];
			// $val2 = mysqli_real_escape_string( $koneksi_db, $value_update[$atr2] );
			$set .= "`$atr2` = '$val2' ";
		} else {

			$atr2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $atr2);
			$val2 = $value_update[$atr2];
			// $val2 = mysqli_real_escape_string( $koneksi_db, $value_update[$atr2] );
			$set .= "`$atr2` = '$val2', ";
		}
	}


	if (is_array($where_update)) {

		$arr_where = array_keys($where_update);
		$where = "";
		foreach ($arr_where as $arr_where2) {

			if (array_key_last($where_update) == $arr_where2) {

				$atr_where2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $arr_where2);
				$val_where2 = $where_update[$arr_where2];
				$where .= "`$atr_where2` = '$val_where2' ";
			} else {

				$atr_where2 = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $arr_where2);
				$val_where2 = $where_update[$arr_where2];
				$where .= "`$atr_where2` = '$val_where2' AND ";
			}
		}
	} else {
		$where_update = $where_update;
		$where = $where_update;
	}


	$sql = "UPDATE `$nama_tabel` SET $set WHERE $where;";

	if ($mode != 'auto') {
		echo "Query :" . $sql;
		exit;
	}

	$table_update = mysqli_query($_SERVER['KONEKSI_DB'], $sql);


	return $table_update;
}



/**
 * Search data to table.
 *  
 * @param	string			$nama_tabel				Nama tabel yang akan di insert.
 * @param	array,string	$where					Array Atribut dan Value yang akan di cari atau String Where yang akan dicari.
 * @param	string			$mode					auto | null untuk melihat query yang akan dijalankan.
 * @return	bool
 */
function table_search(string $nama_tabel = "", $where = '', string $mode = 'auto')
{

	if (is_array($where)) {

		// Memecah Value dan Atribut dari array.
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


	// Mode dev
	if ($mode != 'auto') {
		echo "Query : " . $sql;
		exit;
	}

	$table_search = mysqli_query($_SERVER['KONEKSI_DB'], $sql);


	return $table_search;
}

/**Form Validate */

/**
 * Set rules untuk $_POST or $_FILES variable.
 *  
 * @param	string		$nama_atribut				Nama Atribute atau name=''.
 * @param	string		$paggilan_atribut			Nama Panggilan untuk Atribut.
 * @param	array		$rules						Rules yang ingin diterapkan.
 * @param	array		$custom_message				Custom Message yang ingin ditampilkan.
 * 
 * @return	bool
 */
function set_rules(string $nama_atribut = '', string $panggilan_atribut = '', array $rules = [], array $custom_message = [], string $lang = 'ID')
{

	$_SESSION['set_rules'][$nama_atribut] = array(
		'panggilan' => $panggilan_atribut,
		'rules' => $rules,
		'custom-message' => $custom_message,
		'lang' => $lang,
	);

	return true;
}

/**
 * Untuk menyederhanakan byte.
 *  
 * @param	int		$bytes				Byte yang ingin di konvert.
 * 
 * @return	string
 */
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


/**
 * Untuk melakukan proses validasi form.
 * 
 * Secara default, melakukan validasi required.
 * Jika ada set_rules, maka validasi dilakukan sesuai
 * dengan rules yang telah di set.
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @return	bool
 */
function form_validation(int $with = 1)
{

	
	$_SESSION['error'] = [];

	$hasil_validasi = [];

	foreach ($_SESSION['set_rules'] as $name => $value) {

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

		// else
		// if ( $_POST[$name] == "" ) {

		// 	error( $name , 'Tidak boleh kosong !');
		// 	$hasil_validasi[$name][] = false;
		// }


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

	return $cek_form;
}



function validate_all(int $with = 1)
{

	$_SESSION['error'] = [];

	$hasil_validasi = [];

	foreach ($_POST as $name => $value) {

		if (is_null($_POST[$name]) || $_POST[$name] == "") {

			if ($_SESSION['set_rules'][$name]['custom-message']) {
				error($name,  $_SESSION['set_rules'][$name]['custom-message']);
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
				error($name,  $_SESSION['set_rules'][$name]['custom-message']);
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

	$_SESSION['set_rules'][$nama_atribut] = array(
		'custom-message' => $custom_message,
		'lang' => $lang,
	);

	return true;
}



/**
 * Untuk melakukan pengecekan apakah ada file yang diupload.
 *  
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_FILES yang ingin di validasi.
 * 
 * @return	bool
 */
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


/**
 * Untuk melakukan pengecekan apakah file yang diupload
 * melebihi batas ukuran upload file yang telah ditetapkan.
 *  
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_FILES yang ingin di validasi.
 * @param	mixed|int			$maxsize			Ukuran maksimal file yang diupload dalam Byte.
 * 
 * @return	bool
 */
function files_max_size($name, $max_size)
{

	if (stripos($max_size, 'kb') !== false) {
		$max_size = $max_size * 1024;
	} else

	if (stripos($max_size, 'mb') !== false) {
		$max_size = $max_size * 1024 * 1024;
	} else 

	if (stripos($max_size, 'gb') !== false) {
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



/**
 * Untuk melakukan pengecekan apakah file yang diupload
 * kurang dari batas ukuran upload file yang telah ditetapkan.
 *  
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_FILES yang ingin di validasi.
 * @param	mixed|int			$minsize			Ukuran minimal file yang diupload dalam Byte.
 * 
 * @return	bool
 */
function files_min_size($name, $min_size)
{

	if (stripos($min_size, 'kb') !== false) {
		$min_size = $min_size * 1024;
	} else

	if (stripos($min_size, 'mb') !== false) {
		$min_size = $min_size * 1024 * 1024;
	} else 

	if (stripos($min_size, 'gb') !== false) {
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


/**
 * Untuk melakukan pengecekan apakah file yang diupload
 * sesuai dengan ekstensi yang diminta.
 *  
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_FILES yang ingin di validasi.
 * @param	mixed|string		$allowed_types		Ekstensi yang diperbolehkan; jpg|png|jpeg.
 * 
 * @return	bool
 */
function files_allowed_types($name, $allowed_types)
{
	if ($_FILES[$name]['error'] == 0) {

		$mime_type = mime_content_type($_FILES[$name]['tmp_name']);

		$allowed = explode('|', $allowed_types);
		$tipe = '';

		foreach ($allowed as $allow) {

			$tipe .= '.' . $allow . ' ';
			$mimes = mimes_search($allow);

			if (is_array($mimes)) {

				if (in_array($mime_type, $mimes) === false) {
					$cek[] = FALSE;
				} else {
					$cek[] = TRUE;
				}
			} else {
				if ($mime_type != $mimes) {
					$cek[] = FALSE;
				} else {
					$cek[] = TRUE;
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



/**
 * Untuk melakukan pengecekan apakah nilai dari variabel $_POST
 * sudah tersimpan di tabel.kolom
 *  
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_POST yang ingin di validasi.
 * @param	mixed|string		$nama_table			Nama tabel dan kolom yang ingin dicek; tabel.kolom => tb_login.email
 * 
 * @return	bool
 */
function input_is_unique($name, $nama_table)
{
	if ($_POST[$name] != "") {

		$tabel = reset(explode('.', $nama_table));

		$cek = "SELECT $nama_table FROM $tabel WHERE $nama_table = '$_POST[$name]'";
		$cek_db = $_SERVER['KONEKSI_DB']->query($cek);

		if ($_SERVER['KONEKSI_DB']->connect_errno || mysqli_num_rows($cek_db) != 0) {

			if ($_SESSION['set_rules'][$name]['custom-message']['is_unique'] !== NULL) {
				error($name, $_SESSION['set_rules'][$name]['custom-message']['is_unique']);
			} else {
				error($name, 'Nilai sudah digunakan, silahkan memasukkan nilai yang lain !');
			}
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

		// var_dump( $cek_db ); exit;

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



/**
 * function input_except_unique( name, nama_table, previous_value )
 * 
 * 
 * Untuk melakukan pengecekan apakah nilai dari variabel $_POST
 * sama dengan nilai yang sebelumnya dan sudah tersimpan di tabel.kolom
 *  
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_POST yang ingin di validasi.
 * @param	mixed|string		$nama_table			Nama tabel dan kolom yang ingin dicek; tabel.kolom => tb_login.email
 * @param	mixed|string		$previous_value		Value sebelumnya yang ingin di cek.
 * 
 * @return	bool
 */
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



/**
 * function input_allowed_values( name, value_rules )
 * 
 * 
 * Untuk melakukan pengecekan apakah nilai dari variabel $_POST
 * sama dengan nilai yang sebelumnya dan sudah tersimpan di tabel.kolom
 *  
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_POST yang ingin di validasi.
 * @param	mixed|string		$nama_table			Nama tabel dan kolom yang ingin dicek; tabel.kolom => tb_login.email
 * @param	mixed|string		$previous_value		Value sebelumnya yang ingin di cek.
 * 
 * @return	bool
 */
function input_allowed_values($name, $value_rules)
{
	if ($_POST[$name] != "") {


		$array = array_map('trim', explode(',', $value_rules));

		// $allNumbers = $array == array_filter( array_keys( $array ) , 'is_numeric');
		// if ( $allNumbers ) {
		// 	$checker = in_array($_POST[$name], $array);
		// } else {
		// 	$checker = array_key_exists($_POST[$name], $array);
		// }



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


/**
 * Untuk melakukan pengecekan apakah variabel $_POST tidak nilai,
 * Jika ada nilai maka validasi gagal.
 *  
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_POST yang ingin di validasi.
 * 
 * @return	bool
 */
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


/**
 * Untuk melakukan pengecekan apakah variabel $_POST tidak nilai,
 * Jika ada nilai maka validasi gagal.
 *  
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_POST yang ingin di validasi.
 * 
 * @return	bool
 */
function input_allow_not_required($name)
{

	if ($_POST[$name] != "") {
		success($name);
	} else {
		success($name);
	}
}


/**
 * Untuk melakukan pengecekan apakah variabel $_POST memiliki nilai,
 * Jika tidak ada nilai, maka validasi gagal.
 * 
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	mixed|string		$name				Nama Variabel $_POST yang ingin di validasi.
 * 
 * @return	bool
 */
function input_required($name)
{

	if ($_POST[$name] === NULL || $_POST[$name] == "") {

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


/**
 * Untuk melakukan pengecekan apakah variabel $_POST memiliki nilai,
 * Jika tidak ada nilai, maka validasi gagal.
 * 
 *  
 * return false jika tidak lolos validasi.
 * 
 * 
 * @param	string		$name				Nama Variabel $_POST yang ingin di validasi.
 * @param	int			$length				Nama Variabel $_POST yang ingin di validasi.
 * 
 * @return	bool
 */
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
	$_SESSION['set_rules']	= [];
	$_SESSION['cek_form']	= [];
	$_SESSION['type']	= [];
}

function form_error(string $nama_val = "", string $custom_message = '', $open_tag = "<small class='text-danger'>", $closed_tag = '</small>')
{


	if ($_SESSION['notifikasi'][$nama_val] !== NULL) {
		if ($custom_message != '') {
			return $open_tag . $custom_message . $closed_tag;
		} else {
			return $open_tag . $_SESSION['notifikasi'][$nama_val] . $closed_tag;
		}
	}
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

/** Notification **/

/**
 * Make Toast Bootstrap Notify Component.
 * 
 * @param	string		$state		Primary | Success | Danger.
 * @param	string		$title		Judul Notifikasi.
 * @param	string		$message	Pesan yang ditampilkan.
 * @param	string		$icon		Default icon fas fa-bell.
 * @return	bool
 */
function make_toast($state = 'primary', $title = 'Not Set', $message = '', $icon = '')
{

	make_old('have-toast', true);
	make_old('toast-state', $state);
	make_old('toast-title', $title);
	make_old('toast-message', $message);
	make_old('toast-icon', $icon);
	return true;
}


/**
 * Make Toast SWAL2 Component.
 * 
 * @param	string		$state		Primary | Success | Danger.
 * @param	string		$title		Judul Notifikasi.
 * @param	string		$message	Pesan yang ditampilkan.
 * @param	string		$icon		Default icon fas fa-bell.
 * @return	bool
 */
function make_toast2($state = 'success', $title = 'Not Set', $message = '', $icon = '')
{
	make_old('have-toast2', true);
	make_old('toast2-state', $state);
	make_old('toast2-title', $title);
	make_old('toast2-message', $message);
	make_old('toast2-icon', $icon);
	return true;
}


/**
 * Make Toast Component.
 * 
 * @param	string		$state		Primary | Success | Danger.
 * @param	string		$title		Judul Notifikasi.
 * @param	string		$message	Pesan yang ditampilkan.
 * @param	string		$icon		Default icon fas fa-bell.
 * @return	bool
 */
function make_toast3($state = 'success', $title = 'Not Set', $message = '', $timer = 3000)
{
	make_old('have-toast3', true);
	make_old('toast3-state', $state);
	make_old('toast3-title', $title);
	make_old('toast3-message', $message);
	make_old('toast3-timer', $timer);
	return true;
}









/**
 * Make Cute Alert Toast Component.
 * 
 * @param	string		$state		success|error|warning|info|question
 * @param	string		$title		Judul Notifikasi.
 * @param	string		$message	Pesan yang ditampilkan.
 * @param	int			$timer		Timer untuk alert.
 * @return	bool
 */
function make_toast5($state = 'success', $title = 'Not Set', $message = '', $timer = '5000')
{
	make_old('have-toast5', true);
	make_old('toast5-state', $state);
	make_old('toast5-title', $title);
	make_old('toast5-message', $message);
	make_old('toast5-timer', $timer);


	return true;
}



/**
 * Make Snackbar Component.
 * 
 * @param	string		$message	Pesan yang ditampilkan.
 * @param	string		$position	top-left|top-center|top-right
 * 									bottom-left|bottom-center|bottom-right
 * @return	bool
 */
function make_snackbar($message = 'Not Set', $position = 'bottom-center')
{
	make_old('have-snackbar', true);
	make_old('snackbar-message', $message);
	make_old('snackbar-position', $position);

	return true;
}




/**
 * Make Solo Alert Component.
 * 
 * @param	string		$state			success | error | warning
 * @param	string		$title			Judul Notifikasi.
 * @param	string		$message		Pesan yang ditampilkan.
 * @param	bool		$transparency	False = Transparan | True = No Transparan
 * @return	bool
 */
function make_soloalert($state = 'success', $title = 'Not Set', $message = '', $transparency = false)
{
	make_old('have-soloalert', true);
	make_old('soloalert-state', $state);
	make_old('soloalert-title', $title);
	make_old('soloalert-message', $message);
	make_old('soloalert-transparency', $transparency);

	return true;
}



/**
 * Make Cute Alert Component.
 * 
 * @param	string		$state		success | error | warning | info | question
 * @param	string		$title		Judul Notifikasi.
 * @param	string		$message	Pesan yang ditampilkan.
 * @return	bool
 */
function make_cutealert($state = 'success', $title = 'Not Set', $message = '')
{
	make_old('have-cutealert', true);
	make_old('cutealert-state', $state);
	make_old('cutealert-title', $title);
	make_old('cutealert-message', $message);

	return true;
}

/**
 * Make SWAL2 Component.
 * 
 * @param	string		$state		Primary | Success | Danger.
 * @param	string		$title		Judul Notifikasi.
 * @param	string		$message	Pesan yang ditampilkan.
 * @param	string		$icon		Default icon fas fa-bell.
 * @return	bool
 */
function make_swal2($state = 'success', $title = 'Not Set', $message = '', $timer = 2000)
{
	make_old('have-swal2', true);
	make_old('swal2-state', $state);
	make_old('swal2-title', $title);
	make_old('swal2-message', $message);
	make_old('swal2-timer', $timer);
	return true;
}



function make_sound($state = 'chime', $timer = 1000)
{

	if (!in_array(
		strtolower($state),
		['error', 'warning', 'success', 'bye', 'close', 'welcome']
	)) {
		$state = 'chime';
	} else {
		$state = strtolower($state);
	}

	make_old('have-sound', true);
	make_old('sound-state', $state);
	make_old('sound-timer', $timer);
	return true;
}



/**
 * Give you Sweetalert 2 Notification.
 */
function swal2(string $tipe = "error", string $judul = "Not Set", string $notifikasi = "", int $timer = 2000)
{

	$time = "( Elapsed Time : " . number_format((float) (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 2, '.', '') . " Seconds )";


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


/**
 * Show Toast when toast avail.
 */
function show_notification()
{

	$komponen = '';
	$notifikasi = '';

	echo "<script src='./core/component/jquery.min.js'></script>";


	if ($_SESSION['old']['have-toast'] === true) {
		// Toast
		echo "<script src='./core/component/bootstrap-notify.min.js'></script>";
	}

	if ($_SESSION['old']['have-toast2'] === true) {
		// Swal2
		echo "<link rel='stylesheet' href='./core/component/sweetalert2.css'>";
		echo "<script src='./core/component/sweetalert2.all.js'></script>";
	}

	if ($_SESSION['old']['have-toast3'] === true) {
		// Toast3
		echo "<link rel='stylesheet' href='./core/component/toastr.css'>";
		echo "<script src='./core/component/toastr.js'></script>";
	}

	if ($_SESSION['old']['have-toast4'] === true || $_SESSION['old']['have-toast5'] === true) {
		// Toast3
		echo "<link rel='stylesheet' href='./core/component/cute-alert-style.css' />";
		echo "<script src='./core/component/cute-alert.js'></script>";
	}

	if ($_SESSION['old']['have-swal2'] === true) {
		// Swal2
		echo "<link rel='stylesheet' href='./core/component/sweetalert2.css'>";
		echo "<script src='./core/component/sweetalert2.all.js'></script>";
	}


	if ($_SESSION['old']['have-soloalert'] === true) {
		// Soloalert
		echo "<script src='./core/component/soloalert.js'></script>";
	}

	if ($_SESSION['old']['have-snackbar'] === true) {

		//echo "<link type='text/css' rel='stylesheet' href='./core/component/snackbar/src/materialize.min.css' media='screen,projection'>";
		echo "<link rel='stylesheet' href='./core/component/snackbar/dist/snackbar.min.css' media='screen' title='no title' charset='utf-8'>";
		echo "<link rel='stylesheet' href='./core/component/snackbar/src/prism.css' media='screen' title='no title' charset='utf-8'>";
		// echo "<link rel='stylesheet' href='./core/component/snackbar/src/style.css' media='screen' title='no title' charset='utf-8'>";

		echo "<script src='./core/component/snackbar/src/modernizr.js' charset='utf-8'></script>";
		echo "<script src='./core/component/snackbar/src/materialize.min.js' charset='utf-8'></script>";
		echo "<script src='./core/component/snackbar/src/prism.js' charset='utf-8'></script>";
		echo "<script src='./core/component/snackbar/dist/snackbar.min.js' charset='utf-8'></script>";
		echo "<script src='./core/component/snackbar/src/init.js' charset='utf-8'></script>";
	}



	// Script Khusus
	echo "<script>";
	echo "$(function() {";

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

	echo "})";
	echo "</script>";

	reset_notification();
}



/**
 * Simple way to make javasript alert messages.
 *  
 * @param	string		$msg	Pesan yang ingin ditampilkan.
 * @return	void		Javascript-alert
 */
function alert(string $msg = "")
{
	echo "<script type='text/javascript'>alert('$msg');</script>";
}


/** Expansion 2 **/


function delete_dir(string $lokasi)
{
	$dir = $lokasi;
	$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator(
		$it,
		RecursiveIteratorIterator::CHILD_FIRST
	);
	foreach ($files as $file) {
		if ($file->isDir()) {
			rmdir($file->getRealPath());
		} else {
			unlink($file->getRealPath());
		}
	}
	rmdir($dir);
}


function base_url(string $location = '')
{
	$root = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'];
	$root .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

	$root .= $location;

	return $root;
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






function set_userdata(string $nama, $value)
{
	$_SESSION['userdata'][$nama] = $value;
}

function userdata(string $nama)
{
	return $_SESSION['userdata'][$nama];
}

function unset_userdata(string $nama)
{
	unset($_SESSION['userdata'][$nama]);
}


function api_login(array $login_data, string $url = '', string $method = 'POST', int $timeout = 30000)
{

	if ($url == '') {
		$ch = curl_init('http://sid.polibatam.ac.id/apilogin/web/api/auth/login');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);


		return $result = json_decode(curl_exec($ch));
	} else {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);


		return $result = json_decode(curl_exec($ch));
	}
}




function mimes_search(string $pencarian)
{

	$mimes = array(
		'hqx'	=>	array('application/mac-binhex40', 'application/mac-binhex', 'application/x-binhex40', 'application/x-mac-binhex40'),
		'cpt'	=>	'application/mac-compactpro',
		'csv'	=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'),
		'bin'	=>	array('application/macbinary', 'application/mac-binary', 'application/octet-stream', 'application/x-binary', 'application/x-macbinary'),
		'dms'	=>	'application/octet-stream',
		'lha'	=>	'application/octet-stream',
		'lzh'	=>	'application/octet-stream',
		'exe'	=>	array('application/octet-stream', 'application/x-msdownload'),
		'class'	=>	'application/octet-stream',
		'psd'	=>	array('application/x-photoshop', 'image/vnd.adobe.photoshop'),
		'so'	=>	'application/octet-stream',
		'sea'	=>	'application/octet-stream',
		'dll'	=>	'application/octet-stream',
		'oda'	=>	'application/oda',
		'pdf'	=>	array('application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream'),
		'ai'	=>	array('application/pdf', 'application/postscript'),
		'eps'	=>	'application/postscript',
		'ps'	=>	'application/postscript',
		'smi'	=>	'application/smil',
		'smil'	=>	'application/smil',
		'mif'	=>	'application/vnd.mif',
		'xls'	=>	array('application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'application/x-ms-excel', 'application/x-excel', 'application/x-dos_ms_excel', 'application/xls', 'application/x-xls', 'application/excel', 'application/download', 'application/vnd.ms-office', 'application/msword'),
		'ppt'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.ms-office', 'application/msword'),
		'pptx'	=> 	array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/x-zip', 'application/zip'),
		'wbxml'	=>	'application/wbxml',
		'wmlc'	=>	'application/wmlc',
		'dcr'	=>	'application/x-director',
		'dir'	=>	'application/x-director',
		'dxr'	=>	'application/x-director',
		'dvi'	=>	'application/x-dvi',
		'gtar'	=>	'application/x-gtar',
		'gz'	=>	'application/x-gzip',
		'gzip'  =>	'application/x-gzip',
		'php'	=>	array('application/x-httpd-php', 'application/php', 'application/x-php', 'text/php', 'text/x-php', 'application/x-httpd-php-source'),
		'php4'	=>	'application/x-httpd-php',
		'php3'	=>	'application/x-httpd-php',
		'phtml'	=>	'application/x-httpd-php',
		'phps'	=>	'application/x-httpd-php-source',
		'js'	=>	array('application/x-javascript', 'text/plain'),
		'swf'	=>	'application/x-shockwave-flash',
		'sit'	=>	'application/x-stuffit',
		'tar'	=>	'application/x-tar',
		'tgz'	=>	array('application/x-tar', 'application/x-gzip-compressed'),
		'z'		=>	'application/x-compress',
		'xhtml'	=>	'application/xhtml+xml',
		'xht'	=>	'application/xhtml+xml',
		'zip'	=>	array('application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/s-compressed', 'multipart/x-zip'),
		'rar'	=>	array('application/x-rar', 'application/rar', 'application/x-rar-compressed'),
		'mid'	=>	'audio/midi',
		'midi'	=>	'audio/midi',
		'mpga'	=>	'audio/mpeg',
		'mp2'	=>	'audio/mpeg',
		'mp3'	=>	array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
		'aif'	=>	array('audio/x-aiff', 'audio/aiff'),
		'aiff'	=>	array('audio/x-aiff', 'audio/aiff'),
		'aifc'	=>	'audio/x-aiff',
		'ram'	=>	'audio/x-pn-realaudio',
		'rm'	=>	'audio/x-pn-realaudio',
		'rpm'	=>	'audio/x-pn-realaudio-plugin',
		'ra'	=>	'audio/x-realaudio',
		'rv'	=>	'video/vnd.rn-realvideo',
		'wav'	=>	array('audio/x-wav', 'audio/wave', 'audio/wav'),
		'bmp'	=>	array('image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'),
		'gif'	=>	'image/gif',
		'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
		'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
		'jpe'	=>	array('image/jpeg', 'image/pjpeg'),
		'jp2'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'j2k'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'jpf'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'jpg2'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'jpx'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'jpm'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'mj2'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'mjp2'	=>	array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
		'png'	=>	array('image/png',  'image/x-png'),
		'tiff'	=>	'image/tiff',
		'tif'	=>	'image/tiff',
		'css'	=>	array('text/css', 'text/plain'),
		'html'	=>	array('text/html', 'text/plain'),
		'htm'	=>	array('text/html', 'text/plain'),
		'shtml'	=>	array('text/html', 'text/plain'),
		'txt'	=>	'text/plain',
		'text'	=>	'text/plain',
		'log'	=>	array('text/plain', 'text/x-log'),
		'rtx'	=>	'text/richtext',
		'rtf'	=>	'text/rtf',
		'xml'	=>	array('application/xml', 'text/xml', 'text/plain'),
		'xsl'	=>	array('application/xml', 'text/xsl', 'text/xml'),
		'mpeg'	=>	'video/mpeg',
		'mpg'	=>	'video/mpeg',
		'mpe'	=>	'video/mpeg',
		'qt'	=>	'video/quicktime',
		'mov'	=>	'video/quicktime',
		'avi'	=>	array('video/x-msvideo', 'video/msvideo', 'video/avi', 'application/x-troff-msvideo'),
		'movie'	=>	'video/x-sgi-movie',
		'doc'	=>	array('application/msword', 'application/vnd.ms-office'),
		'docx'	=>	array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword', 'application/x-zip'),
		'dot'	=>	array('application/msword', 'application/vnd.ms-office'),
		'dotx'	=>	array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword'),
		'xlsx'	=>	array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/vnd.ms-excel', 'application/msword', 'application/x-zip'),
		'word'	=>	array('application/msword', 'application/octet-stream'),
		'xl'	=>	'application/excel',
		'eml'	=>	'message/rfc822',
		'json'  =>	array('application/json', 'text/json'),
		'pem'   =>	array('application/x-x509-user-cert', 'application/x-pem-file', 'application/octet-stream'),
		'p10'   =>	array('application/x-pkcs10', 'application/pkcs10'),
		'p12'   =>	'application/x-pkcs12',
		'p7a'   =>	'application/x-pkcs7-signature',
		'p7c'   =>	array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
		'p7m'   =>	array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
		'p7r'   =>	'application/x-pkcs7-certreqresp',
		'p7s'   =>	'application/pkcs7-signature',
		'crt'   =>	array('application/x-x509-ca-cert', 'application/x-x509-user-cert', 'application/pkix-cert'),
		'crl'   =>	array('application/pkix-crl', 'application/pkcs-crl'),
		'der'   =>	'application/x-x509-ca-cert',
		'kdb'   =>	'application/octet-stream',
		'pgp'   =>	'application/pgp',
		'gpg'   =>	'application/gpg-keys',
		'sst'   =>	'application/octet-stream',
		'csr'   =>	'application/octet-stream',
		'rsa'   =>	'application/x-pkcs7',
		'cer'   =>	array('application/pkix-cert', 'application/x-x509-ca-cert'),
		'3g2'   =>	'video/3gpp2',
		'3gp'   =>	array('video/3gp', 'video/3gpp'),
		'mp4'   =>	'video/mp4',
		'm4a'   =>	'audio/x-m4a',
		'f4v'   =>	array('video/mp4', 'video/x-f4v'),
		'flv'	=>	'video/x-flv',
		'webm'	=>	'video/webm',
		'aac'   =>	'audio/x-acc',
		'm4u'   =>	'application/vnd.mpegurl',
		'm3u'   =>	'text/plain',
		'xspf'  =>	'application/xspf+xml',
		'vlc'   =>	'application/videolan',
		'wmv'   =>	array('video/x-ms-wmv', 'video/x-ms-asf'),
		'au'    =>	'audio/x-au',
		'ac3'   =>	'audio/ac3',
		'flac'  =>	'audio/x-flac',
		'ogg'   =>	array('audio/ogg', 'video/ogg', 'application/ogg'),
		'kmz'	=>	array('application/vnd.google-earth.kmz', 'application/zip', 'application/x-zip'),
		'kml'	=>	array('application/vnd.google-earth.kml+xml', 'application/xml', 'text/xml'),
		'ics'	=>	'text/calendar',
		'ical'	=>	'text/calendar',
		'zsh'	=>	'text/x-scriptzsh',
		'7z'	=>	array('application/x-7z-compressed', 'application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'),
		'7zip'	=>	array('application/x-7z-compressed', 'application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'),
		'cdr'	=>	array('application/cdr', 'application/coreldraw', 'application/x-cdr', 'application/x-coreldraw', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr'),
		'wma'	=>	array('audio/x-ms-wma', 'video/x-ms-asf'),
		'jar'	=>	array('application/java-archive', 'application/x-java-application', 'application/x-jar', 'application/x-compressed'),
		'svg'	=>	array('image/svg+xml', 'application/xml', 'text/xml'),
		'vcf'	=>	'text/x-vcard',
		'srt'	=>	array('text/srt', 'text/plain'),
		'vtt'	=>	array('text/vtt', 'text/plain'),
		'ico'	=>	array('image/x-icon', 'image/x-ico', 'image/vnd.microsoft.icon'),
		'odc'	=>	'application/vnd.oasis.opendocument.chart',
		'otc'	=>	'application/vnd.oasis.opendocument.chart-template',
		'odf'	=>	'application/vnd.oasis.opendocument.formula',
		'otf'	=>	'application/vnd.oasis.opendocument.formula-template',
		'odg'	=>	'application/vnd.oasis.opendocument.graphics',
		'otg'	=>	'application/vnd.oasis.opendocument.graphics-template',
		'odi'	=>	'application/vnd.oasis.opendocument.image',
		'oti'	=>	'application/vnd.oasis.opendocument.image-template',
		'odp'	=>	'application/vnd.oasis.opendocument.presentation',
		'otp'	=>	'application/vnd.oasis.opendocument.presentation-template',
		'ods'	=>	'application/vnd.oasis.opendocument.spreadsheet',
		'ots'	=>	'application/vnd.oasis.opendocument.spreadsheet-template',
		'odt'	=>	'application/vnd.oasis.opendocument.text',
		'odm'	=>	'application/vnd.oasis.opendocument.text-master',
		'ott'	=>	'application/vnd.oasis.opendocument.text-template',
		'oth'	=>	'application/vnd.oasis.opendocument.text-web'
	);

	return $mimes[strtolower($pencarian)];
}


/** Expansion Pack **/

function time_elapsed_string($datetime, $full = false)
{
	$now = new DateTime;
	$ago = new DateTime($datetime);
	$diff = $now->diff($ago);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = array(
		'y' => 'year',
		'm' => 'month',
		'w' => 'week',
		'd' => 'day',
		'h' => 'hour',
		'i' => 'minute',
		's' => 'second',
	);
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}
