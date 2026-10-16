<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Coordinator Management"; $route = 'intern_co_management'; 
include "./view/upkpk/template/head.php";

?>

<?php
$id_kampus = $_SESSION['CDC_Data'] ['id_kampus'];

$sql = "SELECT * FROM tb_kampus WHERE id_kampus = '$id_kampus'";
$exec = $_SERVER['KONEKSI_DB']->query( $sql )->fetch_assoc();
$nama_kampus = $exec['nama_kampus'];


?>

<body>
	<div class="wrapper">

        <?php include "./view/upkpk/template/main-header.php";?>
        <?php include "./view/upkpk/template/sidebar.php";?>
		
		<div class="main-panel">


			<div class="container">

				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white pb-2 fw-bold">Coordinator Management</h2>
								<h5 class="text-white op-7 mb-2">Coordinator Management for <?= $nama_kampus ?></h5>
							</div>
							<div class="ml-md-auto py-2 py-md-0">
								<a href="index.php?page=add_intern_co" class="btn btn-primary btn-round"><i class="fa fa-plus"></i> Add Data</a>
							</div>
						</div>
					</div>
				</div>



				<div class="page-inner mt--5">
					

					<div class="row mt--2">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Internship Coordinator List</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table id="basic-datatables" class="display table table-striped table-hover" >
											<thead>
												<tr>
													<th>No.</th>
													<th>Program Study</th>

													<th>NIK</th>
													<th>Name</th>
													<th>Action</th>
												</tr>
											</thead>

                                            <tbody>

												<?php 

												$sql = "SELECT lecturer.*, program_study.* FROM lecturer INNER JOIN program_study ON program_study.kode_prodi = lecturer.prodi_koor WHERE lecturer.id_kampus = '$id_kampus' AND lecturer.is_koor IN (1)" ;
												$sql_supervisor = $_SERVER['KONEKSI_DB']->query( $sql );
												?>

												<?php foreach($sql_supervisor as $index => $data):
													$nik = $data['nim_nik_unit'];
													?>

												<tr>
													<td><?= $index+1 ?>.</td>
													<td><?= $data['jenjang'] ?> <?= $data['prodi'] ?></td>
													<td><?= $data['nim_nik_unit'] ?></td>
													<td><?= $data['name'] ?></td>
													<td>
														<div class="btn-group btn-round">
															<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																Action
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																<button class="dropdown-item" onclick="window.location.href = 'index.php?page=detail_intern_co&nik=<?= base64_encode($data['nim_nik_unit']) ?>'"><i class="fas fa-info-circle mr-2"></i>Detail Data</button>
																<div class="dropdown-divider"></div>

																<button class="dropdown-item" onclick="window.location.href = 'index.php?page=edit_intern_co&nik=<?= base64_encode($data['nim_nik_unit']) ?>'"><i class="fas fa-edit mr-2"></i>Edit Data</button>
																<button class="dropdown-item" onclick="swal_confirm('<?= $data['nim_nik_unit'] ?>')"><i class="fas fa-trash-alt mr-2"></i>Delete Data</button>
															

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


			<?php include './view/upkpk/template/footer.php';?>


		</div>
		
	</div>

	<?php include './view/upkpk/template/script.php';?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>

	<script>
		function swal_confirm( id ) {
			swal.fire({
				title               : 'Delete Confirmation',
				text                :  'Are you sure you want to delete current Internship Coordinator ?',
				icon                : 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						url: "./ajax/delInternshipCoordinator.php",
						type: "POST",
						data: { nim_nik_unit: id },
						dataType: 'text',
					success: function ( response ) {
						window.location.reload(1);
					},
					error: function ( response ) {
						Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
					}
					});
					
				}
			})

		}
	</script>


	<script >
		$(document).ready(function() {
			$('#basic-datatables').DataTable({
			});

		});
	</script>



</body>
</html>