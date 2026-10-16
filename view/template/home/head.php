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

  <!-- Meta for SEO Friendly -->
  <meta name="description" content="<?= $_SERVER['APP_DESCRIPTION'] ?>">
  <meta name="keywords" content="<?= $_SERVER['APP_KEYWORDS'] ?>">
  <meta name="author" content="<?= $_SERVER['APP_AUTHOR'] ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://myinternship.polibatam.ac.id/" />


  <meta name="csrf-token" content="<?= csrf_token() ?>" />


  <!-- Canonical -->
  <link rel="canonical" href="https://myinternship.polibatam.ac.id/" />


  <title><?= $title ?> | <?= $_SERVER['APP_NAME'] ?></title>


  <!-- Favicons -->
  <!-- <link href="assets/jobcy/img/myinternship-logo.png" rel="icon">
  <link href="assets/jobcy/img/apple-touch-icon.png" rel="apple-touch-icon"> -->

  <!-- Icon -->
  <link rel="icon" href="./assets/img/icon.png" type="image/x-icon" />
  <link href="./assets/img/icon.png" rel="apple-touch-icon">


  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/jobcy/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/jobcy/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/jobcy/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/jobcy/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/jobcy/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/jobcy/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/jobcy/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/jobcy/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Vesperr - v4.9.1
  * Template URL: https://bootstrapmade.com/vesperr-free-bootstrap-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

  <!-- PWA Sorta -->
  <link rel="manifest" href="./manifest.webmanifest" />
  <script src="./sw.js"></script>

  <script src="./upup.sw.min.js"></script>
  <script src="./upup.min.js"></script>
  <script>
    UpUp.start({
      'content-url': './offline.html' // show this page to offline users
    });
  </script>
  <!-- <script src="./upup.sw.min.js"></script> -->


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



</head>

<?php

include './database/database_connection.php';
show_notification();

?>