<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $title ?? 'Home' }} | @env('APP_NAME')</title>

    <base href="{{ base_url() }}">

    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">

    <link rel="manifest" href="site.webmanifest">
    <meta name="theme-color" content="#005b9f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="MyInternship">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $_SERVER['APP_DESCRIPTION'] ?? 'Internship Management System' ?>">
    <meta name="keywords" content="<?= $_SERVER['APP_KEYWORDS'] ?? 'Internship, Polibatam' ?>">
    <meta name="author" content="<?= $_SERVER['APP_AUTHOR'] ?? 'PSTEAM' ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://polibatam.ac.id/">

    <meta name="csrf-token" content="<?= csrf_token() ?>">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    
    <script src="./assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { "families": ["Lato:300,400,700,900"] },
            custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ['./assets/css/fonts.min.css']
            },
            active: function() { sessionStorage.fonts = true; }
        });
    </script>

    <script src="./upup.min.js"></script>
    <script>
        if (typeof UpUp !== 'undefined') {
            UpUp.start({
                'content-url': "{{ base_url('offline.html') }}",
                'assets': [ "{{ base_url('assets/jobcy/img/logo.png') }}" ],
                'service-worker-url': 'upup.sw.min.js',
                'cache-version': '{{ date("Ymd") }}',
            });
        }
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>

    <link href="assets/jobcy/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/jobcy/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/jobcy/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/jobcy/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/jobcy/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/jobcy/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/jobcy/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <link href="assets/jobcy/css/style.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #005b9f; /* Polibatam Blue */
            --accent-color: #3498DB;  /* Blue like your button image */
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --bg-light: #f8faff;
            --border-color: #dee2e6;
            --navbar-height: 80px;
            --radius-main: 5px; /* Radius kecil sesuai request (4px - 6px) */
        }

        /* --- Global Reset for Rounded Look --- */
        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text-dark);
            background-color: #fff;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Raleway', sans-serif;
            color: var(--text-dark);
        }

        /* Paksa elemen menggunakan radius kecil (Subtle Rounded) */
        .btn, .card, .form-control, .input-group-text, .alert, .badge, .dropdown-menu, .modal-content, .nav-pills .nav-link {
            border-radius: var(--radius-main) !important;
        }

        /* Khusus gambar agar tidak terlalu kaku, tapi tidak terlalu bulat */
        img.rounded { border-radius: var(--radius-main) !important; }
        img.img-fluid { border-radius: var(--radius-main); }

        a {
            text-decoration: none;
            color: var(--primary-color);
            transition: 0.3s;
        }

        a:hover {
            color: var(--accent-color);
        }

        /* --- Header / Navbar --- */
        #header {
            height: var(--navbar-height);
            transition: all 0.5s;
            z-index: 997;
            background: #fff;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05); /* Shadow tipis */
        }

        #header .logo h1 a {
            color: var(--text-dark);
            font-weight: 700;
            font-size: 24px;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        #header .logo img {
            max-height: 50px;
        }

        /* Nav Links */
        .navbar a, .navbar a:focus {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0 10px 30px;
            font-family: 'Raleway', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-dark);
            white-space: nowrap;
            transition: 0.3s;
        }

        .navbar a:hover, .navbar .active, .navbar .active:focus, .navbar li:hover > a {
            color: var(--accent-color);
        }

        /* Nav Button (Login) - Rounded Small */
        .getstarted, .getstarted:focus {
            background: var(--accent-color);
            padding: 10px 25px;
            margin-left: 30px;
            border-radius: var(--radius-main) !important; /* Radius Kecil */
            color: #fff;
            font-weight: 600;
            border: 1px solid var(--accent-color);
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .getstarted:hover, .getstarted:focus:hover {
            background: var(--primary-color);
            color: #fff;
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        /* --- Main Button Style (Like your screenshot) --- */
        .btn-modern {
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            font-size: 15px;
            display: inline-block;
            padding: 12px 30px;
            border-radius: var(--radius-main); /* Radius Kecil */
            transition: 0.3s;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary-modern {
            background: var(--accent-color); /* Blue #3498DB */
            color: #fff;
            box-shadow: 0 2px 6px rgba(52, 152, 219, 0.3);
        }
        .btn-primary-modern:hover {
            background: var(--primary-color);
            color: #fff;
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.4);
        }

        .btn-outline-modern {
            background: #fff;
            color: var(--text-dark);
            border-color: #ccc;
        }
        .btn-outline-modern:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
            background: #f8faff;
        }

        /* --- Footer (Sesuai Screenshot) --- */
        .footer {
            background: #fff;
            padding: 25px 0;
            border-top: 1px solid var(--border-color);
            font-size: 14px;
            color: #666;
        }

        .footer .copyright {
            float: right;
        }
        
        .footer .nav {
            float: left;
        }

        /* Link Footer pudar */
        .footer a {
            color: #555; 
            font-weight: 500;
        }
        
        .footer a:hover {
            color: var(--primary-color);
        }

        .heart {
            color: #e74c3c;
            animation: beat .5s infinite alternate;
            display: inline-block;
        }

        @keyframes beat{
            to { transform: scale(1.2); }
        }

        /* Mobile Nav Toggle Adjustment */
        .mobile-nav-toggle {
            color: var(--text-dark);
        }

        @media (max-width: 991px) {
            .footer .copyright, .footer .nav {
                float: none;
                text-align: center;
            }
            .footer .nav {
                justify-content: center;
                margin-bottom: 10px;
            }
            #header {
                height: 70px;
            }
        }
    </style>

    {{-- Page Custom CSS --}}
    @yield('head-scripts')

    {{-- Show Notification --}}
    @php
      if(function_exists('show_notification')) {
          show_notification();
      }
    @endphp

    {!! Notification::renderHead() !!}

</head>

<?php
session_start();

if (in_array($_SESSION['role'] ?? '', [NULL, '', '-'])) {
    $index = '/';
    $login = false;
} else {
    $index = '/home';
    $login = true;
}
?>

<body>

    <header id="header" class="fixed-top d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">

            <div class="logo">
                <a href="{{ base_url() }}">
                    <img src="assets/jobcy/img/logo.png" alt="MyInternship Logo" class="img-fluid">
                </a>
            </div>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="<?= base_url($index . '#hero') ?>">Home</a></li>
                    <li><a class="nav-link scrollto" href="<?= base_url($index . '#about') ?>">About</a></li>
                    <li><a class="nav-link scrollto" href="<?= base_url($index . '#services') ?>">Features</a></li>
                    <li>
                        <a class="nav-link scrollto <?php checker($routes ?? [], ['announcement'], 'active') ?>" href="{{ base_url('/announcement') }}">
                            Announcements
                        </a>
                    </li>
                    <li><a class="nav-link scrollto" href="<?= base_url($index . '#faq') ?>">FAQ</a></li>
                    <li><a class="nav-link scrollto" href="<?= base_url($index . '#contact') ?>">Contact Us</a></li>

                    <?php if (!$login) : ?>
                        <li><a class="getstarted scrollto" href="{{ router('login.index') }}">Login Portal</a></li>
                    <?php else : ?>
                        <li><a class="getstarted scrollto" href="{{ router('dashboard') }}">Dashboard <i class="bi bi-arrow-right-short ms-1"></i></a></li>
                    <?php endif; ?>

                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav></div>
    </header>@yield('content')
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <ul class="nav justify-content-center justify-content-md-start">
                         <li class="nav-item">
                            <span class="text-muted">MyInternship v2.0</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="copyright">
                        &copy; <?= date('Y') ?> <strong>Politeknik Negeri Batam</strong>. Made with <i class="fa fa-heart heart"></i> by <a href="https://psteam.id" target="_blank">PSTEAM</a>
                    </div>
                </div>
            </div>
        </div>
    </footer><script src="assets/jobcy/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/jobcy/vendor/aos/aos.js"></script>
    <script src="assets/jobcy/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/jobcy/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/jobcy/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/jobcy/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/jobcy/vendor/php-email-form/validate.js"></script>

    <script src="assets/jobcy/js/main.js"></script>

    {!! Notification::renderFooter() !!} 

    @include('partials.core-functionality')
    @include('partials.notifications')

    @yield('footer-scripts')

</body>
</html>