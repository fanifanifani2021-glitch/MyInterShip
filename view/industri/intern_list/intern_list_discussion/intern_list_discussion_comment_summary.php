<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Comment Summary"; $route = 'intern_list'; 
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


if ($discussion_data['user_type'] == 'spv') {
	$user_type = 'Supervisor';

	// Get Profile Picture
	$sq = "SELECT * FROM user_company WHERE username = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

} else if ($discussion_data['user_type'] == 'hrd') {
	$user_type = 'HRD';

	// Get Profile Picture
	$sq = "SELECT * FROM user_company WHERE username = '" . $discussion_data['user_id'] . "'";
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

}
else if ($discussion_data['user_type'] == 'cdc') {
	$user_type = 'CDC';

	// Get Profile Picture
	$sq = "SELECT * FROM upkpk WHERE username = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

}

else if ($discussion_data['user_type'] == 'mhs') {
	$user_type = 'Student';

	// Get Profile Picture
	$sq = "SELECT * FROM student_internship WHERE nim = '" . $discussion_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

} else {
	$user_type = 'Another';
	$profile_picture = 'default.jpg';
}



$id_comment = base64_decode($_GET['id_comment']);
$sql = $_SERVER['KONEKSI_DB']->query("SELECT * FROM discussion_comment WHERE discussion_comment.id_comment = '$id_comment' AND `user_id` = '$id_user_company' ");

if ( $sql->num_rows != 1 ) {
    make_soloalert('error', 'Not Allowed', 'Comment is not found !');
    redirect_back(1); exit;
}

$comment_data = $sql->fetch_assoc();

if ( $comment_data['user_type'] == 'spv'  ) {
	$user_type = 'Supervisor';


	// Get Profile Picture
	$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $comment_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';


} else if ( $comment_data['user_type'] == 'hrd' ) {
	$user_type = 'HRD';
	// Get Profile Picture
	$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $comment_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';


} else if ( $comment_data['user_type'] == 'dpm' ) {
	$user_type = 'Supervisor';

	// Get Profile Picture
	$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $comment_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';


} else if ( $comment_data['user_type'] == 'dkm' ) {
	$user_type = 'Coordinator';

	// Get Profile Picture
	$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $comment_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';


} 

else if ( $comment_data['user_type'] == 'cdc' ) {
	$user_type = 'CDC';

	// Get Profile Picture
	$sq = "SELECT * FROM upkpk WHERE username = '" . $comment_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

}

else if ( $comment_data['user_type'] == 'mhs' ) {
	$user_type = 'Student';

	// Get Profile Picture
	$sq = "SELECT * FROM student_internship WHERE nim = '" . $comment_data['user_id'] . "'";
	$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';

}
else {
	$user_type = 'Another';
	$profile_picture = 'default.jpg';
}

if ( !in_array( $discussion_data['timestamp_update'] , ['','-', NULL] )  ) {
	$timestamp_discussion = 'Edited ' .time_elapsed_string( $discussion_data['timestamp_update'] );
} else {
	$timestamp_discussion = 'Added ' .time_elapsed_string( $discussion_data['timestamp_add'] );
}



$sql = "SELECT * FROM comment_report WHERE id_comment = '$id_comment'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

$count_report = $exec->num_rows;

?>

<body>
	<div class="wrapper">

        <?php include "./view/industri/template/main-header.php";?>
        <?php include "./view/industri/template/sidebar.php";?>
		
		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Comment Sumary</h4>
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
								<a href="index.php?page=intern_list_discussion_join&id_internship=<?= base64_encode($id_internship) ?>&id_discussion=<?= base64_encode($id_discussion) ?>">Join Discussion</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							

							<li class="nav-item">
								<a href="#">Comment Summary</a>
							</li>


							
						</ul>
					</div>

					<div class="row">


						<!-- Internship History -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Comment Summary
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
															<a ><?= $comment_data['user_name'] ?></a> 
															<small> (<?= $user_type ?>)</small>
															
														</h8>
														<p class="list-group-item-text text-truncate"><?= $timestamp_discussion ?></p>
													</div>
													
												</div>
											</div>

										</div>
									
									</div>


									<div class="form-group">

										<textarea class="form-control" rows="4" style="pointer-events:none;" onclick="return false;"><?= $comment_data['comment'] ?></textarea>
									</div>

									<div class="form-group">
										<a class="mr-1"><i class="fas fa-flag mr-1"></i> Report (<?= $count_report ?>)</a>
										<!-- <a class="mr-1"><i class="fas fa-eye mr-1"></i> <?= $count_comments ?></a> -->
									</div>

								</div>



							</div>

							<div class="card">
								<div class="card-header">
									<div class="card-title">Report on Comment
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
											$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';


										} else if ($res['user_type'] == 'dpm') {
											$user_type = 'Lecturer';


											// Get Profile Picture
											$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'] ?? 'profile.jpg';



										} else if ($res['user_type'] == 'dkm') {
											$user_type = 'Coordinator';

											// Get Profile Picture
											$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" .$res['user_id']. "'";
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
											$timestamp_report = 'Reported on ' .time_elapsed_string( $res['timestamp_add'] );
											$timestamp_report .= ' Edited ' .time_elapsed_string( $res['timestamp_update'] );
										}
										else {
											$timestamp_report = 'Reported on ' .time_elapsed_string( $res['timestamp_add'] );
										}

										if ( !in_array( $res['timestamp_update'] , ['','-', NULL] )  ) {
											$timestamp_report = 'Reported on ' .time_elapsed_string( $res['timestamp_add'] );
											$timestamp_report .= ' Edited ' .time_elapsed_string( $res['timestamp_update'] );
										}
										else {
											$timestamp_report = 'Reported on ' .time_elapsed_string( $res['timestamp_add'] );
										}


									?>

									<div class="list-group list-group-messages list-group-flush ">
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
															<a><?= $res['user_name'] ?></a> 
															<small> (<?= $user_type ?>)</small>
														</h8>
														<p class="list-group-item-text text-truncate"><?= $timestamp_report; ?></p>
													</div>
													
												</div>
											</div>


										</div>


									</div>


									<div class="form-group">
										<form >
										<textarea  class="form-control" rows="4" style="pointer-events:none; resize: none;" onclick="return false;"><?= $res['reason'] ?></textarea>
										</form>
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




    </script>





</body>
</html>