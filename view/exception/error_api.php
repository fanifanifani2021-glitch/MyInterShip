<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();
$title = "500 Internal Error";
include "./view/exception/template/exception-head.php";

?>



<body>
	<div id="container">
		<h1>500 Internal Error</h1>
		<p>Your request cannot be processed, because the server responded with an error.</p>
		<p> <small>API is not available.</small> <small class="super-small"><?php echo $time ?></small></p>
	</div>


</body>
</html>