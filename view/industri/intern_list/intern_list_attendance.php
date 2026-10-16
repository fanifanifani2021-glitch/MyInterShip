<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Intern Attendance";
$route = 'intern_list';
include "./view/industri/template/head.php";

?>

<?php

$id_internship = base64_decode($_GET['id_internship']);
$id_user_company = $_SESSION['id_user_company'];


// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship' AND id_user_company = '$id_user_company' ";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship is not found !');
	redirect_back(1);
	exit;
}

$internship_data = $exec->fetch_object();

$id_spv = $internship_data->id_user_company;


if ($internship_data->status != 'Yes') {
	make_swal2('error', 'Not Allowed', 'Internship is not accepted !');
	redirect_back(1);
	exit;
}


$filter = $_GET['filter'];

if ($filter == '' || $filter === NULL) {

	$sql = "SELECT * FROM attendance WHERE id_internship = '$id_internship'";
	$exec = $_SERVER['KONEKSI_DB']->query($sql);

	if ($exec->num_rows < 1) {
		$filter_value = '';
		$filter_text = 'None';
		$show_export = 'hidden';
	} else {
		$sql = "SELECT MAX(`date`) AS `date` FROM attendance WHERE id_internship = '$id_internship'";
		$exec = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

		$filter_value = date('Y-m', strtotime($exec['date']));
		$filter_text = date('M Y', strtotime($exec['date']));
	}
} else {
	$filter_value = date('Y-m', strtotime($filter . '-01', date('m-Y')));
	$filter_text =  date('M Y', strtotime($filter . '-01', date('m-y')));
}

?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Intern Attendance</h4>
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
								<a href="index.php?page=intern_list">Intern List</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>

							<li class="nav-item">
								<a href="index.php?page=intern_list_detail&id_internship=<?= base64_encode($id_internship) ?>">Intern Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>

							<li class="nav-item">
								<a href="#">Intern Attendance</a>
							</li>
						</ul>
					</div>



					<div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Intern Attendance Data


										<div class="float-right justify-content-beetwen">
											<button data-toggle="modal" data-target="#ModalFilter" class="btn btn-info btn-sm mr-1"><i class="fas fa-filter mr-1"></i>Filter Data</button>

											<div class="btn-group btn-round">
												<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													Bulk Action
												</button>
												<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

													<button class="dropdown-item text-info" id="bulk-action-btn">
														<i class="fas fa-check-circle mr-2"></i>
														Approval Attendance</button>


												</div>
											</div>


										</div>

									</div>
								</div>


								<div class="modal fade" id="ModalFilter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLabel">Filter Data</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<select class="form-control" id="filter_data" onchange="change()">
													<option value="<?= $filter_value ?>" hidden><?= $filter_text ?></option>
													<?php
													$sql = "SELECT DISTINCT(YEAR(`date`)) AS ATT_YEAR FROM attendance WHERE id_internship = '$id_internship'";
													$exec = $_SERVER['KONEKSI_DB']->query($sql);

													if ($exec->num_rows < 1) {
														echo "<option value=''>None</option>";
													}

													foreach ($exec as $i => $data) {
														$year = $data['ATT_YEAR'];

														$sql2 = "SELECT DISTINCT(MONTH(`date`)) AS ATT_DATE FROM attendance WHERE id_internship = '$id_internship' AND YEAR(`date`) = $year ";
														$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
														foreach ($exec2 as $ii => $data2) {
															$new_date = strtotime('01-' . sprintf('%02s', $data2['ATT_DATE']) . '-' . $year);
															$value = date('Y-m', $new_date);
															$text = date('M Y', $new_date);
															echo "<option value='$value'>$text</option>";
														}
													}
													?>
												</select>

												<script type="text/javascript">
													function change() {
														var data = document.getElementById('filter_data').value;
														window.location.href = 'index.php?page=intern_list_attendance&id_internship=<?= base64_encode($id_internship) ?>&filter=' + data;
													}
												</script>
											</div>
											<div class="modal-footer justify-content-between">
												<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Close</button>
												<button type="button" class="btn btn-primary" onclick="change()"><i class="fas fa-check mr-1"></i>Apply Filter</button>
											</div>
										</div>
									</div>
								</div>

								<div class="card-body">
									<div class="table-responsive">
										<table id="basic-datatables" class="display table table-striped table-hover">

											<thead>
												<tr>
													<th>
														<input type="checkbox" id="check-all" class="form-control">
													</th>
													<th>Month</th>
													<!-- <th>Week Num</th> -->
													<th>Date</th>
													<th>Attendance Type</th>
													<th>Approval Lecturer</th>
													<th>Approval Supervisor</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>
												<?php

												$sql = "SELECT * FROM attendance WHERE id_internship = '$id_internship' AND `date` LIKE '%$filter_value%' ";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);

												foreach ($exec as $i => $data) :

													if ($data['approval_spv'] == 'Waiting') {
														$approval_spv = '<button class="btn btn-sm btn-warning">Waiting for Approval</button>';
													} else if ($data['approval_spv'] == 'Yes') {
														$approval_spv = '<button class="btn btn-sm btn-success">Approved</button>';
													} else {
														$approval_spv = '<button class="btn btn-sm btn-danger">Not Approved</button>';
													}


													if ($data['approval_dpm'] == 'Waiting') {
														$approval_dpm = '<button class="btn btn-sm btn-warning">Waiting for Approval</button>';
													} else if ($data['approval_dpm'] == 'Yes') {
														$approval_dpm = '<button class="btn btn-sm btn-success">Approved</button>';
													} else {
														$approval_dpm = '<button class="btn btn-sm btn-danger">Not Approved</button>';
													}

													$loop = $i;

												?>

													<tr>
														<td>
															<input type="checkbox" name="check_all" id="check_all" value="<?= $data['id_attendance'] ?>" />
														</td>
														<td><?= date('M Y', strtotime($data['date'])) ?></td>
														<!-- <td>M<?= $data['week'] ?></td> -->
														<td><?= date('d M Y', strtotime($data['date'])) ?></td>
														<td><?= $data['attendance_type'] ?></td>
														<td class="text-center"><?= $approval_dpm ?></td>
														<td class="text-center"><?= $approval_spv ?></td>
														<td>
															<div class="btn-group btn-round">
																<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Action
																</button>
																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
																	<button class="dropdown-item" data-toggle="modal" data-target="#ModalDetail<?= $loop ?>"><i class="fas fa-info-circle mr-2"></i>Attendance Detail</button>

																	<?php if ($id_spv == $_SESSION['id_user_company']) : ?>
																		<div class="dropdown-divider"></div>
																		<button class="dropdown-item" data-toggle="modal" data-target="#ModalApproval<?= $loop ?>"><i class="fas fa-check-double mr-2"></i>Approval Supervisor</button>
																	<?php endif; ?>



																</div>
															</div>
														</td>
													</tr>


													<div class="modal fade" id="ModalDetail<?= $loop ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
														<div class="modal-dialog modal-lg" role="document">
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title" id="exampleModalLabel">Attendance Detail</h5>
																	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																		<span aria-hidden="true">&times;</span>
																	</button>
																</div>

																<div class="modal-body form-row">


																	<div class="form-group col-md-4">
																		<label>Week Num <b class="text-danger">*</b></label>

																		<select class="form-control <?= show_error('week_num') ?>" name="week_num">
																			<option><?= $data['week'] ?></option>
																		</select>
																	</div>


																	<div class="form-group col-md-4 ">
																		<label>Date <b class="text-danger">*</b></label>
																		<input type="date" name="attendance_date" class="form-control <?= show_error('attendance_date'); ?>" value="<?= $data['date'] ?>" />
																		<?= form_error('attendance_date'); ?>
																	</div>

																	<div class="form-group col-md-4 ">
																		<label>Attendance Type <b class="text-danger">*</b></label>

																		<select class="form-control">
																			<option hidden><?= $data['attendance_type'] ?></option>
																		</select>
																		<?= form_error('attendance_type'); ?>

																	</div>

																	<?php if ($data['attendance_type'] == 'Present') : ?>


																		<div class="form-group col-md-6">
																			<label>Check In <b class="text-danger">*</b></label>
																			<input type="time" value="<?= date('H:i', strtotime($data['check_in'])) ?>" class="form-control <?= show_error('check_in'); ?>" />
																			<?= form_error('check_in'); ?>
																		</div>

																		<div class="form-group col-md-6">
																			<label>Check Out <b class="text-danger">*</b></label>
																			<input type="time" value="<?= date('H:i', strtotime($data['check_out'])) ?>" class="form-control <?= show_error('check_out'); ?>" />
																			<?= form_error('check_out'); ?>
																		</div>

																	<?php endif; ?>


																	<div class="form-group col-md-12">
																		<label class="label-control">Description <b class="text-danger">*</b></label>
																		<textarea class="form-control description" rows="5" placeholder="Describe how was your day going or reason why you're not present on that day"><?= $data['description'] ?></textarea>

																	</div>







																</div>

																<div class="modal-footer justify-content-between">
																	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Close</button>
																</div>


															</div>
														</div>
													</div>


													<?php if ($id_spv == $_SESSION['id_user_company']) : ?>

														<div class="modal fade" id="ModalApproval<?= $loop ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
															<div class="modal-dialog" role="document">
																<div class="modal-content">
																	<div class="modal-header">
																		<h5 class="modal-title" id="exampleModalLabel">Approval Supervisor</h5>
																		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																			<span aria-hidden="true">&times;</span>
																		</button>
																	</div>

																	<form action="index.php?form=attendance_approval&action=supervisor" method="POST">
																		<?= csrf() ?> <?= input_hidden('loop', $loop) ?>
																		<?= input_hidden('id_user_company', $_SESSION['id_user_company']) ?>
																		<?= input_hidden('id_internship', $id_internship) ?>
																		<?= input_hidden('id_attendance', $data['id_attendance']) ?>
																		<div class="modal-body">

																			<div class="form-group">
																				<label class="control-label">Approval Supervisor <b class="text-danger">*</b></label>
																				<select class="form-control" name="approval_spv" id="approval_spv">
																					<option value="<?= $data['approval_spv'] ?>" hidden><?= $data['approval_spv'] ?></option>
																					<option>Yes</option>
																					<option>No</option>
																					<option>Waiting</option>
																				</select>
																			</div>
																		</div>
																		<div class="modal-footer justify-content-between">
																			<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Close</button>
																			<button type="submit" class="btn btn-primary"><i class="fas fa-check-double mr-1"></i>Approval Supervisor</button>
																		</div>

																	</form>


																</div>
															</div>
														</div>
													<?php endif; ?>

												<?php


												endforeach;
												?>
											</tbody>



										</table>
									</div>



									<div class="modal fade" id="ModalApprovalBulk">
										<div class="modal-dialog modal-md">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title">Bulk Approval
														<span class="fw-mediumbold">
															Attendance</span>
													</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">

													<div class="form-group">
														<label for="approval_dpm">Approval Supervisor <label class="text-danger">*</label></label>
														<select class="form-control" name="approval_dropdown" id="approval-dropdown">
															<option hidden value=''>- Choose One -</option>

															<option>Yes</option>
															<option>No</option>
														</select>
														<?= form_error('approval_dpm'); ?>
													</div>


												</div>
												<div class="modal-footer justify-content-between">
													<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
													<button type="button" id="save-approval-btn" class="btn btn-primary"><i class="fas fa-check-double mr-1"></i>Bulk Approval</button>
												</div>

											</div>
											<!-- /.modal-content -->
										</div>
										<!-- /.modal-dialog -->
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

	<?php include "./view/industri/template/script.php"; ?>
	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<link rel='stylesheet' href='./assets/sweetalert2.css'>
	<script src='./assets/sweetalert2.all.js'></script>

	<script src="./library/ckeditor/ckeditor.js"></script>


	<script>
		$(document).ready(function() {
			let _token = $('meta[name="csrf-token"]').attr('content');

			$('#basic-datatables').DataTable({
				pageLength: 25, // Jumlah entri default per halaman
				columnDefs: [{
						targets: 0,
						orderable: false
					} // Menonaktifkan pengurutan pada kolom pertama
				],
			});


			<?php if (old('ModalApproval') !== NULL) :
				echo "$('#ModalApproval" . old('ModalApproval') . "').modal('show')";
			endif; ?>


			$('#save-approval-btn').on('click', function() {

				// Mengumpulkan id_logbook dari cekbox terpilih
				var selectedIds = [];
				$('tbody input[type="checkbox"]:checked').each(function() {
					var idLogbook = $(this).val();
					selectedIds.push(idLogbook);
				});

				console.log(selectedIds);

				// Mendapatkan nilai approval yang dipilih dari modal
				var approvalValue = $('#approval-dropdown').val();

				// Mengirim data ke server menggunakan Ajax
				$.ajax({
					url: 'index.php?request=bulk_approval_attendance&action=supervisor', // Ganti dengan URL yang sesuai
					method: 'POST',
					data: {
						token: _token,
						_method: 'PUT',
						ids: selectedIds,
						approval: approvalValue,
					},
					success: function(response) {
						// Lakukan sesuatu setelah berhasil disimpan, misalnya tutup modal
						$('#ModalApprovalBulk').modal('hide');
						window.location.reload();
						// Tampilkan pesan sukses atau lakukan tindakan lain
					},
					error: function(xhr, status, error) {
						console.log(approvalValue, selectedIds);
						// window.location.reload();

						// Tangani kesalahan jika diperlukan
					}
				});
			});


			$('#bulk-action-btn').on('click', function() {
				// Mengumpulkan id_logbook dari cekbox terpilih
				var selectedIds = [];
				$('tbody input[type="checkbox"]:checked').each(function() {
					var idLogbook = $(this).val();
					selectedIds.push(idLogbook);
				});

				// Cek apakah ada cekbox yang dipilih di tbody
				if (selectedIds.length === 0) {
					Swal.fire({
						icon: 'warning',
						title: 'No Selection',
						text: 'Please select at least one item.',
						confirmButtonText: 'OK'
					});
				} else {
					// Tampilkan id_logbook terpilih di konsol
					console.log('Selected id_attendance:', selectedIds);

					$('#ModalApprovalBulk').modal('show');
					// Lakukan tindakan bulk action di sini
					// Misalnya: hapus data yang dipilih
					// atau tampilkan SweetAlert lain untuk konfirmasi tindakan
					// ...
				}
			});




			// Ketika cekbox di thead diubah
			$('#check-all').change(function() {
				// Set status cekbox di tbody sesuai dengan cekbox di thead
				$('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));
			});

			// Ketika cekbox di tbody diubah
			$('tbody input[type="checkbox"]').change(function() {
				// Cek apakah semua cekbox di tbody tercentang atau ada yang tercentang sebagian
				var allChecked = true;
				var someChecked = false;
				$('tbody input[type="checkbox"]').each(function() {
					if ($(this).prop('checked')) {
						someChecked = true;
					} else {
						allChecked = false;
					}
				});

				// Ubah status cekbox di thead sesuai dengan hasil cek di tbody
				if (allChecked) {
					$('#check-all').prop('checked', true);
					$('#check-all').prop('indeterminate', false);
				} else if (someChecked) {
					$('#check-all').prop('checked', false);
					$('#check-all').prop('indeterminate', true);
				} else {
					$('#check-all').prop('checked', false);
					$('#check-all').prop('indeterminate', false);
				}
			});
		});
	</script>





</body>

</html>