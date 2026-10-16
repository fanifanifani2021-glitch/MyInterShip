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
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://myinternship.polibatam.ac.id/" />

  <!-- Canonical -->
	<link rel="canonical" href="https://myinternship.polibatam.ac.id/" />
	

  <!-- Icon -->
  <link rel="icon" href="./assets/img/icon.png" type="image/x-icon" />
  <link href="./assets/img/icon.png" rel="apple-touch-icon">

  <title><?= $title ?> | <?= $_SERVER['APP_NAME'] ?></title>


  <!-- Google Fonts -->
  <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet"> -->

  <!-- Vendor CSS Files -->
  <link href="./appland-assets/vendor/aos/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/bootstrap.min.css">

  <!-- <link href="./appland-assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->
  <link href="./appland-assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="./appland-assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="./appland-assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="./appland-assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">




  <!-- Template Main CSS File -->
  <link href="./appland-assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Appland - v4.7.0
  * Template URL: https://bootstrapmade.com/free-bootstrap-app-landing-page-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

  <!-- Fonts and icons -->
  <script src="./assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      custom: {
        "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
        urls: ['./assets/css/fonts.min.css']
      },
      active: function() {
        sessionStorage.fonts = true;
      }
    });
  </script>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap');
  </style>


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


</head>

<?php

include './database/database_connection.php';
show_notification();

?>