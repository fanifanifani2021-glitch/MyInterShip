<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Join Discussion"; $route = 'accepted_intern_management'; 
include "./view/industri/template/head.php";

?>

<?php
$id_internship = base64_decode($_GET['id_internship']);

// Cek Internship

$sql = "SELECT * FROM internship WHERE id_internship = :id_internship LIMIT 1";
$exec = $_SERVER['PDO']->prepare($sql);
$exec->execute([ ':id_internship' => $id_internship]);

if ($exec->rowCount() != 1) {
	make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
	redirect_back(1);
	exit();
}


$internship_data = $exec->fetch(PDO::FETCH_ASSOC);
$nim = $internship_data['nim'];

// Student Data
$sql2 = "SELECT * FROM student_internship WHERE nim = :nim LIMIT 1";
$exec2 = $_SERVER['PDO']->prepare($sql2);
$exec2->execute([ ':nim' => $nim]);

if ($exec2->rowCount() != 1) {
	make_swal2('error', 'Not Allowed', 'Student Data is not found !');
	redirect_back(1);
	exit();
}

$student_data = $exec2->fetch( PDO::FETCH_ASSOC );


// Cek Discussion
$id_discussion = base64_decode($_GET['id_discussion']);

$sql = "SELECT * FROM discussion WHERE id_discussion = :id_discussion LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_discussion' => $id_discussion]);

if ($stmt->rowCount() != 1) {
	make_swal2('error', 'Not Found', 'Discussion is not available');
	// make_soloalert('error', 'Not Allowed', 'Disscussion Topic is not found !');
	redirect_back(1);
	exit;
}

$discussion_data = $stmt->fetch(PDO::FETCH_ASSOC);

// if ( $discussion_data['status'] == 'Pending' ) {
// 	make_soloalert('error', 'Not Allowed', 'Disscussion is not open yet, Please wait until discussion is open !');
//     redirect_back(1); exit;
// }

if ( !in_array( $discussion_data['timestamp_update'] , ['','-', NULL] )  ) {
	$timestamp_discussion = 'Added ' .time_elapsed_string( $discussion_data['timestamp_add'] ). ', Edited ' .time_elapsed_string( $discussion_data['timestamp_update'] );
} else {
	$timestamp_discussion = 'Added ' .time_elapsed_string( $discussion_data['timestamp_add'] );
}



$timestamp_discussion = 'Added ' .time_elapsed_string( $discussion_data['timestamp_add'] );
if ( !in_array( $discussion_data['timestamp_update'] , ['','-', NULL] )  ) {
	$timestamp_discussion .= ', Edited ' .time_elapsed_string( $discussion_data['timestamp_update'] );
}


$user_type = 'Another';
$profile_picture = 'default.jpg';
if ($discussion_data['user_type'] == 'spv') {
	$user_type = 'Industry Supervisor';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $discussion_data['user_type'] ];

	$sql = "SELECT profile_picture FROM user_company WHERE id_user_company = :id_user_company LIMIT 1";
	$stmt = $_SERVER['PDO']->prepare($sql);
	$stmt->bindParam(':id_user_company', $discussion_data['user_id']);


} else if ($discussion_data['user_type'] == 'hrd') {
	$user_type = 'Industry HRD';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $discussion_data['user_type'] ];
	
	
	$sql = "SELECT profile_picture FROM user_company WHERE id_user_company = :id_user_company LIMIT 1";
	$stmt = $_SERVER['PDO']->prepare($sql);
	$stmt->bindParam(':id_user_company', $discussion_data['user_id']);

} else if ($discussion_data['user_type'] == 'dpm' || $discussion_data['user_type'] == 'dkm') {
	$user_type = ($discussion_data['user_type'] == 'dpm') ? 'Lecturer' : 'Internship Coordinator';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $discussion_data['user_type'] ];
	
	
	$sql = "SELECT profile_picture FROM lecturer WHERE nim_nik_unit = :nim_nik_unit LIMIT 1";
	$stmt = $_SERVER['PDO']->prepare($sql);
	$stmt->bindParam(':nim_nik_unit', $discussion_data['user_id']);

} else if ($discussion_data['user_type'] == 'cdc') {
	$user_type = 'CDC';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $discussion_data['user_type'] ];
	
	$sql = "SELECT profile_picture FROM upkpk WHERE username = :username LIMIT 1";
	$stmt = $_SERVER['PDO']->prepare($sql);
	$stmt->bindParam(':username', $discussion_data['user_id']);

} else if ($discussion_data['user_type'] == 'mhs') {
	$user_type = 'Student';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $discussion_data['user_type'] ];

	$sql = "SELECT profile_picture FROM student_internship WHERE nim = :nim LIMIT 1";
	$stmt = $_SERVER['PDO']->prepare($sql);
	$stmt->bindParam(':nim', $discussion_data['user_id']);

} else {
	$user_type = 'Another';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ 'anon' ];

	$profile_picture = 'default.jpg';
}

if (isset($stmt) && $stmt->execute()) {
	$profile_data = $stmt->fetch(PDO::FETCH_ASSOC);
	$profile_picture = $profile_data['profile_picture'] ?? 'profile.jpg';
}


if ( $discussion_data['discussion_status'] == 'Open' ) {
	$discussion_status = "<span class='badge badge-success btn-sm'>Open</span>";
} else if ( $discussion_data['discussion_status'] == 'Pending' ) {
	$discussion_status = "<span class='badge badge-warning btn-sm'>Pending</span>";
} else if ( $discussion_data['discussion_status'] == 'Closed' ) {
	$discussion_status = "<span class='badge badge-danger'>Closed</span>";
} else {
	$discussion_status = "<span class='badge badge-dark'>" .$discussion_data['discussion_status'] . "</span>";
}



$sql = "SELECT * FROM discussion_comment WHERE id_discussion = :id_discussion";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute([':id_discussion' => $id_discussion]);

$count_comments = $stmt->rowCount() ?? 0;


$exec = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<body>
	<div class="wrapper">

        <?php include "./view/industri/template/main-header.php";?>
        <?php include "./view/industri/template/sidebar.php";?>
		
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

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=accepted_intern_management">Accepted Intern</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=detail_intern_management&id_internship=<?= base64_encode($id_internship) ?>">Student Detail</a>
							</li>

							
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=discussion_intern_management&id_internship=<?= base64_encode( $id_internship ) ?>">Discussion</a>
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
															<a ><?= $discussion_data['user_name'] ?></a> 
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

									<form action="index.php?form=discussion&action=reply" method="POST"> <?= csrf() ?> <?= input_hidden('id_internship' , $id_internship)?> 
										<?= input_hidden('id_discussion' , $id_discussion ) ?> <?= input_hidden('user_id', $_SESSION['id_user_company']) ?>
										<?= input_hidden('user_name', $_SESSION['user_fullname']) ?> <?= input_hidden('user_type', 'hrd') ?>
									<div class="form-group">
										<div class="input-group">
											<input class="form-control" placeholder="Response" required name="comment">
											<div class="input-group-append">
											<button type="submit" class="btn btn-secondary" <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> ><i class="fas fa-paper-plane mr-2"></i>Reply</button>
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
									


									if ( $count_comments < 1 ) {
										echo "<h4 class=''>Wow such an empty !</h4>";
									}

									foreach ( $exec as $i => $res):

										$id_comment = $res['id_comment'];

										if ($res['user_type'] == 'spv') {
											$user_type = 'Industry Supervisor';
											$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $res['user_type'] ];

											$sql = "SELECT profile_picture FROM user_company WHERE id_user_company = :id_user_company";
											$stmt = $_SERVER['PDO']->prepare($sql);
											$stmt->bindParam(':id_user_company', $res['user_id']);

										} else if ($res['user_type'] == 'hrd') {
											$user_type = 'Industry HRD';
											$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $res['user_type'] ];

											$sql = "SELECT profile_picture FROM user_company WHERE id_user_company = :id_user_company";
											$stmt = $_SERVER['PDO']->prepare($sql);
											$stmt->bindParam(':id_user_company', $res['user_id']);

										} else if ($res['user_type'] == 'dpm' || $res['user_type'] == 'dkm') {
											$user_type = ($res['user_type'] == 'dpm') ? 'Lecturer' : 'Internship Coordinator';
											$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $res['user_type'] ];

											$sql = "SELECT profile_picture FROM lecturer WHERE nim_nik_unit = :nim_nik_unit";
											$stmt = $_SERVER['PDO']->prepare($sql);
											$stmt->bindParam(':nim_nik_unit', $res['user_id']);
										} else if ($res['user_type'] == 'cdc') {
											$user_type = 'CDC';
											$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $res['user_type'] ];
											
											$sql = "SELECT profile_picture FROM upkpk WHERE username = :username";
											$stmt = $_SERVER['PDO']->prepare($sql);
											$stmt->bindParam(':username', $res['user_id']);
										} else if ($res['user_type'] == 'mhs') {
											$user_type = 'Student';
											$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $res['user_type'] ];

											$sql = "SELECT profile_picture FROM student_internship WHERE nim = :nim";
											$stmt = $_SERVER['PDO']->prepare($sql);
											$stmt->bindParam(':nim', $res['user_id']);

										} else {
											$user_type = 'Another';
											$user_type = $_SERVER['DISCUSSION_ACTOR'] [ 'anon' ];

											$profile_picture = 'profile.jpg';
										}

										if (isset($stmt) && $stmt->execute()) {
											$profile_data = $stmt->fetch(PDO::FETCH_ASSOC);
											$profile_picture = $profile_data['profile_picture'] ?? 'profile.jpg';
										}

										$timestamp_comment = 'Added ' .time_elapsed_string( $res['timestamp_add'] );

										if ( !in_array( $res['timestamp_update'] , ['','-', NULL] )  ) {
											$timestamp_comment .= ', Edited ' .time_elapsed_string( $res['timestamp_update'] );
										}


										// Comment Report
										$sql_report = "SELECT * FROM comment_report WHERE id_comment = :id_comment";
										$stmt_report = $_SERVER['PDO']->prepare($sql_report);
										$stmt_report->bindParam(':id_comment', $id_comment);
										$stmt_report->execute();
										$num_rows_report = $stmt_report->rowCount();

										if ($num_rows_report > 0) {
											$report = "<i class='ml-1 fas fa-flag text-danger'></i><small class='text-danger'>" . $num_rows_report . "</small>";
										} else {
											$report = '';
										}


																				
										$sql_report = "SELECT * FROM comment_report WHERE id_comment = :id_comment AND `user_id` = :user_id";
										$stmt_report = $_SERVER['PDO']->prepare($sql_report);
										$stmt_report->bindParam(':id_comment', $id_comment);
										$stmt_report->bindParam(':user_id', $_SESSION['id_user_company']);
										$stmt_report->execute();
										$num_rows_report_user = $stmt_report->rowCount();

										$already_report = ($num_rows_report_user > 0) ? true : false;

									
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
															<small> (<?= $user_type?>)</small>
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

														<?php if ( $res['user_id'] != $_SESSION['id_user_company']) :?>

															<?php if ( $already_report === true ) : ?>
															<button <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> href="#" class="dropdown-item text-danger" onclick="confirm_cancel_report( '<?= $res['id_comment'] ?>' )" <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> ><i class="fas fa-times mr-1"></i>Cancel Report</button>
															<?php else: ?>
															<button <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> href="#" class="dropdown-item text-danger" onclick="confirm_report( '<?= $res['id_comment'] ?>' )" <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> ><i class="fas fa-flag mr-1"></i>Report</button>
															<?php endif; ?>

														<?php endif;?>

														<?php if ( $res['user_id'] == $_SESSION['id_user_company']) :?>

														<button <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> id="editBtn_<?= $i ?>" class="dropdown-item" onclick="toogle_edit( '<?= $i ?>' ) " <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> ><i class="fas fa-edit mr-1"></i>Edit</button>

														<div class="dropdown-divider"></div>
														<button <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> class="dropdown-item text-danger" onclick="confirm_delete( '<?= $res['id_comment'] ?>' )" <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> ><i class="fas fa-trash-alt mr-1"></i>Delete</button>

														<a class="dropdown-item" href="index.php?page=discussion_comment_summary_intern_management&id_internship=<?= base64_encode( $id_internship )?>&id_discussion=<?= base64_encode( $id_discussion)?>&id_comment=<?= base64_encode($res['id_comment']) ?>" ><i class="fas fa-list mr-1"></i>Summary</a>


														<?php endif; ?>
													</div>
												</div>
											</div>


										</div>


									</div>


									<div class="form-group">
										<form id="form_<?= $i?>">
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
			<?php include './view/industri/template/footer.php';?>




		</div>
		
	</div>
	

	<?php include './view/industri/template/script.php';?>

    <!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<!-- SWAL 2 -->
	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>


    <!-- AJAX Script -->
    <script>

        $(document).ready(function() { 

			// Reload page after 10 Minutes
			setTimeout( function () {
				window.location.reload(1);
			}, 600000);

        });

		let _token = $('meta[name="csrf-token"]').attr('content');


		function confirm_delete( id ) {
			swal.fire({
				title               : 'Delete Confirmation',
				text                :  'Are you sure you want to delete current comment ?',
				icon                : 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						url: "index.php?request=comment_delete",
						type: "POST",

						data: {
							'token': _token,
							'_method': 'DELETE',
							'id_comment': id,

						},

						dataType: 'json',
						success: function ( response ) {
							window.location.reload(1);
						},
						error: function ( response ) {
							window.location.reload(1);

							// Swal.fire("Deleting Data Failed !", "Please try again later !", "error");
						}
					});
					
				}
			})

		}

		function confirm_edit( id, loop ) {

			var comment = document.getElementById('commentTextarea_' +loop).value;

			swal.fire({
				title               : 'Edit Confirmation',
				text                :  'Are you sure you want to edit current comment ?',
				icon                : 'question',
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
							console.log(response);
							window.location.reload(1);

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

		function confirm_report( id ) {

			Swal.fire({
				title               : 'Report Confirmation',
				text                :  'Are you sure you want to report current comment ?',
				icon                : 'question',
				input: 'text',
				inputAttributes: {
					autocapitalize: 'off'
				},
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, report it!",
				showLoaderOnConfirm: true,
				
				preConfirm: (login) => {
					console.log( login );
					// AJAX
					$.ajax({
						url: "index.php?request=comment_report",

						type: "POST",
						data: { 
							
							'token': _token,

							id_comment: id,


							id_internship: '<?= $id_internship ?>',
							id_discussion: '<?= $id_discussion ?>',

							user_id: '<?= $_SESSION['id_user_company'] ?>' ,
							
							user_type: 'hrd', 
							user_name: '<?= $_SESSION['user_fullname'] ?>',
							report: login
						},
						
						dataType: 'json',
						success: function ( response ) {
							window.location.reload(1);
						},
						error: function ( response ) {
							window.location.reload(1);
						}
					});
				},
				allowOutsideClick: () => !Swal.isLoading()
				});


		}


		function confirm_cancel_report( id ) {

			swal.fire({
				title               : 'Cancel Report Confirmation',
				text                :  'Are you sure you want to cancel your report on current comment ?',
				icon                : 'question',
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

							user_id: '<?= $_SESSION['id_user_company'] ?>',
							
							user_type: 'hrd', 
							user_name: '<?= $_SESSION['user_fullname'] ?>',
						
						},
						
						
						dataType: 'json',
					success: function ( response ) {
						window.location.reload(1);
					},
					error: function ( response ) {
						window.location.reload(1);
					}
					});
					
				}
			})



		}




    </script>





</body>
</html>