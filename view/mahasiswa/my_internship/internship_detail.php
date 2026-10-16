<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Internship Detail";
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

$exec = $exec->fetch(PDO::FETCH_ASSOC);


$sql = "SELECT * FROM student_internship WHERE nim = :nim LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':nim' => $nim]);

$student_internship = $stmt->fetch(PDO::FETCH_ASSOC);


?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>

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
								<a href="index.php?page=my_internship">My Internship</a>
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


							<!-- If Internship status is yes -->
							<?php
							$row = $exec;


							$sql = "SELECT company.*, company.name AS company_name FROM company WHERE company.id_company = :id_company LIMIT 1";
							$stmt = $pdo->prepare($sql);
							$stmt->execute([':id_company' => $row['id_company']]);
							$company_data = $stmt->fetch(PDO::FETCH_ASSOC);

							$company_name = $company_data['company_name'];
							$company_logo = $company_data['logo'];

							$nik_dospem = $student_internship['nik_dospem'];
							if (empty($nik_dospem) || $nik_dospem === '-') {
								$nik_dospem = 0;
								$dospem = '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';
							} else {
								$sql = "SELECT lecturer.name AS lecturer_name FROM lecturer WHERE nim_nik_unit = :nik_dospem LIMIT 1";
								$stmt = $pdo->prepare($sql);
								$stmt->execute([':nik_dospem' => $nik_dospem]);
								$dosen_pembimbing = $stmt->fetch(PDO::FETCH_ASSOC);

								$dospem = $dosen_pembimbing ? $dosen_pembimbing['lecturer_name'] : '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';
							}

							$user_company = $row['id_user_company'];
							if (empty($user_company) || $user_company === '-') {
								$user_company = 0;
								$supervisor_industri = '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';
							} else {
								$company = $row['id_company'];

								$sql = "SELECT user_company.user_fullname AS supervisor_name FROM user_company WHERE user_company.id_user_company = :id_user_company AND user_company.id_company = :id_company LIMIT 1";
								$stmt = $pdo->prepare($sql);
								$stmt->execute([':id_user_company' => $user_company, ':id_company' => $company]);
								$kompeni_data = $stmt->fetch(PDO::FETCH_ASSOC);

								$supervisor_industri = $kompeni_data ? $kompeni_data['supervisor_name'] : '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';

								$sql2 = "SELECT company.name AS company_name FROM company WHERE company.id_company = :id_company";
								$stmt = $pdo->prepare($sql2);
								$stmt->execute([':id_company' => $company]);

								$mother_kompeni = $stmt->fetch(PDO::FETCH_ASSOC);
								$company_name = $mother_kompeni ? $mother_kompeni['company_name'] : $company_name;
							}

							$status = 'Not Registered';
							$accepted = NULL;
							$internship_period = '';
							$status_lower = strtolower($row['status']);

							if ($status_lower === 'yes') {
								$accepted = 1;
								$status = 'Intern Accepted';
								$internship_period = $row['internship_period'] . " Month";
							} elseif ($status_lower === 'finish') {
								$accepted = 1;
								$status = 'Intern Accepted and Finished';
								$internship_period = $row['internship_period'] . " Month";
							} elseif ($status_lower === 'no') {
								$accepted = 0;
								$status = 'Intern Not Accepted';
							} elseif ($status_lower === 'waiting') {
								$accepted = 0;
								$status = 'Waiting for validation';
							}

							$start_date = $row['start_date'];
							$end_date = $row['end_date'];




							?>



							<div class="card card-profile">
								<!-- <div class="card"> -->
								<div class="card-header" style="background-image: url('viewer.php?src=profile_background'); height: 300px; background-size: cover;background-repeat: no-repeat;">

									<div class="profile-picture">
										<div class="avatar avatar-xxl">
											<img src="viewer.php?src=get_company_logo&location=<?= base64_encode($company_logo) ?>" alt="..." class="avatar-img rounded-circle">
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="user-profile text-center mb-2">
										<div class="name"><?= $company_name ?></div>
									</div>



									<table class="table table-striped mt-2 mt-4">

										<tr>
											<th>Registered Date</th>
											<td><?= date('D, d M Y', strtotime($row['timestamp_register'])) ?></td>
										</tr>

										<!-- <tr>
											<th>Company Name</th>
											<td><?= $company_name ?></td>
										</tr> -->

										<tr>
											<th>Internship Status</th>
											<td><?= $status ?></td>
										</tr>

										<?php if ($accepted == 1) : ?>


											<tr>
												<th>Internship Period</th>
												<td><?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></td>
											</tr>

											<tr>
												<th>Industry Supervisor</th>
												<td><?= $supervisor_industri ?></td>
											</tr>

											<tr>
												<th>University Lecturer</th>
												<td><?= $dospem ?></td>
											</tr>


										<?php endif; ?>


									</table>


									<div class="table-responsive">
										<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround ">
											<?php if ($accepted !== NULL) : ?>
												<a href="index.php?page=my_registration&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fab fa-wpforms mr-1"></i>Registration Data</a>
												<a href="index.php?page=company_internship&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fab fa-wpforms mr-1"></i>Company Profile</a>
											<?php endif; ?>

											<?php if ($accepted == 1) : ?>
												<a href="index.php?page=my_supervisor&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-user-secret mr-1"></i>Supervisor Profile</a>
												<a href="index.php?page=my_lecturer&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-user mr-1"></i>Lecturer Profile</a>

												<a href="index.php?page=jobdesk_internship&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-list mr-1"></i>Jobdesk</a>
											<?php endif; ?>
										</div>
									</div>

									<?php if ($accepted == 1) : ?>

										<div class="table-responsive">
											<div class="mt-1 mb-1 d-sm-inline-flex d-md-inline-flex d-lg-inline-flex justify-content-arround ">
												<a href="index.php?page=discussion_internship&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-comments mr-1"></i>Discussion</a>

												<a href="index.php?page=logbook_internship&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-book mr-1"></i>Logbook</a>
												<a href="index.php?page=attendance_internship&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-calendar mr-1"></i>Attendance</a>
												<a href="index.php?page=final_report_internship&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-book mr-1"></i>Final Report</a>
												<a href="index.php?page=student_feedback_internship&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-comment-dots mr-1"></i>Student Feedback</a>
												<a href="index.php?page=industry_feedback_internship&id_internship=<?= base64_encode($id_internship) ?>" class="btn btn-primary btn-block mr-1 mt-1"><i class="fas fa-comment-dots mr-1"></i>Industry Feedback</a>

											</div>
										</div>
									<?php endif; ?>



								</div>
							</div>


						</div>


					</div>


				</div>
			</div>
			<?php include './view/mahasiswa/template/footer.php'; ?>
		</div>

	</div>

	<?php include './view/mahasiswa/template/min-script.php'; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>


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