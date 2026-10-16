<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Internship Offer'; $route = 'student_offer';
include "./view/mahasiswa/template/head.php";

$sql = "SELECT * FROM company,offer WHERE offer.offer_type = 'Internship' AND offer.id_company = company.id_company ORDER BY offer.status, offer.id_offer DESC";
$sql = $_SERVER['PDO']->query( $sql );
$sql->execute();

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
								<h2 class="text-white pb-2 fw-bold">Internship Offer</h2>
								<h5 class="text-white op-7 mb-2">Internship Offer Available at My Internship</h5>
							</div>
						</div>
					</div>
				</div>



				<div class="page-inner mt--5">
					<div class="row mt--2">

                        <div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title">Internship Offer List</h4>
								</div>

                                <div class="card-body">
                                <div class="table-responsive" id="tabel-ini">
									<table id="ekonomi-kepri" 
										class="display dataTable table table-striped table-hover" >
										<thead>
											<tr>
												<th>No</th>
                                                <th>Name Company</th>
												<th>Position</th>
												<th>Requirement</th>
												<th>City</th>
												<th>Job Type</th>
												<th>Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="tbody-staff">
											<?php
											$no = 1;

											foreach($sql as $row): 


												if ( $row['status'] == 'Open' ) {
													$status = "<button type='button' class='btn btn-success btn-sm'>Open</button>";
												} else {
													$status = "<button type='button' class='btn btn-danger btn-sm'>Closed</button>";
												}
											?>
											<tr>
												<td><?=$no++; ?>.</td>
                                                <td><?=$row['name']?></td>
												<td><?=$row['position']?></td>
												<td><?=$row['requirement']?></td>
												<td><?=$row['city']?></td>
												<td><?=$row['job_type']?></td>
												<td><?=$status?></td>
												<td>
													<div class="btn-group btn-round">
															<button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																Action
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																<?php if ( $row['status'] == 'Open' ) : ?>
																<button onclick="window.location.href='index.php?page=register_offer&id=<?= base64_encode($row['id_offer']) ?>' " type="button" class="dropdown-item">
																	<i class="fab fa-wpforms mr-2"></i>Apply
																</button>
																<div class="dropdown-divider"></div>
																<?php endif; ?>
																<button class="dropdown-item" onclick="window.location.href = 'index.php?page=internship_offer_detail&id_offer=<?= base64_encode($row['id_offer']); ?>' "><i class="fas fa-info-circle mr-2"></i>Offer Detail</button>

															</div>
													</div>
                                                </td>
											</tr>

											<?php 
                                            endforeach; 
                                            ?>
										</tbody>
									</table>
									</div>
                                </div>

							</div>
						</div>


					</div>


				</div>
			</div>

			<?php include "./view/mahasiswa/template/footer.php"; ?>

		</div>
		
	</div>

	<?php include "./view/mahasiswa/template/min-script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<!-- User Custom Script -->
	<script>

        $(document).ready(function() {

			$('#ekonomi-kepri').DataTable({
				"pageLength": 10,
			});

		});

	</script>

</body>
</html>