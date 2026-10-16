<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();
$title = "503 Service Unavailable";
include "./view/exception/template/exception-head.php";

?>

<body>
	<div id="container">
		<h1>503 Service Unavailable</h1>
		<p>Our website is temporarily unavailable.</p>
		<p> <small>Please be patient, We will be back shortly.</small> <small class="super-small"><?php echo $time ?></small></p>
	</div>






</body>
</html>