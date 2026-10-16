<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Intern List";
$route = 'intern_list';
include "./view/industri/template/head.php";

?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>

		<div class="main-panel">


			<div class="container">
				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white pb-2 fw-bold">Intern List</h2>
								<h5 class="text-white op-7 mb-2">Managing Accepted Intern</h5>
							</div>


							<?php if ($_SESSION['user_type'] == 'HRD') : ?>
								<div class="ml-md-auto py-2 py-md-0">
									<button class="btn btn-secondary btn-round" data-toggle="modal" data-target="#ModalTambah"><i class="fa fa-plus mr-1"></i> Add Offer</button>
								</div>
							<?php endif; ?>

						</div>
					</div>
				</div>

				<div class="page-inner mt--5">
					<div class="row mt--2">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Intern Data
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
													<th>Program Study</th>
													<th>Internship Status</th>
													<th>Internship Period</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>

												<?php

												$id_user_company = $_SESSION['id_user_company'];
												$sql = "SELECT student_internship.* , internship.* FROM student_internship, internship WHERE internship.id_user_company = '$id_user_company' AND internship.nim = student_internship.nim AND internship.status NOT IN ('NO', 'No', 'no')";

												$sql_student = $_SERVER['KONEKSI_DB']->query($sql);

												?>

												<?php foreach ($sql_student as $index => $data) :

													$nim = $data['nim'];

												// 	$sql2 = "SELECT COUNT(internship.status) AS status_internship internship WHERE `nim` = '$nim' ";
												// 	$exec = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_object;


												?>

													<tr>
														<td><?= $index + 1 ?>.</td>
														<td><?= $data['nim'] ?></td>
														<td><?= $data['name'] ?></td>
														<td><?= $data['program_study'] ?></td>
														<td><?= $data['status'] ?></td>
														<td><?= date('n', strtotime($data['start_date']) - strtotime($data['end_date']) ) ?> Month(s)</td>

														<td>
															<button class="btn btn-secondary btn-sm" onclick="window.location.href = 'index.php?page=intern_list_detail&id_internship=<?= base64_encode($data['id_internship']) ?>'"><i class="fas fa-info-circle mr-2"></i>Detail</button>



															<!-- <div class="btn-group btn-round">
															<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																Action
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																<button class="dropdown-item" onclick="window.location.href = 'index.php?page=intern_guidance_detail&id_internship=<?= base64_encode($data['id_internship']) ?>'">Intern Detail</button>
																<div class="dropdown-divider"></div>

																<button class="dropdown-item" onclick="window.location.href = 'index.php?page=intern_guidance_logbook&id_internship=<?= base64_encode($data['id_internship']) ?>'">Intern Logbook</button>
																<button class="dropdown-item" onclick="window.location.href = 'index.php?page=intern_guidance_attendance&id_internship=<?= base64_encode($data['id_internship']) ?>'">Intern Attendance</button>
																

															</div>
														</div> -->

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
						url: "./ajax/delStudentIntern.php",
						type: "POST",
						data: {
							nim: id
						},
						dataType: 'text',
						success: function(response) {

							if (response == "OK") {
								window.location.reload(1);
							} else {
								swal.fire({
									title: 'Deleting Data Failed',
									text: 'Please try again later !',
									icon: 'error',
									timer: 2000,
									showCancelButton: false,
									showConfirmButton: true,
								});

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