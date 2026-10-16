<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Student Detail";
$route = 'intern_student_management';
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';

?>

<?php
$nim = base64_decode($_GET['nim']);

$sql = $_SERVER['KONEKSI_DB']->query("SELECT * FROM student_internship WHERE nim = '$nim'");
$data_student = $sql->fetch_object();


// Cek apakah mhs terdaftar sebagai mhs magang
if ($sql->num_rows != 1) {
	make_soloalert('error', 'Not Allowed', 'Student data is not found !');
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
						<h4 class="page-title">Student Detail</h4>
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
								<a href="#">Student Detail</a>
							</li>



						</ul>
					</div>

					<div class="row">


						<!-- Lecturer's Profile -->
						<div class="col-md-12">
							<div class="card card-profile">
								<div class="card-header" style="background-image: url(viewer.php?src=profile_background); height: 300px; background-size: cover;background-repeat: no-repeat;">

									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="viewer.php?src=get_profile&location=<?= base64_encode( $data_student->profile_picture ) ?>" alt="..." class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $data_student->name ?></div>
										<div class="job">Student at <?= $university['nama_kampus'] ?? 'Politeknik Negeri Batam' ?></div>
									</div>

									<!-- GET DATA LECTURER DAN LECTURER PROFILE -->

									<?php
									$sql_lecturer = "SELECT * FROM student_internship LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study WHERE student_internship.nim = '$nim' ";
									$data_lecturer = $_SERVER['KONEKSI_DB']->query($sql_lecturer);
									?>

									<?php foreach ($data_lecturer as $i => $data) : ?>

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
													<label>Study Program</label>
													<input type="text" class="form-control" name="program_study" placeholder="Enter Your Full Name" value="<?= $data['kode_prodi'] ?> <?= $data['prodi'] ?>">
													<?= form_error('program_study') ?>

												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>E-Mail</label>
													<input type="email" class="form-control" name="polibatam_email" placeholder="Enter Your Polibatam E-Mail" value="<?= $data['email'] ?>">
													<?= form_error('polibatam_email') ?>

												</div>
											</div>


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


											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>Whatsapp Number</label>
													<input type="text" class="form-control" name="phone" placeholder="Phone" value="<?= $data['no_whatsapp'] ?>">
													<?= form_error('phone') ?>

												</div>
											</div>

										</div>
									<?php endforeach; ?>


								</div>


							</div>
						</div>



						<!-- Internship History -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Internship History List
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table id="basic-datatables" class="display table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>Register Date</th>
													<th>Company Name</th>
													<th>Status</th>
													<th>Start Date</th>
													<th>End Date</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>

												<?php

												$sql = "SELECT internship.*, company.name FROM internship, company WHERE company.id_company = internship.id_company AND internship.nim = '$nim'";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);
												?>

												<?php foreach ($exec as $i => $intern_data) : ?>

													<tr>
														<td><?= $i + 1 ?>.</td>
														<td><?= date('d-M-Y', strtotime($intern_data['timestamp_register'])) ?></td>
														<td><?= $intern_data['name'] ?></td>
														<td><?= $intern_data['status'] ?></td>
														<td><?= date('d-M-Y', strtotime($intern_data['start_date'])) ?></td>
														<td><?= date('d-M-Y', strtotime($intern_data['end_date'])) ?></td>

														<td>
															<button type="button" onclick="window.location.href='index.php?page=intern_student_internship_detail&id_internship=<?= base64_encode($intern_data['id_internship']) ?>' " class="btn btn-secondary btn-sm"> <i class="fas fa-info-circle mr-2"></i>Detail</button>

														</td>
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

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>


	<!-- AJAX Script -->
	<script>
		$(document).ready(function() {

			$('#basic-datatables').DataTable({});

			<?php if (old('ModalAssignSupervisor') !== NULL) :
				echo "$('#ModalAssignSupervisor').modal('show')";
			endif; ?>
			search_lecturer($('#lecturer_nik').val());

		});

		function search_lecturer(nik) {
			var submit_btn = document.getElementById('submit_btn');
			var lecturer_waiting = document.getElementById('lecturer_waiting');
			var lecturer_notification = document.getElementById('lecturer_notification');
			var search_result = document.getElementsByClassName('search-result');
			var search_status = document.getElementById('search_status');
			search_status.value = 'Failed';
			submit_btn.disabled = true;

			if (nik.length >= 6) {
				$('#lecturer_nik').val($('#lecturer_nik').val().substr(0, 6));
			}

			if (nik.length != 0 && nik.length != 6) {

				for (z = 0; z <= search_result.length - 1; z++) {
					search_result[z].setAttribute('hidden', true);
					search_result[z].removeAttribute('value');
				}

				lecturer_waiting.hidden = true;
				lecturer_notification.hidden = false;
				search_status.value = 'Failed';
			}

			if (nik.length != 0 && nik.length == 6) {

				lecturer_waiting.hidden = false;
				lecturer_notification.hidden = true;

				$.ajax({

					url: './ajax/getDataSupervisorPolibatam.php',
					type: 'post',
					data: {
						'nim_nik_unit': nik
					},
					dataType: 'json',


					success: function(response) {


						var len = response.length;
						if (response[0]['hasil_pencarian'] == "Berhasil") {
							console.log(response[0]);

							submit_btn.disabled = false;
							lecturer_waiting.hidden = true;
							lecturer_notification.hidden = true;


							search_status.value = 'Success';

							document.getElementById('lecturer_name').value = response[0]['name'];
							document.getElementById('lecturer_email').value = response[0]['email'];

							for (z = 0; z <= search_result.length - 1; z++) {
								search_result[z].removeAttribute('hidden');
							}

						} else {

							lecturer_waiting.hidden = true;
							lecturer_notification.hidden = false;


							search_status.value = 'Failed';

							for (z = 0; z <= search_result.length - 1; z++) {
								search_result[z].setAttribute('hidden', true);
								search_result[z].removeAttribute('value');
							}
						}
					},

					error: function(response) {
						lecturer_waiting.hidden = true;
						lecturer_notification.hidden = false;

						search_status.value = 'Failed';

						for (z = 0; z <= search_result.length - 1; z++) {
							search_result[z].setAttribute('hidden', true);
							search_result[z].removeAttribute('value');
						}
					}

				});





			}
		}
	</script>





</body>

</html>