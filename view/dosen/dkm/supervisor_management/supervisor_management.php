<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Lecturer Management";
$route = 'supervisor_management';
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dkm.php';
?>

<?php

$id_kampus = $_SESSION['Lecturer_Data'] ['id_kampus'] ?? NULL;

$filter = $_GET['filter'];

if ($filter == '') {
	$filter = $_SESSION['prodi_koor'];
}

if ($filter == '' || $filter === NULL || $filter == 'All') {
	$filter_text = 'All';
	$filter_value = 'All';
} else {
	$filter_value = $filter;

	$sql_prodi = "SELECT * FROM program_study WHERE id_kampus = '$id_kampus' AND kode_prodi = '$filter_value'";
	$exec33 = $_SERVER['KONEKSI_DB']->query($sql_prodi);

	foreach ($exec33 as $da) {
		$filter_text_id = $da['jenjang'] . " " . $da['prodi'];

		$filter_text_en = $da['jenjang'] . " " . $da['study_program'];
	}

	$filter_text = $filter_text_en;
}


if ($filter_value == 'All') {
	$sql_spv = "SELECT * FROM lecturer WHERE id_kampus = '$id_kampus' AND nim_nik_unit IN ( SELECT DISTINCT(nik_dospem) FROM student_internship WHERE id_kampus = '$id_kampus' )";
} else {
	$sql_spv = "SELECT * FROM lecturer WHERE id_kampus = '$id_kampus' AND nim_nik_unit IN ( SELECT DISTINCT(nik_dospem) FROM student_internship WHERE id_kampus = '$id_kampus' AND program_study = '" . $_SESSION['prodi_koor'] . "' )";
}
// var_dump( $_SESSION['Lecturer_Data'], $sql_spv ); exit;

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
								<h2 class="text-white pb-2 fw-bold">Lecturer Management</h2>
								<h5 class="text-white op-7 mb-2">List of Polibatam Lecturer</h5>
							</div>

							<div class="ml-md-auto py-2 py-md-0">
								<button onclick="window.location.href = 'index.php?page=add_supervisor'" class="btn btn-primary btn-round"><i class="fas fa-plus mr-1"></i>Add Data</button>

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
										<span class="text-sm">Lecturer List</span>
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
																$sql2 = "SELECT DISTINCT major, jurusan FROM program_study WHERE id_kampus = '$id_kampus' ";

																foreach ($exec2 = $_SERVER['KONEKSI_DB']->query($sql2) as $row2) :

																	$jurusan = $row2['jurusan'];
																?>

																	<!-- <optgroup label="<?= $row2['jurusan'] ?>" > -->
																	<optgroup label="<?= $row2['major'] ?>">

																		<?php
																		$sql3 = "SELECT * FROM program_study WHERE jurusan = '$jurusan' AND id_kampus = '$id_kampus' ORDER BY jenjang ASC ";

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
																window.location.href = 'index.php?page=supervisor_management&filter=' + study.val();
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
													<th>NIK</th>
													<th>Name</th>
													<th>Total Student</th>
													<th>Status</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>

												<?php

												$sql_supervisor = $_SERVER['KONEKSI_DB']->query($sql_spv);
												?>

												<?php foreach ($sql_supervisor as $index => $data) :

													$nik = $data['nim_nik_unit'];

													$total_mhs = $_SERVER['KONEKSI_DB']->query("SELECT COUNT( DISTINCT(`nim`) ) AS total_mhs FROM student_internship WHERE nik_dospem = '$nik' AND id_kampus = '$id_kampus' ")->fetch_assoc()['total_mhs'];
												?>

													<tr>
														<td><?= $index + 1 ?>.</td>
														<td><?= $data['nim_nik_unit'] ?></td>
														<td><?= $data['name'] ?></td>
														<td><?= $total_mhs ?> Person</td>
														<td><?= $data['status'] ?></td>
														<td>
															<button class="btn btn-secondary btn-sm" onclick="window.location.href = 'index.php?page=supervisor_detail&nik=<?= base64_encode($data['nim_nik_unit']) ?>'"><i class="fa fa-info-circle mr-1"></i>Detail</button>
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


	<script>
		$(document).ready(function() {
			$('#basic-datatables').DataTable({});

		});
	</script>



</body>

</html>