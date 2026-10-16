<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Attendance"; $route = 'intern_student_management'; 
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';

?>

<?php

$id_internship = base64_decode($_GET['id_internship']);
$nim = base64_decode($_GET['nim']);


// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship' ";
$exec = $_SERVER['KONEKSI_DB']->query($sql);


if ( $exec->num_rows != 1 ) {
	make_swal2('error', 'Not Allowed', 'Internship is not found !');
	redirect_back(1); exit;
}

$internship_data = $exec->fetch_object();


if ( $internship_data->status != 'Yes' ) {
	make_swal2('error', 'Not Allowed', 'Internship is not accepted !');
	redirect_back(1); exit;
}


$filter = $_GET['filter'];

if ( $filter == '' || $filter === NULL ) {

	$sql = "SELECT * FROM attendance WHERE id_internship = '$id_internship'";
	$exec = $_SERVER['KONEKSI_DB']->query($sql);

	if ( $exec->num_rows < 1 ) {
		$filter_value = '';
		$filter_text = 'None'; 
		$show_export = 'hidden';
	} else {
		$sql = "SELECT MAX(`date`) AS `date` FROM attendance WHERE id_internship = '$id_internship'";
		$exec = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

		$filter_value = date('Y-m', strtotime($exec['date']) );
		$filter_text = date('M Y', strtotime($exec['date']) );
	}


} else {
	$filter_value = date( 'Y-m', strtotime( $filter. '-01' ,date('m-Y')));
	$filter_text =  date( 'M Y', strtotime( $filter. '-01' ,date('m-y')));

}

// Get Student
$nim = $internship_data->nim;

$query = $_SERVER['PDO']->prepare("SELECT * FROM student_internship WHERE nim = :nim");


$query->execute(['nim' => $nim]);

$student_data = $query->fetch( PDO::FETCH_ASSOC );

$nik_dpm = $student_data['nik_dospem'];
?>

<body>
	<div class="wrapper">

        <?php include "./view/dosen/template/main-header.php";?>
        <?php include "./view/dosen/template/sidebar.php";?>
		
		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Attendance</h4>
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
								<a href="index.php?page=intern_student_management">Intern Students Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_detail&nim=<?= base64_encode($nim) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Attendance</a>
							</li>
						</ul>
					</div>



                    <div class="row">

						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Attendance Data


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
												$sql = "SELECT DISTINCT(YEAR(`date`)) AS ATT_YEAR FROM attendance WHERE id_internship = '$id_internship'";
												$exec = $_SERVER['KONEKSI_DB']->query($sql);

												if ( $exec->num_rows < 1 ) {
													echo "<option value=''>None</option>";
												}

												foreach ( $exec as $i => $data ) {
													$year = $data['ATT_YEAR'];

													$sql2 = "SELECT DISTINCT(MONTH(`date`)) AS ATT_DATE FROM attendance WHERE id_internship = '$id_internship' AND YEAR(`date`) = $year ";
													$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
													foreach ( $exec2 as $ii => $data2 ) {
														$new_date = strtotime( '01-' .sprintf( '%02s', $data2['ATT_DATE'] ). '-' .$year);
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
												window.location.href='index.php?page=intern_student_attendance&nim=<?= base64_encode($nim)?>&id_internship=<?= base64_encode($id_internship) ?>&filter=' +data;
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
													<th>Month</th>
													<th>Week Num</th>
													<th>Date</th>
													<th>Attendance Type</th>
													<th>Approval Lecturer</th>
													<th>Approval Supervisor</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>
												<?php

													$sql = "SELECT * FROM attendance WHERE id_internship = '$id_internship' AND `date` LIKE '%$filter_value%' ";
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

													$loop = $i;

												?>

												<tr>
													<td><?= $i + 1 ?></td>
													<td><?= date('M Y', strtotime($data['date'] )) ?></td>
													<td>M<?= $data['week'] ?></td>
													<td><?= date('D, d-M-Y', strtotime($data['date'] )) ?></td>
													<td><?= $data['attendance_type'] ?></td>
													<td class="text-center"><?= $approval_dpm ?></td>
													<td class="text-center"><?= $approval_spv ?></td>
													<td>
														<div class="btn-group btn-round">
															<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																Action
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
																<!-- <button class="dropdown-item" data-toggle="modal" data-target="#ModalDetail<?= $loop ?>">Attendance Detail</button> -->
																<button class="dropdown-item" onclick="window.location.href='index.php?page=intern_student_attendance_detail&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>&id_attendance=<?= base64_encode($data['id_attendance']) ?>' ">Attendance Detail</button>
																
																<?php if ( $nik_dpm == $_SESSION['nim_nik_unit'] ) : ?>
																<div class="dropdown-divider"></div>
																<button class="dropdown-item" data-toggle="modal" data-target="#ModalApproval<?= $loop ?>">Approval Lecturer</button>
																<?php endif; ?>

															</div>
														</div>
													</td>
												</tr>



												<?php if ( $nik_dpm == $_SESSION['nim_nik_unit'] ) : ?>

												<div class="modal fade" id="ModalApproval<?= $loop ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
												<div class="modal-dialog" role="document">
													<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="exampleModalLabel">Approval Lecturer</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
														</button>
													</div>

													<form action="index.php?form=attendance_approval&action=lecturer" method="POST"> 
														<?= csrf() ?> <?= input_hidden('loop', $loop ) ?> 
														<?= input_hidden('nim' , $nim )?> 
														<?= input_hidden('nik' , $_SESSION['nim_nik_unit']) ?>
														<?= input_hidden('id_internship' , $id_internship)?>
														<?= input_hidden('id_attendance' , $data['id_attendance'])?>
													<div class="modal-body">

														<div class="form-group">
															<label class="control-label">Approval Lecturer <b class="text-danger">*</b></label>
															<select class="form-control" name="approval_dpm" id="approval_dpm">
																<option value="<?= $data['approval_dpm']?>" hidden><?= $data['approval_dpm']?></option>
																<option>Yes</option>
																<option>No</option>
																<option>Waiting</option>
															</select>
														</div>
													</div>
													<div class="modal-footer justify-content-between">
														<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Close</button>
														<button type="submit" class="btn btn-primary"><i class="fas fa-check-double mr-1"></i>Approval Lecturer</button>
													</div>

													</form>


													</div>
												</div>
												</div>

												<?php endif; ?>

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
			<?php include './view/dosen/template/footer.php';?>
		</div>
		
	</div>
	
	<?php include "./view/dosen/template/script.php"; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<!-- SWAL2 -->
	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>

	<!-- CKEDITOR -->
	<script src="./library/ckeditor/ckeditor.js"></script>

	<script>

		$(document).ready(function() { 

			// CKEDITOR
			CKEDITOR.replaceClass = 'description';

			$('#basic-datatables').DataTable({
			});

			
			<?php if ( old('ModalApproval') !== NULL ) : 
				echo "$('#ModalApproval" .old('ModalApproval')."').modal('show')";
			endif;?>

		});


	</script>




</body>
</html>