<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Company List";
$route = 'company_list';
include "./view/dosen/template/head.php";

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
								<h2 class="text-white pb-2 fw-bold">Company List</h2>
								<h5 class="text-white op-7 mb-2">List of Company</h5>
							</div>
						</div>
					</div>
				</div>



				<div class="page-inner mt--5">
					<div class="row mt--2">
						<div class="col-md-12">


							<!-- <div class="card">
								<div class="card-header">
									<div class="card-title">Company List
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">

										<table aria-label="All Company Data Available in My Internship" id="basic-datatables" class="display dataTable table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>Name</th>
													<th>Type</th>
													<th>City</th>
													<th>Access Type</th>
													<th>Status</th>
													<th style="width: 5%">Action</th>
												</tr>
											</thead>
											<tbody id="tbody-staff">
												<?php
												$sql = "SELECT * FROM company ORDER BY id_company DESC";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);
												$no = 1;

												foreach ($exec as $row) :

													if (in_array($row['status'], ['verified', 'Verified', 1])) {
														$status = '<button type="button" class="btn btn-sm btn-success">Verified</button>';
													} else

											if (in_array($data['status'], [0, 'not verified', 'Not Verified'])) {
														$status = '<button type="button" class="btn btn-sm btn-danger">Not Verified</button>';
													} else

											if (in_array($data['status'], [2, 'blacklist', 'Blacklist'])) {
														$status = '<button type="button" class="btn btn-sm btn-dark">Blacklist</button>';
													}

												?>
													<tr>
														<td><?= $no++ ?>.</td>
														<td><?= $row['name'] ?></td>
														<td><?= ucwords($row['type']) ?></td>
														<td><?= $row['city'] ?></td>
														<td><?= $row['access_type'] ?> <i class="ml-2 fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="1 - Can Make Offer; 2 - Cannot Make Offer"></i></td>
														<td><?= $status ?></td>
														<td>
															<a aria-label="Detail Company <?= $row['name'] ?>" href="index.php?page=company_list_detail&id_company=<?= base64_encode($row['id_company']) ?>" type="button" class="btn btn-secondary btn-sm">
																<i class="fa fa-info-circle mr-1"></i> Detail
															</a>

														</td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div> -->


							<div class="card">
								<div class="card-header">
									<div class="card-title">Company List
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">

										<table aria-label="All Company Data Available in My Internship" id="datatables" class="display dataTable table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>Name</th>
													<th>Type</th>
													<th>City</th>
													<!-- <th>Access Type</th> -->
													<th>Status</th>
													<th style="width: 5%">Action</th>
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
			<?php include './view/dosen/template/footer.php'; ?>
		</div>

	</div>


	<?php include "./view/dosen/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<script>
		$(document).ready(function() {
			$('#basic-datatables').DataTable({});

			let _token = $('meta[name="csrf-token"]').attr('content');

			let dtable =
				$('#datatables').DataTable({
					"responsive": true,
					"searching": true, // aktifkan fitur pencarian
					"processing": true,
					"serverSide": true,
					// "language": {
					// 	"decimal": "",
					// 	"emptyTable": "Tidak ada data yang ditemukan",
					// 	"info": "Menampilkan data ke _START_ - _END_ dari _TOTAL_ data",
					// 	"infoEmpty": "Menampilkan 0 dari 0 data",
					// 	"infoFiltered": "(difilter dari _MAX_ data)",
					// 	"infoPostFix": "",
					// 	"thousands": ".",
					// 	"lengthMenu": "Menampilkan _MENU_ data",
					// 	"loadingRecords": "",
					// 	"processing": "",
					// 	"search": "Cari:",
					// 	"zeroRecords": "Tidak ada data yang sesuai",
					// 	"paginate": {
					// 		"first": "Pertama",
					// 		"last": "Terakhir",
					// 		"next": "Selanjutnya",
					// 		"previous": "Sebelumnya"
					// 	},
					// },
					"ajax": {
						"url": "index.php?form=company_data",
						"type": "POST",

						"error": function(jqXHR, textStatus, errorThrown) {

							// console.log(jqXHR.responseText, textStatus);

							setTimeout(function() {
								swal.fire({

									title: 'Failed',
									text: 'Failed to fetch company data !',
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
							"data": 'id_company',
							"orderable": true,
							"searchable": false,
							"render": function(data, type, row, meta) {
								return meta.row + meta.settings._iDisplayStart + 1;
							}
						},
						{
							"data": "name",
							"searchable": true,
							"orderable": true
						},
						{
							"data": "type",
							"searchable": true,
							"orderable": true
						},
						{
							"data": "city",
							"searchable": true,
							"orderable": true
						},

						// {
						// 	"data": "access_type",
						// 	"searchable": true,
						// 	"orderable": true
						// },

						{
							"data": "status_label",
							"searchable": false,
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
						console.log(response.json);
					}


				});

		});
	</script>



</body>

</html>