<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();
$title = "URI Redirect - Not Found";
include "template/exception-head.php";

http_response_code(200);


?>

<body>
	<div id="container">
		<h1>MyInternship URI Redirect - 404 URL Not Found</h1>

		<!-- <p><i class="fas fa-spin fa-spinner"></i> Please wait while you are being redirected to <?= $url ?></p>  -->

		<p>Based on our result, your requested URL ( <?= $url ?> ) is not found.</p>

		<p>If you are not still wish to access it, please click <a href="<?= $url ?>">here</a></p>

		<p><small>Please make sure your link is safe before accessing it. Consider using security tools or verifying the link's authenticity.</small></p>


	</div>

	</div>

	<div id="container">
		<h1>Fun Facts</h1>
		<p id="fun_fact"><i class="fas fa-spinner fa-spin mr-1"></i> Loading Fun Fact . . .</p>
	</div>


	<script>
		var limit = 1;
		$.ajax({
			method: 'GET',
			url: 'https://api.api-ninjas.com/v1/facts?limit=' + limit,
			headers: {
				'X-Api-Key': '<?= env('API_NINJAS_KEY') ?>'
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