<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();
$title = "419 Page Expired";
include "./view/exception/template/exception-head.php";

?>

<body>

	<div id="container">
		<h1>419 Page Expired</h1>
		<p>Your request cannot be processed, because your session has expired.</p>
		<p> <small>Try to relogin to extend your session.</small> <small class="super-small"><?php echo $time ?></small> </p>		

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