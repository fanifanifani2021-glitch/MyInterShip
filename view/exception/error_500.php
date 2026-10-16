<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();
http_response_code(500);
$title = "500 Internal Error";
include "./view/exception/template/exception-head.php";


?>

<body>
	<div id="container">
		<h1>500 Internal Error</h1>
		<p>Your request cannot be processed, because the server responded with an error.</p>
		<!-- <p> <small>Did you mistype the page you were looking for?</small> </p> -->
		<p> <small>Did you mistype the link you were looking for?</small> <small class="super-small"><?php echo $time ?></small></p>

	</div>

	<div id="container" class="text-center">

		<p>
			<a type="button" href="javascript: history.back()" style="text-decoration: none;">
				<span class="fas fa-arrow-circle-left"></span>
				Go Back
			</a>
		</p>

	</div>

	<div id="container" class="text-center">

		<p>
			<a type="button" href="index.php" style="text-decoration: none;">
				<span class="fas fa-home"></span>
				Home
			</a>
		</p>

	</div>

</body>
</html>