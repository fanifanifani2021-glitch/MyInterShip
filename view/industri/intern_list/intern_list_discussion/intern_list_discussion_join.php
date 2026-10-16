<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Intern Discussion"; $route = 'intern_list'; 
include "./view/industri/template/head.php";

?>

<?php
$id_internship = base64_decode($_GET['id_internship']);
$id_user_company = $_SESSION['id_user_company'];

$sql = $_SERVER['KONEKSI_DB']->query("SELECT internship.* FROM internship WHERE internship.id_internship = '$id_internship' AND internship.id_user_company = '$id_user_company'");

if ( $sql->num_rows != 1 ) {
    make_soloalert('error', 'Not Allowed', 'Student data is not found !');
    redirect_back(1); exit;
}


// Cek Discussion
$id_discussion = base64_decode($_GET['id_discussion']);
$sql = $_SERVER['KONEKSI_DB']->query("SELECT * FROM discussion WHERE discussion.id_discussion = '$id_discussion'");

if ( $sql->num_rows != 1 ) {
    make_soloalert('error', 'Not Allowed', 'Disscussion Topic is not found !');
    redirect_back(1); exit;
}

$discussion_data = $sql->fetch_assoc();

if ( $discussion_data['status'] == 'Pending' ) {
	make_soloalert('error', 'Not Allowed', 'Disscussion is not open yet, Please wait until discussion is open !');
    redirect_back(1); exit;
}

if ( !in_array( $discussion_data['timestamp_update'] , ['','-', NULL] )  ) {
	$timestamp_discussion = 'Added ' .time_elapsed_string( $discussion_data['timestamp_add'] ). ', Edited ' .time_elapsed_string( $discussion_data['timestamp_update'] );
} else {
	$timestamp_discussion = 'Added ' .time_elapsed_string( $discussion_data['timestamp_add'] );
}



if ($discussion_data['user_type'] == 'spv') {
	$user_type = 'Supervisor';

	// Get Profile Picture
	$sq = "SELECT * FROM user_company WHERE id_user_company = '".$discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';


} else if ($discussion_data['user_type'] == 'hrd') {
	$user_type = 'HRD';

	// Get Profile Picture
	$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';


} else if ($discussion_data['user_type'] == 'dpm') {
	$user_type = 'Supervisor';


	// Get Profile Picture
	$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

} else if ($discussion_data['user_type'] == 'dkm') {
	$user_type = 'Coordinator';

	// Get Profile Picture
	$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

} else if ($discussion_data['user_type'] == 'mhs') {
	$user_type = 'Student';

	// Get Profile Picture
	$sq = "SELECT * FROM student_internship WHERE nim = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

}

else if ($discussion_data['user_type'] == 'cdc') {
	$user_type = 'CDC';

	// Get Profile Picture
	$sq = "SELECT * FROM upkpk WHERE username = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

}

 else {
	$user_type = 'Another';
	$profile_picture = 'default.jpg';
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


$sql = "SELECT * FROM discussion_comment WHERE id_discussion = '$id_discussion'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

$count_comments = $exec->num_rows;

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
								<a href="index.php?page=intern_list_discussion&id_internship=<?= base64_encode($id_internship) ?>">Intern Discussion</a>
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
														<img src="./upload/profile_picture/<?= $profile_picture ?>" alt="..." class="avatar-img rounded-circle">
													</div>
												</a>
											</div>
											<div class="list-group-item-body pl-3 pl-md-4">
												<div class="row">
													<div class="col-12 col-lg-10">

														<h8 class="list-group-item-title">
															<a ><?= $discussion_data['user_name'] ?></a> 
															<small> (<?= $user_type?>) <?= $discussion_status ?></small> 
														</h8>
														<p class="list-group-item-text text-truncate"><?= $timestamp_discussion ?> </p>
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
										<?= input_hidden('user_name', $_SESSION['user_fullname']) ?> <?= input_hidden('user_type', 'spv') ?>
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
									


									if ( $exec->num_rows < 1 ) {
										echo "<h4 class=''>Wow such an empty !</h4>";
									}

									foreach ( $exec as $i => $res):

										$id_comment = $res['id_comment'];

										if ($res['user_type'] == 'spv') {
											$user_type = 'Supervisor';

											// Get Profile Picture
											$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

										} else if ($res['user_type'] == 'hrd') {
											$user_type = 'HRD';

											// Get Profile Picture
											$sq = "SELECT * FROM user_company WHERE id_user_company = '" .$res['user_id']. "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';


										} else if ($res['user_type'] == 'dpm') {
											$user_type = 'Supervisor';


											// Get Profile Picture
											$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';



										} else if ($res['user_type'] == 'dkm') {
											$user_type = 'Coordinator';

											// Get Profile Picture
											$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

										}
										
										else if ($res['user_type'] == 'cdc') {
											$user_type = 'CDC';

											// Get Profile Picture
											$sq = "SELECT * FROM upkpk WHERE username = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

										}
										 else if ($res['user_type'] == 'mhs') {
											$user_type = 'Student';

											// Get Profile Picture
											$sq = "SELECT * FROM student_internship WHERE nim = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

										} else {
											$user_type = 'Another';
											$profile_picture = 'default.jpg';
										}


										if ( !in_array( $res['timestamp_update'] , ['','-', NULL] )  ) {
											$timestamp_comment = 'Added ' .time_elapsed_string( $res['timestamp_add'] ). ', Edited ' .time_elapsed_string( $res['timestamp_update'] );

										}
										else {
											$timestamp_comment = 'Added ' .time_elapsed_string( $res['timestamp_add'] );
										}


										// Comment Report
										$sql_report = "SELECT * FROM comment_report WHERE id_comment = '$id_comment'";
										$exec_report = $_SERVER['KONEKSI_DB']->query( $sql_report );
										if ( $exec_report->num_rows > 0 ) {
											$report = "<i class='ml-1 fas fa-flag text-danger'></i><small class='text-danger'>" .$exec_report->num_rows. "</small>";
										} else {
											
											$report = '';
										}

										$sql_report = "SELECT * FROM comment_report WHERE id_comment = '$id_comment' AND `user_id` = '".$_SESSION['id_user_company']."'";
										$exec_report = $_SERVER['KONEKSI_DB']->query( $sql_report );
										if ( $exec_report->num_rows > 0 ) {
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
														<img src="./upload/profile_picture/<?= $profile_picture?>" alt="..." class="avatar-img rounded-circle">
													</div>
												</a>
											</div>
											<div class="list-group-item-body pl-3 pl-md-4">
												<div class="row">
													<div class="col-12 col-lg-10">
														<small class="text-sm list-group-item-title">
															<a ><?= $res['user_name'] ?></a> 
															<small> (<?= $user_type?>)</small>
														</small>
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
															<button href="#" class="dropdown-item" onclick="confirm_cancel_report( '<?= $res['id_comment'] ?>' )" <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> >Cancel Report</butt>
															<?php else: ?>
															<button href="#" class="dropdown-item" onclick="confirm_report( '<?= $res['id_comment'] ?>' )" <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> >Report</button>
															<?php endif; ?>

														<?php endif;?>

														<?php if ( $res['user_id'] == $_SESSION['id_user_company']) :?>

														<button id="editBtn_<?= $i ?>" class="dropdown-item" onclick="toogle_edit( '<?= $i ?>' ) " <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> >Edit</button>

														<div class="dropdown-divider"></div>
														<button class="dropdown-item" onclick="confirm_delete( '<?= $res['id_comment'] ?>' )" <?php if ( !in_array( $discussion_data['discussion_status'], ['OPEN', 'Open', 'open'] ) ) { echo "disabled"; } ?> >Delete</button>

														<button class="dropdown-item" onclick=" window.location.href='index.php?page=intern_list_discussion_comment_summary&id_internship=<?= base64_encode( $id_internship )?>&id_discussion=<?= base64_encode( $id_discussion)?>&id_comment=<?= base64_encode($res['id_comment']) ?>'" >Summary</button>


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

	<?php include "./view/industri/template/script.php"; ?>
	<!-- Datatables -->
	<script src="./assets/js/plugin/datatables/datatables.min.js"></script>
	
    <!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<!-- SWAL 2 -->
	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>


    <!-- AJAX Script -->
    <script>

        $(document).ready(function() { 

			$('#basic-datatables').DataTable({
			});

			<?php if ( old('ModalAdd') !== NULL ) : 
				echo "$('#ModalAdd').modal('show')";
			endif;?>

			// Reload page after 10 Minutes
			setTimeout( function () {
				window.location.reload(1);
			}, 600000);

        
        });

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
						url: "./ajax/delComment.php",
						type: "POST",
						data: { id_comment: id, },
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
						url: "./ajax/editComment.php",
						type: "POST",
						data: { id_comment: id, comment: comment },
						dataType: 'text',
					success: function ( response ) {
						window.location.reload(1);
					},
					error: function ( response ) {
						Swal.fire("Editing Data Failed !", "Please try again later !", "error");
					}
					});
					
				}
			})

		}

		function toogle_edit( loop ) {

			var btn = document.getElementById('btnAction_'+loop);
			var comment_area = document.getElementById('commentTextarea_'+loop);
			var edit_btn = document.getElementById('editBtn_'+loop);
			var form = document.getElementById('form_'+loop);
			

			comment_area.scrollIntoView(true);


			if ( btn.hidden == true ) {
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


		function confirm_report( id ) {

			Swal.fire({
				title               : 'Report Confirmation',
				text                :  'Are you sure you want to report current comment ? Please provide us with more information',
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
						url: "./ajax/reportComment.php",
						type: "POST",
						data: { id_comment: id, user_id: '<?= $_SESSION['id_user_company'] ?>' ,
								user_type: 'spv', user_name: '<?= $_SESSION['user_fullname'] ?>',
								report: login },
						dataType: 'text',
						success: function ( response ) {
							window.location.reload(1);
						},
						error: function ( response ) {
							Swal.fire("Reporting Comment Failed !", "Please try again later !", "error");
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
						url: "./ajax/reportCancelComment.php",
						type: "POST",
						data: { id_comment: id, user_id: '<?= $_SESSION['id_user_company'] ?>'},
						dataType: 'text',
					success: function ( response ) {
						window.location.reload(1);
					},
					error: function ( response ) {
						Swal.fire("Cancel Report Failed !", "Please try again later !", "error");
					}
					});
					
				}
			})



		}




    </script>





</body>
</html>