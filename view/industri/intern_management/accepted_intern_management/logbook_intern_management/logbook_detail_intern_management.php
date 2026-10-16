<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Logbook Detail";
$route = 'accepted_intern_management';
include "./view/industri/template/head.php";

?>

<?php

$nim = base64_decode($_GET['nim']);
$id_logbook = base64_decode($_GET['id_logbook']);
$id_internship = base64_decode($_GET['id_internship']);



// Pengecekan Logbook
$sql = "SELECT * FROM logbook WHERE id_internship = '$id_internship' AND id_logbook = '$id_logbook'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('error', 'Logbook not found !', 'Logbook data is not found !');
	redirect_back(1);
	exit;
}


$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('warning', 'Not Allowed', 'Student doesn`t have access to this page !');
	redirect_back(1);
	exit;
}

$data_mhs = $exec->fetch_assoc();



?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Logbook Detail</h4>
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
								<a href="index.php?page=accepted_intern_management">Accepted Intern</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=detail_intern_management&id_internship=<?= base64_encode($id_internship) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>


							<li class="nav-item">
								<a href="<?= base_url('index.php?page=logbook_intern_management&id_internship=' . base64_encode($id_internship)) ?>">Logbook</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>


							<li class="nav-item">
								<a href="#">Logbook Detail</a>
							</li>





						</ul>
					</div>



					<div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Student Detail
									</div>
								</div>

								<?php

								$sql = "SELECT * FROM logbook WHERE id_logbook = '$id_logbook'";

								$exec = $_SERVER['KONEKSI_DB']->query($sql);

								?>

								<?php foreach ($exec as $data) : ?>
									<!-- Student's Data -->
									<div class="card-body form-row">



										<div class="form-group col-md-6">
											<label>NIM </label>
											<input type="text" class="form-control" value="<?= $data_mhs['nim'] ?>" readonly />
										</div>

										<div class="form-group col-md-6">
											<label>Name </label>
											<input type="text" class="form-control" placeholder="Enter Your Full Name" value="<?= $data_mhs['name'] ?>" readonly>
										</div>

										<div class="form-group col-md-6">

											<label>E-Mail </label>
											<input type="email" class="form-control" placeholder="Enter Your E-Mail" value="<?= $data_mhs['email'] ?>" readonly>


										</div>


										<div class="form-group col-md-6">

											<label>Other E-Mail </label>
											<input type="email" class="form-control" placeholder="Enter Your Other E-Mail" value="<?= $data_mhs['other_email'] ?>" readonly>
										</div>


										<div class="form-group col-md-6">

											<label>No WhatsApp </label>
											<input type="tel" class="form-control" name="no_whatsapp" placeholder="Whatsapp Number" value="<?= $data_mhs['no_whatsapp'] ?>" readonly>
											<?= form_error('no_whatsapp') ?>

										</div>

										<div class="form-group col-md-6">

											<label>Phone Number </label>
											<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data_mhs['phone'] ?>" readonly>

										</div>

									</div>

								<?php endforeach; ?>


							</div>

							<div class="card">
								<div class="card-header">
									<div class="card-title">Logbook Detail
									</div>
								</div>

								<?php

								$sql = "SELECT * FROM logbook WHERE id_logbook = '$id_logbook'";

								$exec = $_SERVER['KONEKSI_DB']->query($sql);

								?>

								<?php foreach ($exec as $data) : ?>
									<!-- Student's Data -->
									<div class="card-body form-row">

										<div class="form-group col-md-4">
											<label>Week Num <b class="text-danger">*</b></label>

											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text" id="basic-addon1">M</span>
												</div>
												<input type="number" min="0" name="week_num" class="form-control" value="<?= $data['week_num'] ?>" />
											</div>
										</div>


										<div class="form-group col-md-4">
											<label>Start Date <b class="text-danger">*</b></label>
											<input type="date" name="start_date" class="form-control" value="<?= $data['start_date'] ?>" />
										</div>

										<div class="form-group col-md-4">
											<label>End Date <b class="text-danger">*</b></label>
											<input type="date" name="end_date" class="form-control" value="<?= $data['end_date'] ?>" />
										</div>


										<div class="form-group col-md-12">
											<label class="label-control">Description <b class="text-danger">*</b></label>
											<textarea class="form-control" id="description" name="description" rows="5" placeholder="Describe all job you`ve done this week"><?= $data['description'] ?></textarea>

										</div>

										<div class="form-group col-md-12">
											<label class="label-control">Documentation <b class="text-danger">*</b></label>
											<textarea class="form-control" id="documentation" name="documentation" rows="5" placeholder="Provide some photos to prove your job"><?= $data['documentation'] ?></textarea>

										</div>







									</div>

								<?php endforeach; ?>


							</div>


						</div>



					</div>


				</div>
			</div>
			<?php include './view/industri/template/footer.php'; ?>
		</div>

	</div>

	<?php include './view/industri/template/script.php'; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>


	<!-- Sweetalert -->
	<link rel='stylesheet' href='./assets/sweetalert2.css'>
	<script src='./assets/sweetalert2.all.js'></script>


	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>


	<script>
		// CKEDITOR Initialize
		$(function() {



			// CKEDITOR
			var description = CKEDITOR.replace('description', {
				language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Describe all job you`ve done this week',
				removeButtons: 'PasteFromWord',
			});



			var documentation = CKEDITOR.replace('documentation', {
				language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Provide some photos to prove your job',
				removeButtons: 'PasteFromWord'
			});






		});
	</script>






</body>

</html>