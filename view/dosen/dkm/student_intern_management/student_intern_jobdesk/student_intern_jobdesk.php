<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Jobdesk';
$route = 'student_intern_management';
include "./view/dosen/template/head.php";


$id_internship = base64_decode($_GET['id_internship']);

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship is not found !');
	redirect_referrer(1);
	exit();
}

$data_internship = $exec->fetch_assoc();
$nim = $data_internship['nim'];
$_GET['nim'] = $nim;

// Prodi
$sql = "SELECT * FROM student_internship WHERE nim = '$nim'";
$mhs_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();
$kode_prodi = $mhs_data['program_study'];
$_GET['kode_prodi'] = $kode_prodi;

$sql 	= "SELECT * FROM jobdesc WHERE kode_prodi = '$kode_prodi'";
$exec	= $_SERVER['KONEKSI_DB']->query($sql);

// if ($exec->num_rows != 1) {
// 	make_swal2('error', 'Not Allowed', 'Internship is not found !');
// 	redirect_referrer(1);
// 	exit();
// }

$num = $exec->num_rows;
$row = $exec->fetch_assoc();
$id_job = $row['id_jobdesc'];

$sql 	= "SELECT * FROM jobdesc_intern WHERE id_internship = '$id_internship'";
$exec2	= $_SERVER['KONEKSI_DB']->query($sql);

$num = $exec2->num_rows;

if ($num == 1) {

	$data_job_intern = mysqli_fetch_assoc($exec2);

	$num_jobdesc = $exec2->num_rows;

	$_GET['id_jobdesc_intern'] = $data_job_intern['id_jobdesc_intern'];
	$id_jobdesc_intern = $data_job_intern['id_jobdesc_intern'];
	$id_jobdesc = $data_job_intern['id_jobdesc'];

	$act = "index.php?form=jobdesk_spv&aksi=edit";
} else {
	$act = "index.php?form=jobdesk_spv&aksi=add";
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
						<h4 class="page-title">Jobdesk</h4>
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
								<a href="index.php?page=student_intern_detail&nim=<?= base64_encode($nim) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Jobdesk</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

						<!-- Jobdesc -->
						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Jobdesk</h4>
								</div>


								<?php if ($num != 1) { ?>
									<div class="card-body form-row">

										<button class="btn btn-warning">Supervisor belum mengisi jobdesk !</button>

									</div>

								<?php } else { ?>

									<div class="card-body form-row">

										<div class="form-group col-md-12">
											<label>Question 1 <b class="text-danger">*</b></label> <br>
											<label><?= $row['pertanyaan_1'] ?></label>

											<textarea class="form-control <?= show_error('jawaban_1') ?>" name="jawaban_1" id="jawaban_1" placeholder="Fill Answer for Question 1" rows="3"><?= $data_job_intern['jawaban_1'] ?></textarea>

											<?= form_error('jawaban_1') ?>
										</div>


										<div class="form-group col-md-12">
											<label>Question 2 <b class="text-danger">*</b></label> <br>
											<label><?= $row['pertanyaan_2'] ?></label>

											<textarea class="form-control <?= show_error('jawaban_2') ?>" name="jawaban_2" id="jawaban_2" placeholder="Fill Answer for Question 2" rows="3"><?= $data_job_intern['jawaban_2'] ?></textarea>

											<?= form_error('jawaban_2') ?>
										</div>


									</div>

								<?php } ?>

							</div>
						</div>


						<?php if ($num_jobdesc == 1) {
							$id_internship = $id_internship;
							$id_jobdesc_intern = $id_jobdesc_intern;

							if (in_array($row['need_monitoring'], ['YES', 'Yes', 'yes'])) {
								view('dosen/dkm/student_intern_management/student_intern_jobdesk/monitoring_jobtype');
							} else {
								view('dosen/dkm/student_intern_management/student_intern_jobdesk/no_monitoring');
							}
						}
						?>

					</div>


				</div>



			</div>




			<?php include "./view/dosen/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/dosen/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<script>


		$(document).ready(function() {

			// CKEDITOR
			var jawaban_1 = CKEDITOR.replace('jawaban_1', {
				language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Fill your Answer for Question 1',
				removeButtons: 'PasteFromWord',
			});

			var jawaban_2 = CKEDITOR.replace('jawaban_2', {
				language: 'en',
				extraPlugins: 'editorplaceholder',
				editorplaceholder: 'Fill your Answer for Question 2',
				removeButtons: 'PasteFromWord',
			});


			$('#basic-datatables').DataTable({});

			$('#table').DataTable({});





		});
	</script>

</body>

</html>