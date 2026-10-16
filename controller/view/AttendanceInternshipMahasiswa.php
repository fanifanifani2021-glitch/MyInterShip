<?php

middleware('MahasiswaMiddleware');

$id_internship = base64_decode($_GET['id_internship']);
$nim = $_SESSION['nim'];

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = :id_internship AND nim = :nim LIMIT 1";
$exec = $_SERVER['PDO']->prepare($sql);
$exec->execute([':nim' => $nim, ':id_internship' => $id_internship]);

if ($exec->rowCount() != 1) {
	make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
	redirect_back(1);
	exit();
}

$internship_data = $exec->fetch(PDO::FETCH_ASSOC);


switch ($method):
	default:
		view('exception/error_404');

		break;

	case 'index':


		if (in_array(strtolower($internship_data['status']), ['waiting', 'no'])) {
			make_swal2('error', 'Not Allowed', 'Internship is not accepted !');
			redirect_back(1);
			exit;
		}

		$sql = "SELECT * FROM attendance WHERE id_internship = :id_internship ORDER BY `date` DESC";

		$stmt = $_SERVER['PDO']->prepare($sql);
		$stmt->bindParam(':id_internship', $id_internship, PDO::PARAM_INT);

		$stmt->execute();
		$exec = $stmt->fetchAll(PDO::FETCH_ASSOC);


		// Current Attendance
		$today = date('Y-m-d');

		$sql2 = "SELECT * FROM attendance WHERE id_internship = :id_internship AND `date` LIKE :today LIMIT 1";
		$exec2 = $_SERVER['PDO']->prepare($sql2);
		$exec2->bindParam(':id_internship', $id_internship, PDO::PARAM_INT);
		$exec2->bindValue(':today', '%' . $today . '%', PDO::PARAM_STR);
		$exec2->execute();
		

		if ( $exec2->rowCount() != 1 ) {
			$have_attendance = true;
		} else {
			$have_attendance = false;
		}


		renderView('student.my_internship.attendance.index', [
			'routes' => 'my_internship', 'title' => 'Attendance',
			'id_internship' => $id_internship, 'exec' => $exec,
			'have_attendance' => $have_attendance, 'today' => $today
		]);
		break;



endswitch;
