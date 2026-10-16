<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Job Offers';
$route = 'industry_offer';
include "./view/industri/template/head.php";

?>

<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">

				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white pb-2 fw-bold">Job Offers</h2>
								<h5 class="text-white op-7 mb-2">Managing Industry Job Offers</h5>
							</div>


							<?php if ($_SESSION['user_type'] == 'HRD') : ?>
								<div class="ml-md-auto py-2 py-md-0">
									<button class="btn btn-secondary btn-round" data-toggle="modal" data-target="#ModalTambah"><i class="fa fa-plus mr-1"></i> Add Offer</button>
								</div>
							<?php endif; ?>

						</div>
					</div>
				</div>

				<div class="page-inner mt--5">
					<div class="row mt--2">
						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center">
										<h4 class="card-title">Offer List</h4>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive" id="tabel-ini">
										<table id="ekonomi-kepri" class="display dataTable table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>Position</th>
													<th>Offer Type</th>
													<th>City</th>
													<th>Job Type</th>
													<th>Status</th>
													<th>Action</th>
												</tr>
											</thead>

										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php include "./view/industri/template/footer.php"; ?>

			<!-- modal tambah hrd -->
			<div class="modal fade" id="ModalTambah">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">
								<span class="fw-mediumbold">
									Add Offer</span>
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>

						<form action="index.php?form=offer&aksi=tambah" method="POST" id="form-tambah"> <?= csrf() ?> <?= input_hidden('id_company', $_SESSION['id_company']) ?>

							<div class="modal-body">
								<?php if ($_SESSION['old']['notifikasi-error']) : ?>

									<div class="form-group">
										<label class="btn btn-danger text-white"><?= old('notifikasi-error') ?></label>
									</div>

								<?php endif; ?>

								<div class="form-group">
									<label for="position">Position <label class="text-danger">*</label></label>
									<input type="text" placeholder="Fill Position" class="form-control <?= show_error('position'); ?>  " name="position" id="position">
									<?= form_error('position'); ?>
								</div>

								<div class="form-group">
									<label for="description">Description <label class="text-danger">*</label></label>
									<textarea class="form-control <?= show_error('description'); ?>" placeholder="Fill Description" id="description" name="description" rows="4"></textarea>
									<?= form_error('description'); ?>
								</div>

								<div class="form-group">
									<label for="requirement">Requirement <label class="text-danger">*</label></label>
									<textarea type="text" placeholder="Fill Requirement" class="form-control <?= show_error('requirement'); ?>  " name="requirement" id="requirement"></textarea>
									<?= form_error('requirement'); ?>
								</div>

								<div class="form-group">
									<label for="employer_type">Employer Type <label class="text-danger">*</label></label>
									<input type="text" placeholder="Fill Employer type" class="form-control <?= show_error('employer_type'); ?>  " name="employer_type" id="employer_type">
									<!-- <select class="form-control <?= show_error('employer_type'); ?>" name="employer_type" id="employer_type">
									<option value="" hidden>- Choose Employer Type -</option>
									<option>Internship</option>
									<option>Part Time</option>
									<option>Employee</option>
								</select>   -->

									<?= form_error('employer_type'); ?>
								</div>

								<div class="form-group">
									<label for="city">City <label class="text-danger">*</label></label>
									<input type="text" placeholder="Fill City" class="form-control <?= show_error('city'); ?>  " name="city" id="city">
									<?= form_error('city'); ?>
								</div>

								<div class="form-group">
									<label for="job_type">Job Type <label class="text-danger">*</label></label>
									<input type="text" placeholder="Fill Job Type" class="form-control <?= show_error('job_type'); ?>  " name="job_type" id="job_type">
									<?= form_error('job_type'); ?>
								</div>

								<div class="form-group">
									<label for="start_date">Start Date <label class="text-danger">*</label></label>
									<input type="date" placeholder="Fill Start Date" class="form-control <?= show_error('start_date'); ?>  " name="start_date" id="start_date">
									<?= form_error('start_date'); ?>
								</div>

								<div class="form-group">
									<label for="end_date">End Date <label class="text-danger">*</label></label>
									<input type="date" placeholder="Fill End Date" class="form-control <?= show_error('end_date'); ?>  " name="end_date" id="end_date">
									<?= form_error('end_date'); ?>
								</div>

								<div class="form-group">
									<label for="offer_type">Offer Type <label class="text-danger">*</label></label>
									<select class="form-control <?= show_error('offer_type'); ?>" name="offer_type" id="offer_type" onchange="change_allowence()">
										<option value="" hidden>- Choose Offer Type -</option>
										<option>Full Employement</option>
										<option>Part Time</option>
										<option>Internship</option>
									</select>
									<?= form_error('offer_type'); ?>
								</div>

								<script>
									function change_allowence() {
										let offer = $('#offer_type').val();
										let div = document.getElementById('allowence');
										let label = document.getElementById('label_allowence');
										let input = document.getElementById('salary_range');

										if (offer == 'Internship') {

											div.hidden = false;
											label.innerHTML = 'Allowence';
											input.val = '';
											input.placeholder = 'Fill Allowence';

										} else if (offer == 'Full Employement') {
											div.hidden = false;
											label.innerHTML = 'Salary Range';
											input.val = '';
											input.placeholder = 'Fill Salary Range';

										} else {
											div.hidden = true;
											label.innerHTML = '';
											input.val = 0;

										}
									}
								</script>


								<div class="form-group" id="allowence" hidden>
									<label for="salary_range" id="label_allowence">Allowence <b class="text-danger">*</b></label>
									<small class="text-sm">Opsional</small>
									<input type="text" placeholder="Fill Allowence" id="salary_range" class="form-control <?= show_error('salary_range'); ?>  " name="salary_range" id="salary_range">
									<?= form_error('salary_range'); ?>
								</div>

							</div>

							<div class="modal-footer justify-content-between">
								<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
								<button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save</button>
							</div>

						</form>

					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.tutupmodal -->

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

						<form action="index.php?form=offer&aksi=hapus" method="POST"> <?= csrf(); ?>
							<input type="hidden" id="id_hapus" name="id_hapus" value="">

							<div class="modal-body">
								Are You Sure to Delete This Data?
							</div>
							<div class="modal-footer justify-content-between">
								<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
								<button type="submit" class="btn btn-primary pull-right"><i class="fas fa-trash mr-1"></i>Delete</button>
							</div>
						</form>

					</div>
				</div>
			</div>
			<!-- tutup modal -->


			<!-- modal hapus -->
			<div class="modal fade" id="ModalChange" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header no-bd">
							<h5 class="modal-title">
								<span class="fw-mediumbold">
									Change Status</span>
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>

						<form action="index.php?form=offer&aksi=change_status" method="POST"> <?= csrf(); ?>
							<input type="hidden" id="id_change" name="id_change" value="<?= old('id_change') ?>">

							<div class="modal-body">
								<div class="form-group">
									<label>Offer Status <b class="text-danger">*</b></label>
									<select class="form-control <?= show_error('offer_status') ?>" name="offer_status">
										<!-- <option value="" hidden>- Select Offer Status -</option> -->
										<option hidden id="offer_status"></option>
										<option>Open</option>
										<option>Closed</option>
									</select>

									<?= form_error('offer_status') ?>
								</div>
							</div>
							<div class="modal-footer justify-content-between">
								<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
								<button type="submit" class="btn btn-primary pull-right"><i class="fas fa-sync mr-1"></i>Change Status</button>
							</div>
						</form>

					</div>
				</div>
			</div>
			<!-- tutup modal -->


		</div>

	</div>

	<!--   Core JS Files   -->
	<script src="./assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="./assets/js/core/popper.min.js"></script>
	<script src="./assets/js/core/bootstrap.min.js"></script>

	<!-- jQuery UI -->
	<script src="./assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="./assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

	<!-- jQuery Scrollbar -->
	<script src="./assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

	<!-- Moment JS -->
	<script src="./assets/js/plugin/moment/moment.min.js"></script>


	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>


	<!-- Bootstrap Toggle -->
	<script src="./assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>


	<!-- Dropzone -->
	<script src="./assets/js/plugin/dropzone/dropzone.min.js"></script>

	<!-- Fullcalendar -->
	<script src="./assets/js/plugin/fullcalendar/fullcalendar.min.js"></script>

	<!-- DateTimePicker -->
	<script src="./assets/js/plugin/datepicker/bootstrap-datetimepicker.min.js"></script>

	<!-- Bootstrap Tagsinput -->
	<script src="./assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>

	<!-- Bootstrap Wizard -->
	<script src="./assets/js/plugin/bootstrap-wizard/bootstrapwizard.js"></script>

	<!-- jQuery Validation -->
	<script src="./assets/js/plugin/jquery.validate/jquery.validate.min.js"></script>

	<!-- Summernote -->
	<script src="./assets/js/plugin/summernote/summernote-bs4.min.js"></script>

	<!-- Select2 -->
	<script src="./assets/js/plugin/select2/select2.full.min.js"></script>


	<!-- Owl Carousel -->
	<script src="./assets/js/plugin/owl-carousel/owl.carousel.min.js"></script>

	<!-- Atlantis JS -->
	<script src="./assets/js/atlantis.min.js"></script>




	<script>
		$(document).ready(function() {


			let editor1 = CKEDITOR.replace('description', {
				removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize',
			});
			editor1.updateElement();


			let editor2 = CKEDITOR.replace('requirement', {
				removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize',
			});
			editor2.updateElement();


			let _token = $('meta[name="csrf-token"]').attr('content');

			let dtable =
				$('#ekonomi-kepri').DataTable({
					"responsive": true,
					"searching": true, // aktifkan fitur pencarian
					"processing": true,
					"serverSide": true,
					// "language": {
					// 		"decimal": ",",

					// 		"emptyTable": "Tidak ada data yang ditemukan",
					// 		"info": "Menampilkan data ke _START_ - _END_ dari _TOTAL_ data",
					// 		"infoEmpty": "Menampilkan 0 dari 0 data",
					// 		"infoFiltered": "(difilter dari _MAX_ data)",
					// 		"infoPostFix": "",
					// 		"thousands": ".",
					// 		"lengthMenu": "Menampilkan _MENU_ data",
					// 		"loadingRecords": "",
					// 		"processing": "",
					// 		"search": "Cari:",
					// 		"zeroRecords": "Tidak ada data yang sesuai",
					// 		"paginate": {
					// 			"first": "Pertama",
					// 			"last": "Terakhir",
					// 			"next": "Selanjutnya",
					// 			"previous": "Sebelumnya"
					// 		},
					// 	},
					"ajax": {
						"url": "index.php?form=offer2&action=data",
						"type": "POST",

						"error": function(jqXHR, textStatus, errorThrown) {

							// console.log(jqXHR.responseText, textStatus);
							// console.log(errorThrown);

							setTimeout(function() {
								swal.fire({

									title: 'Failed',
									text: 'Failed to fetch university data !',
									icon: 'error',
									timer: 2000,
									showConfirmButton: true
								});
							}, 10);
						},


						"data": function(d) {
							d.start = d.start; // set default value for start
							d.length = d.length || 10; // set default value for length
							d.order = d.order; // set default value for length
							return $.extend({}, d, {
								"token": _token // add extra data if needed
							});
						},

					},
					"columns": [{
							"data": 'id',
							"orderable": false,
							"searchable": false,
							"render": function(data, type, row, meta) {
								return meta.row + meta.settings._iDisplayStart + 1;
							}
						},
						{
							"data": "position",
							"searchable": true,
							"orderable": true
						},
						{
							"data": "offer_type",
							"searchable": true,
							"orderable": true
						},
						{
							"data": "city",
							"searchable": true,
							"orderable": true
						},
						{
							"data": "job_type",
							"searchable": true,
							"orderable": true
						},
						{
							"data": "status",
							"searchable": true,
							"orderable": true
						},

						{
							"data": "aksi",
							"searchable": false,
							"orderable": false

						}
					],

					"order": [
						[1, 'asc']
					],

					"drawCallback": function(response) {
						// console.log(response.json);
					}
				});



			$('#ekonomi-kepri').on('click', '.btn-delete', function() {
				var customerId = $(this).data('id'); // ambil ID dari data customer yang ingin dihapus

				if (confirm("Anda yakin ingin menghapus data ini?")) {
					$.ajax({
						url: 'index.php?request=university_delete',
						type: 'POST',
						data: {
							'token': _token,
							'_method': 'DELETE',
							'id': customerId,
						},

						success: function(response, xhr, status, error) {


							$('#ekonomi-kepri').DataTable().ajax.reload();

							setTimeout(function() {
								swal.fire({

									title: 'Success',
									text: 'University data successfully deleted !',
									icon: 'success',
									timer: 2000,
									showConfirmButton: true
								});
							}, 10);

						},


						error: function(response, xhr, status, error) {
							alert("Terjadi kesalahan saat menghapus data!");
						}
					});
				}
			});

			$('#btn-add').on('click', function() {
				$('#Modal').modal('show');
			});

			submitFormAjax('#form-tambah', 'POST', function(response) {


				$('#ekonomi-kepri').DataTable().ajax.reload();

				$('#ModalTambah').modal('hide');


				getNotificationForm('#form-tambah');

				resetForm('#form-tambah');

			});

			resetFormAjax('#form-tambah');


		});
	</script>

	<script>
		$(document).ready(function() {
			<?php if (old('old_tambah') !== NULL) : echo "$('#ModalTambah').modal('show');";
			endif; ?>
			<?php if (old('ModalChange') !== NULL) : echo "$('#ModalChange').modal('show');";
			endif; ?>



			// $('#ekonomi-kepri').DataTable({
			// 	"pageLength": 10,
			// });
		});

		function modal_hapus(id) {
			var id_hapus = document.getElementById('id_hapus');
			id_hapus.value = id;
		}

		function modal_change(id, offer) {
			var id_change = document.getElementById('id_change');
			id_change.value = id;

			var offer_status = document.getElementById('offer_status');
			offer_status.value = offer;
			offer_status.innerText = offer;

		}
	</script>

</body>

</html>