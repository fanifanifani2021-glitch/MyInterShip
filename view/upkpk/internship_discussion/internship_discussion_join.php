<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

if ($coor == 1) {
	$route = 'intern_co_management';
} else {
	$route = 'intern_student_list';
}

$title = "Join Discussion";
include "./view/upkpk/template/head.php";

include './view/upkpk/template/cek_login.php';


?>

<?php
$id_internship = base64_decode($_GET['id_internship']);
$nim = base64_decode($_GET['nim']);
$nik = $_SESSION['nim_nik_unit'] ?? $_SESSION['username'];

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship' ";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('warning', 'Not Found', 'Internship is not found !');
	redirect_back(1);
	exit;
}


// Cek Discussion
$id_discussion = base64_decode($_GET['id_discussion']);
$sql = $_SERVER['KONEKSI_DB']->query("SELECT * FROM discussion WHERE discussion.id_discussion = '$id_discussion'");

if ($sql->num_rows != 1) {
	make_soloalert('error', 'Not Allowed', 'Disscussion Topic is not found !');
	redirect_back(1);
	exit;
}

$discussion_data = $sql->fetch_assoc();

if ($discussion_data['status'] == 'Pending') {
	make_soloalert('error', 'Not Allowed', 'Disscussion is not open yet, Please wait until discussion is open !');
	redirect_back(1);
	exit;
}

if (!in_array($discussion_data['timestamp_update'], ['', '-', NULL])) {
	$timestamp_discussion = 'Added ' . time_elapsed_string($discussion_data['timestamp_add']) . ', Edited ' . time_elapsed_string($discussion_data['timestamp_update']);
} else {
	$timestamp_discussion = 'Added ' . time_elapsed_string($discussion_data['timestamp_add']);
}


if ($discussion_data['user_type'] == 'spv') {
	$user_type = 'Supervisor';
	$user_type = $_SERVER['DISCUSSION_ACTOR'][$discussion_data['user_type']];

	// Get Profile Picture
	$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
} else if ($discussion_data['user_type'] == 'hrd') {
	$user_type = 'HRD';
	$user_type = $_SERVER['DISCUSSION_ACTOR'][$discussion_data['user_type']];

	// Get Profile Picture
	$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
} else if ($discussion_data['user_type'] == 'dpm') {
	$user_type = 'Lecturer';
	$user_type = $_SERVER['DISCUSSION_ACTOR'][$discussion_data['user_type']];

	// Get Profile Picture
	$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
} else if ($discussion_data['user_type'] == 'dkm') {
	$user_type = 'Coordinator';
	$user_type = $_SERVER['DISCUSSION_ACTOR'][$discussion_data['user_type']];

	// Get Profile Picture
	$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
} else if ($discussion_data['user_type'] == 'mhs') {
	$user_type = 'Student';
	$user_type = $_SERVER['DISCUSSION_ACTOR'][$discussion_data['user_type']];

	// Get Profile Picture
	$sq = "SELECT * FROM student_internship WHERE nim = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
} else if ($discussion_data['user_type'] == 'cdc') {
	$user_type = 'CDC';
	$user_type = $_SERVER['DISCUSSION_ACTOR'][$discussion_data['user_type']];

	// Get Profile Picture
	$sq = "SELECT * FROM upkpk WHERE username = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
} else {
	$user_type = 'Another';
	$user_type = $_SERVER['DISCUSSION_ACTOR']['anon'];

	$profile_picture = 'default.jpg';
}


if ($discussion_data['discussion_status'] == 'Open') {
	$discussion_status = "<span class='badge badge-success btn-sm'>Open</span>";
} else if ($discussion_data['discussion_status'] == 'Pending') {
	$discussion_status = "<span class='badge badge-warning btn-sm'>Pending</span>";
} else if ($discussion_data['discussion_status'] == 'Closed') {
	$discussion_status = "<span class='badge badge-danger'>Closed</span>";
} else {
	$discussion_status = "<span class='badge badge-dark'>" . $discussion_data['discussion_status'] . "</span>";
}


// $sql = "SELECT * FROM discussion_comment WHERE id_discussion = '$id_discussion'";
// $exec = $_SERVER['KONEKSI_DB']->query($sql);

// $count_comments = $exec->num_rows;

$sql = "SELECT * FROM discussion_comment WHERE id_discussion = :id_discussion";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_discussion' => $id_discussion]);

$count_comments = $stmt->rowCount() ?? 0;


$exec = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<body>
	<div class="wrapper">

		<?php include "./view/upkpk/template/main-header.php"; ?>
		<?php include "./view/upkpk/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Join Discussion</h4>
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="index.php">
									<i class="flaticon-home"></i>
								</a>
							</li>


							<?php if ($coor == 1) : ?>
								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=intern_co_management">Coordinator Management</a>
								</li>

								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=detail_intern_co&nik=<?= base64_encode($coordinator_data['nim_nik_unit']) ?>">Coordinator Detail</a>
								</li>

								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=student_detail_intern_co&nim=<?= base64_encode($student_data['nim']) ?>&coor=1">Student Detail</a>
								</li>

							<?php else : ?>


								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=student_management">Student Management</a>
								</li>

								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=student_detail_intern_co&nim=<?= base64_encode($nim) ?>">Student Detail</a>
								</li>

							<?php endif; ?>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="<?= base_url('index.php?page=internship_detail_intern_co&nim=' . base64_encode($nim) . '&id_internship=' . base64_encode($id_internship)) ?> <?= ($coor == 1) ? '&coor=1' : ''; ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="<?= base_url('index.php?page=discussion_intern_co&nim=' . base64_encode($nim) . '&id_internship=' . base64_encode($id_internship)) ?> <?= ($coor == 1) ? '&coor=1' : ''; ?>">Discussion</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Join Discussion</a>
							</li>



						</ul>
					</div>


					<div class="row">


						<!-- Internship History -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Discussion Topic
									</div>
								</div>


								<div class="card-body">


									<div class="list-group list-group list-group-flush ">
										<div class="list-group-item">
											<div class="list-group-item-figure">

												<a href="#" class="user-avatar">
													<div class="avatar">
														<img src="index.php?viewer=get_profile&thumbnail=1&location=<?= base64_encode($profile_picture) ?>" alt="..." class="avatar-img rounded-circle">
													</div>
												</a>
											</div>
											<div class="list-group-item-body pl-3 pl-md-4">
												<div class="row">
													<div class="col-12 col-lg-10">
														<h8 class="list-group-item-title">
															<a><?= $discussion_data['user_name'] ?></a>
															<small> (<?= $user_type ?>) <?= $discussion_status ?></small>
														</h8>
														<p class="list-group-item-text text-truncate"><?= $timestamp_discussion ?></p>
													</div>

												</div>
											</div>

										</div>

									</div>


									<div class="form-group">

										<textarea class="form-control" rows="4" style="pointer-events:none;" onclick="return false;"><?= $discussion_data['discussion_topic'] ?></textarea>
									</div>

									<div class="form-group">
										<a class="mr-1"><i class="fas fa-comments mr-1"></i> Comments (<?= $count_comments ?>)</a>
										<!-- <a class="mr-1"><i class="fas fa-eye mr-1"></i> <?= $count_comments ?></a> -->
									</div>

									<form action="index.php?form=discussion&action=reply" method="POST"> <?= csrf() ?> <?= input_hidden('id_internship', $id_internship) ?>
										<?= input_hidden('id_discussion', $id_discussion) ?> <?= input_hidden('user_id', $_SESSION['username']) ?>
										<?= input_hidden('user_name', $_SESSION['name']) ?> <?= input_hidden('user_type', 'cdc') ?>
										<div class="form-group">
											<div class="input-group">
												<input class="form-control" placeholder="Response" required name="comment">
												<div class="input-group-append">
													<button type="submit" class="btn btn-secondary" <?php if (!in_array($discussion_data['discussion_status'], ['OPEN', 'Open', 'open'])) {
																										echo "disabled";
																									} ?>><i class="fas fa-paper-plane mr-2"></i>Reply</button>
												</div>
											</div>
										</div>
									</form>

								</div>



							</div>

							<div class="card">
								<div class="card-header">
									<div class="card-title">Discussion Reply
									</div>
								</div>


								<div class="card-body">


									<?php



									// if ($exec->num_rows < 1) {
									if ($count_comments < 1) {

										echo "<h4 class=''>Wow such an empty !</h4>";
									}

									foreach ($exec as $i => $res) :

										$id_comment = $res['id_comment'];


										if ($res['user_type'] == 'spv') {
											$user_type = 'Supervisor';
											$user_type = $_SERVER['DISCUSSION_ACTOR'][$res['user_type']];


											// Get Profile Picture
											$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
										} else if ($res['user_type'] == 'hrd') {
											$user_type = 'HRD';
											$user_type = $_SERVER['DISCUSSION_ACTOR'][$res['user_type']];

											// Get Profile Picture
											$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
										} else if ($res['user_type'] == 'dpm') {
											$user_type = 'Lecturer';
											$user_type = $_SERVER['DISCUSSION_ACTOR'][$res['user_type']];

											// Get Profile Picture
											$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
										} else if ($res['user_type'] == 'dkm') {
											$user_type = 'Coordinator';
											$user_type = $_SERVER['DISCUSSION_ACTOR'][$res['user_type']];

											// Get Profile Picture
											$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
										} else if ($res['user_type'] == 'mhs') {
											$user_type = 'Student';
											$user_type = $_SERVER['DISCUSSION_ACTOR'][$res['user_type']];

											// Get Profile Picture
											$sq = "SELECT * FROM student_internship WHERE nim = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
										} else if ($res['user_type'] == 'cdc') {
											$user_type = 'CDC';
											$user_type = $_SERVER['DISCUSSION_ACTOR'][$res['user_type']];

											// Get Profile Picture
											$sq = "SELECT * FROM upkpk WHERE username = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';
										} else {
											$user_type = 'Another';
											$user_type = $_SERVER['DISCUSSION_ACTOR']['anon'];

											$profile_picture = 'default.jpg';
										}

										$timestamp_comment = 'Added ' . time_elapsed_string($res['timestamp_add']);

										if (!in_array($res['timestamp_update'], ['', '-', NULL])) {
											$timestamp_comment .= ', Edited ' . time_elapsed_string($res['timestamp_update']);
										}


										// Comment Report
										$sql_report = "SELECT * FROM comment_report WHERE id_comment = '$id_comment'";
										$exec_report = $_SERVER['KONEKSI_DB']->query($sql_report);
										if ($exec_report->num_rows > 0) {
											$report = "<i class='ml-1 fas fa-flag text-danger'></i><small class='text-danger'>" . $exec_report->num_rows . "</small>";
										} else {

											$report = '';
										}

										$sql_report = "SELECT * FROM comment_report WHERE id_comment = '$id_comment' AND `user_id` = '" . $nik . "'";
										$exec_report = $_SERVER['KONEKSI_DB']->query($sql_report);
										if ($exec_report->num_rows > 0) {
											$already_report = true;
										} else {
											$already_report = false;
										}

									?>

										<div class="list-group list-group-messages list-group-flush ">
											<div class="list-group-item">
												<div class="list-group-item-figure">

													<a href="#" class="user-avatar">
														<div class="avatar">
															<img src="index.php?viewer=get_profile&thumbnail=1&location=<?= base64_encode($profile_picture) ?>" alt="..." class="avatar-img rounded-circle">
														</div>
													</a>
												</div>
												<div class="list-group-item-body pl-3 pl-md-4">
													<div class="row">
														<div class="col-12 col-lg-10">
															<h8 class=" list-group-item-title">
																<a href="#"><?= $res['user_name'] ?></a>
																<small> (<?= $user_type ?>)</small>
															</h8>
															<p class="list-group-item-text text-truncate"><?= $timestamp_comment; ?> <?= $report ?></p>
														</div>

													</div>
												</div>

												<div class="list-group-item-figure">
													<div class="dropdown">
														<button class="btn-dropdown" data-toggle="dropdown">
															<i class="icon-options-vertical"></i>
														</button>
														<div class="dropdown-arrow"></div>
														<div class="dropdown-menu dropdown-menu-right">

															<?php if ($res['user_id'] != $_SESSION['username']) : ?>

																<?php if ($already_report === true) : ?>
																	<button <?php if (!in_array($discussion_data['discussion_status'], ['OPEN', 'Open', 'open'])) {
																				echo "disabled";
																			} ?> href="#" class="dropdown-item text-danger" onclick="confirm_cancel_report( '<?= $res['id_comment'] ?>' )"><i class="fas fa-times mr-1"></i>Cancel Report</button>
																<?php else : ?>
																	<button <?php if (!in_array($discussion_data['discussion_status'], ['OPEN', 'Open', 'open'])) {
																				echo "disabled";
																			} ?> href="#" class="dropdown-item text-danger" onclick="confirm_report( '<?= $res['id_comment'] ?>' )"><i class="fas fa-flag mr-1"></i>Report</button>
																<?php endif; ?>

															<?php endif; ?>

															<?php if ($res['user_id'] == $_SESSION['username']) : ?>

																<button <?php if (!in_array($discussion_data['discussion_status'], ['OPEN', 'Open', 'open'])) {
																			echo "disabled";
																		} ?> id="editBtn_<?= $i ?>" class="dropdown-item" onclick="toogle_edit( '<?= $i ?>' ) "><i class="fas fa-edit mr-1"></i>Edit</button>

																<div class="dropdown-divider"></div>
																<button <?php if (!in_array($discussion_data['discussion_status'], ['OPEN', 'Open', 'open'])) {
																			echo "disabled";
																		} ?> class="dropdown-item text-danger" onclick="confirm_delete( '<?= $res['id_comment'] ?>' )"><i class="fas fa-trash-alt mr-1"></i>Delete</button>

																<a class="dropdown-item" href="index.php?page=discussion_comment_summary_intern_co&nim=<?= base64_encode($nim) ?>&id_internship=<?= base64_encode($id_internship) ?>&id_discussion=<?= base64_encode($id_discussion) ?>&id_comment=<?= base64_encode($res['id_comment']) ?><?= ($coor == '1') ? '&coor=1' : ''; ?>"><i class="fas fa-list mr-1"></i>Summary</a>


															<?php endif; ?>
														</div>
													</div>
												</div>


											</div>


										</div>


										<div class="form-group">
											<form id="form_<?= $i ?>">
												<textarea id="commentTextarea_<?= $i ?>" class="form-control" rows="4" style="pointer-events:none; resize: none;" onclick="return false;"><?= $res['comment'] ?></textarea>
											</form>
										</div>

										<div class="form-group" id="btnAction_<?= $i ?>" hidden>
											<button onclick="toogle_edit( '<?= $i ?>' )" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
											<button onclick="confirm_edit('<?= $res['id_comment'] ?>', '<?= $i ?>')" class="btn btn-secondary"><i class="fas fa-edit mr-1"></i>Edit</button>
										</div>

									<?php endforeach; ?>

								</div>



							</div>


						</div>






					</div>


				</div>
			</div>
			<?php include './view/upkpk/template/footer.php'; ?>




		</div>

	</div>

	<?php include "./view/upkpk/template/script.php"; ?>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<!-- SWAL 2 -->
	<link rel='stylesheet' href='./assets/sweetalert2.css'>
	<script src='./assets/sweetalert2.all.js'></script>


	<!-- AJAX Script -->
	<script>
		$(document).ready(function() {

			$('#basic-datatables').DataTable({});

			<?php if (old('ModalAdd') !== NULL) :
				echo "$('#ModalAdd').modal('show')";
			endif; ?>

			// Reload page after 10 Minutes
			setTimeout(function() {
				window.location.reload(1);
			}, 600000);


		});

		let _token = $('meta[name="csrf-token"]').attr('content');

		function confirm_delete(id) {
			swal.fire({
				title: 'Delete Confirmation',
				text: 'Are you sure you want to delete current comment ?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						// url: "./ajax/delComment.php",
						url: "index.php?request=comment_delete",

						type: "POST",
						data: {
							// id_comment: id,

							'token': _token,
							'_method': 'DELETE',
							'id_comment': id,
						},
						dataType: 'text',
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

		function confirm_edit(id, loop) {

			var comment = document.getElementById('commentTextarea_' + loop).value;

			swal.fire({
				title: 'Edit Confirmation',
				text: 'Are you sure you want to edit current comment ?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, edit it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {

					// AJAX
					$.ajax({
						url: "index.php?request=comment_edit",

						type: "POST",
						data: {
							'token': _token,
							'_method': 'PUT',

							id_comment: id,
							comment: comment
						},
						dataType: 'json',
						success: function(response) {
							window.location.reload(1);
						},
						error: function(response) {
							window.location.reload(1);

							// Swal.fire("Editing Data Failed !", "Please try again later !", "error");
						}
					});

				}
			})

		}

		function toogle_edit(loop) {

			var btn = document.getElementById('btnAction_' + loop);
			var comment_area = document.getElementById('commentTextarea_' + loop);
			var edit_btn = document.getElementById('editBtn_' + loop);
			var form = document.getElementById('form_' + loop);


			comment_area.scrollIntoView(true);


			if (btn.hidden == true) {
				edit_btn.classList.add('text-danger');
				edit_btn.innerHTML = '<i class="fas fa-times mr-1"></i> Cancel';

				btn.hidden = false;
				comment_area.style.pointerEvents = '';

			} else {
				edit_btn.classList.remove('text-danger');

				edit_btn.innerHTML = '<i class="fas fa-edit mr-1"></i> Edit';

				form.reset();
				btn.hidden = true;
				comment_area.style.pointerEvents = 'none';

			}

		}

		function toogle_edit2(loop) {

			var btn = document.getElementById('btnAction_' + loop);
			var comment_area = document.getElementById('commentTextarea_' + loop);
			var edit_btn = document.getElementById('editBtn_' + loop);
			var form = document.getElementById('form_' + loop);


			comment_area.scrollIntoView(true);


			if (btn.hidden == true) {
				edit_btn.innerText = 'Cancel';

				btn.hidden = false;
				comment_area.style.pointerEvents = '';

			} else {
				edit_btn.innerText = 'Edit';

				form.reset();
				btn.hidden = true;
				comment_area.style.pointerEvents = 'none';

			}

		}


		function confirm_report(id) {

			Swal.fire({
				title: 'Report Confirmation',
				text: 'Are you sure you want to report current comment ?',
				icon: 'question',
				input: 'text',
				inputAttributes: {
					autocapitalize: 'off'
				},
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, report it!",
				showLoaderOnConfirm: true,

				preConfirm: (login) => {
					console.log(login);
					// AJAX
					$.ajax({

						url: "index.php?request=comment_report",

						type: "POST",
						data: {

							'token': _token,

							id_comment: id,

							id_internship: '<?= $id_internship ?>',
							id_discussion: '<?= $id_discussion ?>',


							user_id: '<?= $_SESSION['nim_nik_unit'] ?? $_SESSION['username'] ?>',
							user_type: 'cdc',
							user_name: '<?= $_SESSION['name'] ?>',
							report: login
						},
						dataType: 'json',
						success: function(response) {
							window.location.reload(1);
						},
						error: function(response) {
							Swal.fire("Reporting Comment Failed !", "Please try again later !", "error");
						}
					});
				},
				allowOutsideClick: () => !Swal.isLoading()
			});


		}


		function confirm_cancel_report(id) {

			swal.fire({
				title: 'Cancel Report Confirmation',
				text: 'Are you sure you want to cancel your report on current comment ?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, cancel my report !",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						url: "index.php?request=comment_cancel_report",

						type: "POST",
						data: {

							'token': _token,
							'_method': 'DEL',

							id_comment: id,

							id_internship: '<?= $id_internship ?>',
							id_discussion: '<?= $id_discussion ?>',
							
							user_id: '<?= $_SESSION['nim_nik_unit'] ?? $_SESSION['username'] ?>',
							user_type: 'cdc',
							user_name: '<?= $_SESSION['name'] ?>',


						},
						dataType: 'text',
						success: function(response) {
							window.location.reload(1);
						},
						error: function(response) {
							Swal.fire("Cancel Report Failed !", "Please try again later !", "error");
						}
					});

				}
			})



		}
	</script>





</body>

</html>