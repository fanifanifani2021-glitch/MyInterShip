<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Jobdesk Management';
$route = 'jobdesk_management';
include "./view/dosen/template/head.php";

$ps = $_SESSION['prodi_koor'];

$sql = "SELECT * FROM jobdesc WHERE kode_prodi = '$ps' ORDER BY id_jobdesc DESC LIMIT 1";
$exec = $_SERVER['DB']->query($sql);

if ($exec->num_rows == 0) {

	$pertanyaan1 = "Do the Students do work in other fields of work, please provide more information";
	$pertanyaan2 = "During the internship period, what contributions do you expect from the student?";


	$data = [
		'id_jobdesc' => NULL, 'kode_prodi' => $ps,
		'pertanyaan_1' => $pertanyaan1, 'pertanyaan_2' => $pertanyaan2,
		'need_monitoring' => 'No', 'additional_information' => '-'
	];
	$insert = table_insert('jobdesc', $data);

	$id_jobdesc = $_SERVER['DB']->insert_id;

	$sql = "SELECT * FROM jobdesc WHERE id_jobdesc = '$id_jobdesc' ORDER BY id_jobdesc DESC LIMIT 1";
	$exec = $_SERVER['DB']->query($sql);
	$row = $exec->fetch_assoc();
} else {
	$row = $exec->fetch_assoc();
}

$id_jobdesc = $row['id_jobdesc'];
$sql2 = "SELECT * FROM detail_jobdesc WHERE id_jobdesc = '$id_jobdesc' ORDER BY id_detail DESC";
$detail_jobdesc = $_SERVER['DB']->query($sql2);

?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Jobdesk Management</h4>
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
								<a href="#">Jobdesk Management</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row">

						<!-- <div class="col-md-12" id="sini">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Add Jobdesk Jobtype</h4>
								</div>



								<form action="index.php?form=jobdesk_dkm&aksi=addjob" method="POST"><?= csrf(); ?>

									<input type="hidden" name="id_jobdesc" id="id_jobdesc" value="<?= $row['id_jobdesc'] ?>">

									<div class="card-body form-row">

										<div class="form-group col-md-12">
											<label>Jobtype <b class="text-danger">*</b></label>

											<input required type="text" class="form-control" name="jenis_pekerjaan" id="jenis_pekerjaan" placeholder="Input Jobtype" aria-label="Input Jobtype" aria-describedby="basic-addon2">


										</div>


										<div class="form-group col-md-12">
											<label>Job Description <b class="text-danger">*</b></label>
											<textarea required class="form-control" name="job_description" rows="5" placeholder="Fill Additional Information"></textarea>
										</div>
									</div>

									<div class="card-footer justify-content-start">
										<button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i>Save</button>
									</div>
								</form>

							</div>
						</div> -->

						<div class="col-md-12">


							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Jobtype List
										<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#ModalAdd"><i class="fas fa-plus mr-2"></i>Add Data</button>
									</h4>
								</div>



								<div class="card-body">

									<div class="table-responsive">
										<table id="basic-datatables" class="display dataTable table table-striped table-hover">
											<thead>
												<tr>
													<th style="width: 5%">No</th>
													<th>Jobtype</th>
													<th>Job Description</th>
													<th style="width: 10%">Action</th>
												</tr>
											</thead>
											<tbody id="tbody-staff">
												<?php
												$no = 1;
												foreach ($detail_jobdesc as $data) :
												?>
													<tr>
														<td><?= $no++ ?>.</td>
														<td><?= $data['jenis_pekerjaan'] ?></td>
														<td><?= $data['job_description'] ?></td>
														<td>
															<div class="btn-group btn-round">
																<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Action
																</button>
																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
																	<button class="dropdown-item" data-toggle="modal" data-target="#ModalEdit" onclick="modal_edit('<?php echo $data['id_detail']; ?>', '<?php echo $data['jenis_pekerjaan']; ?>', '<?php echo $data['job_description'] ?>')"><i class="fa fa-edit mr-1"></i>Edit Data</button>
																	<button class="dropdown-item" data-toggle="modal" data-target="#ModalHapus" onclick="modal_hapus('<?php echo $data['id_detail']; ?>')"><i class="fas fa-trash-alt mr-1"></i>Delete Data</button>
																</div>
															</div>
														</td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>


									<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header no-bd">
													<h5 class="modal-title">
														<span class="fw-mediumbold">
															Add Jobtype</span>
													</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<form action="index.php?form=jobdesk_dkm&aksi=addjob" method="POST"><?= csrf(); ?>


													<input type="hidden" name="id_jobdesc" id="id_jobdesc" value="<?= $row['id_jobdesc'] ?>">

													<div class="modal-body">

														<div class="form-group col-md-12">
															<label>Jobtype <b class="text-danger">*</b></label>

															<input required type="text" class="form-control" name="jenis_pekerjaan" id="jenis_pekerjaan" placeholder="Example : Designer" aria-label="Input Jobtype" aria-describedby="basic-addon2">


														</div>


														<div class="form-group col-md-12">
															<label>Job Description <b class="text-danger">*</b></label>
															<textarea required class="form-control" name="job_description" rows="5" placeholder="Example : Icons, Posters, Book Design"></textarea>
														</div>
													</div>

													<div class="modal-footer justify-content-between">
														<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
														<button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i>Save</button>

													</div>
												</form>

											</div>
										</div>
									</div>

									<!-- modal hapus -->
									<div class="modal fade" id="ModalHapus" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header no-bd">
													<h5 class="modal-title">
														<span class="fw-mediumbold">
															Delete Data</span>
													</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<form action="index.php?form=jobdesk_dkm&aksi=deljob" method="POST">
														<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
														<input type="hidden" id="id_hapus" name="id_hapus" value="">
														Are You Sure to Delete Current Jobtype Data ?
												</div>
												<div class="modal-footer justify-content-between">
													<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
													<button type="submit" class="btn btn-primary pull-right"><i class="fas fa-trash-alt mr-2"></i>Delete</button>
												</div>
												</form>

											</div>
										</div>
									</div>
									<!-- tutup modal -->


									<!-- modal tambah hrd -->
									<div class="modal fade" id="ModalEdit">
										<div class="modal-dialog modal-md">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title">
														<span class="fw-mediumbold">
															Edit Jobtype</span>
													</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>

												<form action="index.php?form=jobdesk_dkm&aksi=editjob" method="POST">
													<?= csrf() ?>
													<input type="hidden" value="" name="id_detail_edit" id="id_detail_edit">
													<div class="modal-body">
														<div class="form-group">
															<label for="jenis_pekerjaan_edit">Jobtype <label class="text-danger">*</label></label>
															<input type="text" placeholder="Input Jobtype" class="form-control <?= show_error('jenis_pekerjaan_edit') ?>" name="jenis_pekerjaan_edit" id="jenis_pekerjaan_edit" required>
															<?= form_error('jenis_pekerjaan_edit'); ?>
														</div>

														<div class="form-group">
															<label for="job_description_edit">Job Description <label class="text-danger">*</label></label>
															<textarea rows="5" type="text" placeholder="Input Jobtype" class="form-control <?= show_error('job_description_edit') ?>" name="job_description_edit" id="job_description_edit" required></textarea>
															<?= form_error('job_description_edit'); ?>
														</div>
													</div>

													<div class="modal-footer justify-content-between">
														<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
														<button type="submit" class="btn btn-primary"><i class="fas fa-edit mr-2"></i>Edit</button>
													</div>
												</form>

											</div>
											<!-- /.modal-content -->

										</div>
										<!-- /.modal-dialog -->
									</div>
									<!-- /.tutupmodal -->

								</div>
							</div>
						</div>

						<!-- Jobdesc -->
						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-header">
									<h4 class="card-title">Additional Question</h4>
								</div>

								<form class="" action="<?= $act; ?>" method="POST" id="form_jobdesc"><?= csrf(); ?>
									<input type="hidden" name="id_jobdesc" id="id_jobdesc" value="<?= $row['id_jobdesc'] ?>">
									<input type="hidden" name="prodi" id="prodi" value="<?= $ps ?>">
									<input type="hidden" name="need_monitoring" id="need_monitoring" value="No">
									<input type="hidden" name="additional_information" id="additional_information" value="-">


									<div class="card-body form-row">

										<div class="form-group col-md-12">
											<label>Question 1 <b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('pertanyaan_1') ?>" name="pertanyaan_1" id="pertanyaan_1" placeholder="Fill Question 1" rows="3"><?php if ($row['pertanyaan_1'] == "") {
																																																echo "";
																																															} else {
																																																echo $row['pertanyaan_1'];
																																															} ?></textarea>

											<?= form_error('pertanyaan_1') ?>
										</div>

										<div class="form-group col-md-12">
											<label>Question 2 <b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('pertanyaan_2') ?>" name="pertanyaan_2" id="pertanyaan_2" placeholder="Fill Question 2" rows="3"><?php if ($row['pertanyaan_2'] == "") {
																																																echo "";
																																															} else {
																																																echo $row['pertanyaan_2'];
																																															} ?></textarea>
											<?= form_error('pertanyaan_2') ?>
										</div>


									</div>
								</form>


								<!-- <div class="card-footer">
									<?php if ($_SESSION['old']['open-form'] == 'open') : ?>

										<button type="button" id="edit_btn" onclick="enable_form()" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>

										<button id="cancel_btn" onclick="confirmation();" type="button" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
										<button id="reset_btn" onclick="reset_confirmation()" type="reset" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
										<?php if ($num == '1') { ?>
											<button onclick="submit_form()" id="save_btn" class="btn btn-success"><i class="fas fa-save mr-1"></i>Save</button>
										<?php }
										if ($num == '0') { ?>
											<button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
										<?php } ?>

									<?php else : ?>
										<button type="button" id="edit_btn" onclick="enable_form()" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>

										<button id="cancel_btn" onclick="confirmation();" type="button" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
										<button id="reset_btn" onclick="reset_confirmation()" type="reset" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
										<?php if ($num == '1') { ?>
											<button onclick="submit_form()" id="save_btn" class="btn btn-success"><i class="fas fa-save mr-1"></i>Save</button>
										<?php }
										if ($num == '0') { ?>
											<button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
										<?php } ?>
									<?php endif; ?>



								</div> -->
							</div>
						</div>


					</div>


				</div>



			</div>




			<?php include "./view/dosen/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/dosen/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<!-- User Custom Script -->
	<script>
		function submit_form() {
			console.log('helo there');
			$('form#form_jobdesc').submit();
		}

		function confirmation() {
			// alert('Are you sure you want to cancel ? (Your change will not be saved)');
			SoloAlert.confirm({
				title: "Are you sure you want to cancel ?",
				body: "Changes you made will not be saved !",
				useTransparency: true,
				onOk: () => {
					disabled_form();
				}
			});

		}

		function reset_confirmation() {

			SoloAlert.confirm({
				title: "Are you sure you want to reset ?",
				body: "Changes you made will be reset to default !",
				useTransparency: true,
				onOk: () => {
					form_reset();
				}
			});

		}




		function form_reset() {

			$("#form_jobdesc").trigger("reset");
		};

		function modal_hapus(id) {
			var id_hapus = document.getElementById('id_hapus');
			id_hapus.value = id;
		}

		function modal_edit(id, jenis, job) {
			$('#id_detail_edit').val(id);
			$('#jenis_pekerjaan_edit').val(jenis);
			$('#job_description_edit').text(job);
			$('#job_description_edit').val(job);


		}
	</script>

	<script>
		function disabled_form() {

			$("#edit_btn").prop("disabled", false);
			$("#edit_btn").prop("hidden", false);


			$("#cancel_btn").prop("hidden", true);
			$("#cancel_btn").prop("disabled", true);

			$("#reset_btn").prop("hidden", true);
			$("#reset_btn").prop("disabled", true);

			$("#save_btn").prop("disabled", true);

			$("#form_jobdesc :input").prop("disabled", true);
			$("#form_jobdesc").trigger("reset");

			$("#form_jobdesc :input").removeClass('is-invalid is-valid border-danger border-success');



			$("small").empty();
		};


		function enable_form() {


			$("#edit_btn").prop("disabled", true);
			$("#edit_btn").prop("hidden", true);


			$("#cancel_btn").prop("hidden", false);
			$("#cancel_btn").prop("disabled", false);

			$("#reset_btn").prop("hidden", false);
			$("#reset_btn").prop("disabled", false);


			$("#save_btn").prop("disabled", false);

			$("#form_jobdesc :input").prop("disabled", false);

		}

		$(document).ready(function() {
			$('#basic-datatables').DataTable({});

			<?php if ($_SESSION['old']['open-form'] != 'open') : echo 'disabled_form();';
			endif; ?>

			<?php if ($_SESSION['old']['ModalEdit'] == 'open') : echo '$("#ModalEdit").modal("show");';
			endif; ?>


			<?php
			if (old('scroll') != NULL) {
				echo '
					var elmnt = document.getElementById("sini");
					elmnt.scrollIntoView();
					';
			}
			?>

		});
	</script>

</body>

</html>