<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page Title -->
    <title>{{ $title ?? 'Internship Data' }} | @env('APP_NAME')</title>

    <!-- Rewrite Base Url -->
    <base href="{{ base_url() }}">

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
    <script src="./upup.min.js"></script>
    <script>
        if (typeof UpUp !== 'undefined') {
            UpUp.start({
                // 1. Halaman HTML Offline
                'content-url': "{{ base_url('offline.html') }}",

                // 2. DAFTAR ASET WAJIB (Logo, Font, CSS)
                // Masukkan path logo Anda di sini agar didownload oleh Service Worker
                'assets': [
                    "{{ base_url('assets/jobcy/img/logo.png') }}",
                    // Jika ada font atau gambar lain, masukkan di sini juga
                ],

                // 3. Lokasi Service Worker
                'service-worker-url': 'upup.sw.min.js',

                // 4. Versioning (Optional)
                'cache-version': '{{ date("Ymd") }}',
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
    PAGE CSS FILES
    ======================================================== -->
    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/atlantis2.css') ?>">

    {{-- Page Custom CSS --}}
    @stack('head-scripts')


    {{-- Show Notification --}}
    @php
      if(function_exists('show_notification')) {
          show_notification();
      }
    @endphp

    {!! Notification::renderHead() !!}

</head>

<body>
    <div class="wrapper horizontal-layout-3">

        <div class="main-header no-box-shadow" data-background-color="blue2">
            <div class="nav-top">
                <div class="container d-flex flex-row">


                    <button class="navbar-toggler sidenav-toggler2 ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon">
                            <i class="icon-menu"></i>
                        </span>
                    </button>
                    <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>


                    <!-- Logo Header -->
                    <a href="<?= base_url() ?>" class="logo d-flex align-items-center">
                        <img src="index.php?viewer=main_logo" alt="navbar brand" class="navbar-brand" style="width: 140px; height: auto;">

                        <!-- <img src="" alt="navbar brand" class="navbar-brand"> -->
                    </a>
                    <!-- End Logo Header -->

                    <!-- Navbar Header -->
                    <nav class="navbar navbar-header-left navbar-expand-lg p-0">
                        <ul class="navbar-nav page-navigation pl-md-3">
                            <h3 class="title-menu d-flex d-lg-none">
                                Menu
                                <div class="close-menu"> <i class="flaticon-cross"></i></div>
                            </h3>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('home') ?>" role="button">
                                    Home
                                </a>

                            </li>

                            <?php if (isset($_SESSION['role'])) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url() ?>" role="button">
                                    Dashboard
                                </a>

                            </li>

                            <?php endif; ?>


                        </ul>
                    </nav>

                    <nav class="navbar navbar-header navbar-expand-lg p-0">
                        <div class="container-fluid p-0">
                            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">

                                <!-- Tanggal -->
                                <li class="nav-item dropdown hidden-caret">
                                    <a aria-label="Current Date and Calendar" class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                                        <span id="date"><?= date('D, d M Y') ?></span>
                                    </a>
                                    <ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
                                        <div class="dropdown-user-scroll scrollbar-outer">
                                            <div class="card-body text-center text-accent-1">
                                                <h3><?= date('D, d M Y') . 'M' ?></h3>
                                            </div>
                                        </div>
                                    </ul>
                                </li>


                                <!-- Jam -->
                                <li class="nav-item dropdown hidden-caret">
                                    <a aria-label="Current Time" class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                                        <span id="clock"><?= date('H : i : s') ?></span>
                                    </a>
                                    <ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
                                        <div class="dropdown-user-scroll scrollbar-outer">
                                            <div class="card-body text-center text-accent-1 ">
                                                <h3>Jakarta, Indonesia</h3>
                                                <h1>
                                                    <span id="clock2"><?= date('H : i : s') ?></span>
                                                </h1>

                                            </div>
                                        </div>
                                    </ul>
                                </li>

                                <li class="nav-item dropdown hidden-caret">
                                    <a class="nav-link" href="#" role="button" data-toggle="modal" data-target="#Modalkalender" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-calendar"></i>
                                    </a>


                                </li>


                            </ul>
                        </div>
                    </nav>

                    <!-- End Navbar -->
                </div>
            </div>
        </div>


        @yield('main-content')

        <footer class="footer">
            <div class="container">
                <nav class="pull-left">
                    <ul class="nav">
                        <!-- <li class="nav-item">
							<a class="nav-link" href="#">
							My Internship
							</a>
						</li> -->
                        <!-- <li class="nav-item">
							<a class="nav-link" href="#">
							Help
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">
								Licenses
							</a>
						</li> -->
                    </ul>
                </nav>
                <div class="copyright ml-auto">
                    2022, made with <i class="fa fa-heart heart text-danger"></i> by <a href="https://psteam.id">PSTEAM</a>
                </div>
            </div>
        </footer>


        <div class="modal fade" id="Modalkalender" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header no-bd">
                        <h5 class="modal-title">
                            <span class="fw-mediumbold">
                                Calendar</span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="calendar"></div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary btn-block" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <!--   Core JS Files   -->
    <!-- <script src="./assets/js/core/jquery.3.2.1.min.js"></script> -->
    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery UI -->
    <script src="./assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="./assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="./assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Moment JS -->
    <script src="./assets/js/plugin/moment/moment.min.js"></script>

    <!-- Bootstrap Toggle -->
    <script src="./assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
    <!-- jQuery Scrollbar -->
    <script src="./assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Fullcalendar -->
    <script src="./assets/js/plugin/fullcalendar/fullcalendar.min.js"></script>

    <!-- Atlantis JS -->
    <script src="./assets/js/atlantis.min.js"></script>

    <script>
        $(document).ready(function() {

            clock_run();

            show_calendar();
        });


        function show_calendar() {
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            var className = Array('fc-primary', 'fc-danger', 'fc-black', 'fc-success', 'fc-info', 'fc-warning', 'fc-danger-solid', 'fc-warning-solid', 'fc-success-solid', 'fc-black-solid', 'fc-success-solid', 'fc-primary-solid');


            $calendar = $('#calendar');
            $calendar.fullCalendar({
                fixedWeekCount: false, // Set false agar jumlah minggu yang ditampilkan menyesuaikan dengan bulan aktif
            });
        }


        function clock_run() {

            'use strict';

            let d = new Date();

            let en_day = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            let en_month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            let day = en_day[d.getDay()];
            let date = d.getDate();
            let month = en_month[d.getMonth()];
            let year = (d.getYear() + 1900);

            let curr_date = day + ', ' + date + ' ' + month + ' ' + year;

            localStorage.setItem('curr_date', curr_date);
            let old_date = localStorage.getItem('curr_date');

            if ($("#date").text() != curr_date) {
                localStorage.setItem('curr_date', curr_date);
                $("#date").text(curr_date);
            }


            setInterval(function() {

                let d = new Date();

                let day = en_day[d.getDay()];
                let date = d.getDate();
                let month = en_month[d.getMonth()];
                let year = (d.getYear() + 1900);

                let date_day = day + ', ' + date + ' ' + month + ' ' + year;

                if (date_day != old_date) {
                    localStorage.setItem('curr_date', date_day);
                    $("#date").text(date_day);
                }


                let hours = d.getHours();
                let minutes = d.getMinutes();
                let seconds = d.getSeconds();


                let time = ((hours < 10 ? "0" : "") + hours) + ' : ' + ((minutes < 10 ? "0" : "") + minutes) + ' : ' + ((seconds < 10 ? "0" : "") + seconds);

                $("#clock").text(time);
                $("#clock2").text(time);

            }, 1000);


        }

    </script>

</body>

</html>
