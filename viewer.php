<?php

error_reporting(false);
session_start();

include 'database/config.php';
include 'database/database_connection.php';




if ($_SERVER['ON_MAINTENANCE']) {
	if (!$_SESSION['ADMIN_MAINTENANCE']) {
		file_checker();
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


if ($_SESSION['token'] == '') {
	file_checker();
	exit;
}



$src = $_GET['src'];

if ($src == 'final_report') {
	$location = base64_decode($_GET['file']);
	allowed_types('./upload/lampiran_internship/' . $location, ['pdf']);
	file_checker('./upload/lampiran_internship/' . $location);
} else 


if ($src == 'get_weather') {

	$location = base64_decode($_GET['location']);
	$file_headers = @get_headers($location);


	// Pengecekan apakah data perlu diresync atau tidak
	$resync = true;
	if (isset($_SESSION['VIEWER_REQUEST'])) {
		$nextSyncDate = $_SESSION['VIEWER_REQUEST']['RESYNC'];
		$currentDate = date('Y-m-d');

		if ($currentDate <= $nextSyncDate) {

			$resync = false;
			$cacheFilePath = $_SESSION['VIEWER_REQUEST']['CACHE'];

			if (is_file($cacheFilePath)) {
				header('Content-Type: image');
				readfile($cacheFilePath);
				exit;
			}
		}
	}

	if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
		file_checker();
		exit;
	}



	// Generate nama file unik dengan ekstensi .png
	$filename = 'weather-icon-' . uniqid() . '.png';

	// Lokasi folder cache
	$cacheFolder = './cache/viewer/'; // Ganti dengan lokasi folder cache yang sesuai

	if (!is_dir($cacheFolder)) {
		mkdir($cacheFolder, 0755);
	}

	// Lokasi lengkap file dalam folder cache
	$cacheFilePath = $cacheFolder . $filename;

	// Download dan simpan gambar dalam folder cache
	if (copy($location, $cacheFilePath)) {
		// Atur header untuk jenis konten sebagai gambar
		header('Content-Type: image');

		// Baca dan kembalikan file gambar aslinya
		readfile($cacheFilePath);

		// Simpan informasi file yang telah tersimpan ke dalam variabel $_SESSION['VIEWER_REQUEST']
		$_SESSION['VIEWER_REQUEST'] = array(
			'LOCATION' => $location,
			'CACHE' => $cacheFilePath,
			'RESYNC' => date('Y-m-d', strtotime('+1 day'))
			// Tambahkan informasi lain yang perlu Anda simpan
		);

		exit;
	} else {
		// Jika gagal menyimpan gambar, keluarkan pesan error
		file_checker();
		exit;
	}
} else

if ($src == 'manual_guide') {
	$file = './system_requirement/panduan_myinternship.pdf';

	allowed_types($file, ['pdf']);
	file_checker($file);
	exit;
} else 

if ($src == 'get_img') {
	$location = base64_decode($_GET['location']);
	$file_headers = @get_headers($location);

	if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
		file_checker();
		exit;
	}

	$image = imagecreatefromstring(file_get_contents($location));
	header('Content-Type: image/png');
	imagepng($image);
	exit;
} else 

if ($src == 'get_university_logo') {


	$location = base64_decode($_GET['location']);
	if ($location == '' || !$location) {
		file_checker('./core/company-default.png');
		exit;
	}


	$file_headers = @get_headers($location);

	if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
		file_checker('./core/company-default.png');
		exit;
	}

	$image = imagecreatefromstring(file_get_contents($location));
	file_checker(imagepng($image, null, 1), true);
	exit;
} else 

if ($src == 'get_company_logo') {

	$location = base64_decode($_GET['location']);
	$file_headers = @get_headers($location);

	if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
		file_checker('./core/company-default.png');
		exit;
	}

	$image = imagecreatefromstring(file_get_contents($location));
	// header('Content-Type: image/png');
	// imagepng($image);
	file_checker(imagepng($image, null, 1), true);
	exit;
} else 

if ($src == 'profile_background') {
	$file = $_SERVER['PROFILE_BACKGROUND'];
	allowed_types($file, ['png', 'jpg', 'jpeg']);
	file_checker($file, true);
	exit;
} else

if ($src == 'get_profile') {

	$thumbnail = (in_array($_GET['thumbnail'], [1, true])) ? true : false;
	$location = base64_decode($_GET['location']) ?? 'profile.jpg';
	$file = './upload/profile_picture/' . $location;

	allowed_types($file, ['png', 'jpg', 'jpeg']);
	file_checker($file, $thumbnail);
	exit;
} else

if ($src == 'main_logo') {
	$file = $_SERVER['MAIN_LOGO'];

	allowed_types($file, ['png', 'jpg', 'jpeg']);
	file_checker($file);
	exit;
} else

if ($src == 'profile_picture') {


	// Cek thumbnail & download param
	$thumbnail = (in_array($_GET['thumbnail'] ?? 0, [1, true])) ? true : true;
	$thumbnail = (in_array($_GET['download'] ?? 0, [1, true])) ? false : $thumbnail;

	// Ambil nama file dari session atau default
	$location = $_SESSION['profile_picture'] ?? 'profile.jpg';
	$file = './upload/profile_picture/' . $location;

	// Cek apakah file profile_picture ada
	if (!file_exists($file) || !is_readable($file)) {
		// Jika tidak ada, fallback ke default.jpg
		$file = './upload/default.jpg';

		if (!file_exists($file) || !is_readable($file)) {
			// Jika default.jpg pun tidak ada, fallback terakhir ke gambar internet
			header("Location: https://via.placeholder.com/300x300?text=Profile+Picture");
			exit;
		}
	}

	// Validasi file type
	allowed_types($file, ['png', 'jpg', 'jpeg']);

	// Tampilkan file
	file_checker($file, $thumbnail);
} else


if ($src == 'announcement') {
	$id_announcement = base64_decode($_GET['id']);

	$sql = "SELECT * FROM announcement WHERE id_announcement = :id_announcement";
	$sql = $_SERVER['PDO']->prepare($sql);
	$sql->execute(['id_announcement' => $id_announcement]);

	if ($sql->rowCount() != 1) {
		file_checker();
		exit;
	}

	$announcement = $sql->fetch();

	$lokasi = './upload/announcement/' . $id_announcement . '/' . $announcement['picture'];
	allowed_types($lokasi, ['jpg', 'png', 'jpeg']);
	file_checker($lokasi);
	exit;
} else


if ($src == 'attachment_announcement') {
	$id_announcement = base64_decode($_GET['id']);

	$sql = "SELECT * FROM announcement WHERE id_announcement = :id_announcement";
	$sql = $_SERVER['PDO']->prepare($sql);
	$sql->execute(['id_announcement' => $id_announcement]);

	if ($sql->rowCount() != 1) {
		file_checker();
		exit;
	}

	$announcement = $sql->fetch();

	$lokasi = './upload/announcement/' . $id_announcement . '/' . $announcement['attachment'];
	allowed_types($lokasi, ['jpg', 'png', 'jpeg', 'pdf']);
	file_checker($lokasi);
	exit;
} else


if ($src == 'excel_generate') {

	$location = base64_decode($_GET['location']) ?? NULL;

	$file = 'excel/generate/' . $location ?? NULL;
	$path_parts = pathinfo($file);
	if (!in_array(strtolower($path_parts['extension']), ['xls', 'xlsx'])) {
		file_checker();
	}

	file_checker($file);
	exit;
} else


if ($src == 'excel_template') {

	$location = base64_decode($_GET['location']) ?? NULL;

	$file = 'excel/template/' . $location;
	$path_parts = pathinfo($file);

	if (!in_array(strtolower($path_parts['extension']), ['xls', 'xlsx'])) {
		file_checker();
	}

	file_checker($file);
	exit;
} else {
	file_checker();
	exit;
}















function file_checker($filepath = './core/404.png', $thumbnail = false, $filename = 'viewer')
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
		$file_name = ($filename != 'viewer') ? $filename . $info : basename($filepath);


		header('Content-Description: File Transfer');
		header("Accept-Ranges: bytes");
		header('Content-Length: ' . filesize($filepath));
		// header('filename="'.$file_name.'"');
		header('Content-Disposition: attachment; filename="' . $file_name . '"');
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
