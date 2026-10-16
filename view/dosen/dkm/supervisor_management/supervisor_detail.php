<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Lecturer Detail";
$route = 'supervisor_management';
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dkm.php';
?>

<?php
$nim_nik_unit = base64_decode($_GET['nik']);

$sql = $_SERVER['KONEKSI_DB']->query("SELECT * FROM lecturer WHERE nim_nik_unit = '$nim_nik_unit'");
$data_lecturer = $sql->fetch_object();

if ($sql->num_rows != 1) {
	make_soloalert('error', 'Not Allowed', 'Lecturer data is not found !');
	redirect_back(1);
	exit;
}

$sql_lecturer = "SELECT lecturer.*, lecturer_profile.* FROM lecturer LEFT JOIN lecturer_profile ON lecturer.nim_nik_unit = lecturer_profile.nim_nik_unit WHERE lecturer.nim_nik_unit = '$nim_nik_unit' ";
$data_lecturer = $_SERVER['KONEKSI_DB']->query($sql_lecturer);

if ($data_lecturer->num_rows < 1) {
	make_soloalert('error', 'Not Allowed', 'Lecturer data is not found !');
	redirect_back(1);
	exit;
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
						<h4 class="page-title">Lecturer Detail</h4>
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
								<a href="index.php?page=supervisor_management">Lecturer Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Lecturer Detail</a>
							</li>



						</ul>
					</div>

					<div class="row">


						<!-- Lecturer's Profile -->
						<?php foreach ($data_lecturer as $i => $data) : ?>

							<div class="col-md-12">
								<div class="card card-profile">
									<div class="card-header" style="background-image: url('./assets/img/blogpost.jpg'); height: 300px; background-size: cover;background-repeat: no-repeat;">
										<div class="profile-picture">
											<div class="avatar avatar-xxl">
												<img src="./upload/profile_picture/<?= $data['profile_picture'] ?>" alt="..." class="avatar-img rounded-circle">
											</div>
										</div>
									</div>



									<div class="card-body">
										<div class="user-profile text-center">
											<div class="name"><?= $data['name'] ?></div>
											<div class="job">Dosen <?= $data['user_type'] ?></div>
										</div>

										<!-- GET DATA LECTURER DAN LECTURER PROFILE -->

										<div class="row mt-3">
											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>Name</label>
													<input type="text" class="form-control" name="name" placeholder="Enter Your Full Name" value="<?= $data['name'] ?>">
													<?= form_error('name') ?>

												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>Polibatam E-Mail></label>
													<input type="email" class="form-control" name="polibatam_email" placeholder="Enter Your Polibatam E-Mail" value="<?= $data['email_polibatam'] ?>" readonly>
													<?= form_error('polibatam_email') ?>

												</div>
											</div>

										</div>

										<div class="row mt-3">

											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>Other E-Mail</label>
													<input type="email" class="form-control" name="other_email" placeholder="Enter Your Other E-Mail" value="<?= $data['other_email'] ?>">
													<?= form_error('other_email') ?>

												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>Phone Number</label>
													<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data['phone'] ?>">
													<?= form_error('phone') ?>

												</div>
											</div>
										</div>

									</div>

								</div>
							</div>


						<?php endforeach; ?>



						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Student List
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table id="basic-datatables" class="display table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>NIM</th>
													<th>Name</th>
													<th>Company</th>
													<th>Internship Period</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>
												<?php
												// $sql = "SELECT distinct(internship.nim) AS nim, internship.*, student_internship.name, user_company.*, company.name AS company_name FROM internship, user_company, company, student_internship WHERE company.id_company = internship.id_company AND user_company.id_user_company = internship.id_user_company AND student_internship.nim = internship.nim AND internship.nik = '$nim_nik_unit' ";
												$sql = "SELECT * FROM student_internship WHERE student_internship.nik_dospem = '$nim_nik_unit' ";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);
												foreach ($exec as $i => $data) :

													$nim = $data['nim'];

													$sql_internship = "SELECT internship.*, company.* FROM internship INNER JOIN company ON company.id_company = internship.id_company WHERE internship.id_internship IN ( SELECT MAX(id_internship) FROM internship WHERE internship.nim = '$nim' ) ";
													$internship = $_SERVER['KONEKSI_DB']->query($sql_internship);


													if ( $internship->num_rows == 0 ) {
														$nama_perusahaan = '-';
														$internship_period = 'NaN';
													} else {
														$data_internship = $internship->fetch_assoc();

														$nama_perusahaan = $data_internship['name'];

														$start_date = $data_internship['start_date'];
														$end_date = $data_internship['end_date'];

														$internship_period = date('D, d M Y', strtotime($start_date)). ' until ' .date('D, d M Y', strtotime($end_date)). ' (' .range_time_elapsed_string($start_date, $end_date). ')';
													}

					

												?>

													<tr>
														<td><?= $i + 1 ?>.</td>
														<td><?= $data['nim'] ?></td>
														<td><?= $data['name'] ?></td>
														<td><?= $nama_perusahaan ?></td>
														<td><?= $internship_period ?></td>
														<td><a class="btn btn-info btn-sm" href="index.php?page=student_intern_detail&nim=<?= base64_encode($data['nim']) ?>"><i class="fas fa-info-circle mr-1"></i>Student Detail</a></td>
													</tr>

												<?php endforeach; ?>

											</tbody>


										</table>
									</div>
								</div>
							</div>
						</div>



					</div>
				</div>
			</div>
			<?php include './view/dosen/template/footer.php'; ?>
		</div>

	</div>

	<?php include "./view/dosen/template/script.php"; ?>
	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<link rel='stylesheet' href='./assets/sweetalert2.css'>
	<script src='./assets/sweetalert2.all.js'></script>

	<script>
		function swal_confirm(id) {
			swal.fire({
				title: 'Confirm Delete',
				text: 'Are you sure you want to delete this data?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						url: "./ajax/delLecturer.php",
						type: "POST",
						data: {
							nim_nik_unit: id
						},
						dataType: 'text',
						success: function(response) {

							console.log(id);
							console.log(response);

							if (response == "OK") {

								swal.fire({
									title: 'Deleting Data Success',
									text: 'Lecturer data have been succesfully deleted !',
									icon: 'success',
									timer: 2000,
									showCancelButton: false,
									showConfirmButton: true,
								})

								setTimeout(function() {
									window.location.reload(1);
								}, 1500);


							} else {
								swal.fire({
									title: 'Deleting Data Failed',
									text: 'Please try again later !',
									icon: 'error',
									timer: 2000,
									showCancelButton: false,
									showConfirmButton: true,
								})

								setTimeout(function() {
									window.location.reload(1);
								}, 1500);

							}
						},
						error: function(response) {
							Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
						}
					});

				}
			})

		}
	</script>


	<script>
		$(document).ready(function() {
			$('#basic-datatables').DataTable({});

		});
	</script>



</body>

</html>