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


		$sql = "SELECT * FROM logbook WHERE id_internship = :id_internship ORDER BY id_logbook DESC";

		$stmt = $_SERVER['PDO']->prepare($sql);
		$stmt->bindValue(':id_internship', $id_internship, PDO::PARAM_INT);

		$stmt->execute();
		$exec = $stmt->fetchAll(PDO::FETCH_ASSOC);


		renderView('student.my_internship.logbook.index', [
			'title' => 'Logbook Internship', 'routes' => 'my_internship',
			'id_internship' => $id_internship,
			'exec' => $exec
		]);

		break;

    case 'add':

        $sql = "SELECT * FROM logbook WHERE id_internship = :id_internship ORDER BY id_logbook DESC LIMIT 1";
        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->execute([':id_internship' => $id_internship]);

        if ($stmt->rowCount() != 1) {
            $last_logbook = [];
        } else {
            $last_logbook = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $last_week = (int) $last_logbook['week_num'] + 1 ?? 1;


        renderView('student.my_internship.logbook.add', [

            'title' => 'Add Logbook',
            'routes' => 'my_internship',
            'id_internship' => $id_internship,
            'last_logbook' => $last_logbook,
            'last_week' => $last_week
        ]);
        break;


	case 'edit':


		$id_logbook = base64_decode($_GET['id_logbook']);


		// Pengecekan Logbook
		// Query menggunakan prepared statement dengan parameter yang diikat
		$sql = "SELECT * FROM logbook WHERE id_internship = :id_internship AND id_logbook = :id_logbook LIMIT 1";
		$statement = $_SERVER['PDO']->prepare($sql);
		$statement->bindParam(':id_internship', $id_internship);
		$statement->bindParam(':id_logbook', $id_logbook);
		$statement->execute();


		// Cek logbook
		if ($statement->rowCount() != 1) {
			make_swal2('error', 'Logbook not found', 'Logbook data is not found !');
			redirect_back(1);
			exit;
		}


		// Ambil hasil query sebagai objek
		$data_logbook = $statement->fetch(PDO::FETCH_ASSOC);

        renderView('student.my_internship.logbook.edit', [
			'title' => 'Edit Logbook', 'routes' => 'my_internship',
			'id_internship' => $id_internship, 'data' => $data_logbook
		]);
		break;


	case 'detail':


		$id_logbook = base64_decode($_GET['id_logbook']);


		// Pengecekan Logbook
		// Query menggunakan prepared statement dengan parameter yang diikat
		$sql = "SELECT * FROM logbook WHERE id_internship = :id_internship AND id_logbook = :id_logbook LIMIT 1";
		$statement = $_SERVER['PDO']->prepare($sql);
		$statement->bindParam(':id_internship', $id_internship);
		$statement->bindParam(':id_logbook', $id_logbook);
		$statement->execute();


		// Cek logbook
		if ($statement->rowCount() != 1) {
			make_swal2('error', 'Logbook not found', 'Logbook data is not found !');
			redirect_back(1);
			exit;
		}

		$data_logbook = $statement->fetch(PDO::FETCH_ASSOC);

		
		// Gunakan statement prepared untuk menghindari SQL injection
		$sql = "SELECT * FROM student_internship WHERE nim = :nim";
		$stmt = $_SERVER['PDO']->prepare($sql);

		// Bind parameter ke statement prepared
		$stmt->bindParam(':nim', $nim, PDO::PARAM_STR);

		// Eksekusi statement prepared
		$stmt->execute();

		// Periksa apakah hasil kueri menghasilkan satu baris
		if ($stmt->rowCount() != 1) {
			$data_mhs = [];
		} else {
			$data_mhs = $stmt->fetch(PDO::FETCH_ASSOC);
		}

		view('mahasiswa/logbook_internship/detail_logbook_internship', [
			'title' => 'Edit Logbook', 'route' => 'my_internship',
			'data_mhs' => $data_mhs,
			'id_logbook' => $id_logbook,
			'id_internship' => $id_internship, 'data' => $data_logbook
		]);
		break;

endswitch;
