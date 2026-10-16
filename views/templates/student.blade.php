<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page Title -->
    <title>{{ $title ?? 'Student Dashboard' }} | @env('APP_NAME')</title>

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
    <meta name="description" content="<?= $_SERVER['APP_DESCRIPTION'] ?? '' ?>">
    <meta name="keywords" content="<?= $_SERVER['APP_KEYWORDS'] ?? '' ?>">
    <meta name="author" content="<?= $_SERVER['APP_AUTHOR'] ?? '' ?>">
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

    <!-- UpUp Service Worker -->
    <script src="./upup.min.js"></script>
    <script>
        if (typeof UpUp !== 'undefined') {
            UpUp.start({
                'content-url': "{{ base_url('offline.html') }}",
                'assets': [
                    "{{ base_url('assets/jobcy/img/logo.png') }}",
                ],
                'service-worker-url': 'upup.sw.min.js',
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

    <!-- CSS Files -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/atlantis.css">

    <!-- CKEDITOR -->
    <script src="./library/ckeditor/ckeditor.js"></script>

    <!-- Fullcalendar -->
    <script src="./assets/js/plugin/fullcalendar/fullcalendar.min.js"></script>

    <style>
        .fc-sun {
            color: red;
        }
    </style>
    
    @php
        use \Application\Helpers\Student\StudentLayoutHelper;
    @endphp
    
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

    <div class="wrapper">
        <!-- Calendar Modal -->
        <div class="modal fade" id="Modalkalender" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header no-bd">
                        <h5 class="modal-title">
                            <span class="fw-mediumbold">Calendar</span>
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

        <div class="main-header">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="blue2">
                <a href="{{ router('landing') }}" class="logo">
                    <img src="{{ router('main.logo') }}" alt="navbar brand" class="navbar-brand" style="width: 180px; height: auto;">
                </a>
                <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                        <i class="icon-menu"></i>
                    </span>
                </button>
                <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="icon-menu"></i>
                    </button>
                </div>
            </div>

            <!-- Navbar Header -->
            <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue">
                <div class="container-fluid">
                    <div class="collapse" id="search-nav">
                        <ul class="navbar-nav navbar-left topbar-nav nav-search mr-md-3 align-items-center">
                            <!-- Date Widget -->
                            <li class="nav-item dropdown hidden-caret">
                                <a aria-label="Current Date and Calendar" class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                                    <span id="date"><?= date('D, d M Y') ?></span>
                                </a>
                                <ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <div class="card-body text-center text-accent-1">
                                            <h3><?= date('D, d M Y') ?></h3>
                                        </div>
                                    </div>
                                </ul>
                            </li>

                            <!-- Clock Widget -->
                            <li class="nav-item dropdown hidden-caret">
                                <a aria-label="Current Time" class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                                    <span id="clock"><?= date('H : i : s') ?></span>
                                </a>
                                <ul class="float-right dropdown-menu dropdown-calendar dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <div class="card-body text-center text-accent-1">
                                            <h3>Batam, Indonesia</h3>
                                            <h1>
                                                <span id="clock2"><?= date('H : i : s') ?></span>
                                            </h1>
                                        </div>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <li class="nav-item toggle-nav-search hidden-caret">
                            <a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false" aria-controls="search-nav">
                                <i class="fa fa-clock"></i>
                            </a>
                        </li>

                        <li class="nav-item dropdown hidden-caret">
                            <a class="nav-link" href="#" role="button" data-toggle="modal" data-target="#Modalkalender" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-calendar"></i>
                            </a>
                        </li>

                        <!-- Notifications -->
                        <li class="nav-item dropdown hidden-caret" id="notification">
                            <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                <span id="count_notification"></span>
                            </a>
                            <ul class='dropdown-menu messages-notif-box animated fadeIn' aria-labelledby='notifDropdown'>
                                <li>
                                    <div class='dropdown-title'>Notifications</div>
                                </li>
                                <li>
                                    <div class='dropdown-title'>You don't have new notifications</div>
                                </li>
                            </ul>
                        </li>

                        <!-- Profile Dropdown -->
                        <li class="nav-item dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                                <div class="avatar-sm">
                                    <img src="{{ router('user.profile-picture') }}" alt="..." class="avatar-img rounded-circle">
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <div class="dropdown-user-scroll scrollbar-outer">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-lg"><img src="{{ router('user.profile-picture') }}" alt="image profile" class="avatar-img rounded"></div>
                                            <div class="u-text">
                                                <h5><?= $_SESSION['name'] ?? 'Student' ?></h5>
                                                <p class="text-muted"><?= $_SESSION['role'] ?? 'Mahasiswa' ?></p>
                                                <a href="{{ router('student.identity.index') }}" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ router('student.identity.index') }}">My Profile</a>
                                        <a class="dropdown-item" href="{{ router('student.internship') }}">My Internship</a>
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="logout_confirm()">Logout</a>
                                    </li>
                                </div>
                            </ul>
                        </li>

                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-style-2">
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <div class="user">
                        <div class="avatar-sm float-left mr-2">
                            <img src="{{ router('user.profile-picture') }}" alt="..." class="avatar-img rounded-circle">
                        </div>
                        <div class="info">
                            <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                                <span>
                                    <span class="wrap2"><?= $_SESSION['name'] ?? 'Student' ?></span>
                                    <span class="user-level">NIM : <?= $_SESSION['nim'] ?? '-' ?></span>
                                    {{-- 🎓 Bersih tanpa SQL mentah --}}
                                    <span class="user-level wrap2">
                                        Student at {{ StudentLayoutHelper::getUniversityName() }}
                                    </span>
                                </span>
                            </a>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <!-- Sidebar Navigation Menu -->
                    <ul class="nav nav-primary">
                        <li class="nav-item @if ( ($routes ?? '') == 'dashboard' ) active @endif">
                            <a href="{{ router('landing') }}" class="collapsed" aria-expanded="false">
                                <i class="fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item @if ( ($routes ?? '') == 'home' ) active @endif">
                            <a href="{{ router('home') }}" class="collapsed" aria-expanded="false">
                                <i class="fas fa-home"></i>
                                <p>Home</p>
                            </a>
                        </li>

                        <li class="nav-item @if ( ($routes ?? '') == 'student_identity' ) active @endif">
                            <a href="{{ router('student.identity.index') }}" class="collapsed d-flex align-items-center justify-content-between" aria-expanded="false">
                                <div>
                                    <i class="fas fa-id-card"></i>
                                    <p class="d-inline">Student Identity</p>
                                </div>
                                @php $isProfileReady = StudentLayoutHelper::isProfileComplete(); @endphp
                                @if(!$isProfileReady)
                                    <span class="badge badge-danger badge-pill font-weight-bold" style="font-size: 10px; padding: 3px 7px;" title="Profile / Documents Incomplete">
                                        Incomplete
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Internship Menu</h4>
                        </li>

                        <li class="nav-item @if ( in_array(($routes ?? ''), ['student_offer', 'register_offer']) ) active @endif">
                            <a href="{{ router('student/internship_offer.list') }}" class="collapsed" aria-expanded="false">
                                <i class="fas fa-clipboard-list"></i>
                                <p>Internship Offer</p>
                            </a>
                        </li>

                        {{-- Submenu Register Internship --}}
                        <li class="nav-item @if ( in_array(($routes ?? ''), ['register_cooperation', 'register_independent']) ) active @endif">
                            <a data-toggle="collapse" href="#register_internship" class="collapsed" aria-expanded="false">
                                <i class="fab fa-wpforms"></i>
                                <p>Register Internship</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse @if ( in_array(($routes ?? ''), ['register_cooperation', 'register_independent']) ) show @endif" id="register_internship">
                                <ul class="nav nav-collapse">
                                    <li class="@if ( ($routes ?? '') == 'register_cooperation' ) active @endif">
                                        <a href="{{ router('student.register_internship.cooperation') }}">
                                            <span class="sub-item">Register Cooperation Internship</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        {{-- MENU MY INTERNSHIP + COUNTER MAGANG AKTIF --}}
                        <li class="nav-item @if ( ($routes ?? '') == 'my_internship' ) active @endif">
                            <a href="{{ router('student.internship') }}" class="collapsed d-flex align-items-center justify-content-between" aria-expanded="false">
                                <div>
                                    <i class="fas fa-briefcase"></i>
                                    <p class="d-inline">My Internship</p>
                                </div>
                                @php 
                                    $activeCount = StudentLayoutHelper::activeInternshipCount(); 
                                @endphp
                                @if($activeCount > 0)
                                    <span class="badge badge-success badge-pill font-weight-bold" style="font-size: 10px; padding: 3px 7px;">
                                        Active
                                    </span>
                                @endif
                            </a>
                        </li>

                        {{-- MENU INTERNSHIP LETTER + COUNTER SURAT APPROVED --}}
                        <li class="nav-item @if ( ($routes ?? '') == 'my_internship_letter' ) active @endif">
                            <a href="{{ router('student.internship_letter.index') }}" class="collapsed d-flex align-items-center justify-content-between" aria-expanded="false">
                                <div>
                                    <i class="fas fa-file-alt"></i>
                                    <p class="d-inline">Internship Letter</p>
                                </div>
                                @php 
                                    $letterCount = StudentLayoutHelper::approvedLetterCount(); 
                                @endphp
                                @if($letterCount > 0)
                                    <span class="badge badge-warning text-white badge-pill font-weight-bold" style="font-size: 10px; padding: 3px 7px;">
                                        {{ $letterCount }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Account</h4>
                        </li>

                        {{-- MENU HELPDESK + COUNTER TIKET --}}
                        <li class="nav-item @if( in_array(($routes ?? ''), ['helpdesk', 'student_helpdesk']) ) active @endif">
                            <a href="{{ router('student.helpdesk.index') }}" class="collapsed d-flex align-items-center justify-content-between" aria-expanded="false">
                                <div>
                                    <i class="fas fa-headset"></i>
                                    <p class="d-inline">Helpdesk / Support</p>
                                </div>
                                @php 
                                    $helpdeskCount = StudentLayoutHelper::helpdeskCount(); 
                                @endphp
                                @if($helpdeskCount > 0)
                                    <span class="badge badge-info badge-pill font-weight-bold" style="font-size: 10px; padding: 3px 7px;">
                                        {{ $helpdeskCount }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="javascript:void(0)" onclick="logout_confirm()" class="collapsed" aria-expanded="false">
                                <i class="fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">
            @yield('main-content')

            <footer class="footer">
                <div class="container">
                    <nav class="pull-left">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ base_url('/security-policy') }}">
                                    Security Policy
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div class="copyright ml-auto">
                        &copy; <?= date('Y') ?> <strong>Politeknik Negeri Batam</strong>. Made with <i class="fa fa-heart heart text-danger"></i> by 
                        <a href="https://psteam.id" target="_blank" class="font-weight-bold" style="color: #005b9f;">PSTEAM</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
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
            $calendar = $('#calendar');
            $calendar.fullCalendar({
                fixedWeekCount: false,
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

    {!! Notification::renderFooter() !!}

    @include('partials.core-functionality')
    @include('partials.notifications')

    @stack('footer-scripts')

</body>
</html>