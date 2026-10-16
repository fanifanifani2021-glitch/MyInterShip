<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false); session_start();

$title = "Students Management"; $route = 'intern_student_management';
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';

?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">


				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
							<h2 class="text-white pb-2 fw-bold">Students Management</h2>
								<h5 class="text-white op-7 mb-2">List of Intern Students under Supervisor</h5>
							</div>
						</div>
					</div>
				</div>



				<div class="page-inner mt--5">
					<div class="row mt--2">


						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Students List
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
													<th>E-Mail</th>
													<th>Study Program</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>

												<?php
												$nik = $_SESSION['nim_nik_unit'];
												$sql = "SELECT DISTINCT(`nim`) AS nim FROM student_internship WHERE nik_dospem = '$nik'";
												$sql_student = $_SERVER['KONEKSI_DB']->query($sql);

												?>

												<?php foreach ($sql_student as $index => $d) :

													$nim = $d['nim'];

													$sql = "SELECT MAX(internship.id_internship) AS max_intern FROM internship WHERE internship.nim = '$nim'";
													$id_intern = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc()['max_intern'];

													$sql1 = "SELECT student_internship.* FROM student_internship WHERE student_internship.nim = '$nim' ";
													$data = $_SERVER['KONEKSI_DB']->query($sql1)->fetch_assoc();

													$sql2 = "SELECT COUNT(internship.status) AS status_internship FROM internship WHERE `nim` = '$nim' ";
													$exec = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();

													if ($exec['status_internship'] == '0') {
														$internship_status = 'Open to Work';
													} else {
														$internship_status = 'Working';
													}
												?>

													<tr>
														<td><?= $index + 1 ?>.</td>
														<td><?= $nim ?></td>
														<td><?= $data['name'] ?></td>
														<td><?= $data['email'] ?></td>
														<td><?= $data['program_study'] ?></td>

														<td>
															<a href="index.php?page=intern_student_detail&nim=<?= base64_encode($data['nim']) ?>" class="btn btn-info btn-sm"><i class="fas fa-info-circle mr-1"></i>Detail</a>
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
	
	<?php include './view/dosen/template/script.php'; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<!-- Atlantis JS -->
	<script src="./assets/js/atlantis.min.js"></script>

	<!-- SWAL2 -->
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