<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();
// show_notification();

$title = 'Dashboard Mahasiswa'; $route = 'dashboard';
include "./view/mahasiswa/template/head.php";

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
								<h1 class="text-white pb-2 fw-bold">Welcome To My Internship</h1>
								<h4 class="text-white op-7 mb-2">Managing Industrial Learning through Structured Internship</h4>
							</div>
						</div>
					</div>
				</div>

				<div class="page-inner mt-4">
					<div class="page-header text-center justify-content-center">
						<h4 class="page-title">How to Use this App</h4>
					</div>
				</div>

				<div class="page-inner mt-1">
					<div class="row mt--2">

						<div class="col-md-6">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">Manual Guide Application</div>
									<div class="card-category">Download Manual Guide</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-1"></div>
											<h4 class="fw-bold mt-3 mb-0 text-primary">
											<!-- <a style="text-decoration: none;" href="" download="" target="_blank"> <i class="fas fa-download"></i> Click Here </a> -->
											<a style="text-decoration: none;" href="#" data-toggle="modal" data-target="#Modalkalender"> <i class="fas fa-download"></i> Click Here </a>
											
											
										</h4>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="card full-height">
								<div class="card-body">
									<div class="card-title">Video Tutorial To Use Application</div>
									<div class="card-category">Download Video Tutorial</div>
									<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
										<div class="px-2 pb-2 pb-md-0 text-center">
											<div id="circles-1"></div>
											<h4 class="fw-bold mt-3 mb-0 text-primary">
											<a style="text-decoration: none;" href="" download="" target="_blank"> <i class="fas fa-download"></i> Click Here </a>
											</h4>
										</div>
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
	


</body>
</html>