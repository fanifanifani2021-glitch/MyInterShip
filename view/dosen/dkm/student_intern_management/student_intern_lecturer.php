<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Lecturer Profile'; $route = 'student_intern_management';
include "./view/dosen/template/head.php";


?>


<?php



$id_internship = base64_decode($_GET['id_internship']);

$sql = "SELECT internship.* FROM internship WHERE internship.id_internship = '$id_internship'";
$exec2 = $_SERVER['KONEKSI_DB']->query($sql);
$internship_data = $exec2->fetch_assoc();

if ( $exec2->num_rows != 1 ) {
	make_swal2('warning', 'Not Found', 'Internship data is not found!');
	redirect_back(1); exit;
}


$nim = $internship_data['nim'];

$sql = "SELECT student_internship.* FROM student_internship WHERE student_internship.nim = '$nim'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ( $exec2->num_rows != 1 ) {
	make_swal2('warning', 'Not Found', 'Student data is not found!');
	redirect_back(1); exit;
}

$student_data = $exec->fetch_assoc();
$nik_dospem = $student_data['nik_dospem'];

$sql = "SELECT lecturer.* FROM lecturer WHERE lecturer.nim_nik_unit = '$nik_dospem'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);
$data = $exec->fetch_assoc();

$id_kampus = $data['id_kampus'];
$sql_kampus = "SELECT * FROM tb_kampus WHERE id_kampus = '$id_kampus'";
$university = $_SERVER['KONEKSI_DB']->query($sql_kampus)->fetch_assoc();



$sql2 = "SELECT lecturer_profile.* FROM lecturer_profile WHERE lecturer_profile.nim_nik_unit = '$nik'";
$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
$data2 = $exec2->fetch_assoc();
if ( $exec->num_rows != 1 ) {
	make_swal2('warning', 'Not Found', 'Leturer has not been set yet !');
	redirect_back(1); exit;
}



?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>


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
								<a href="index.php?page=student_intern_management">Students Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=student_intern_detail&nim=<?= base64_encode($internship_data['nim']) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
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
											<img src="viewer.php?src=get_profile&location=<?= base64_encode($data['profile_picture']) ?? 'profile.jpg' ?>" alt="..." class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $data['name'] ?></div>
										<div class="job">Lecturer at <?= $university['nama_kampus'] ?? 'Politeknik Negeri Batam' ?></div>
									</div>

									<!-- GET DATA LECTURER DAN LECTURER PROFILE -->

									<div class="row mt-3">
										<div class="col-md-12">
											<div class="form-group form-group-default">
												<label>Name</label>
												<input type="text" class="form-control" name="name" value="<?= $data['name']?>">
												<?= form_error('name') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>NIK</label>
												<input type="text" class="form-control" name="name" value="<?= $data['nim_nik_unit']?>">
												<?= form_error('name') ?>

											</div>
										</div>



										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>E-Mail</label>
												<input type="text" class="form-control" name="program_study" value="<?= $data['email_polibatam'] ?>">
												<?= form_error('program_study') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Other E-Mail</label>
												<input type="text" class="form-control" name="program_study" value="<?= $data2['other_email'] ?>">
												<?= form_error('program_study') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Phone Number</label>
												<input type="email" class="form-control" name="polibatam_email" value="<?= $data2['phone'] ?>">
												<?= form_error('polibatam_email') ?>

											</div>
										</div>



									</div>


								</div>


							</div>
						</div>


					</div>


				</div>
			</div>

			<?php include "./view/dosen/template/footer.php"; ?>

		</div>
		
	</div>

	<?php include "./view/dosen/template/script.php"; ?>



</body>
</html>