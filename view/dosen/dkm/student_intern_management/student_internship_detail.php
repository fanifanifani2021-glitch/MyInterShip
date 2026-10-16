<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Internship Detail";
$route = 'student_intern_management';
include "./view/dosen/template/head.php";

?>

<?php
$id_internship = base64_decode($_GET['id_internship']);

$sql = $_SERVER['KONEKSI_DB']->query("SELECT * FROM internship WHERE id_internship = '$id_internship'");
$data_internship = $sql->fetch_assoc();

if ($sql->num_rows != 1) {
	make_soloalert('error', 'Not Allowed', 'Internship data is not found !');
	redirect_back(1);
	exit;
}

// Get NIM
$nim = $data_internship['nim'];
$student_data = $_SERVER['KONEKSI_DB']->query("SELECT * FROM student_internship WHERE nim = '$nim'")->fetch_assoc();

?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Internship Detail</h4>
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
								<a href="index.php?page=student_intern_detail&nim=<?= base64_encode($data_internship['nim']) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Internship Detail</a>
							</li>



						</ul>
					</div>




					<div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-body">
									<table class="table table-striped">


										<!-- If Internship status is yes -->
										<?php
										$row = $data_internship;

										$sql2 = "SELECT company.name AS company_name FROM company WHERE company.id_company = '" . $row['id_company'] . "' ";
										$company_name = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc()['company_name'];



										if ($student_data['nik_dospem'] == '' || $student_data['nik_dospem'] == '-') {
											$nik_dospem = 0;
											$dospem = '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';
										} else {
											$nik_dospem = $student_data['nik_dospem'];

											$sql2 = "SELECT lecturer.name AS lecturer_name FROM lecturer WHERE nim_nik_unit = '$nik_dospem' ";
											$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
											$dospem = mysqli_fetch_assoc($exec2)['lecturer_name'];
										}

										if ($row['id_user_company'] == '' || $row['id_user_company'] == '-') {
											$user_company = 0;
											$supervisor_industri = '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';

										} else {
											$user_company = $row['id_user_company'];
											$company = $row['id_company'];

											$sql2 = "SELECT user_company.user_fullname AS supervisor_name FROM user_company WHERE user_company.id_user_company = '$user_company' ";
											$kompeni_data = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();


											$supervisor_industri = $kompeni_data['supervisor_name'];

											$sql2 = "SELECT company.name AS company_name FROM company WHERE company.id_company = '$company' ";
											$kompeni_data = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();
											$company_name = $kompeni_data['company_name'];
										}

										if (in_array($row['status'], ['YES', 'Yes', 'yes']) === true) {
											$accepted = 1;
											$status = 'Intern Accepted and Ongoing';
											$internship_period = $row['internship_period'] . " Month";
										} else 

												if (in_array($row['status'], ['FINISH', 'Finish', 'finish']) === true) {
											$accepted = 1;
											$status = 'Intern Accepted and Finished';
											$internship_period = $row['internship_period'] . " Month";
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

										$start_date = $row['start_date'];
										$end_date = $row['end_date'];


										?>
										<tr>
											<th>Registered Date</th>
											<td><?= date('D, d M Y', strtotime($row['timestamp_register'])) ?></td>
										</tr>

										<tr>
											<th>Company Name</th>
											<td><a target="_blank" href="<?= base_url('index.php?page=company_list_detail&id_company=' .base64_encode( $row['id_company'] ) ) ?>"><?= $company_name ?></a></td>
										</tr>

										<tr>
											<th>Internship Status</th>
											<td><?= $status ?></td>
										</tr>

										<?php if ($accepted == 1) : ?>


											<tr>
												<th>Internship Period</th>
												<td><?= date('D, d M Y', strtotime($start_date)) ?> until <?= date('D, d M Y', strtotime($end_date)) ?> (<?= range_time_elapsed_string( $start_date, $end_date ) ?>)</td>
											</tr>

											<tr>
												<th>Industry Supervisor</th>
												<td><a href="<?= base_url('index.php?page=student_intern_supervisor&id_internship=' .base64_encode($id_internship)) ?>"><?= $supervisor_industri ?></a></td>
											</tr>

											<tr>
												<th>Polibatam Supervisor</th>
												<td><a href="<?= base_url('index.php?page=student_intern_lecturer&id_internship=' .base64_encode($id_internship))  ?>"><?= $dospem ?></a></td>
											</tr>


										<?php endif; ?>


									</table>


									<?php if ($accepted == 1 && $student_data['program_study'] == $_SESSION['prodi_koor'] ) : ?>

										<div class="table-responsive mb-2 mb-md-0">

											<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround ">
												<!-- <a href="#" class="btn btn-primary btn-block mr-1 mt-1" data-toggle="modal" data-target="#ModalAssignSupervisor"><i class="fas fa-user-secret mr-1"></i>Assign Lecturer</span></a> -->


												<?php if (in_array($row['status'], ['FINISH', 'Finish', 'finish']) !== true) { ?>
												<a href="index.php?page=student_intern_certificate&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-certificate mr-1"></i>Internship Certificate</a>
												<?php } ?>


											</div>
										</div>
									<?php endif; ?>

									<hr>



									<div class="table-responsive mb-2 mb-md-0">
										<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround ">
											<?php if ($accepted !== NULL) : ?>
												<a href="index.php?page=student_intern_registration&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fab fa-wpforms mr-1"></i>Registration Data</a>
												<!-- <a href="index.php?page=student_intern_company&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fab fa-wpforms mr-1"></i>Company Profile</a> -->
												<a href="<?= base_url('index.php?page=company_list_detail&id_company=' .base64_encode( $row['id_company'] ) ) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-building mr-1"></i>Company Profile</a>
												
												<?php endif; ?>

											<?php if ($accepted == 1) : ?>
												<a href="index.php?page=student_intern_supervisor&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-user-secret mr-1"></i>Supervisor Profile</a>
												<a href="index.php?page=student_intern_lecturer&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-user mr-1"></i>Lecturer Profile</a>

												<?php if ( in_array( $student_data['program_study'] , ['LPI'] ) ) : ?>
												<a href="index.php?page=student_intern_competency&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-star mr-1"></i>Student Competency</a>
												<?php endif; ?>

												<a href="index.php?page=student_intern_jobdesk&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-list mr-1"></i>Jobdesk</a>

											<?php endif; ?>
										</div>
									</div>

									<?php if ($accepted == 1) : ?>

										<div class="table-responsive mb-2 mb-md-0">

											<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround ">

												<a href="index.php?page=student_intern_discussion&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-comments mr-1"></i>Discussion</a>

												<a href="index.php?page=student_intern_logbook&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-book mr-1"></i>Logbook</a>
												<a href="index.php?page=student_intern_attendance&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-calendar mr-1"></i>Attendance</a>
												<a href="index.php?page=student_intern_final_report&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-book mr-1"></i>Final Report</a>
												<a href="index.php?page=student_intern_student_feedback&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-comment-dots mr-1"></i>Student Feedback</a>
												<a href="index.php?page=student_intern_industry_feedback&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-comment-dots mr-1"></i>Industry Feedback</a>

											</div>


										</div>



									<?php endif; ?>



								</div>
							</div>

						</div>


					</div>



				</div>
			</div>


			<?php include './view/dosen/template/footer.php'; ?>




			<!-- modal tambah hrd -->
			<div class="modal fade" id="ModalAssignSupervisor">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">
								<span class="fw-mediumbold">
									Assign Lecturer</span> 
								<small class="text-sm text-muted">for current Student</small>
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>

						<form action="index.php?form=dkm&aksi=assign_polibatam_supervisor" method="POST">
							<?= csrf() ?> <?= input_hidden('nim', $data_internship['nim']) ?> <?= input_hidden('id_internship', $id_internship) ?>
							<div class="modal-body form-row">


								<div class="form-group col-md-12">
									<label>Lecturer's NIK <b class="text-danger">*</b></label>
									<div class="input-group">
										<input type="text" class="form-control" name="lecturer_nik" id="lecturer_nik" value="<?= old('lecturer_nik') ?>" onkeyup="search_lecturer( this.value )" placeholder="Enter Lecturer's NIK" />
										<div class="input-group-append">
											<button class="btn btn-black btn-border" type="button" onclick="search_lecturer( document.getElementById('lecturer_nik').value )"><i class="fas fa-search mr-1"></i>Click Me !</button>
										</div>
									</div>
									<?= form_error('lecturer_nik') ?>

								</div>

								<div class="form-group col-md-12" hidden id="lecturer_notification">
									<label class="alert alert-warning">Lecturer is not found !</label>
								</div>

								<div class="form-group col-md-12" id="lecturer_waiting" hidden>
									<label class="alert alert-info"><i class="fas fa-spin fa-spinner mr-1"></i>Please wait while system is searching for the lecturer !</label>
								</div>

								<div class="form-group col-md-12" hidden>
									<label>Search Status <b class="text-danger">*</b></label>
									<input type="text" class="form-control" name="search_status" id="search_status" value="Failed" />

									<?= form_error('search_status') ?>

								</div>


								<div class="form-group col-md-6 search-result" hidden>
									<label>Lecturer's Name <b class="text-danger">*</b></label>
									<input type="text" class="form-control " name="lecturer_name" id="lecturer_name" readonly />

									<?= form_error('lecturer_name') ?>
								</div>

								<div class="form-group col-md-6 search-result" hidden>
									<label>Lecturer E-Mail <b class="text-danger">*</b></label>
									<input type="text" class="form-control" name="lecturer_email" id="lecturer_email" readonly />

									<?= form_error('lecturer_email') ?>
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