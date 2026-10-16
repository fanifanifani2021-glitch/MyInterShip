<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Students Management";
$route = 'student_intern_management';
include "./view/dosen/template/head.php";

?>

<?php

$id_kampus = $_SESSION['Lecturer-Data'] ['id_kampus'] ?? 1;
$filter = $_GET['filter'];

if ($filter == '') {
	$filter = $_SESSION['prodi_koor'];
}

if ($filter == '' || $filter === NULL || $filter == 'All') {
	$filter_text = 'All';
	$filter_value = 'All';
} else {
	$filter_value = $filter;

	$sql_prodi = "SELECT * FROM program_study WHERE kode_prodi = '$filter_value'";
	$exec33 = $_SERVER['KONEKSI_DB']->query($sql_prodi);

	foreach ($exec33 as $da) {
		$filter_text_id = $da['jenjang'] . " " . $da['prodi'];

		$filter_text_en = $da['jenjang'] . " " . $da['study_program'];
	}

	$filter_text = $filter_text_en;
}


if ($filter_value == 'All') {
	$sql_2 = "SELECT * FROM student_internship LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study WHERE student_internship.id_kampus = '$id_kampus' ";
} else {
	$sql_2 = "SELECT * FROM student_internship LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study WHERE student_internship.id_kampus = '$id_kampus' AND student_internship.program_study = '" . $filter . "' ORDER BY nik_dospem";
}


$sql3 = "SELECT nama_kampus FROM tb_kampus WHERE id_kampus = :id_kampus";
$query3 = $_SERVER['PDO']->prepare( $sql3 );
$query3->execute( [ ':id_kampus' => $_SESSION['Lecturer-Data'] ['id_kampus'] ] );
$data_kampus = $query3->fetch( PDO::FETCH_ASSOC );

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
								<h5 class="text-white op-7 mb-2">Students List in <?= $data_kampus['nama_kampus'] ?? 'Polibatam' ?></h5>
							</div>

							<div class="ml-md-auto py-2 py-md-0">
								<button onclick="window.location.href = 'index.php?page=import_student_intern'" class="btn btn-primary btn-round"><i class="fas fa-file-import mr-1"></i>Import Data</button>
								<button onclick="window.location.href = 'index.php?page=add_student_intern'" class="btn btn-primary btn-round"><i class="fas fa-plus mr-1"></i>Add Data</button>
							</div>


						</div>
					</div>
				</div>



				<div class="page-inner mt--5">

					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">

										<span class="text-sm">Students List</span>
										<button data-toggle="modal" data-target="#ModalFilter" class="btn btn-primary btn-round btn-sm float-right"><i class="fas fa-filter mr-1"></i>Filter</button>


										<div class="modal fade" id="ModalFilter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="exampleModalLabel">Filter Study Program</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<div class="form-group">
															<label>Study Program</label>
															<select class="form-control" id="study_program">
																<option value="<?= $filter_value ?>" hidden><?= $filter_text ?></option>
																<option value="All">All</option>
																<?php
																$sql2 = "SELECT DISTINCT major, jurusan FROM program_study WHERE id_kampus = '" .$_SESSION['Lecturer-Data'] ['id_kampus']. "'";

																foreach ($exec2 = $_SERVER['KONEKSI_DB']->query($sql2) as $row2) :

																	$jurusan = $row2['jurusan'];
																?>

																	<!-- <optgroup label="<?= $row2['jurusan'] ?>" > -->
																	<optgroup label="<?= $row2['major'] ?>">

																		<?php
																		$sql3 = "SELECT * FROM program_study WHERE jurusan = '$jurusan' AND id_kampus = '" .$_SESSION['Lecturer-Data'] ['id_kampus']. "' ORDER BY jenjang ASC ";

																		foreach ($exec3 = $_SERVER['KONEKSI_DB']->query($sql3) as $row3) :
																		?>

																			<!-- <option value="<?php echo $row3['kode_prodi'] ?>"><?= $row3['jenjang'] ?> <?= $row3['prodi'] ?></option> -->
																			<option value="<?php echo $row3['kode_prodi'] ?>"><?= $row3['jenjang'] ?> <?= $row3['study_program'] ?></option>

																		<?php endforeach; ?>

																	</optgroup>


																<?php endforeach; ?>
															</select>
															<small class="invalid-feedback text-sm text-danger">Choose one</small>
														</div>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
														<button type="button" id="btn_filter" onclick="cek_filter()" class="btn btn-primary"><i class="fas fa-check mr-1"></i>Apply</button>
													</div>

													<script type="text/javascript">
														function cek_filter() {
															let study = $('#study_program');

															if (study.val() == '') {
																study.addClass('is-invalid');
															} else {
																window.location.href = 'index.php?page=student_intern_management&filter=' + study.val();
															}
														}
													</script>
												</div>
											</div>
										</div>
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
													<th>Program Study</th>
													<!-- <th>Internship Status</th> -->
													<th>Supervisor</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>

												<?php
												$sql_student = $_SERVER['KONEKSI_DB']->query($sql_2);

												?>

												<?php foreach ($sql_student as $index => $data) :

													$nim = $data['nim'];

													$sql2 = "SELECT COUNT(internship.status) AS status_internship FROM internship WHERE `nim` = '$nim'";
													$exec = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();

													// Kalau belum ada daftar sama sekali, maka open to work
													if ($exec['status_internship'] == '0') {
														$internship_status = 'Open to Work';
														$internship_period = '-';
													}

													// Jika sudah ada daftar
													else {

														$sql2 = "SELECT MAX(`id_internship`) AS last_intern FROM internship WHERE `nim` = '$nim'";
														$exec = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();

														$last_intern = $exec['last_intern'];

														$sql3 = "SELECT * FROM internship WHERE `id_internship` = '$last_intern' ";
														$exec = $_SERVER['KONEKSI_DB']->query($sql3)->fetch_assoc();

														$status = $exec['status'];

														if ($status == 'Waiting' || $status == 'waiting') {
															$internship_status = 'Waiting for Validation';
															$internship_period = '-';
														} else if ($status == 'No' || $status == 'no') {
															$internship_status = 'Rejected, Open to Work';
															$internship_period = '-';
														} else if ($status == 'Yes' || $status == 'yes') {
															$internship_status = 'Working';
															$internship_period = $exec['internship_period'] . " Month";
														} else {
															$internship_status = 'Other';
															$internship_period = '-';
														}
													}

													if (in_array($data['nik_dospem'], [NULL, '', '-'])) {
														$dospem = "Not Set";
													} else {
														$sql2 = "SELECT `name` FROM lecturer WHERE `nim_nik_unit` = '" . $data['nik_dospem'] . "'";
														$exec = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();

														$dospem = $exec['name'] ?? 'Lecturer Not Found !';
													}
												?>

													<tr>
														<td><?= $index + 1 ?>.</td>
														<td><?= $data['nim'] ?></td>
														<td><?= $data['name'] ?></td>
														<td><?= $data['email'] ?></td>
														<!-- <td><?= $data['program_study'] ?></td> -->
														<td><?= $data['jenjang'] ?> <?= $data['prodi'] ?></td>
														<td><?= $dospem ?></td>

														<td>
															<div class="btn-group btn-round">
																<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Action
																</button>
																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																	<button class="dropdown-item" onclick="window.location.href = 'index.php?page=student_intern_detail&nim=<?= base64_encode($data['nim']) ?>'"><i class="fa fa-info-circle mr-1"></i>Detail</button>

																	<?php if ($_SESSION['prodi_koor'] == $data['program_study'] && $_SESSION['is_koor'] === true) : ?>

																		<button class="dropdown-item" onclick="show_assign('<?= $data['nim'] ?>')"><i class="fa fa-user-secret mr-1"></i>Assign Supervisor</button>

																		<div class="dropdown-divider"></div>

																		<button class="dropdown-item" onclick="window.location.href = 'index.php?page=edit_student_intern&nim=<?= base64_encode($data['nim']) ?>'"><i class="fa fa-edit mr-1"></i>Edit Data</button>
																		<button class="dropdown-item" onclick="swal_confirm('<?= $data['nim'] ?>')"><i class="fa fa-trash mr-1"></i>Delete Data</button>
																	<?php endif; ?>

																</div>
															</div>
														</td>

													</tr>

												<?php $index++;
												endforeach; ?>

											</tbody>
										</table>
									</div>
								</div>

								<form id="FormAssign" method="POST" action="<?= base_url('index.php?form=setPolibatamSupervisor') ?>"> <?= csrf() ?>
									<div id="show_assign"></div>
								</form>
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

	<!-- Select2 -->
	<script src="./assets/js/plugin/select2/select2.full.min.js"></script>

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

	<script>
		function show_assign(id) {

			// AJAX
			$.ajax({
				url: "./ajax/getLecturerSupervisor.php",
				type: "POST",
				data: {
					nim: id
				},
				dataType: 'text',
				success: function(data) {
					$('#show_assign').html(data);
					$('#ModalAssign').modal('show');

					$('#lecturer_nik').select2({
						theme: "bootstrap",
						width: '100%',
						dropdownParent:$("#ModalAssign"),
					});
				},
				error: function(response) {
					Swal.fire("Failed", "Failed to fetch Lecturer Data !", "error");
				}
			});


		}
	</script>


	<script>
		$(document).ready(function() {
			$('#basic-datatables').DataTable({});

		});
	</script>



</body>

</html>