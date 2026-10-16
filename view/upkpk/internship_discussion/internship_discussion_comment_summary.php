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
$nik = $_SESSION['nim_nik_unit'];

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = :id_internship AND nim = :nim LIMIT 1";
$exec = $_SERVER['PDO']->prepare($sql);
$exec->execute([':nim' => $nim, ':id_internship' => $id_internship]);

if ($exec->rowCount() != 1) {
	make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
	redirect_back(1);
	exit();
}



// Cek Discussion
$id_discussion = base64_decode($_GET['id_discussion']);
$sql = "SELECT * FROM discussion WHERE id_discussion = :id_discussion AND id_internship = :id_internship LIMIT 1";
$stmt = $_SERVER['PDO']->prepare( $sql );
$stmt->execute( [':id_discussion' => $id_discussion, ':id_internship' => $id_internship] );

if ($exec->rowCount() != 1) {
    make_soloalert('error', 'Not Allowed', 'Disscussion Topic is not found !');
    redirect_back(1); exit;
}

// $discussion_data = $sql->fetch_assoc();
$discussion_data = $stmt->fetch( PDO::FETCH_ASSOC );

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

$username = $_SESSION['username'];
$id_comment = base64_decode($_GET['id_comment']);


$sql = "SELECT * FROM discussion_comment WHERE id_comment = :id_comment AND `user_id` = :user_id ";
$stmt2 = $_SERVER['PDO']->prepare( $sql );

$stmt2->execute( [':id_comment' => $id_comment, 'user_id' => $username ] );

if ( $stmt2->rowCount() != 1 ) {
    make_soloalert('error', 'Not Allowed', 'Comment not found');
    redirect_back(1); exit;
}

$comment_data = $stmt2->fetch( PDO::FETCH_ASSOC );


if ($comment_data['user_type'] == 'spv') {
	$user_type = 'Industry Supervisor';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $comment_data['user_type'] ];

	$sql = "SELECT profile_picture FROM user_company WHERE id_user_company = :id_user_company LIMIT 1";
	$stmt = $_SERVER['PDO']->prepare($sql);
	$stmt->bindParam(':id_user_company', $comment_data['user_id']);
} else if ($comment_data['user_type'] == 'hrd') {
	$user_type = 'Industry HRD';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $comment_data['user_type'] ];

	$sql = "SELECT profile_picture FROM user_company WHERE id_user_company = :id_user_company LIMIT 1";
	$stmt = $_SERVER['PDO']->prepare($sql);
	$stmt->bindParam(':id_user_company', $comment_data['user_id']);

} else if ($comment_data['user_type'] == 'dpm' || $comment_data['user_type'] == 'dkm') {
	$user_type = ($comment_data['user_type'] == 'dpm') ? 'Lecturer' : 'Internship Coordinator';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $comment_data['user_type'] ];

	$sql = "SELECT profile_picture FROM lecturer WHERE nim_nik_unit = :nim_nik_unit LIMIT 1";
	$stmt = $_SERVER['PDO']->prepare($sql);
	$stmt->bindParam(':nim_nik_unit', $comment_data['user_id']);
} else if ($comment_data['user_type'] == 'cdc') {
	$user_type = 'CDC';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $comment_data['user_type'] ];

	$sql = "SELECT profile_picture FROM upkpk WHERE username = :username LIMIT 1";
	$stmt = $_SERVER['PDO']->prepare($sql);
	$stmt->bindParam(':username', $comment_data['user_id']);
} else if ($comment_data['user_type'] == 'mhs') {
	$user_type = 'Student';
	$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $comment_data['user_type'] ];

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



$timestamp_discussion = 'Added ' . time_elapsed_string($comment_data['timestamp_add']);

if (!in_array($comment_data['timestamp_update'], ['', '-', NULL])) {
	$timestamp_discussion .= ', Edited ' . time_elapsed_string($comment_data['timestamp_update']);
}


// Prepare the query using a prepared statement to prevent SQL injection
$sql = "SELECT * FROM comment_report WHERE id_comment = :id_comment";
$exec = $_SERVER['PDO']->prepare($sql);
$exec->bindParam(':id_comment', $id_comment, PDO::PARAM_INT);

// Execute the query
$exec->execute();

// Get the count of rows returned
$count_report = $exec->rowCount();

?>


<body>
	<div class="wrapper">

		<?php include "./view/upkpk/template/main-header.php"; ?>
		<?php include "./view/upkpk/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Comment Summary</h4>
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
								<a href="<?= base_url('index.php?page=discussion_join_intern_co&nim=' . base64_encode($nim) . '&id_discussion=' . base64_encode($id_discussion)). '&id_internship=' . base64_encode($id_internship) ?> <?= ($coor == 1) ? '&coor=1' : ''; ?>">Join Discussion</a>
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
														<img src="index.php?viewer=get_profile&thumbnail=1&location=<?= base64_encode(  $profile_picture ) ?>" alt="..." class="avatar-img rounded-circle">
													</div>
												</a>
											</div>
											<div class="list-group-item-body pl-3 pl-md-4">
												<div class="row">
													<div class="col-12 col-lg-10">
														<h8 class="list-group-item-title">
															<a><?= $comment_data['user_name'] ?></a>
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
										<a class="mr-1 text-danger"><i class="fas fa-flag mr-1"></i> Report (<?= $count_report ?>)</a>
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



									if ($count_report < 1) {
										echo "<h4 class=''>Wow such an empty !</h4>";
									}

									foreach ($exec as $i => $res) :

										$id_comment = $res['id_comment'];

										if ($res['user_type'] == 'spv') {
											$user_type = 'Industries Supervisor';


											// Get Profile Picture
											$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'];
										} else if ($res['user_type'] == 'hrd') {
											$user_type = 'Industries HRD';

											// Get Profile Picture
											$sq = "SELECT * FROM user_company WHERE id_user_company = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'];
										} else if ($res['user_type'] == 'dpm') {
											$user_type = 'Lecturer Supervisor';


											// Get Profile Picture
											$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'];
										} else if ($res['user_type'] == 'dkm') {
											$user_type = 'Intern Coordinator';

											// Get Profile Picture
											$sq = "SELECT * FROM lecturer WHERE nim_nik_unit = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'];
										} else if ($res['user_type'] == 'mhs') {
											$user_type = 'Intern Student';

											// Get Profile Picture
											$sq = "SELECT * FROM student_internship WHERE nim = '" . $res['user_id'] . "'";
											$profile_picture = $_SERVER['KONEKSI_DB']->query($sq)->fetch_assoc()['profile_picture'];
										} else {
											$user_type = 'Another';
											$profile_picture = 'default.jpg';
										}


										if (!in_array($res['timestamp_update'], ['', '-', NULL])) {
											$timestamp_report = 'Reported on ' . time_elapsed_string($res['timestamp_add']);
											$timestamp_report .= ' Edited ' . time_elapsed_string($res['timestamp_update']);
										} else {
											$timestamp_report = 'Reported on ' . time_elapsed_string($res['timestamp_add']);
										}

										if (!in_array($res['timestamp_update'], ['', '-', NULL])) {
											$timestamp_report = 'Reported on ' . time_elapsed_string($res['timestamp_add']);
											$timestamp_report .= ' Edited ' . time_elapsed_string($res['timestamp_update']);
										} else {
											$timestamp_report = 'Reported on ' . time_elapsed_string($res['timestamp_add']);
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
											<form>
												<textarea class="form-control" rows="4" style="pointer-events:none; resize: none;" onclick="return false;"><?= $res['reason'] ?></textarea>
											</form>
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
	</script>





</body>

</html>