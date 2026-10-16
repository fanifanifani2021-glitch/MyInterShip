<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page Title -->
    <title><?php echo e($title ?? 'Auth'); ?> | <?php echo env('APP_NAME'); ?></title>

    <!-- Rewrite Base Url -->
    <base href="<?php echo e(base_url()); ?>">

    <!-- Icons -->
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">

    <!-- PWA -->
    <link rel="manifest" href="site.webmanifest">
    <meta name="theme-color" content="#FFAD46">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="MyInternship">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $_SERVER['APP_DESCRIPTION'] ?>">
    <meta name="keywords" content="<?= $_SERVER['APP_KEYWORDS'] ?>">
    <meta name="author" content="<?= $_SERVER['APP_AUTHOR'] ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://polibatam.ac.id/">

    <!-- Meta for Security -->
    <meta name="csrf-token" content="<?= csrf_token() ?>">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    
    <!-- Fonts and icons -->
    <script src="./assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                "families": ["Lato:300,400,700,900"]
            }
            , custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"]
                , urls: ['./assets/css/fonts.min.css']
            }
            , active: function() {
                sessionStorage.fonts = true;
            }
        });

    </script>

    <!-- UpUp Service Worker -->
    <script src="upup.min.js"></script>
    <script>
        if (typeof UpUp !== 'undefined') {
            UpUp.start({
                // 1. Halaman HTML Offline
                'content-url': "<?php echo e(base_url('offline.html')); ?>",

                // 2. DAFTAR ASET WAJIB (Logo, Font, CSS)
                // Masukkan path logo Anda di sini agar didownload oleh Service Worker
                'assets': [
                    "<?php echo e(base_url('assets/jobcy/img/logo.png')); ?>",
                    // Jika ada font atau gambar lain, masukkan di sini juga
                ],

                // 3. Lokasi Service Worker
                'service-worker-url': 'upup.sw.min.js',
                
                // 4. Versioning (Optional)
                'cache-version': '<?php echo e(date("Ymd")); ?>',
            });
        }
    </script>

    <!-- PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>


    <!-- =======================================================
    * PAGE CSS FILES
    ======================================================== -->

    <!-- Vendor CSS Files -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/atlantis.css">

    
    <?php echo $__env->yieldPushContent('head-scripts'); ?>

    
    <?php
      if(function_exists('show_notification')) {
          show_notification();
      }
    ?>

    <?php echo Notification::renderHead(); ?>


</head>

<body class="login">


    <?php echo $__env->yieldContent('login-form'); ?>

    <script src="./assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="./assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap.min.js"></script>
    <script src="./assets/js/atlantis.min.js"></script>
    <!-- Add the following script -->
    <script>
        $(document).ready(function() {
            $(document).on('submit', function(event) {
                $('#spinner_icon').show();
                $('#sign_in_button').prop('disabled', true); // Disable the button during the process
            });
        });

    </script>

    <?php echo Notification::renderFooter(); ?> 

    <?php echo $__env->make('partials.core-functionality', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.notifications', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldPushContent('footer-scripts'); ?>

</body>

</html>
</html>
<?php /**PATH C:\xampp_new\htdocs\pblKita\views/templates/auth.blade.php ENDPATH**/ ?>