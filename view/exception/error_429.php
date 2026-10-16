<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();
http_response_code(403);

$title = "429 Too Many Requests";
include "./view/exception/template/exception-head.php";
?>


<body>
	<div id="container">
		<h1>429 Too Many Requests</h1>
		<p>Your requst cannot be processed.</p>
		<p> <small class="super-small"><?php echo $time ?></small> </p>			
		
	</div>


</body>
</html>