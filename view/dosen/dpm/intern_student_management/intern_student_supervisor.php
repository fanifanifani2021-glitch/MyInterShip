<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Supervisor Profile';
$route = 'intern_student_management';
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';
?>


<?php



$id_internship = base64_decode($_GET['id_internship']);

$sql = "SELECT internship.* FROM internship WHERE internship.id_internship = '$id_internship'";
$exec2 = $_SERVER['KONEKSI_DB']->query($sql);
$internship_data = $exec2->fetch_assoc();

if ($exec2->num_rows != 1) {
	make_swal2('warning', 'Not Allowed', 'Supervisor has not been set !');
	redirect_back(1);
	exit;
}

$nim = $internship_data['nim'];


$id_user_company = $internship_data['id_user_company'];

$sql = "SELECT user_company.* FROM user_company WHERE user_company.id_user_company = '$id_user_company'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('warning', 'Not Allowed', 'Supervisor has not been set !');
	redirect_back(1);
	exit;
}

$data = $exec->fetch_assoc();
$data2 = $exec2->fetch_assoc();

$id_company = $data['id_company'];

$sql3 = "SELECT * FROM company WHERE company.id_company = '$id_company'";
$company = $_SERVER['KONEKSI_DB']->query($sql3)->fetch_assoc();




?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">


					<div class="page-header">
						<h4 class="page-title">Supervisor Profile</h4>
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
								<a href="index.php?page=intern_student_management">Students Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_detail&nim=<?= base64_encode($nim) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Supervisor Profile</a>
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
										<div class="name"><?= $data['user_fullname'] ?></div>
										<div class="job">Supervisor at <?= $company['name'] ?? 'Politeknik Negeri Batam' ?></div>
									</div>

									<!-- GET DATA LECTURER DAN LECTURER PROFILE -->

									<div class="row mt-3">
										<div class="col-md-12">
											<div class="form-group form-group-default">
												<label>Name</label>
												<input type="text" class="form-control" name="name" value="<?= $data['user_fullname']?>">
												<?= form_error('name') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>E-Mail</label>
												<input type="text" class="form-control" name="program_study" value="<?= $data['user_email'] ?>">
												<?= form_error('program_study') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Phone Number</label>
												<input type="email" class="form-control" name="polibatam_email" value="<?= $data['user_phone'] ?>">
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