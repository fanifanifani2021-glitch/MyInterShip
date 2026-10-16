<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">


<?php
error_reporting( false ); session_start();
$title = "403 Forbidden";
include "./view/exception/template/exception-head.php";

?>


<body>
	<div id="container">
		<h1>403 Forbidden</h1>
		<p>Your request cannot be processed, because the server responded with an error.</p>
		<p> <small>You don't have permission to access this page, maybe try to login ?</small> <small class="super-small"><?php echo $time ?></small></p>		
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