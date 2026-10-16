<?php

error_reporting(false);
session_start();

$src = $_GET['viewer'];

switch ($src) {

	default:
		file_checker();
		break;

	case 'academic_transcript':
		$nim = base64_decode($_GET['nim']);
		$sql = "SELECT * FROM `student_profile` WHERE nim = :nim LIMIT 1";
		$stmt = $_SERVER['PDO']->prepare($sql);
		$stmt->execute(['nim' => $_SESSION['nim']]);
		$student_profile = $stmt->fetch(PDO::FETCH_ASSOC);

		allowed_types('./upload' . $student_profile['academic_transcript'], ['pdf']);
		file_checker('./upload' . $student_profile['academic_transcript']);

		break;


	case 'final_report':
		$location = base64_decode($_GET['file']);

		allowed_types('./upload/lampiran_internship/' . $location, ['pdf']);
		file_checker('./upload/lampiran_internship/' . $location);

		break;


	case 'internship_signature':
		$id_internship = base64_decode($_GET['id_internship']);
		$id_attendance = base64_decode($_GET['id_attendance']);

		if ($_SESSION['role'] == 'Mahasiswa') {
			// Cek NIM
			$sql = "SELECT * FROM internship WHERE id_internship = :id_internship AND nim = :nim LIMIT 1";
			$stmt = $_SERVER['PDO']->prepare($sql);
			$stmt->execute(['id_internship' => $id_internship, 'nim' => $_SESSION['nim']]);

			if ($stmt->rowCount() != 1) {
				file_checker();
				exit;
			}
		}

		// Ambil data attendance dan signature
		$sql = "SELECT `attendance`.*, `attendance-signature`.`signature` 
				FROM `attendance` 
				LEFT JOIN `attendance-signature` ON `attendance`.`id_attendance` = `attendance-signature`.`id_attendance` 
				WHERE `attendance`.`id_attendance` = :id_attendance 
				AND `attendance`.`id_internship` = :id_internship 
				LIMIT 1";

		$stmt = $_SERVER['PDO']->prepare($sql);
		$stmt->execute(['id_attendance' => $id_attendance, 'id_internship' => $id_internship]);

		if ($stmt->rowCount() != 1) {
			file_checker();
			exit;
		}

		$attendance = $stmt->fetch(PDO::FETCH_ASSOC);

		// Cek tipe file dan jalankan file_checker
		allowed_types($attendance['signature'], ['png', 'jpg', 'jpeg']);
		file_checker($attendance['signature'], isset($_GET['download']) && $_GET['download'] ? true : false);


		break;

	case 'mou':

		$mou_dir = './upload/company_mou/';
		$id_mou = base64_decode($_GET['id_mou']);

		$sql = "SELECT * FROM company_mou WHERE id_mou = :id_mou LIMIT 1";
		$stmt = $_SERVER['PDO']->prepare($sql);
		$stmt->bindParam(':id_mou', $id_mou, PDO::PARAM_INT);

		$stmt->execute();


		if ($stmt->rowCount() != 1) {
			file_checker();
			exit;
		}

		$mou = $stmt->fetch(PDO::FETCH_ASSOC);

		if (is_file($mou_dir . $mou['mou_file'])) {
			allowed_types($mou_dir . $mou['mou_file'], ['pdf']);
			file_checker($mou_dir . $mou['mou_file'], false, $mou['mou_file'], ($_GET['download']) ? true : false);
			exit;
		} else {
			file_checker();
			exit;
		}
		break;


	case 'url':
	case 'uri':
	case 'URL':
	case 'URI':
		$location = base64_decode($_GET['location']);

		$file_headers = @get_headers($location);

		if (!$file_headers || $file_headers[0] == 'HTTP/1.0 404 Not Found') {
			view('exception/url_not_found', ['url' => $location]);
			exit;
		}


		view('exception/url_redirect', ['url' => $location]);
		exit;

		break;

	case 'internship_attachment':

		$location = base64_decode($_GET['location']);

		if (!is_file($location)) {
			file_checker();
			exit;
		}

		allowed_types($location, ['pdf']);
		file_checker($location);

		break;


	case 'manual_guide':
		$file = './system_requirement/panduan_myinternship.pdf';

		allowed_types($file, ['pdf']);
		file_checker($file);
		break;


	case 'get_img':
		$location = base64_decode($_GET['location']);
		$file_headers = @get_headers($location);

		if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
			file_checker();
		}

		$image = imagecreatefromstring(file_get_contents($location));
		file_checker(imagepng($image, null, 1), true);
		break;

	case 'get_university_logo':

		$location = base64_decode($_GET['location']);
		$file_headers = @get_headers($location);

		if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
			file_checker('./core/company-default.png');
		}

		$image = imagecreatefromstring(file_get_contents($location));
		file_checker(imagepng($image, null, 1), true);
		break;

	case 'get_company_logo':

		$location = base64_decode($_GET['location']);
		$file_headers = @get_headers($location);

		if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
			file_checker('./core/company-default.png');
		}

		$image = imagecreatefromstring(file_get_contents($location));
		file_checker(imagepng($image, null, 1), true);

		break;

	case 'profile_background':
		$file = $_SERVER['PROFILE_BACKGROUND'];
		allowed_types($file, ['png', 'jpg', 'jpeg']);
		file_checker($file, true);
		break;


	case 'get_profile':

		$thumbnail = (in_array($_GET['thumbnail'], [1, true])) ? true : false;
		$location = base64_decode($_GET['location']) ?? 'profile.jpg';
		$file = './upload/profile_picture/' . $location;

		allowed_types($file, ['png', 'jpg', 'jpeg']);
		file_checker($file, $thumbnail);
		break;


	case 'main_logo':
		$file = $_SERVER['MAIN_LOGO'];

		allowed_types($file, ['png', 'jpg', 'jpeg']);
		file_checker($file);
		break;

	case 'profile_picture':

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
		break;



	case 'announcement':
		$id_announcement = decrypt($_GET['id']);

		$sql = "SELECT * FROM announcement WHERE id_announcement = :id_announcement";
		$sql = $_SERVER['PDO']->prepare($sql);
		$sql->execute(['id_announcement' => $id_announcement]);

		if ($sql->rowCount() != 1) {
			file_checker();
		}

		$announcement = $sql->fetch();

		$lokasi = './upload/announcement/' . $id_announcement . '/' . $announcement['picture'];
		allowed_types($lokasi, ['jpg', 'png', 'jpeg']);
		file_checker($lokasi);
		break;


	case 'attachment_announcement':
		$id_announcement = decrypt($_GET['id']);


		$sql = "SELECT * FROM announcement WHERE id_announcement = :id_announcement";
		$sql = $_SERVER['PDO']->prepare($sql);
		$sql->execute(['id_announcement' => $id_announcement]);

		if ($sql->rowCount() != 1) {
			file_checker();
		}

		$announcement = $sql->fetch();

		$lokasi = './upload/announcement/' . $id_announcement . '/' . $announcement['attachment'];
		allowed_types($lokasi, ['jpg', 'png', 'jpeg', 'pdf']);
		file_checker($lokasi);
		break;

	case 'excel_generate':

		$location = base64_decode($_GET['location']) ?? NULL;

		$file = 'excel/generate/' . $location ?? NULL;
		$path_parts = pathinfo($file);
		if (!in_array(strtolower($path_parts['extension']), ['xls', 'xlsx'])) {
			file_checker();
		}

		file_checker($file);
			if ( is_file($file) ) {
				unlink( $file );				
			}
		
		break;


	case 'excel_template':

		$location = base64_decode($_GET['location']) ?? NULL;

		$file = 'excel/template/' . $location;
		$path_parts = pathinfo($file);

		if (!in_array(strtolower($path_parts['extension']), ['xls', 'xlsx'])) {
			file_checker();
		}

		file_checker($file);
		break;
}
