<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Coordinator Detail";
$route = 'intern_co_management';
include "./view/upkpk/template/head.php";

?>

<?php
$nim_nik_unit = base64_decode($_GET['nik']);

$sql_lecturer = "SELECT lecturer.*, lecturer_profile.* FROM lecturer LEFT JOIN lecturer_profile ON lecturer.nim_nik_unit = lecturer_profile.nim_nik_unit WHERE lecturer.nim_nik_unit = '$nim_nik_unit' ";
$sql = $_SERVER['KONEKSI_DB']->query($sql_lecturer);

if ($sql->num_rows != 1) {
	make_soloalert('error', 'Edit Data Failed', 'Lecturer data is not found !');
	redirect_back(1);
	exit;
}

$data_lecturer = $sql->fetch_assoc();

?>

<body>
	<div class="wrapper">

		<?php include "./view/upkpk/template/main-header.php"; ?>
		<?php include "./view/upkpk/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">


					<div class="page-header">
						<h4 class="page-title">Coordinator Detail</h4>
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
								<a href="index.php?page=intern_co_management">Coordinator Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Coordinator Detail</a>
							</li>
						</ul>
					</div>

					<div class="row">


						<!-- Lecturer's Profile -->
						<div class="col-md-12">
							<div class="card card-profile">
								<div class="card-header" style="background-image: url('./assets/img/blogpost.jpg'); height: 300px; background-size: cover;background-repeat: no-repeat;">

									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="./upload/profile_picture/<?= $data_lecturer['profile_picture'] ?>" alt="..." class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<?php

								$data = $data_lecturer;
								$prodi_koor = $data['prodi_koor'];
								?>


								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $data['name'] ?></div>
										<div class="job">Lecturer</div>
									</div>

									<!-- GET DATA LECTURER DAN LECTURER PROFILE -->

									<div class="row mt-3">
										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Name</label>
												<input type="text" class="form-control" name="name" value="<?= $data['name'] ?>">
												<?= form_error('name') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Polibatam E-Mail</label>
												<input type="email" class="form-control" name="polibatam_email" value="<?= $data['email_polibatam'] ?>">
												<?= form_error('polibatam_email') ?>

											</div>
										</div>

									</div>

									<div class="row mt-3">

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Other E-Mail <b class="text-danger">*</b></label>
												<input type="email" class="form-control" name="other_email" value="<?= $data['other_email'] ?>">
												<?= form_error('other_email') ?>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group form-group-default">
												<label>Phone Number <b class="text-danger">*</b></label>
												<input type="text" class="form-control" name="phone" value="<?= $data['phone'] ?>">
												<?= form_error('phone') ?>

											</div>
										</div>
									</div>


								</div>

							</div>
						</div>


						<!-- List DPM -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Supervisor List in Study Program <?= $prodi_koor ?>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table id="basic-datatables2" class="display table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>NIK</th>
													<th>Name</th>
													<th>Status</th>
													<th>Total Student</th>
												</tr>
											</thead>

											<tbody>
												<?php
												$sql = "SELECT * FROM lecturer WHERE nim_nik_unit IN ( SELECT  DISTINCT(nik_dospem) FROM student_internship WHERE program_study = '" . $prodi_koor . "' )";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);
												foreach ($exec as $i => $data) :

													$nik = $data['nim_nik_unit'];

													$total_mhs = $_SERVER['KONEKSI_DB']->query("SELECT COUNT( DISTINCT(nim) ) AS total_mhs FROM student_internship WHERE nik_dospem = '$nik' ")->fetch_assoc()['total_mhs'];


												?>

													<tr>
														<td><?= $i + 1 ?>.</td>
														<td><?= $data['nim_nik_unit'] ?></td>
														<td><?= $data['name'] ?></td>
														<td><?= $data['status'] ?></td>

														<!-- Hitung person nya -->
														<td><?= $total_mhs ?? '0'; ?> Person</td>
													</tr>

												<?php endforeach; ?>

											</tbody>


										</table>
									</div>
								</div>
							</div>
						</div>

						<!-- List Dosen Pembimbing -->
						<div class="col-md-12">
							<div id="filter_result3"></div>
							<div id="filter_result2"></div>
						</div>



						<!-- List Mahasiswa Magang -->
						<div class="col-md-12">
							<div id="filter_result2"></div>
							<div id="filter_result"></div>
						</div>





					</div>
				</div>
			</div>
			<?php include './view/upkpk/template/footer.php'; ?>
		</div>

	</div>
	<?php include './view/upkpk/template/script.php'; ?>

	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<script>
		$(document).ready(function() {
			show_data();
			show_lecturer();
		});
	</script>


	<script type='text/javascript'>
		function show_data() {

			let spinner = "<i class='fas fa-spinner fa-spin mr-1 fa-2x'></i> <span>Searching . . .</span>";
			$('#filter_result2').html(spinner);



			$.ajax({
				type: 'POST',
				url: 'index.php?form=filter_intern_student_list',
				data: {
					'coor': '1',
					'major': 'All',
					'program_study': '<?= $prodi_koor ?>',
					'token': '<?= $_SESSION['token'] ?>',
				},
				success: function(data, textStatus, XHR) {
					console.log("berhasil");

					$('#filter_result2').html('');
					$('#filter_result').html(data);

					$('#basic-datatables').DataTable({});
				},
				error: function(data) {
					console.log(data);
					$('#filter_result2').html('');
					$('#filter_result').html('Failed to fetch data !');
				},
			});


		}

		function show_lecturer() {

			let spinner = "<i class='fas fa-spinner fa-spin mr-1 fa-2x'></i> <span>Searching . . .</span>";
			$('#filter_result2').html(spinner);



			$.ajax({
				type: 'POST',
				url: 'index.php?form=filter_lecturer',
				data: {
					'coor': '1',
					'major': 'All',
					'program_study': '<?= $prodi_koor ?>',
					'token': '<?= $_SESSION['token'] ?>',
				},
				success: function(data, textStatus, XHR) {
					console.log("berhasil");

					$('#filter_result3').html('');
					$('#filter_result2').html(data);

					$('#basic-datatables2').DataTable({});
				},
				error: function(data) {
					console.log(data);
					$('#filter_result3').html('');
					$('#filter_result2').html('Failed to fetch data !');
				},
			});


		}
	</script>




</body>

</html>