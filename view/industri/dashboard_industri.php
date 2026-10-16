<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();


$title = 'Dashboard Industry';
$route = 'dashboard';
include "./view/industri/template/head.php";



?>


<?php


$count_university = $_SERVER['KONEKSI_DB']->query("SELECT COUNT(id_kampus) AS Count_Kampus FROM tb_kampus")->fetch_assoc()['Count_Kampus'];
$count_company = $_SERVER['KONEKSI_DB']->query("SELECT COUNT(id_company) AS Count_company FROM company")->fetch_assoc()['Count_company'];
$count_lecturer = $_SERVER['KONEKSI_DB']->query("SELECT * FROM lecturer")->num_rows;


$count_student = $_SERVER['KONEKSI_DB']->query("SELECT DISTINCT(nim) FROM internship WHERE `status` IN ('Yes', 'yes') AND id_company = '" . $_SESSION['id_company'] . "' ")->num_rows;
$count_spv = $_SERVER['KONEKSI_DB']->query("SELECT * FROM user_company WHERE user_type NOT IN ('HRD') AND id_company = '" . $_SESSION['id_company'] . "' ")->num_rows;
$count_offer = $_SERVER['KONEKSI_DB']->query("SELECT * FROM offer WHERE id_company = '" . $_SESSION['id_company'] . "' ")->num_rows;

$count_hrd = $_SERVER['KONEKSI_DB']->query("SELECT * FROM user_company WHERE user_type = 'HRD' AND id_company = '" . $_SESSION['id_company'] . "' ")->num_rows;

$count_all_student = $_SERVER['KONEKSI_DB']->query("SELECT * FROM student_internship")->num_rows;
$count_prodi = $_SERVER['KONEKSI_DB']->query('SELECT * FROM program_study')->num_rows;

?>


<body>
	<div class="wrapper">

		<?php include "./view/industri/template/main-header.php"; ?>
		<?php include "./view/industri/template/sidebar.php"; ?>


		<div class="main-panel">
			<div class="container">

				<div class="panel-header bg-primary-gradient">
					<div class="page-inner py-5">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h1 class="text-white pb-2 fw-bold">Welcome To My Internship</h1>
								<h4 class="text-white op-7 mb-2">Managing Industrial Learning through Structured Internship</h4>
							</div>
						</div>
					</div>
				</div>

				<div class="page-inner mt--5">
					<div class="row mt--2">

						<div class="col-md-6">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">My Internship Statistics</div>
									<div class="card-category">Daily information about statistics in system</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-0"></div>
											<h6 class="fw-bold mt-3 mb-0">University</h6>
										</div>
										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-1"></div>
											<h6 class="fw-bold mt-3 mb-0">Company</h6>
										</div>

										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-5"></div>
											<h6 class="fw-bold mt-3 mb-0">Lecturer</h6>
										</div>

									</div>

								</div>
							</div>
						</div>

						<!-- Politeknik Statistik -->
						<div class="col-md-6">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">My Company statistics</div>
									<div class="card-category">Daily information about your company statistics in system</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">

										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-3"></div>
											<h6 class="fw-bold mt-3 mb-0">Student</h6>
										</div>


										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-2"></div>
											<h6 class="fw-bold mt-3 mb-0">Supervisor</h6>
										</div>
										
										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-4"></div>
											<h6 class="fw-bold mt-3 mb-0">Offer</h6>
										</div>
									</div>

								</div>
							</div>
						</div>


						<div class="col-md-12">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">Manual Guide Application</div>
									<div class="card-category">Download Manual Guide</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										<div class="px-2 pb-2 pb-md-0 text-center">
											<h4 class="fw-bold mt-3 mb-0 text-primary">
												<a style="text-decoration: none;" title="Download Manual Guide for UPKPK" aria-label="Download Manual Guide for UPKPK" href="viewer.php?src=manual_guide" download="" target="_blank"> <i class="fas fa-download"></i> Download Manual Guide </a>
											</h4>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- <div class="col-md-6">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">Tutorial Video Application</div>
									<div class="card-category">Watch Tutorial Video</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										<div class="px-2 pb-2 pb-md-0 text-center">
											<h4 class="fw-bold mt-3 mb-0 text-primary">
												<a style="text-decoration: none;" title="Watch Video Tutorial in Youtube" aria-label="Watch Video Tutorial in Youtube" href="https://youtube.com/" download="" target="_blank"> <i class="fab fa-youtube"></i> Watch Video Tutorial</a>
											</h4>
										</div>
									</div>
								</div>
							</div>
						</div> -->


					</div>
				</div>
			</div>

			<?php include "./view/industri/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/industri/template/script.php"; ?>


	<!-- Chart Circle -->
	<script src="./assets/js/plugin/chart-circle/circles.min.js"></script>


	<script>
		Circles.create({
			id: 'circles-0',
			radius: 45,
			value: '<?= $count_university ?>',
			maxValue: '<?= $count_university ?>',
			width: 7,
			text: '<?= $count_university ?>',
			colors: ['#f1f1f1', '#FE5D26'],
			duration: 400,
			wrpClass: 'circles-wrp',
			textClass: 'circles-text',
			styleWrapper: true,
			styleText: true
		})

		Circles.create({
			id: 'circles-1',
			radius: 45,
			value: '<?= $count_company ?>',
			maxValue: '<?= $count_company ?>',
			width: 7,
			text: '<?= $count_company ?>',
			colors: ['#f1f1f1', '#F2C078'],
			duration: 400,
			wrpClass: 'circles-wrp',
			textClass: 'circles-text',
			styleWrapper: true,
			styleText: true
		})

		Circles.create({
			id: 'circles-2',
			radius: 45,
			value: '<?= $count_spv ?? 0 ?>',
			maxValue: '<?= $count_spv ?? 0 ?>',
			width: 7,
			text: '<?= $count_spv ?? 0 ?>',
			colors: ['#f1f1f1', '#FAEDCA'],
			duration: 400,
			wrpClass: 'circles-wrp',
			textClass: 'circles-text',
			styleWrapper: true,
			styleText: true
		})

		Circles.create({
			id: 'circles-3',
			radius: 45,
			value: '<?= $count_student ?? 0 ?>',
			maxValue: '<?= $count_student ?? 0 ?>',
			width: 7,
			text: '<?= $count_student ?? 0 ?>',
			colors: ['#f1f1f1', '#c1dbb3'],
			duration: 400,
			wrpClass: 'circles-wrp',
			textClass: 'circles-text',
			styleWrapper: true,
			styleText: true
		})


		Circles.create({
			id: 'circles-4',
			radius: 45,
			value: '<?= $count_offer ?>',
			maxValue: '<?= $count_offer ?>',
			width: 7,
			text: '<?= $count_offer ?>',
			colors: ['#f1f1f1', '#6861CE'],
			duration: 400,
			wrpClass: 'circles-wrp',
			textClass: 'circles-text',
			styleWrapper: true,
			styleText: true
		})

		Circles.create({
			id: 'circles-5',
			radius: 45,
			value: '<?= $count_lecturer ?>',
			maxValue: '<?= $count_lecturer ?>',
			width: 7,
			text: '<?= $count_lecturer ?>',
			colors: ['#f1f1f1', '#7ebc89'],
			duration: 400,
			wrpClass: 'circles-wrp',
			textClass: 'circles-text',
			styleWrapper: true,
			styleText: true
		})
	</script>



</body>

</html>