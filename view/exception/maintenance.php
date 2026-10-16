<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();
$title = "503 Server Under Maintenance";
include "template/exception-head.php";

http_response_code(503);


?>

<body>
	<div id="container">
		<h1>503 Server Under Maintenance</h1>
		<p>Our website will be temporarily unavailable until further notice.</p>
		<p> <small>Please be patient, We will be back shortly.</small> <small class="super-small"><?php echo $time ?></small></p>
	</div>


	<div id="container" class="card text-center">

		<div class="card-header">Login Admin Maintenance</div>

		<form action="" method="post">
			<div class="card-body form-row">
				<div class="form-group col-md-4">
					<!-- <label class="label-control">Username</label> -->
					<input type="text" placeholder="Enter Username" required name="username" class="form-control">
				</div>

				<div class="form-group col-md-4">
					<!-- <label class="label-control">Password</label> -->
					<input type="password" placeholder="Enter Password" required name="password" class="form-control">
				</div>

				<div class="form-group col-md-4">
					<button type="submit" name="login_admin" class="btn btn-primary btn-block">Login</button>
				</div>
			</div>
		</form>
	</div>

	<div id="container">
		<h1>Fun Facts</h1>
		<p id="fun_fact"><i class="fas fa-spinner fa-spin mr-1"></i> Loading Fun Fact . . .</p>
	</div>

	<?php
	if (isset($_POST['login_admin'])) {


		if (!array_key_exists($_POST['username'], $_SERVER['ADMIN_MAINTENANCE'])) {
			swal2('error', 'Not Authorize');
			exit;
			// redirect_back(1); exit();
		} else

		if (!password_verify($_POST['password'], $_SERVER['ADMIN_MAINTENANCE'][$_POST['username']])) {
			swal2('error', 'Not Authorize');
			exit;

			// make_swal2('error', 'Not Authorize'); redirect_back(1); exit();
		} else {
			session_start();
			make_snackbar('Welcome to ' . $_SERVER['APP_NAME'] . ' Admin Maintenance !');

			$_SESSION['ADMIN_MAINTENANCE'] = TRUE;
			header('Location: ' . base_url());
		}
	}

	?>


	<script>
		var limit = 1;
		$.ajax({
			method: 'GET',
			url: 'https://api.api-ninjas.com/v1/facts?limit=' + limit,
			headers: {
				'X-Api-Key': 'O4hkJAmK+Wyn/rXNbIYSbg==uX243bOdoIk8iAVZ'
			},
			contentType: 'application/json',
			success: function(result) {
				console.log(result[0]['fact']);
				$('#fun_fact').text(result[0]['fact']);
			},
			error: function ajaxError(jqXHR) {
				console.error('Error: ', jqXHR.responseText);
			}
		});
	</script>


</body>

</html>