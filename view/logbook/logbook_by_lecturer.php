<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Logbook Student";
$route = 'logbook_student';
include "./view/dosen/template/head.php";


// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';

?>
<style>
	#check-all.indeterminate~.custom-control-label::before {
		content: "-";
	}
</style>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Student Logbook</h4>
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
								<a href="#">Student Logbook</a>
							</li>





						</ul>
					</div>

					<div class="row">


						<!-- Internship History -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Student Logbook

										<div class="float-right justify-content-beetwen">
											<!-- <button data-toggle="modal" data-target="#ModalFilter" class="btn btn-info btn-sm btn-round mr-1"><i class="fas fa-filter mr-1"></i>Filter Data</button> -->

											<div class="btn-group btn-round">
												<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													Bulk Action
												</button>
												<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

													<button class="dropdown-item text-info" id="bulk-action-btn">
														<i class="fas fa-check-circle mr-2"></i>
														Approval Logbook</button>


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
													<option value="All">All</option>
													<option value="<?= $filter_value ?>" hidden><?= $filter_text ?></option>
													<?php

													foreach ($response->filter as $key => $value) {
														$new_date = strtotime('01-' . sprintf('%02s', $value->LOGBOOK_DATE) . '-' . $value->LOGBOOK_YEAR);
														$value = date('Y-m', $new_date);
														$text = date('M Y', $new_date);
														echo "<option value='$value'>$text</option>";
													}
													?>
												</select>

												<script type="text/javascript">
													function change() {
														var data = document.getElementById('filter_data').value;
														window.location.href = 'index.php?page=intern_student_logbook&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>&filter=' + data;
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
													<!-- <th>No.</th> -->
													<th>Student Name</th>
													<th>Week Num</th>
													<th>Logbook Period</th>
													<th>Approval Lecturer</th>
													<th>Approval Supervisor</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>
												<?php

												foreach ($response->logbook as $i => $data) :

													if ($data->approval_spv == 'Waiting') {
														$approval_spv = '<button class="btn btn-sm btn-warning">Waiting for Approval</button>';
													} else if ($data->approval_spv == 'Yes') {
														$approval_spv = '<button class="btn btn-sm btn-success">Approved</button>';
													} else {
														$approval_spv = '<button class="btn btn-sm btn-danger">Not Approved</button>';
													}


													if ($data->approval_dpm == 'Waiting') {
														$approval_dpm = '<button class="btn btn-sm btn-warning">Waiting for Approval</button>';
														$warna_btn = "text-warning";
													} else if ($data->approval_dpm == 'Yes') {
														$approval_dpm = '<button class="btn btn-sm btn-success">Approved</button>';
														$warna_btn = "text-success";
													} else {
														$approval_dpm = '<button class="btn btn-sm btn-danger">Not Approved</button>';
														$warna_btn = "text-danger";
													}

												?>

													<tr>
														<td><input type="checkbox" class="form-control" value="<?= $data->id_logbook ?>"></td>
														<!-- <td><?= $i + 1 ?></td> -->
														<td><?= $data->name ?></td>
														<td>M<?= $data->week_num ?></td>
														<td><?= date('d M Y', strtotime($data->start_date)) . ' - ' . date('d M Y', strtotime($data->end_date)) ?></td>
														<td class="text-center"><?= $approval_dpm ?></td>
														<td class="text-center"><?= $approval_spv ?></td>
														<td>
															<div class="btn-group btn-round">
																<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Action
																</button>
																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																	<button class="dropdown-item text-info" onclick="window.location.href = 'index.php?page=intern_student_logbook_detail&nim=<?= base64_encode($data->nim) ?>&id_internship=<?= base64_encode($data->id_internship) ?>&id_logbook=<?= base64_encode($data->id_logbook) ?>'">

																		<i class="fas fa-info-circle mr-2"></i>
																		Detail Logbook</button>

																	<div class="dropdown-divider"></div>

																	<button class="dropdown-item <?= $warna_btn ?>" data-toggle="modal" data-target="#ModalApproval<?= $i + 1 ?>">
																		<i class="fas fa-check-circle mr-2"></i>
																		Approval Logbook</button>


																</div>
															</div>
														</td>
													</tr>


													<!-- Validate Logbook -->
													<div class="modal fade" id="ModalApproval<?= $i + 1 ?>">
														<div class="modal-dialog modal-md">
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title">Approval
																		<span class="fw-mediumbold">
																			Logbook</span>
																	</h5>
																	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																		<span aria-hidden="true">&times;</span>
																	</button>
																</div>
																<div class="modal-body">

																	<form action="index.php?form=logbook_approval&action=lecturer" method="POST">
																		<?= csrf() ?> <?= input_hidden('id_logbook', $data->id_logbook) ?> <?= input_hidden('nim', $data->nim) ?>
																		<?= input_hidden('nik', $_SESSION['nim_nik_unit']) ?> <?= input_hidden('id_internship', $data->id_internship) ?>
																		<?= input_hidden('loop', $i + 1) ?>

																		<div class="form-group">
																			<label for="approval_dpm">Approval Lecturer<label class="text-danger">*</label></label>
																			<select class="form-control" name="approval_dpm" id="approval_dpm">
																				<option hidden><?= $data->approval_dpm ?></option>
																				<option>Yes</option>
																				<option>No</option>
																				<option>Waiting</option>
																			</select>
																			<?= form_error('approval_dpm'); ?>
																		</div>


																</div>
																<div class="modal-footer justify-content-between">
																	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
																	<button type="submit" class="btn btn-primary"><i class="fas fa-check-double mr-1"></i>Approval Logbook</button>
																</div>
																</form>

															</div>
															<!-- /.modal-content -->
														</div>
														<!-- /.modal-dialog -->
													</div>
													<!-- /.tutupmodal -->

												<?php
												endforeach;
												?>
											</tbody>



										</table>
									</div>
								</div>
							</div>
						</div>


						<div class="modal fade" id="ModalApprovalBulk">
							<div class="modal-dialog modal-md">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">Bulk Approval
											<span class="fw-mediumbold">
												Logbook</span>
										</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">

										<div class="form-group">
											<label for="approval_dpm">Approval Lecturer<label class="text-danger">*</label></label>
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
			<?php include './view/dosen/template/footer.php'; ?>




		</div>

	</div>

	<?php include "./view/dosen/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>


	<!-- AJAX Script -->
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


			$('#save-approval-btn').on('click', function() {

				// Mengumpulkan id_logbook dari cekbox terpilih
				var selectedIds = [];
				$('tbody input[type="checkbox"]:checked').each(function() {
					var idLogbook = $(this).val();
					selectedIds.push(idLogbook);
				});

				// Mendapatkan nilai approval yang dipilih dari modal
				var approvalValue = $('#approval-dropdown').val();

				// Mengirim data ke server menggunakan Ajax
				$.ajax({
					url: 'index.php?request=bulk_approval_logbook&action=lecturer', // Ganti dengan URL yang sesuai
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
						// console.log( approvalValue, selectedIds );
						window.location.reload();

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
					console.log('Selected id_logbook:', selectedIds);

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

			<?php if (old('ModalApproval') !== NULL) :
				echo "$('#ModalApproval" . old('ModalApproval') . "').modal('show')";
			endif; ?>

		});
	</script>





</body>

</html>