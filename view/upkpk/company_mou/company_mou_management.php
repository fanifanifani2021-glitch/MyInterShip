<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

include "./view/upkpk/template/head.php";

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
								<h2 class="text-white pb-2 fw-bold">Company MoU Management</h2>
								<h5 class="text-white op-7 mb-2">Managing Company MoU</h5>
							</div>
							<div class="ml-md-auto py-2 py-md-0">
								<a href="index.php?page=add_mou" class="btn btn-primary btn-round"><i class="fa fa-plus mr-2"></i> Add MoU</a>
							</div>
						</div>
					</div>
				</div>

				<div class="page-inner mt--5">
					<div class="row mt--2">
						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="card-title">
										<span class="text-sm">Company MoU</span>

									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive" id="tabel-ini">
										<table id="ekonomi-kepri" class="display dataTable table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>Company</th>
													<th>Type</th>
													<th>Date of MoU</th>
													<th>File</th>
													<th>Status</th>
													<th style="width: 5%">Action</th>
												</tr>
											</thead>
											<tbody id="tbody-staff">
												<?php

												foreach ($data as $no => $row) :
													
															if( in_array($row['mou_status'], [1, 'verified', 'Verified'] ) ){
																$status = '<button type="button" class="btn btn-sm btn-success">Verified</button>';
															} else
															if( in_array($row['mou_status'], [0, 'not verified', 'Not Verified'] ) ){
																$status = '<button type="button" class="btn btn-sm btn-danger">Not Verified</button>';
															} else
															if( in_array($row['mou_status'], [2, 'blacklist', 'Blacklist'] ) ){
																$status = '<button type="button" class="btn btn-sm btn-dark">Blacklist</button>';
															}
															mysqli_connect();
													$mou_file = 'index.php?viewer=mou&id_mou=' .base64_encode( $row['id_mou'] ). '&file='.base64_encode($row['mou_file'] );
												?>
													<tr>
														<td><?= $no+1 ?>.</td>
														<td><?= $row['company_name'] ?></td>
														<td><?= $row['mou_type'] ?></td>
														<td><?= date('d M Y', strtotime($row['mou_date']) ) ?></td>
														<td>
														<a target="_blank" class="btn btn-sm btn-round btn-info" href="<?= $mou_file ?>" ><i class="fas fa-file-pdf mr-2"></i>Mou File</a>
															
														</td>
														<td><?= $row['mou_status'] ?></td>
														<td>
															<div class="btn-group btn-round">
																<button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Action
																</button>
																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																	<button onclick="window.location.href='index.php?page=detail_mou&id_mou=<?= base64_encode($row['id_mou']); ?>' " type="button" class="dropdown-item">
																		<i class="fas fa-info-circle mr-2"></i>MoU Detail
																	</button>

																	<div class="dropdown-divider"></div>
																	<button class="dropdown-item" onclick="window.location.href='index.php?page=edit_mou&id_mou=<?= base64_encode( $row['id_mou'] ) ?>'"><i class="fas fa-edit mr-2"></i>Edit Data</button>
																	<button class="dropdown-item text-danger" data-toggle="modal" data-target="#ModalHapus" onclick="modal_hapus('<?= base64_encode($row['id_mou']) ?>')"><i class="fas fa-trash-alt mr-2"></i>Delete Data</button>


																</div>
															</div>

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

			<?php include "./view/upkpk/template/footer.php"; ?>
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
							<form action="index.php?form=company_mou&action=delete" method="POST">
								<?= csrf(); ?>
								<?= input_hidden('id_hapus'); ?>
								Are You Sure to Delete Current Company MoU Data ?
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

		</div>

	</div>

	<?php include "./view/upkpk/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>


	<script>
		$(document).ready(function() {
			$('#ekonomi-kepri').DataTable({
				"pageLength": 10,
			});
		});

		function modal_hapus(id) {
			var id_hapus = document.getElementById('id_hapus');
			id_hapus.value = id;
		}
	</script>

</body>

</html>