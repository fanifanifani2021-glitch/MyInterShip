<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Intern Logbook"; $route = 'intern_list'; 
include "./view/industri/template/head.php";

?>

<?php
$id_internship = base64_decode($_GET['id_internship']);
$id_user_company = $_SESSION['id_user_company'];

$sql = $_SERVER['KONEKSI_DB']->query("SELECT internship.* FROM internship WHERE internship.id_internship = '$id_internship' AND id_user_company = '$id_user_company'");

if ( $sql->num_rows != 1 ) {
    make_soloalert('error', 'Not Allowed', 'Student data is not found !');
    redirect_back(1); exit;
}

$internship_data = $sql->fetch_assoc();


$filter = $_GET['filter'];

if ( $filter == '' || $filter === NULL ) {

	$sql = "SELECT * FROM logbook WHERE id_internship = '$id_internship'";
	$exec = $_SERVER['KONEKSI_DB']->query($sql);

	if ( $exec->num_rows < 1 ) {
		$filter_value = '';
		$filter_text = 'None'; 
		$show_export = 'hidden';
	} else {
		$sql = "SELECT MAX(`start_date`) AS `start_date` FROM logbook WHERE id_internship = '$id_internship'";
		$exec = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

		$filter_value = date('Y-m', strtotime($exec['start_date']) );
		$filter_text = date('M Y', strtotime($exec['start_date']) );
	}


} else {
	$filter_value = date( 'Y-m', strtotime( $filter. '-01' ,date('m-Y')));
	$filter_text =  date( 'M Y', strtotime( $filter. '-01' ,date('m-y')));

}


?>

<body>
	<div class="wrapper">

        <?php include "./view/industri/template/main-header.php";?>
        <?php include "./view/industri/template/sidebar.php";?>
		
		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Intern Logbook</h4>
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
								<a href="index.php?page=intern_list_detail&id_internship=<?= base64_encode( $id_internship ) ?>">Intern Detail</a>
							</li>


							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Intern Logbook</a>
							</li>

							


							
						</ul>
					</div>

					<div class="row">


						<!-- Internship History -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Intern Logbook Data

										<div class="float-right justify-content-beetwen">

											<button data-toggle="modal" data-target="#ModalFilter" class="btn btn-info btn-sm btn-round mr-1"><i class="fas fa-filter mr-1"></i>Filter Data</button>
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
											<option value="<?= $filter_value?>" hidden><?= $filter_text ?></option>
											<?php
												$sql = "SELECT DISTINCT(YEAR(`start_date`)) AS LOGBOOK_YEAR FROM logbook WHERE id_internship = '$id_internship'";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);

												if ( $exec->num_rows < 1 ) {
													echo "<option value=''>None</option>";
												}

												foreach ( $exec as $i => $data ) {
													$year = $data['LOGBOOK_YEAR'];

													$sql2 = "SELECT DISTINCT(MONTH(`start_date`)) AS LOGBOOK_DATE FROM logbook WHERE id_internship = '$id_internship' AND YEAR(`start_date`) = $year ";
													$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
													foreach ( $exec2 as $ii => $data2 ) {
														$new_date = strtotime( '01-' .sprintf( '%02s', $data2['LOGBOOK_DATE'] ). '-' .$year);
														$value = date( 'Y-m', $new_date );
														$text = date( 'M Y', $new_date );
														echo "<option value='$value'>$text</option>";
													}
												}
											?>
										</select>

										<script type="text/javascript">
											function change() {
												var data = document.getElementById('filter_data').value;
												window.location.href='index.php?page=intern_list_logbook&id_internship=<?= base64_encode($id_internship) ?>&filter=' +data;
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
										<table id="basic-datatables" class="display table table-striped table-hover" >

										<thead>
											<tr>
												<th>No.</th>
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

												$sql = "SELECT * FROM logbook WHERE id_internship = '$id_internship' AND `start_date` LIKE '%$filter_value%'";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);

												foreach ( $exec as $i => $data ):

													if ( $data['approval_spv'] == 'Waiting'  ) {
														$approval_spv = '<button class="btn btn-sm btn-warning">Waiting for Approval</button>';
													} else if ( $data['approval_spv'] == 'Yes' ) {
														$approval_spv = '<button class="btn btn-sm btn-success">Approved</button>';
													} else {
														$approval_spv = '<button class="btn btn-sm btn-danger">Not Approved</button>';
													}


													if ( $data['approval_dpm'] == 'Waiting'  ) {
														$approval_dpm = '<button class="btn btn-sm btn-warning">Waiting for Approval</button>';
													} else if ( $data['approval_dpm'] == 'Yes' ) {
														$approval_dpm = '<button class="btn btn-sm btn-success">Approved</button>';
													} else {
														$approval_dpm = '<button class="btn btn-sm btn-danger">Not Approved</button>';
													}

											?>

											<tr>
												<td><?= $i + 1 ?></td>
												<td>M<?= $data['week_num'] ?></td>
												<td><?= date('D, d-M-Y', strtotime($data['start_date'] )) ?></td>
												<td><?= date('D, d-M-Y', strtotime($data['end_date'] )) ?></td>
												<td class="text-center"><?= $approval_dpm ?></td>
												<td class="text-center"><?= $approval_spv ?></td>
												<td>
													<div class="btn-group btn-round">
														<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
															Action
														</button>
														<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

															<button class="dropdown-item" onclick="window.location.href = 'index.php?page=intern_list_logbook_detail&id_internship=<?= base64_encode($data['id_internship']) ?>&id_logbook=<?= base64_encode($data['id_logbook']) ?>'">Detail Logbook</button>

															<div class="dropdown-divider"></div>

															<?php if ( $internship_data['id_user_company'] == $_SESSION['id_user_company']) :?>
															<button class="dropdown-item" data-toggle="modal" data-target="#ModalApproval<?= $i + 1 ?>">Approval Logbook</button>
															<?php else : ?>
															<button disabled class="dropdown-item" data-toggle="modal" data-target="#ModalApproval<?= $i + 1 ?>">Approval Logbook</button>
															<?php endif; ?>


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

															<form action="index.php?form=logbook_approval&action=supervisor" method="POST"> 
																<?= csrf() ?> <?= input_hidden('id_logbook', $data['id_logbook'])?>
																<?= input_hidden('id_user_company', $_SESSION['id_user_company'])?> <?= input_hidden('id_internship', $id_internship)?>
																<?= input_hidden('loop' , $i+1) ?>
                        
                                                            <div class="form-group">
                                                            <label for="approval_spv">Approval Supervisor <label class="text-danger">*</label></label>
                                                            <select class="form-control" name="approval_spv" id="approval_spv">
                                                                <option hidden><?= $data['approval_spv']; ?></option>
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                                <option>Waiting</option>
                                                            </select>
                                                            <?= form_error('approval_spv'); ?>
                                                            </div>


                                                            </div>
                                                            <div class="modal-footer justify-content-between">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                                                            <button type="submit" class="btn btn-primary"><i class="fas fa-check-double mr-1"></i>Approval Logbook</button>
                                                            </form>

															</div>
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




					</div>
				</div>
			</div>
			<?php include './view/industri/template/footer.php';?>




		</div>
		
	</div>
	<!--   Core JS Files   -->
	<script src="./assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="./assets/js/core/popper.min.js"></script>
	<script src="./assets/js/core/bootstrap.min.js"></script>
	<!-- jQuery UI -->
	<script src="./assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="./assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
	<!-- Bootstrap Toggle -->
	<script src="./assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
	<!-- jQuery Scrollbar -->
	<script src="./assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>
	<!-- Atlantis JS -->
	<script src="./assets/js/atlantis.min.js"></script>

    <!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>


    <!-- AJAX Script -->
    <script>

        $(document).ready(function() { 

			$('#basic-datatables').DataTable({
			});

			<?php if ( old('ModalApproval') !== NULL ) : 
				echo "$('#ModalApproval" .old('ModalApproval')."').modal('show')";
			endif;?>
        
        });

    </script>





</body>
</html>