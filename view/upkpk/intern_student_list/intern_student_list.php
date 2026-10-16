<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Student Management";
$route = 'intern_student_list';
include "./view/upkpk/template/head.php";

$id_kampus = $_SESSION['CDC_Data'] ['id_kampus'];
$sql = "SELECT * FROM tb_kampus WHERE id_kampus = '$id_kampus'";
$exec = $_SERVER['KONEKSI_DB']->query( $sql )->fetch_assoc();
$nama_kampus = $exec['nama_kampus'];


?>

<body>
	<div class="wrapper">

		<?php include "./view/upkpk/template/main-header.php"; ?>
		<?php include "./view/upkpk/template/sidebar.php"; ?>

		<div class="main-panel">


			<div class="container">

				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white pb-2 fw-bold">Student Management</h2>
								<h5 class="text-white op-7 mb-2">Student Management for <?= $nama_kampus ?></h5>



							</div>
							<div class="ml-md-auto py-2 py-md-0">
								<a aria-label="Import Student" href="<?= base_url('index.php?page=import_student_intern') ?>" role="button" class="btn btn-primary btn-round"><i class="fa fa-file-import mr-2"></i> Import</a>
								<a aria-label="Add New Student" href="<?= base_url('index.php?page=add_student_intern') ?>" role="button" class="btn btn-primary btn-round"><i class="fa fa-plus mr-2"></i> Add Student</a>
							</div>

						</div>
					</div>
				</div>



				<div class="page-inner mt--5">


					<div class="row mt--2">
						<div class="col-md-12">

							<!-- Filter -->
							<div class="card">
								<form id="form_filter"> <?= csrf() ?> <?= input_hidden('id_kampus', $_SESSION['CDC_Data'] ['id_kampus']) ?>
									<div class="card-body form-row">


										<div class="form-group col-md-4">
											<label>Major <b class="text-danger">*</b></label>
											<select class="form-control" id="major" name="major" required>
												<option value="All">All Major</option>
												<?php
												$s = $_SERVER['KONEKSI_DB']->query("SELECT DISTINCT(`major`) AS major FROM program_study WHERE id_kampus = '$id_kampus'");

												foreach ($s as $d) : ?>

													<option value="<?= $d['major'] ?>"><?= $d['major'] ?></option>


												<?php endforeach;
												?>
											</select>
											<small class="invalid-feedback text-sm text-danger">Choose one</small>
										</div>


										<div class="form-group col-md-4">
											<label>Study Program</label>


											<select id="program_study" name="program_study" class="form-control">
												<option value="All">All Study Program</option>


												<?php

												$sql = "SELECT DISTINCT(program_study.major) FROM program_study WHERE id_kampus = '$id_kampus' ORDER BY program_study.major";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);

												foreach ($exec as $data_jurusan) :
													$jurusan = $data_jurusan['major'];
												?>

													<optgroup label="<?= $jurusan ?>">

														<option value="All-<?= $data_jurusan['major'] ?>">All Study Program in <?= $data_jurusan['major'] ?></option>

														<?php

														$sql2 = "SELECT program_study.* FROM program_study WHERE program_study.major = '$jurusan' AND program_study.id_kampus = '$id_kampus' ORDER BY program_study.jenjang";
														$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

														foreach ($exec2 as $data_prodi) :
														?>

															<option value="<?= $data_prodi['kode_prodi'] ?>"><?= $data_prodi['jenjang'] ?> <?= $data_prodi['prodi'] ?> - (<?= $data_prodi['jenjang'] ?> <?= $data_prodi['study_program'] ?>)</option>

														<?php endforeach; ?>



													</optgroup>

												<?php endforeach; ?>
											</select>

											<?= form_error('program_study') ?>

										</div>




									</div>
									<div class="card-footer">
										<button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i>Filter</button>
									</div>


								</form>

							</div>

							<div id="filter_result2"> </div>
							<div id="filter_result"> </div>


						</div>
					</div>
				</div>
			</div>


			<?php include './view/upkpk/template/footer.php'; ?>


		</div>

	</div>

	<?php include './view/upkpk/template/script.php'; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<link rel='stylesheet' href='./assets/sweetalert2.css'>
	<script src='./assets/sweetalert2.all.js'></script>


	<script type='text/javascript'>
		$("#form_filter").on('submit', function(e) {

			let spinner = "<i class='fas fa-spinner fa-spin mr-1 fa-2x'></i> <span>Searching . . .</span>";
			$('#filter_result2').html(spinner);


			e.preventDefault();


			$.ajax({
				type: 'POST',
				url: 'index.php?form=filter_intern_student_list',
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,


				success: function(data, textStatus, XHR) {
					$('#filter_result2').html('');
					$('#filter_result').html(data);

					$('#basic-datatables').DataTable({});
				},
				error: function(data) {
					$('#filter_result2').html('');
					$('#filter_result').html('');

					swal2('error', 'Failed to show Student Internship Data', 'Please try again later', 2000);

				},
			});


		});


		function close_filter() {

			console.log("ok");

			swal.fire({
				title: 'Close Confirmation',
				text: 'Are you sure you want to close current filter ?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, close !",
			}).then((result) => {

				if (result.isConfirmed) {
					/* Read more about isConfirmed, isDenied below */
					let spinner = "<i class='fas fa-spinner fa-spin mr-1 fa-2x'></i> <span>Closing . . .</span>";
					$('#filter_result2').html(spinner);

					setTimeout(function() {
						$('#filter_result2').html('');
						$('#filter_result').html('');
					}, 500);

				}

			})


		}
	</script>

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

							// $('show_notifications').html(response);

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


	<?php if ( $_GET['show'] == 1 ) : ?>
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#program_study').val('<?= $_GET['prodi'] ?? 'IF' ?>');
				$('#major').val('<?= $_GET['major'] ?? 'Informatics Engineering' ?>');
				$('#form_filter').submit();
			});
		</script>
	<?php endif; ?>


</body>

</html>