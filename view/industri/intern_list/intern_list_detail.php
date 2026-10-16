<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Intern Detail";
$route = 'intern_list';
include "./view/industri/template/head.php";

?>

<?php

$id_user_company = $_SESSION['id_user_company'];

$id_internship = base64_decode($_GET['id_internship']);

$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship' AND id_user_company = '$id_user_company'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_soloalert('error', 'Not Allowed', 'Internship data is not found !');
	redirect_back(1);
	exit();
}


$nim = $exec->fetch_array()['nim'];

$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$student_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

$id_kampus = $student_data['id_kampus'];
$sql4 = "SELECT * FROM tb_kampus WHERE id_kampus = '$id_kampus'";
$university = $_SERVER['KONEKSI_DB']->query($sql4)->fetch_assoc();

?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Intern Detail</h4>
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
								<a href="index.php?page=intern_list">Intern List</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Intern Detail</a>
							</li>
						</ul>
					</div>

					<?php
					$sql = "SELECT internship.* FROM internship WHERE nim = '$nim' AND internship.id_internship = '$id_internship'";
					$exec = $_SERVER['KONEKSI_DB']->query($sql);


					foreach ($exec as $row) :

						$registered_date = $row['timestamp_register'];
						$id_internship = $row['id_internship'];
						$id_company = $row['id_company'];

						$sql2 = "SELECT company.name AS company_name FROM company WHERE company.id_company = '$id_company' ";
						$company_name = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc()['company_name'];

						if ( in_array($student_data['nik_dospem'], [NULL,'', '-']) ) {
							$nik_dospem = 0;
							$dospem = 'Not Set';
							$dospem = '<button class="btn btn-round btn-danger">Not Set</button>';
						} else {
							$nik_dospem = $student_data['nik_dospem'];

							$sql2 = "SELECT lecturer.name AS lecturer_name FROM lecturer WHERE nim_nik_unit = '$nik_dospem' ";
							$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
							$dospem = mysqli_fetch_assoc($exec2)['lecturer_name'];
						}


						if ($row['id_user_company'] == '') {
							$user_company = 0;
							$supervisor_industri = '<button class="btn btn-round btn-danger">Not Set</button>';
						} else {
							$user_company = $row['id_user_company'];


							$sql2 = "SELECT company.name AS company_name FROM company, user_company WHERE company.id_company = user_company.id_company AND user_company.id_user_company = '$user_company' ";
							$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

							$sql2 = "SELECT user_company.user_fullname AS supervisor_name FROM user_company WHERE user_company.id_user_company = '$user_company' ";
							$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
							$supervisor_industri = mysqli_fetch_assoc($exec2)['supervisor_name'];
						}


						if (in_array($row['status'], ['YES', 'Yes', 'yes']) === true) {
							$accepted = 1;
							$status = 'Intern Accepted';
							$internship_period = $row['internship_period'] . " Month";
							$internship_period = date('d M Y', strtotime($row['start_date'])) . ' until ' .  date('d M Y', strtotime($row['end_date'])) .  date(' (n)', strtotime($row['end_date']) - strtotime($row['start_date'])) . ' Month(s)';


							$start_date = $row['start_date'];
							$end_date = $row['end_date'];
						} else 

											if (in_array($row['status'], ['FINISH', 'Finish', 'finish']) === true) {
							$accepted = 1;
							$status = 'Intern Accepted and Finished';
							$internship_period = $row['internship_period'] . " Month";
							$internship_period = date('d M Y', strtotime($row['start_date'])) . ' until ' .  date('d M Y', strtotime($row['end_date'])) .  date(' (n)', strtotime($row['end_date']) - strtotime($row['start_date'])) . ' Month(s)';

							$start_date = $row['start_date'];
							$end_date = $row['end_date'];
						} else
											if (in_array($row['status'], ['NO', 'No', 'no']) === true) {
							$accepted = 0;
							$status = 'Intern Not Accepted';
						} else 
											if (in_array($row['status'], ['WAITING', 'Waiting', 'waiting']) === true) {
							$accepted = 0;
							$status = 'Waiting for validation';
						} else {
							$accepted = NULL;
							$status = 'Not Registered';
						}


					endforeach;
					?>



					<div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">


							<div class="card card-profile">
								<div class="card-header" style="background-image: url(viewer.php?src=profile_background); height: 300px; background-size: cover;background-repeat: no-repeat;">
									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="viewer.php?src=get_profile&location=<?= base64_encode($student_data['profile_picture']) ?>" alt="..." class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $student_data['name'] ?></div>
										<div class="job">Student at <?= $university['nama_kampus'] ?? 'Politeknik Negeri Batam' ?></div>
									</div>

									<table class="table table-striped mt-2 mt-4">

										<tbody>
											<tr>
												<th>Registered Date</th>
												<td><?= date('D, d M Y', strtotime($registered_date) ) ?></td>
											</tr>


											<tr>
												<th>Internship Period</th>
												<td><?= $internship_period ?></td>
											</tr>

											<tr>
												<th>Supervisor</th>
												<td><?= $supervisor_industri ?></td>
											</tr>

											<tr>
												<th>Lecturer</th>
												<td><?= $dospem ?></td>
											</tr>




										</tbody>
									</table>




									<!-- <div class="row mt-3">

										<div class="col-md-12">
											<div class="form-group form-group-default">
												<label>Registered Date </label>
												<input type="text" class="form-control" value="<?= date('d M Y', strtotime($registered_date)) ?>">

											</div>
										</div>


										<div class="col-md-12">
											<div class="form-group form-group-default">
												<label>Internship Status </label>
												<input type="text" class="form-control" value="<?= $status ?>">

											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group form-group-default">
												<label>Company Name</label>
												<input type="text" class="form-control" value="<?= $company_name ?>">
											</div>
										</div>

										<?php if ($accepted == 1) : ?>




											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>Polibatam Supervisor </label>
													<input type="text" class="form-control" value="<?= $dospem ?>">
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>Industry Supervisor </label>
													<input type="text" class="form-control" value="<?= $supervisor_industri ?>">

												</div>
											</div>


											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>Start Date </label>
													<input type="text" class="form-control" value="<?= $start_date ?>">
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group form-group-default">
													<label>End Date </label>
													<input type="text" class="form-control" value="<?= $end_date ?>">
												</div>
											</div>

											<div class="col-md-12">
												<div class="form-group form-group-default">
													<label>Internship Period </label>
													<input type="text" class="form-control" value="<?= $internship_period ?> ">
												</div>
											</div>

										<?php endif; ?>



									</div> -->

									<div class="table-responsive">
										<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround">
											<?php if ($accepted !== NULL) : ?>
												<a href="index.php?page=intern_list_registration&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fab fa-wpforms mr-1"></i>Registration Data</a>

												<!-- <a href="index.php?page=intern_list_student&id_internship=<?= base64_encode($id_internship) ?>"  class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-graduation-cap mr-1"></i>Student Profile</a> -->
												<!-- <a target="_blank" href="index.php?viewer=location&location=<?= base64_encode('https://talent-hub.id/index.php?page=mhsprofile&nim=' .base64_encode($nim) ) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-graduation-cap mr-1"></i>Student Profile</a> -->
												<a target="_blank" href="index.php?viewer=url&location=<?= base64_encode( 'https://talent-hub.id/index.php?page=mhsprofile&nim=' .base64_encode($nim) ) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-graduation-cap mr-1"></i>Student Profile</a>

												<a href="index.php?page=intern_list_lecturer&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-user-secret mr-1"></i>Lecturer Profile</a>
											<?php endif; ?>

											<?php if ($accepted == 1) : ?>
												<a href="index.php?page=intern_list_jobdesk&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-list mr-1"></i>Jobdesc</a>

												<?php if ($student_data['program_study'] == 'LPI') : ?>
													<a href="index.php?page=intern_list_competency&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-star mr-1"></i>Student Competency</a>
												<?php endif; ?>



											<?php endif; ?>
										</div>
									</div>

									<div class="table-responsive">
										<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround ">

											<?php if ($accepted == 1) : ?>
												<a href="index.php?page=intern_list_discussion&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-comments mr-1"></i>Discussion</a>

												<a href="index.php?page=intern_list_logbook&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-book mr-1"></i>Logbook</a>

												<a href="index.php?page=intern_list_attendance&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-calendar mr-1"></i>Attendance</a>
												<a href="index.php?page=intern_list_student_feedback&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-comment-dots mr-1"></i>Student Feedback</a>
												<a href="index.php?page=intern_list_industry_feedback&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-comment-dots mr-1"></i>Industry Feedback</a>
												<a href="index.php?page=intern_list_final_report&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-file mr-1"></i>Final Report</a>


											<?php endif; ?>
										</div>
									</div>

								</div>


							</div>
						</div>


						<!-- Internship History -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Internship History
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

												$sql = "SELECT internship.*, company.name FROM internship, company WHERE company.id_company = internship.id_company AND internship.status NOT IN ('No') AND internship.nim = '$nim'";
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
															<?php if ($intern_data['id_internship'] == $id_internship) {
																echo "<span class='badge badge-success'>Current Internship</sp>";
															} else { ?>
																<button type="button" class="btn btn-secondary btn-sm"> <i class="fas fa-clock mr-1"></i>Internship History</button>
																<!-- <div class="btn-group btn-round">
															<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																Action
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																<button class="dropdown-item" onclick="window.location.href = 'index.php?page=intern_guidance_registration&id_internship=<?= base64_encode($intern_data['id_internship']) ?>'">Registration Data</button>
																<div class="dropdown-divider"></div>

																<button class="dropdown-item" onclick="window.location.href = 'index.php?page=intern_guidance_logbook&id_internship=<?= base64_encode($intern_data['id_internship']) ?>'">Logbook</button>
																<button class="dropdown-item" onclick="window.location.href = 'index.php?page=intern_guidance_attendance&id_internship=<?= base64_encode($intern_data['id_internship']) ?>'">Attendance</button>
															

															</div>
														</div> -->
															<?php } ?>
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
			<?php include './view/industri/template/footer.php'; ?>
		</div>

	</div>
	<!--   Core JS Files   -->
	<script src="./assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="./assets/js/core/popper.min.js"></script>
	<script src="./assets/js/core/bootstrap.min.js"></script>
	<!-- jQuery UI -->
	<script src="./assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="./assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
	<!-- Bootstrap Toggle -->
	<script src="./assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
	<!-- jQuery Scrollbar -->
	<script src="./assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>
	<!-- Atlantis JS -->
	<script src="./assets/js/atlantis.min.js"></script>

	<link rel='stylesheet' href='./assets/sweetalert2.css'>
	<script src='./assets/sweetalert2.all.js'></script>

	<script>
		$(document).ready(function() {

			$('#basic-datatables').DataTable({});

		});


		function swal_confirm(id) {
			swal.fire({
				title: 'Delete Confirmation',
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
							window.location.reload(1);
						},
						error: function(response) {
							Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
						}
					});

				}
			})

		}
	</script>




</body>

</html>