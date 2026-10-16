<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "CDC Management"; $route = 'upkpk_management'; 
include "./view/upkpk/template/head.php";

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
								<h2 class="text-white pb-2 fw-bold">CDC Management</h2>
								<h5 class="text-white op-7 mb-2">Managing CDC User</h5>
							</div>
							<div class="ml-md-auto py-2 py-md-0">
								<a aria-label="Add New UPKPK User" href="index.php?page=add_upkpk" class="btn btn-primary btn-round"><i class="fa fa-plus"></i> Add Data</a>
							</div>
						</div>
					</div>
				</div>



				<div class="page-inner mt--5">
					

					<div class="row mt--2">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">CDC Users List
								
                                    </div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table id="basic-datatables" class="display table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>Name</th>
													<th>Username</th>
													<th>Status</th>
													<th>University</th>
													<th>Action</th>
												</tr>
											</thead>

                                            <tbody>

												<?php 
												$sql = "SELECT upkpk.*, tb_kampus.* FROM upkpk LEFT JOIN tb_kampus on tb_kampus.id_kampus = upkpk.id_kampus WHERE username NOT IN ('".$_SESSION['username']."')" ;
												$query = $_SERVER['KONEKSI_DB']->query( $sql );
												?>

												<?php foreach($query as $index => $data):
													?>

												<tr>
													<td><?= $index+1 ?>.</td>
													<td><?= $data['name'] ?></td>
													<td><?= $data['username'] ?></td>
													<td><?= $data['status'] ?></td>
													<td><?= $data['nama_kampus'] ?></td>
													<td>
														<div class="btn-group btn-round">
															<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																Action
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																<button class="dropdown-item" onclick="reset_password('<?= base64_encode($data['id_upkpk']) ?>')"><i class="fas fa-key mr-2"></i>Reset Password</button>
																<div class="dropdown-divider"></div>

																<button aria-label="Edit CDC User" class="dropdown-item" onclick="window.location.href = 'index.php?page=edit_upkpk&id=<?= base64_encode($data['id_upkpk']) ?>'"><i class="fas fa-edit mr-2"></i>Edit Data</button>
																<button aria-label="Delete CDC User" class="dropdown-item" onclick="swal_confirm('<?= base64_encode($data['id_upkpk']) ?>')"><i class="fas fa-trash-alt mr-2"></i>Delete Data</button>
															

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

	<?php include "./view/upkpk/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<!-- Sweet Alert -->
	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>

	<script>
		function swal_confirm( id ) {
			swal.fire({
				title               : 'Delete Confirmation',
				text                :  'Are you sure you want to delete this data?',
				icon                : 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						url: "./ajax/delCDC.php",
						type: "POST",
						data: { id_upkpk: id },
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

		function reset_password( id ) {
			swal.fire({
				title               : 'Reset Password Confirmation',
				text                :  'Are you sure you want to Reset Password for current user ?',
				icon                : 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						url: "./ajax/resetPasswordCDC.php",
						type: "POST",
						data: { id_upkpk: id },
						dataType: 'text',
					success: function ( response ) {
						window.location.reload(1);
					},
					error: function ( response ) {
						Swal.fire("Reset Password Failed !", "Please try again later !", "error");
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