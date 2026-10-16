<?php error_reporting(false);
session_start(); ?>

<head>
    <base href="<?=  base_url() ?>">

    <!-- Meta for Compatibility -->
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html;">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<meta name="theme-color" content="#FFAD46"/>

	<!-- Meta for Security -->
	<!-- <meta http-equiv="Content-Security-Policy" content="default-src https: 'unsafe-eval' 'unsafe-inline'; object-src 'none'"> -->
	<meta name="csrf-token" content="<?= csrf_token() ?>" />


	<!-- Meta for SEO Friendly -->
	<meta name="description" content="<?= $_SERVER['APP_DESCRIPTION'] ?>">
	<meta name="keywords" content="<?= $_SERVER['APP_KEYWORDS'] ?>">
	<meta name="author" content="<?= $_SERVER['APP_AUTHOR'] ?>">
	<!-- <meta name="robots" content="noindex,nofollow"> -->

	<!-- Icon -->
	<link rel="icon" href="./assets/img/icon.png" type="image/x-icon" />
	<link href="./assets/img/icon.png" rel="apple-touch-icon">

	<title><?= $title ?> | <?= $_SERVER['APP_NAME'] ?></title>


	<!-- Font Awesome -->
	<link rel="stylesheet" href="./component/vendor/@fortawesome/fontawesome-free/css/all.min.css" type="text/css">
	<link rel="stylesheet" href="./component/vendor/font-awesome/css/font-awesome.min.css" type="text/css">

	<!-- Bootsrap -->
	<link rel="stylesheet" href="./component/css/bootstrap.min.css">

	<!-- Hover CSS -->
	<link rel="stylesheet" href="./core/component/vendor/hover.min.css">

	<script src='./core/component/jquery.min.js'></script>

	<style type="text/css">

		::selection { background-color: #E13300; color: white; }
		::-moz-selection { background-color: #E13300; color: white; }

		body {
			background-color: #fff;
			margin: 40px;
			font: 13px/20px normal Helvetica, Arial, sans-serif;
			color: #4F5155;
		}

		a {
			color: #003399;
			background-color: transparent;
			font-weight: normal;
		}

		h1 {
			color: #444;
			background-color: transparent;
			border-bottom: 1px solid #D0D0D0;
			font-size: 19px;
			font-weight: normal;
			margin: 0 0 14px 0;
			padding: 14px 15px 10px 15px;
		}

		h2 {
			color: #444;
			background-color: transparent;
			border-bottom: 1px solid #D0D0D0;
			font-size: 15px;
			font-weight: normal;
			margin: 0 0 14px 0;
			padding: 14px 15px 10px 15px;
		}

		code {
			font-family: Consolas, Monaco, Courier New, Courier, monospace;
			font-size: 12px;
			background-color: #f9f9f9;
			border: 1px solid #D0D0D0;
			color: #002166;
			display: block;
			margin: 14px 0 14px 0;
			padding: 12px 10px 12px 10px;
		}

		#container {
			margin: 10px;
			border: 1px solid #D0D0D0;
			box-shadow: 0 0 8px #D0D0D0;
		}

		p {
			margin: 12px 15px 12px 15px;
		}


		.super-small {
			font-size: 8px;
			font-weight: normal;
		}

	</style>
</head>


<?php
	$time = "( Elapsed Time : " .number_format( (float) (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) , 2, '.', '' ). " Seconds )";
?>


