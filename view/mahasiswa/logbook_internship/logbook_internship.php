<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();


include "./view/mahasiswa/template/head.php";

?>

<style>
	#check-all.indeterminate~.custom-control-label::before {
		content: "-";
	}
</style>


<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Logbook Internship</h4>
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
								<a href="index.php?page=internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>


							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Logbook Internship</a>
							</li>
						</ul>
					</div>



					<div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">


								<div class="card-header">
									<div class="card-title">Logbook Internship Data

										<div class="float-right justify-content-beetwen">

											<div class="btn btn-group ">
												<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													Bulk Action
												</button>
												<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

													<button class="dropdown-item text-danger" id="bulk-action-btn">
														<i class="fas fa-trash-alt mr-2"></i>
														Delete Logbook</button>

														<!-- <button class="dropdown-item text-info" id="bulk-action-download-btn">
														<i class="fas fa-download mr-2"></i>
														Download</button> -->


												</div>
											</div>

											<button data-toggle="modal" data-target="#ModalFilter" class="btn btn-primary btn-sm "><i class="fas fa-filter mr-2"></i>Filter Data</button>
											<button onclick="window.location.href='index.php?page=add_logbook_internship&id_internship=<?= base64_encode($id_internship) ?>'" class="btn btn-primary btn-sm "><i class="fas fa-plus mr-2"></i>Add Data</button>
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
													<option>All</option>
													<?php
													$sql = "SELECT DISTINCT(YEAR(`start_date`)) AS LOGBOOK_YEAR FROM logbook WHERE id_internship = :id_internship";
													$stmt = $_SERVER['PDO']->prepare($sql);
													$stmt->bindValue(':id_internship', $id_internship, PDO::PARAM_INT);
													$stmt->execute();

													if ($stmt->rowCount() < 1) {
														echo "<option value=''>None</option>";
													} else {
														while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
															$year = $data['LOGBOOK_YEAR'];

															$sql2 = "SELECT DISTINCT(MONTH(`start_date`)) AS LOGBOOK_DATE FROM logbook WHERE id_internship = :id_internship AND YEAR(`start_date`) = :year";
															$stmt2 = $_SERVER['PDO']->prepare($sql2);
															$stmt2->bindValue(':id_internship', $id_internship, PDO::PARAM_INT);
															$stmt2->bindValue(':year', $year, PDO::PARAM_INT);
															$stmt2->execute();

															while ($data2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
																$new_date = strtotime('01-' . sprintf('%02s', $data2['LOGBOOK_DATE']) . '-' . $year);
																$value = date('Y-m', $new_date);
																$text = date('M Y', $new_date);
																echo "<option value='$value'>$text</option>";
															}
														}
													}

													?>
												</select>

												<script type="text/javascript">
													function change() {
														var data = document.getElementById('filter_data').value;
														window.location.href = 'index.php?page=logbook_internship&id_internship=<?= base64_encode($id_internship) ?>&filter=' + data;
													}
												</script>
											</div>
											<div class="modal-footer justify-content-between">
												<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i>Close</button>
												<button type="button" class="btn btn-primary" onclick="change()"><i class="fas fa-check mr-2"></i>Apply Filter</button>
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
													<th>Week Num</th>
													<th>Start Date</th>
													<th>End Date</th>
													<th>Approval Lecturer</th>
													<th>Approval Supervisor</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>
												<?php
												foreach ($exec as $i => $data) {
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
												?>
													<tr>
														<td><input type="checkbox" class="form-control" value="<?= $data['id_logbook'] ?>"></td>

														<!-- <td><?= $i + 1 ?></td> -->
														<td>M<?= $data['week_num'] ?></td>
														<td><?= date('D, d-M-Y', strtotime($data['start_date'])) ?></td>
														<td><?= date('D, d-M-Y', strtotime($data['end_date'])) ?></td>
														<td class="text-center"><?= $approval_dpm ?></td>
														<td class="text-center"><?= $approval_spv ?></td>
														<td>
															<div class="btn-group ">
																<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Action
																</button>
																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
																	<a class="dropdown-item text-info" onclick="window.location.href = 'index.php?page=detail_logbook_internship&id_internship=<?= base64_encode($data['id_internship']) ?>&id_logbook=<?= base64_encode($data['id_logbook']) ?>'"><i class="fas fa-info-circle mr-2"></i>Detail Logbook</a>
																	<div class="dropdown-divider"></div>
																	<!--<button class="dropdown-item" onclick="window.location.href = '#' ">Print Logbook</button>-->
																	<a class="dropdown-item text-warning" onclick="window.location.href = 'index.php?page=edit_logbook_internship&id_internship=<?= base64_encode($data['id_internship']) ?>&id_logbook=<?= base64_encode($data['id_logbook']) ?>'"><i class="fas fa-edit mr-2"></i>Edit Data</a>
																	<a class="dropdown-item text-danger" onclick="confirm_delete( '<?= encrypt($data['id_logbook']) ?>' , 'M<?= $data['week_num'] ?>' ) "><i class="fas fa-trash-alt mr-2"></i>Delete Data</a>
																</div>
															</div>
														</td>
													</tr>
												<?php
												}
												?>
											</tbody>



										</table>
									</div>
								</div>
							</div>
						</div>



						<div class="modal fade" id="ModalBulk">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">Bulk Delete
											<span class="fw-mediumbold">
												Logbook</span>
										</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">


										<div class=" custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="confirm_bulk"> 

											<label class=" custom-control-label m-0" for="confirm_bulk"> <b class="text-danger">*</b>
												I understand that performing this <b>bulk delete action</b> will <b>permanently remove</b> the selected data and this action cannot be undone.
											</label>
										</div>

									</div>
									<div class="modal-footer justify-content-between">
										<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
										<button type="button" id="save-approval-btn" class="btn btn-primary"><i class="fas fa-trash-alt mr-2"></i>Bulk Delete</button>
									</div>

								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
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

				if (!$('#confirm_bulk').prop('checked')) {
					alert('Please confirm the bulk delete action.');
					return;
				}

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
					url: 'index.php?request=bulk_delete_logbook', // Ganti dengan URL yang sesuai
					method: 'POST',
					data: {
						token: _token,
						_method: 'DELETE',
						ids: selectedIds,
						id_internship: '<?= base64_encode($id_internship) ?>',
					},
					success: function(response) {
						// Lakukan sesuatu setelah berhasil disimpan, misalnya tutup modal
						$('#ModalBulk').modal('hide');
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

					$('#ModalBulk').modal('show');
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


		function confirm_delete(id, week_num) {
			swal.fire({
				title: 'Delete Confirmation',
				text: 'Are you sure you want to delete ' + week_num + ' Logbook ?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {
					var csrfToken = $('meta[name="csrf-token"]').attr('content');

					// AJAX
					$.ajax({
						url: "index.php?request=logbook_delete",
						type: "POST",
						data: {
							_method: 'DELETE',
							token: csrfToken,
							id_logbook: id,
						},
						dataType: 'json', // Menambahkan dataType sebagai 'json' untuk menentukan respons yang diharapkan dalam format JSON
						success: function(response, status, xhr) {
							window.location.reload(1);
						},
						error: function(xhr, status, error) {
							window.location.reload(1);
						}
					});


				}
			})

		}
	</script>




</body>

</html>