<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = "Attendance Internship";
$route = 'my_internship';
include "./view/mahasiswa/template/head.php";

?>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

<?php



// $filter = $_GET['filter'];

// if ( $filter == '' || $filter === NULL ) {

// 	$sql = "SELECT * FROM attendance WHERE id_internship = '$id_internship'";
// 	$exec = $_SERVER['KONEKSI_DB']->query($sql);

// 	if ( $exec->num_rows < 1 ) {
// 		$filter_value = '';
// 		$filter_text = 'None'; 
// 		$show_export = 'hidden';
// 	} else {

// 		$sql = "SELECT MAX(`date`) AS `date` FROM attendance WHERE id_internship = '$id_internship'";
// 		$exec = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

// 		$filter_value = date('Y-m', strtotime($exec['date']) );
// 		$filter_text = date('M Y', strtotime($exec['date']) );

// 	}


// } else {
// 	$filter_value = date( 'Y-m', strtotime( $filter. '-01' ,date('m-Y')));
// 	$filter_text =  date( 'M Y', strtotime( $filter. '-01' ,date('m-y')));

// 	$sql = "SELECT * FROM attendance WHERE id_internship = '$id_internship' AND `date` LIKE '%$filter_value%'";
// 	$exec = $_SERVER['KONEKSI_DB']->query($sql);

// 	if ( $exec->num_rows < 1 ) {
// 		$show_export = 'hidden';
// 	}
// }


// // Cek Today
// $today = date('Y-m-d');

// $sql2 = "SELECT * FROM attendance WHERE id_internship = '$id_internship' AND `date` LIKE '%$today%' ";
// $exec2 = $_SERVER['KONEKSI_DB']->query($sql2);

?>

<body>
	<div class="wrapper">

		<?php include "./view/mahasiswa/template/main-header.php"; ?>
		<?php include "./view/mahasiswa/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Attendance Internship</h4>
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
								<a href="#">Attendance Internship</a>
							</li>
						</ul>
					</div>



					<div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Attendance


										<div class="float-right justify-content-beetwen">

											<?php if (!in_array($filter_value, ['All'])) : ?>
												<button <?= $show_export ?> onclick="window.location.href = 'index.php?page=print_attendance_internship&id_internship=<?= base64_encode($id_internship) ?>&filter=<?= $filter_value ?>'" class="btn btn-info btn-sm btn-round mr-1"><i class="fas fa-download mr-1"></i>Download</button>
											<?php endif; ?>

											<button data-toggle="modal" data-target="#ModalFilter" class="btn btn-info btn-sm btn-round mr-1"><i class="fas fa-filter mr-1"></i>Filter Data</button>

											<?php
											if ($have_attendance) { ?>
												<button class="btn btn-info btn-sm btn-round" onclick="window.location.href = 'index.php?page=check_in_attendance_internship&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-sign-in-alt mr-1"></i>Check In</button>
											<?php } ?>

											<button class="btn btn-info btn-sm btn-round" onclick="window.location.href = 'index.php?page=addhistory_attendance_internship&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-plus mr-1"></i>Add Attendance</button>

											<button class="btn btn-info btn-sm btn-round" data-target="#HelperAttendance" data-toggle="modal"><i class="fas fa-question-circle mr-1"></i>Help</button>

											<!-- <button class="btn btn-info btn-sm btn-round" onclick="window.location.href = 'index.php?page=add_attendance_internship&id_internship=<?= base64_encode($id_internship) ?>'"><i class="fas fa-plus mr-1"></i>Add Data</button> -->
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


												<div class="form-group">

													<label>Choose Month</label>

													<select class="form-control" id="filter_data" onchange="change()">
														<option value="<?= $filter_value ?>" hidden><?= $filter_text ?></option>
														<option>All</option>




														<?php
														if ($count_year < 1) {
															// echo "<option value=''>None</option>";
														} else {
															foreach ($year_sort as $i => $data) {
																$year = $data['ATT_YEAR'];
																$month = $data['ATT_MONTH'];

																$new_date = new DateTime("$year-$month-01");
																$value = $new_date->format('Y-m');
																$text = $new_date->format('M Y');
																echo "<option value='$value'>$text</option>";
															}
														}



														?>
													</select>

												</div>


												<script type="text/javascript">
													function change() {
														var data = document.getElementById('filter_data').value;
														window.location.href = 'index.php?page=attendance_internship&id_internship=<?= base64_encode($id_internship) ?>&filter=' + data;
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


								<div class="modal fade" id="HelperAttendance" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLabel">How it works? MyInternship Attendance</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body form-row">
												<div class="form-group col-md-12">

													To Filter your Attendance Data, you need to follow these steps:
													<ol>
														<li>Click the "Filter Data" button.</li>
														<li>Choose the desired month.</li>
														<li>After that, click the "Apply" button.</li>
														<li>The data displayed in the table will be filtered based on the selected month.</li>
													</ol>

													To download your Attendance Data, you need to follow these steps:
													<ol>
														<li>Filter your attendance (Step Above).</li>
														<li>After that, click the 'Download' button.</li>
														<li>Your browser will initiate the download of Attendance Data based on the selected month.</li>
													</ol>

												</div>
												<div class="form-group col-md-12">
													<div class="custom-control custom-checkbox">
														<input type="checkbox" class="custom-control-input" id="dontRemindMe" onclick="handleCloseModal()">
														<label class="custom-control-label" for="dontRemindMe">I have read this, don't remind me</label>
													</div>
												</div>
											</div>
											<div class="modal-footer text-center">
												<button data-dismiss="modal" class="btn btn-primary w-100">Close</button>
											</div>
										</div>
									</div>
								</div>



								<div class="card-body">

									<div class="row mb-2">
										<div class="col-md-3">
											<label for="monthFilter">Filter by Month:</label>
											<select id="monthFilter" class="form-control">
												<option value="">All</option>
												<!-- Options will be added dynamically -->
											</select>
										</div>
									</div>


									<div class="table-responsive">


										<table id="basic-datatables" class="display table table-striped table-hover" style="width:100%">
											<thead>
												<tr>
													<th>No.</th>
													<th>Month</th>
													<!-- <th>Week Num</th> -->
													<th>Date</th>
													<th>Attendance</th>
													<th>Lecturer Approval</th>
													<th>Supervisor Approval</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>
												<?php

												foreach ($exec as $i => $data) :

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
														<td><?= $i + 1 ?>.</td>
														<td><?= date('M Y', strtotime($data['date'])) ?></td>
														<!-- <td>M<?= $data['week'] ?></td> -->
														<td><?= date('D, d M', strtotime($data['date'])) ?></td>
														<td><?= $data['attendance_type'] ?></td>
														<td class="text-center"><?= $approval_dpm ?></td>
														<td class="text-center"><?= $approval_spv ?></td>
														<td>
															<div class="btn-group btn-round">

																<?php if (in_array($data['attendance_type'], ['PRESENT', 'Present', 'present']) && in_array($data['check_out'], [NULL, '', '-'])) : ?>
																	<button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href = 'index.php?page=check_out_attendance_internship&id_internship=<?= base64_encode($data['id_internship']) ?>&id_attendance=<?= base64_encode($data['id_attendance']) ?>'"> <i class="fas fa-sign-out-alt mr-1"></i>
																		Check Out
																	</button>
																<?php endif; ?>

																<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Action
																</button>



																<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

																	<button class="dropdown-item" onclick="window.location.href = 'index.php?page=detail_attendance_internship&id_internship=<?= base64_encode($data['id_internship']) ?>&id_attendance=<?= base64_encode($data['id_attendance']) ?>'"><i class="fas fa-info-circle mr-2"></i>Detail Attendance</button>
																	<div class="dropdown-divider"></div>

																	<button class="dropdown-item" onclick="window.location.href = 'index.php?page=edit_attendance_internship&id_internship=<?= base64_encode($data['id_internship']) ?>&id_attendance=<?= base64_encode($data['id_attendance']) ?>'"><i class="fas fa-edit mr-1"></i>Edit Data</button>
																	<button class="dropdown-item text-danger" onclick="confirm_delete( '<?= encrypt($data['id_attendance']) ?>' , '<?= date('M Y', strtotime($data['date'])) ?> M<?= $data['week'] ?>' ) "><i class="fas fa-trash-alt mr-1"></i>Delete Data</button>


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
			<?php include './view/mahasiswa/template/footer.php'; ?>
		</div>

	</div>

	<?php include './view/mahasiswa/template/min-script.php'; ?>

	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>

	<link rel='stylesheet' href='./assets/sweetalert2.css'>
	<script src='./assets/sweetalert2.all.js'></script>

	<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>


	<script>
		$(document).ready(function() {

			var table = $('#basic-datatables').DataTable({
				responsive: true,
				columnDefs: [{
						width: '5%',
						targets: 0
					}, // Kolom No.
					{
						width: '15%',
						targets: 1
					}, // Kolom Month
					{
						width: '15%',
						targets: 2
					}, // Kolom Date
					{
						width: '20%',
						targets: 3
					}, // Kolom Attendance Type
					{
						width: '20%',
						targets: 4
					}, // Kolom Approval Lecturer
					{
						width: '20%',
						targets: 5
					}, // Kolom Approval Supervisor
					{
						width: '5%',
						targets: 6
					} // Kolom Action
				]
			});



			// Get data from the column "Month" (index 1)
			var months = [];
			table.column(1).data().each(function(value, index) {
				if (months.indexOf(value) === -1 && value) {
					months.push(value);
				}
			});

			// Populate the dropdown with unique month values
			var monthFilter = $('#monthFilter');
			months.forEach(function(month) {
				monthFilter.append($('<option>', {
					value: month,
					text: month
				}));
			});

			// Event listener for dropdown filter
			monthFilter.on('change', function() {
				var month = $(this).val();
				table.column(1).search(month).draw(); // Column 1 is "Month"
			});

			if (localStorage.getItem('hideHelperAttendance') !== 'true') {
				$('#HelperAttendance').modal('show');
			}
		});

		function handleCloseModal() {
			var dontRemindMe = document.getElementById('dontRemindMe').checked;
			if (dontRemindMe) {
				// Set a cookie or localStorage to remember the user's choice
				localStorage.setItem('hideHelperAttendance', 'true');
			}

			console.log(localStorage.getItem('hideHelperAttendance'))
		}


		let _token = $('meta[name="csrf-token"]').attr('content');

		function confirm_delete(id, week_num) {
			swal.fire({
				title: 'Delete Confirmation',
				text: 'Are you sure you want to delete ' + week_num + ' Attendance ?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						url: "index.php?request=attendance_delete",
						type: "POST",
						data: {
							'token': _token,
							'_method': 'DELETE',
							id_attendance: id
						},

						dataType: 'json',
						success: function(response) {
							window.location.reload(1);
						},
						error: function(response) {
							window.location.reload(1);
							// Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
						}
					});

				}
			})

		}
	</script>




</body>

</html>