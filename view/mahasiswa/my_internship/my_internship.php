<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "My Internship";
$route = 'my_internship';
include "./view/mahasiswa/template/head.php";

$nim = $_SESSION['nim'];


?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white pb-2 fw-bold">My Internship</h2>
								<h5 class="text-white op-7 mb-2">Internship History</h5>
							</div>
						</div>
					</div>
				</div>
				
				<div class="page-inner mt--5">
					<div class="row mt--2">


						<!-- Internship History -->
						<div class="col-md-12">


							<div class="card">
								<div class="card-header">
									<div class="card-title">Internship History List
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table id="basic-datatables" class="display table table-striped table-hover">
											<thead>
												<tr>
													<th>No.</th>
													<th>Company Name</th>
													<th>Status</th>
													<th>Start Date</th>
													<th>End Date</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>

												<?php

												$sql = "SELECT internship.*, company.name FROM internship, company WHERE company.id_company = internship.id_company AND internship.nim = :nim";
												$exec = $_SERVER['PDO']->prepare($sql);
												$exec->execute([':nim' => $_SESSION['nim']]);

												?>

												<?php foreach ($exec as $i => $intern_data) : ?>

													<tr>
														<td><?= $i + 1 ?>.</td>
														<!-- <td><?= date('d-M-Y', strtotime($intern_data['timestamp_register'])) ?></td> -->
														<td><?= $intern_data['name'] ?></td>
														<td><?= $intern_data['status'] ?></td>
														<td><?= date('d-M-Y', strtotime($intern_data['start_date'])) ?></td>
														<td><?= date('d-M-Y', strtotime($intern_data['end_date'])) ?></td>

														<td>

															<button type="button" onclick="window.location.href='index.php?page=internship_detail&id_internship=<?= base64_encode($intern_data['id_internship']) ?>' " class="btn btn-secondary btn-sm"> <i class="fas fa-info-circle mr-1"></i>Detail</button>


															<?php if ($intern_data['id_internship'] == $id_internship) {
																echo "<span class='badge badge-success'>Current Internship</sp>";
															} else { ?>
																<!-- <button type="button" class="btn btn-secondary btn-sm"> <i class="fas fa-info-circle mr-1"></i>Detail</button> -->

																<!-- <div class="btn-group btn-round">
																<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Action
																</button>
																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																	<button class="dropdown-item" onclick="window.location.href = 'index.php?page=my_registration&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($intern_data['id_internship']) ?>'">Registration Data</button>

																	<?php

																	$sql = "SELECT * FROM logbook WHERE id_internship = '" . $intern_data['id_internship'] . "'";
																	$exec = $_SERVER['KONEKSI_DB']->query($sql);

																	$sql2 = "SELECT * FROM attendance WHERE id_internship = '" . $intern_data['id_internship'] . "'";
																	$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

																	if ($intern_data['status'] == 'Yes' || $intern_data['status'] == 'yes' || $intern_data['status'] == 'Finish' || $intern_data['status'] == 'finish' || $exec->num_rows > 0 || $exec2->num_rows > 0) :
																	?>
																	<div class="dropdown-divider"></div>

																	<button class="dropdown-item" onclick="window.location.href = 'index.php?page=jobdesk_internship&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($data['id_internship']) ?>'">Jobdesk</button>
																	<button class="dropdown-item" onclick="window.location.href = 'index.php?page=attendance_internship&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($data['id_internship']) ?>'">Attendance</button>

																	<button class="dropdown-item" onclick="window.location.href = 'index.php?page=logbook_internship&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($data['id_internship']) ?>'">Logbook</button>
																	<?php endif; ?>

																</div>
															</div> -->
															<?php } ?>

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

			$('#basic-datatables').DataTable({});

		});
	</script>




</body>

</html>