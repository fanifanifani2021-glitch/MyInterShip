<?php error_reporting(false);
session_start(); ?>

<head>

    <base href="<?=  base_url() ?>">


	<!-- Meta for Compatibility -->
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html;">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<meta name="theme-color" content="#FFAD46" />

	<!-- Meta for Security -->
	<!-- <meta http-equiv="Content-Security-Policy" content="default-src https: 'unsafe-eval' 'unsafe-inline'; object-src 'none'"> -->
	<meta name="csrf-token" content="<?= csrf_token() ?>" />


	<!-- Meta for SEO Friendly -->
	<meta name="description" content="<?= $_SERVER['APP_DESCRIPTION'] ?>">
	<meta name="keywords" content="<?= $_SERVER['APP_KEYWORDS'] ?>">
	<meta name="author" content="<?= $_SERVER['APP_AUTHOR'] ?>">
	<!-- <meta name="robots" content="noindex,nofollow"> -->

	<!-- Icon -->
	<link rel="canonical" href="https://myinternship.polibatam.ac.id"/>
	<link rel="icon" href="./assets/img/icon.png" type="image/x-icon" />
	<link href="./assets/img/icon.png" rel="apple-touch-icon" type="image/x-icon">

	<title><?= $title ?> | <?= $_SERVER['APP_NAME'] ?></title>


	<!-- Fonts and icons -->
	<script src="./assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {
				"families": ["Lato:300,400,700,900"]
			},
			custom: {
				"families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
				urls: ['./assets/css/fonts.min.css']
			},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="./assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="./assets/css/atlantis.css">



</head>


<?php
include "cek_login.php";
include "./database/database_connection.php";
show_notification();

?>