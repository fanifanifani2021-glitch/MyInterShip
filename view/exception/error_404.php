<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();
http_response_code(404);

$title = "404 Page Not Found";
include "./view/exception/template/exception-head.php";
?>


<body>
	<div id="container">
		<h1>404 Page Not Found</h1>
		<p>Your requsted page is not found in this server.</p>
		<p> <small>Did you mistype the page you were looking for?</small> <small class="super-small"><?php echo $time ?></small> </p>			
		
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