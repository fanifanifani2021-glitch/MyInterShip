<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Supervisor Profile';
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


$id_user_company = $internship_data['id_user_company'];

$sql = "SELECT user_company.*, company.* FROM user_company 
	LEFT JOIN company ON company.id_company = user_company.id_company 
	WHERE user_company.id_user_company = :id_user_company";

$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_user_company' => $id_user_company]);


if ($stmt->rowCount() != 1) {
	make_swal2('warning', 'Not Allowed', 'Supervisor not been set yet !');
	redirect_back(1);
	exit;
}

$data = $stmt->fetch(PDO::FETCH_ASSOC);
$data2 = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>


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

								<!-- Student's Data -->
								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $data['user_fullname'] ?> <a class="text-sm" onclick="copyToClipboard('<?= $data['user_fullname'] ?>')"><i class="fas fa-copy"></i></a></div>
										<div class="job"><?= $data['user_type'] ?> at <?= $data['name'] ?></div>
									</div>
								</div>

								<div class="card-body form-row">

									<div class="form-group col-md-12">

										<label>Name </label>

										<input type="text" class="form-control <?= show_error('name') ?>" disabled name="user_fullname" value="<?= $data['user_fullname']; ?>" placeholder="Supervisor Name" />



									</div>


									<div class="form-group col-md-6">

										<label>E-Mail </label>

										<div class="input-group">
											<input type="email" class="form-control <?= show_error('company_email') ?>" disabled name="company_email" value="<?= $data['user_email']; ?>" placeholder="Supervisor E-Mail" />
											<div class="input-group-append">
												<!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
												<a class="btn btn-outline-secondary" href="mailto:<?= $data['user_email']; ?>" target="_blank"><i class="fas fa-envelope mr-1"></i>E-Mail</a>
											</div>
										</div>


									</div>


									<div class="form-group col-md-6">

										<label>Phone Number </label>

										<div class="input-group">
											<input type="tel" class="form-control <?= show_error('user_phone') ?>" disabled name="user_phone" value="<?= $data['user_phone']; ?>" placeholder="Supervisor E-Mail" />
											<div class="input-group-append">
												<!-- <a class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i>Show</a> -->
												<a class="btn btn-outline-secondary" href="tel:<?= $data['user_phone']; ?>" target="_blank"><i class="fas fa-phone mr-1"></i>Call</a>
											</div>
										</div>


										<?= form_error('phone') ?>

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