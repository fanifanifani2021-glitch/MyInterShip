<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Lecturer Profile';
$route = 'my_internship';
include "./view/mahasiswa/template/head.php";


?>


<?php


$nim = $_SESSION['nim'];
$id_internship = base64_decode($_GET['id_internship']);


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


$exec = $exec->fetch(PDO::FETCH_ASSOC);


$sql = "SELECT * FROM student_internship WHERE nim = :nim LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':nim' => $nim]);

if ($stmt->rowCount() != 1) {
	$data2 = [];
} else {
	$data2 = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (in_array($data2['nik_dospem'], ['', '-', NULL])) {
	make_swal2('warning', 'Not Allowed', 'Your Lecturer is not set yet !');
	redirect_back(1);
	exit;
}


$sql = "SELECT lecturer_profile.*, lecturer.* FROM lecturer 
	LEFT JOIN lecturer_profile ON lecturer_profile.nim_nik_unit = lecturer.nim_nik_unit 
	WHERE lecturer.nim_nik_unit = :nik_dospem LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':nik_dospem' => $data2['nik_dospem']]);

if ($stmt->rowCount() != 1) {
	$data = [];
} else {
	$data = $stmt->fetch(PDO::FETCH_ASSOC);
}

$id_kampus = $data['id_kampus'];

$sql = "SELECT tb_kampus.* FROM tb_kampus WHERE tb_kampus.id_kampus = :id_kampus LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_kampus' => $id_kampus]);
if ($stmt->rowCount() != 1) {
	$university = [];
} else {
	$university = $stmt->fetch(PDO::FETCH_ASSOC);
}


?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Lecturer Profile</h4>
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="index.php">
									<i class="flaticon-home"></i>
								</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=my_internship">My Internship</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Lecturer Profile</a>
							</li>
						</ul>
					</div>



					<!-- Form -->
					<div class="row">

						<div class="col-md-12">


							<div class="card card-profile">
								<div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 300px; background-size: cover;background-repeat: no-repeat;">

									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="viewer.php?src=get_profile&location=<?= base64_encode($data['profile_picture']) ?>" alt="..." class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $data['name'] ?> <a class="text-sm" onclick="copyToClipboard('<?= $data['name'] ?>')"><i class="fas fa-copy"></i></a></div>
										<div class="job">Lecturer in <?= $university['nama_kampus'] ?? 'Politeknik Negeri Batam' ?></div>
									</div>

									<!-- GET DATA LECTURER DAN LECTURER PROFILE -->



									<div class="row mt-3">

										<div class="col-md-12">
											<div class="form-group ">
												<label>Nama</label>
												<input type="text" class="form-control" name="name" placeholder="Enter Your Full Name" value="<?= $data['name'] ?>">

											</div>
										</div>


										<div class="col-md-6">
											<div class="form-group ">
												<label>NIK</label>
												<input type="text" class="form-control" name="name" placeholder="Enter Your Full Name" value="<?= $data['nim_nik_unit'] ?>">

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<label>E-Mail</label>
												<input type="text" class="form-control" name="program_study" placeholder="Enter Your Full Name" value="<?= $data['email_polibatam'] ?>">

											</div>
										</div>

										<?php if (!in_array($data['other_email'], [null, '', '-'])) : ?>

											<div class="col-md-6">
												<div class="form-group">
													<label>Other E-Mail</label>
													<input type="email" class="form-control" name="polibatam_email" placeholder="Enter Your Polibatam E-Mail" value="<?= $data['other_email'] ?>">

												</div>
											</div>
										<?php endif; ?>



										<?php if (!in_array($data['phone'], [null, '', '-'])) : ?>
											<div class="col-md-6">
												<div class="form-group">
													<label>Phone Number</label>
													<input type="email" class="form-control" name="other_email" placeholder="Enter Your Other E-Mail" value="<?= $data['phone'] ?>">

												</div>
											</div>

										<?php endif; ?>

									</div>


								</div>


							</div>




						</div>


					</div>


				</div>
			</div>

			<?php include "./view/mahasiswa/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/mahasiswa/template/min-script.php"; ?>


</body>

</html>