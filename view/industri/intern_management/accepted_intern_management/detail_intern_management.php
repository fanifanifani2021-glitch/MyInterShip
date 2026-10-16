<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Student Detail";
$route = 'accepted_intern_management';
include "./view/industri/template/head.php";

?>

<?php
$id_internship = base64_decode($_GET['id_internship']);

$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
	redirect_back(1);
	exit;
}

$internship_data = $exec->fetch_assoc();
$nim = $internship_data['nim'];


$sql = "SELECT * FROM student_internship LEFT JOIN program_study ON student_internship.program_study = program_study.kode_prodi WHERE student_internship.nim = '$nim' ";
$student_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

$id_kampus = $student_data['id_kampus'];
$sql3 = "SELECT tb_kampus.* FROM tb_kampus WHERE id_kampus = '$id_kampus' ";
$university = $_SERVER['KONEKSI_DB']->query($sql3)->fetch_assoc();



?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>

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
								<a href="index.php?page=accepted_intern_management">Accepted Intern</a>
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
								<div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 250px; background-size:cover;">
									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="viewer.php?src=get_profile&location=<?= base64_encode($student_data['profile_picture']) ?>" alt="Student Profile Picture" class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="user-profile text-center">
										<div class="name"><?= $student_data['name'] ?></div>
										<div class="desc">Student at <?= $university['nama_kampus'] ?? 'Politeknik Negeri Batam' ?></div>
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

										if (in_array($student_data['nik_dospem'], [NULL, '', '-'])) {
											$nik_dospem = 0;
											$dospem = 'Not Set';
											$dospem = '<button class="btn btn-round btn-danger">Not Set</button>';
										} else {
											$nik_dospem = $student_data['nik_dospem'];

											$sql2 = "SELECT lecturer.name AS lecturer_name FROM lecturer WHERE nim_nik_unit = '$nik_dospem' ";
											$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
											$lecturer_data = mysqli_fetch_assoc($exec2);
											$dospem = $lecturer_data['lecturer_name'];
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
											$internship_period = date('d M Y', strtotime($row['start_date'])) . ' until ' .  date('d M Y', strtotime($row['end_date'])) . ' (' . range_time_elapsed_string($row['start_date'], $row['end_date']) . ')';

											$start_date = $row['start_date'];
											$end_date = $row['end_date'];
										} else 

											if (in_array($row['status'], ['FINISH', 'Finish', 'finish']) === true) {
											$accepted = 1;
											$status = 'Intern Accepted and Finished';
											$internship_period = date('d M Y', strtotime($row['start_date'])) . ' until ' .  date('d M Y', strtotime($row['end_date'])) .  ' (' . range_time_elapsed_string($row['start_date'], $row['end_date']) . ')';
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

									<table class="table table-striped mt-2 mt-4">

										<tbody>
											<tr>
												<th>Registered Date</th>
												<td><?= date('D, d M Y', strtotime($registered_date)) ?></td>
											</tr>

											<tr>
												<th>Study Program</th>
												<td><?= $student_data['jenjang'] . ' ' . $student_data['study_program'] ?></td>
											</tr>



											<?php if ($accepted == 1) : ?>


												<tr>
													<th>Internship Period</th>
													<td><?= $internship_period ?> </td>
												</tr>

												<tr>
													<th>Supervisor</th>
													<td><a href="<?= base_url('index.php?page=supervisor_intern_management&&id_internship=' . base64_encode($id_internship)) ?>"><?= $supervisor_industri ?></a></td>
												</tr>

												<tr>
													<th>Lecturer</th>
													<td><a href="<?= base_url('index.php?page=lecturer_intern_management&id_internship=' . base64_encode($id_internship)) ?>"><?= $dospem ?></a></td>
												</tr>


											<?php endif; ?>


										</tbody>
									</table>


									<div class="table-responsive">
										<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround">
											<?php if ($accepted != 0) : ?>

												<button class="btn btn-md btn-secondary btn-block mr-1 mt-1" data-toggle="modal" data-target="#ModalAssignSupervisor"><i class="fas fa-user-plus mr-1"></i>Set Supervisor and Internship Period</button>

											<?php endif; ?>

										</div>
									</div>

									<div class="separator-solid"></div>

									<div class="table-responsive">
										<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround">

											<?php if ($accepted !== NULL) : ?>
												<a href="index.php?page=registration_intern_management&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-md btn-secondary btn-block mr-1 mt-1"><i class="fab fa-wpforms mr-1"></i>Registration Data</a>
											<?php endif; ?>


											<?php if ($accepted != 0) : ?>

												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=supervisor_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-user-secret mr-1"></i>Supervisor Profile</button>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=lecturer_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-graduation-cap mr-1"></i>Lecturer Profile</button>

												<?php if ($student_data['program_study'] == 'LPI') : ?>
													<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=competency_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-star mr-1"></i>Student Competency</button>
												<?php endif; ?>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=jobdesk_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-list mr-1"></i>Jobdesk</button>

											<?php endif; ?>

										</div>
									</div>

									<?php if ($accepted != 0) : ?>


										<div class="table-responsive">

											<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround">

												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=discussion_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-comments mr-1"></i>Discussion</button>

												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=attendance_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-calendar mr-1"></i>Attendance</button>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=logbook_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-book mr-1"></i>Logbook</button>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=student_feedback_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-book mr-1"></i>Student Feedback</button>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=industry_feedback_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-book mr-1"></i>Industry Feedback</button>
												<button class="btn btn-md btn-secondary btn-block mt-1 mr-1" onclick="window.location.href = 'index.php?page=final_report_intern_management&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-book mr-1"></i>Final Report</button>

											</div>


										</div>
									<?php endif; ?>

								</div>


							</div>
						</div>



						<!-- modal tambah hrd -->
						<div class="modal fade" id="ModalAssignSupervisor">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">
											<span class="fw-mediumbold">
												Assign Supervisor</span>
										</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>

									<form action="index.php?form=hrd&aksi=assign_supervisor" method="POST">
										<?= csrf() ?> <?= input_hidden('id_internship', $id_internship) ?>
										<div class="modal-body form-row">


											<div class="form-group col-md-12">
												<label>Supervisor <b class="text-danger">*</b></label>

												<select class="form-control <?= show_error('id_supervisor') ?>" name="id_supervisor">
													<option value="" hidden>- Select One -</option>
													<?php

													$sql = "SELECT * FROM user_company WHERE user_type NOT IN ('HRD') AND id_company = '" . $_SESSION['id_company'] . "' ";

													foreach ($_SERVER['KONEKSI_DB']->query($sql) as $data) :
													?>

														<option value="<?= $data['id_user_company'] ?>"><?= $data['user_fullname'] ?></option>

													<?php endforeach; ?>
												</select>


												<?= form_error('id_supervisor') ?>

											</div>



											<div class="form-group col-md-6">
												<label class="label-control">Start Date <b class="text-danger">*</b></label>

												<input type="date" class="form-control" name="start_date" value="<?= old('start_date', $start_date) ?>" />

											</div>

											<div class="form-group col-md-6">
												<label class="label-control">End Date <b class="text-danger">*</b></label>

												<input type="date" class="form-control" name="end_date" value="<?= old('end_date', $end_date) ?>" />

											</div>





										</div>
										<div class="modal-footer justify-content-between">
											<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
											<button type="submit" id="submit_btn" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Assign</button>
									</form>

								</div>
							</div>
							<!-- /.modal-content -->
						</div>
						<!-- /.modal-dialog -->
					</div>
					<!-- /.tutupmodal -->






				</div>
			</div>
		</div>
		<?php include './view/industri/template/footer.php'; ?>









	</div>

	</div>

	<?php include './view/industri/template/script.php'; ?>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>


	<!-- AJAX Script -->
	<script>
		$(document).ready(function() {

			$('#basic-datatables').DataTable({});

			<?php if (old('ModalAssignSupervisor') !== NULL) :
				echo "$('#ModalAssignSupervisor').modal('show')";
			endif; ?>


		});
	</script>





</body>

</html>