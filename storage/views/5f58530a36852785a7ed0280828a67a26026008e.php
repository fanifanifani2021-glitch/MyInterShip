<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page Title -->
    <title><?php echo e($title ?? 'Industry Dashboard'); ?> | <?php echo env('APP_NAME'); ?></title>

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
    <script src="./upup.min.js"></script>
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
    PAGE CSS FILES
    ======================================================== -->
    <!-- CSS Files -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/atlantis.css">

    <!-- CKEDITOR -->
    <script src="./library/ckeditor/ckeditor.js"></script>

    <?php
    // Query pertama untuk menghitung jumlah attendance
    $notif_attendance = "SELECT COUNT(attendance.id_attendance) AS count_attendance
    FROM attendance
    INNER JOIN internship ON attendance.id_internship = internship.id_internship
    INNER JOIN student_internship ON internship.nim = student_internship.nim
    LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
    WHERE internship.id_company = :id_company
    AND attendance.approval_spv = :approval_spv";

    // Query kedua untuk menghitung jumlah logbook
    $notif_logbook = "SELECT COUNT(logbook.id_logbook) AS count_logbook
    FROM logbook
    INNER JOIN internship ON logbook.id_internship = internship.id_internship
    INNER JOIN student_internship ON internship.nim = student_internship.nim
    LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
    WHERE internship.id_company = :id_company
    AND logbook.approval_spv = :approval_spv";

    // Query ketiga untuk menghitung jumlah discussion
    $notif_discussion = "SELECT COUNT(discussion.id_discussion) AS count_discussion
    FROM discussion
    INNER JOIN internship ON discussion.id_internship = internship.id_internship
    INNER JOIN student_internship ON internship.nim = student_internship.nim
    LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
    WHERE internship.id_company = :id_company
    AND internship.status = :internship_status AND discussion.discussion_status = :discussion_status";

    // Menambahkan kondisi jika user_type adalah 'spv'
    if ($_SESSION['user_type'] == 'spv') {
    $notif_attendance .= " AND internship.id_user_company = :id_user_company";
    $notif_logbook .= " AND internship.id_user_company = :id_user_company";
    $notif_discussion .= " AND internship.id_user_company = :id_user_company";
    }

    // Persiapan untuk eksekusi query pertama
    $stmt_attendance = $_SERVER['PDO']->prepare($notif_attendance);
    $params_attendance = [':id_company' => $_SESSION['id_company'], ':approval_spv' => 'Waiting'];
    if ($_SESSION['user_type'] == 'spv') {
    $params_attendance[':id_user_company'] = $_SESSION['id_user_company'];
    }
    $stmt_attendance->execute($params_attendance);
    $result_attendance = $stmt_attendance->fetch(PDO::FETCH_ASSOC);
    $notif['count_attendance'] = $result_attendance['count_attendance'];

    // Persiapan untuk eksekusi query kedua
    $stmt_logbook = $_SERVER['PDO']->prepare($notif_logbook);
    $params_logbook = [':id_company' => $_SESSION['id_company'], ':approval_spv' => 'Waiting'];
    if ($_SESSION['user_type'] == 'spv') {
    $params_logbook[':id_user_company'] = $_SESSION['id_user_company'];
    }
    $stmt_logbook->execute($params_logbook);
    $result_logbook = $stmt_logbook->fetch(PDO::FETCH_ASSOC);
    $notif['count_logbook'] = $result_logbook['count_logbook'];

    // Persiapan untuk eksekusi query ketiga
    $stmt_discussion = $_SERVER['PDO']->prepare($notif_discussion);
    $params_discussion = [':id_company' => $_SESSION['id_company'], ':discussion_status' => 'Open', ':internship_status' => 'Yes'];
    if ($_SESSION['user_type'] == 'spv') {
    $params_discussion[':id_user_company'] = $_SESSION['id_user_company'];
    }
    $stmt_discussion->execute($params_discussion);
    $result_discussion = $stmt_discussion->fetch(PDO::FETCH_ASSOC);
    $notif['count_discussion'] = $result_discussion['count_discussion'];
    ?>

    
    <?php echo $__env->yieldPushContent('head-scripts'); ?>
    
    <style type="text/css">
        /* Posisi relatif untuk ikon agar badge bisa ditempatkan relatif terhadapnya */
        .notification-icon {
            position: relative;
            /* Sesuaikan ukuran ikon jika diperlukan */
        }

        /* Badge notifikasi kecil hijau */
        .custom-notification-badge {
            position: absolute;
            top: -8px;
            /* Sesuaikan posisi badge secara vertikal */
            right: -8px;
            /* Sesuaikan posisi badge secara horizontal */
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            /* Ukuran badge */
            font-size: 10px;
            /* Ukuran angka */
            line-height: 1;
            min-width: 16px;
            /* Pastikan ukuran minimal badge */
            text-align: center;
            /* Pusatkan angka di dalam badge */
        }

        .fc-sun {
            color: red;
            /* Mengubah warna font menjadi merah pada hari Minggu */
        }

    </style>


    
    <?php
      if(function_exists('show_notification')) {
          show_notification();
      }
    ?>

    <?php echo Notification::renderHead(); ?>


</head>


<body>

    <div class="wrapper">

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

        <div class="main-header">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="blue2">

                <a href="<?php echo e(router('dashboard')); ?>" class="logo">
                    <img src="<?php echo e(router('main.logo')); ?>" alt="navbar brand" class="navbar-brand" style="width: 180px; height: auto;">

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
            <!-- End Logo Header -->

            <!-- Navbar Header -->
            <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue">

                <div class="container-fluid">



                    <div class="collapse" id="search-nav">
                        <ul class="navbar-nav navbar-left topbar-nav nav-search mr-md-3 align-items-center">


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

                        <!-- Notification -->
                        <li class="nav-item dropdown hidden-caret" id="notification">



                            <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>


                                <?php if( $notif['count_logbook'] + $notif['count_attendance'] + $notif['count_discussion'] > 0 ): ?>
                                <span id="count_notification" class="notification"><?php echo e($notif['count_logbook'] + $notif['count_attendance'] + $notif['count_discussion']); ?></span>

                                <?php else: ?>
                                <span id="count_notification"></span>
                                <?php endif; ?>
                            </a>


                            <ul class='dropdown-menu messages-notif-box animated fadeIn' aria-labelledby='notifDropdown' id=''>

                                <li>
                                    <div class='dropdown-title'>New Notification</div>
                                </li>


                                <?php if( $notif['count_logbook'] > 0 || $notif['count_attendance'] > 0 || $notif['count_discussion'] > 0 ): ?>

                                <?php if( $notif['count_logbook'] > 0): ?>
                                <li>
                                    <div class="notif-center">
                                        <a href="<?php echo e(base_url('index.php?page=intern_logbook')); ?>">
                                            <div class="notif-icon notif-primary"> <i class="fa fa-book"></i> </div>
                                            <div class="notif-content">
                                                <span class="block">
                                                    <?php echo e($notif['count_logbook']); ?> New Logbook to validate
                                                </span>
                                                
                                            </div>
                                        </a>

                                    </div>
                                </li>

                                <?php endif; ?>

                                <?php if( $notif['count_attendance'] > 0): ?>
                                <li>
                                    <div class="notif-center">
                                        <a href="<?php echo e(base_url('index.php?page=intern_attendance')); ?>">
                                            <div class="notif-icon notif-secondary"> <i class="fa fa-calendar"></i> </div>
                                            <div class="notif-content">
                                                <span class="block">
                                                    <?php echo e($notif['count_attendance']); ?> New Attendance to validate
                                                </span>
                                                
                                            </div>
                                        </a>

                                    </div>
                                </li>

                                <?php endif; ?>


                                <?php if( $notif['count_discussion'] > 0): ?>
                                <li>
                                    <div class="notif-center">
                                        <a href="<?php echo e(base_url('index.php?page=intern_discussion')); ?>">
                                            <div class="notif-icon notif-success"> <i class="fa fa-comments"></i> </div>
                                            <div class="notif-content">
                                                <span class="block">
                                                    <?php echo e($notif['count_discussion']); ?> New Discussion to Join
                                                </span>
                                            </div>
                                        </a>

                                    </div>
                                </li>

                                <?php endif; ?>

                                <?php else: ?>
                                <li>
                                    <div class='dropdown-title'>You don't have new notification</div>
                                </li>

                                <?php endif; ?>




                            </ul>

                        </li>

                        <!-- Profil -->
                        <li class="nav-item dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                                <div class="avatar-sm">
                                    <img src="<?php echo e(router('user.profile-picture')); ?>" alt="..." class="avatar-img rounded-circle">
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <div class="dropdown-user-scroll scrollbar-outer">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-lg"><img src="<?php echo e(router('user.profile-picture')); ?>" alt="image profile" class="avatar-img rounded"></div>
                                            <div class="u-text">
                                                <h5><?= $_SESSION['user_fullname'] ?></h5>
                                                <p class="text-muted"><?= $_SESSION['user_type'] ?> at <?= $_SESSION['company_name'] ?></p>
                                                <a href="index.php?page=industry_profile" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?php echo e(router('dashboard')); ?>">My Dashboard</a>
                                        <a class="dropdown-item" href="index.php?page=industry_profile">My Profile</a>
                                        <a class="dropdown-item" href="<?php echo e(router('industry.my_company')); ?>">My Company</a>
                                        <!-- <a class="dropdown-item" href="#">Inbox</a> -->
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?php echo e(router('home')); ?>">Home</a>
                                        <a class="dropdown-item" href="<?php echo e(router('announcement.index')); ?>">Announcements</a>
                                        <div class="dropdown-divider"></div>


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
                            <img src="<?php echo e(router('user.profile-picture')); ?>" alt="..." class="avatar-img rounded-circle">
                        </div>
                        <div class="info">
                            <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                                <span>

                                    <span class="wrap2"><?= $_SESSION['user_fullname']?></span>
                                    <span class="user-level"><?= $_SESSION['user_type'] ?> at</span>
                                    <span class="user-level"><?= $_SESSION['company_name'] ?></span>
                                </span>
                            </a>
                            <div class="clearfix"></div>

                        </div>
                    </div>
                    <ul class="nav nav-primary">
                        <li class="nav-item <?php if ( $route == 'dashboard' ) { echo "active"; }?>">
                            <a href="<?php echo e(router('dashboard')); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>


                        <li class="nav-item <?php if ( $route == 'home' ) { echo "active"; }?>">
                            <a href="<?php echo e(router('home')); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-home"></i>
                                <p>Home</p>
                            </a>
                        </li>



                        <li class="nav-item <?php if ( $route == 'my_company' ) { echo "active"; }?>">
                            <a href="<?php echo e(router('industry.my_company')); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-building"></i>
                                <p>My Company</p>
                            </a>
                        </li>

                        <li class="nav-item <?php if ( $route == 'industry_offer' ) { echo "active"; } ?>">
                            <a href="index.php?page=industry_offer" class="collapsed" aria-expanded="false">
                                <i class="fas fa-briefcase"></i>
                                <p>Job Offers</p>
                            </a>
                        </li>


                        <?php if( $_SESSION['user_type'] == 'HRD' ): ?>


                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Internship Preparation</h4>
                        </li>

                        <li class="nav-item <?php if ( $route == 'industry_supervisor_management' ) { echo "active"; } ?>">
                            <a href="index.php?page=industry_supervisor_management" class="collapsed" aria-expanded="false">
                                <i class="fas fa-user-secret"></i>
                                <p>Supervisor Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php if ( $route == 'attendance_location' ) { echo "active"; } ?>">
                            <a href="<?php echo e(router('industry/attendance_location.index')); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-map-marker"></i>
                                <p>Attendance Location</p>
                            </a>
                        </li>


                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Internship Menu</h4>
                        </li>


                        <li class="nav-item <?php checker ( $route, ['intern_candidate', 'validate_intern'], 'active' ) ?>">
                            <a href="index.php?page=intern_candidate" class="collapsed" aria-expanded="false">
                                <i class="fab fa-wpforms"></i>
                                <p class='wrap'>Internship Candidate</p>
                            </a>
                        </li>

                        
                        <li class="nav-item <?php checker( $route, ['internship_management', 'accepted_intern_management', 'rejected_intern_management', 'finished_intern_management'], 'active') ?>">
                            <a href="index.php?page=internship_management" class="collapsed" aria-expanded="false">
                                <i class="fas fa-users"></i>
                                <p class='wrap'>Internship Management</p>
                            </a>
                        </li>




                        

                        <?php endif; ?>


                        <?php if( $_SESSION['user_type'] == 'supervisor' || $_SESSION['user_type'] == 'Supervisor' ): ?>

                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Supervisor Menu</h4>
                        </li>

                        <li class="nav-item <?php checker( $route, ['intern_list'], 'active' )?>">
                            <a href="index.php?page=intern_list" class="collapsed" aria-expanded="false">
                                <i class="fas fa-users"></i>
                                <p class="wrap">Intern List</p>
                            </a>
                        </li>

                        <?php endif; ?>


                        <li class="nav-item submenu <?php if( in_array($routes, ['intern_logbook', 'intern_attendance', 'intern_administration']) ): ?> active <?php endif; ?>">

                            <a data-toggle="collapse" href="#internship_data" class="collapsed" aria-expanded="true">
                                <i class="fab fa-wpforms"></i>
                                <p>Internship Data</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse show" id="internship_data">
                                <ul class="nav nav-collapse open">
                                    <li class="nav-item <?php checker($routes, ['intern_logbook'], 'active') ?>">
                                        <a aria-label="Go to Logbook" href="index.php?page=intern_logbook" class="collapsed" aria-expanded="false">
                                            <i class="fas fa-book notification-icon">
                                                <?php if( $notif['count_logbook'] > 0 ): ?>
                                                <span class="custom-notification-badge bg-success"><?php echo e($notif['count_logbook']); ?></span>
                                                <span class="sr-only"><?php echo e($notif['count_logbook']); ?> Logbook Waiting for Validation</span>
                                                <?php endif; ?>

                                            </i>

                                            <p class="wrap">Student Logbook

                                            </p>
                                        </a>
                                    </li>


                                    <li class="nav-item <?php checker($routes, ['intern_attendance'], 'active') ?>">
                                        <a aria-label="Go to Attendance" href="index.php?page=intern_attendance" class="collapsed" aria-expanded="false">
                                            <i class="fas fa-calendar notification-icon">
                                                <?php if($notif['count_attendance'] > 0): ?>
                                                <span class="custom-notification-badge bg-success"><?php echo e($notif['count_attendance']); ?></span>
                                                <?php endif; ?>
                                            </i>
                                            <p class="wrap">Student Attendance</p>
                                        </a>
                                    </li>


                                    <li class="nav-item <?php checker($routes, ['intern_discussion'], 'active') ?>">
                                        <a aria-label="Go to Discussion" href="index.php?page=intern_discussion" class="collapsed" aria-expanded="false">
                                            <i class="fas fa-comments notification-icon">
                                                <?php if($notif['count_discussion'] > 0): ?>
                                                <span class="custom-notification-badge bg-success"><?php echo e($notif['count_discussion']); ?></span>
                                                <?php endif; ?>
                                            </i>
                                            <p class="wrap">Student Discussion</p>
                                        </a>
                                    </li>


                                    <li class="nav-item <?php checker($routes, ['intern_administration'], 'active') ?>">
                                        <a aria-label="Go to Administration" href="index.php?page=intern_administration" class="collapsed" aria-expanded="false">
                                            <i class="fas fa-file"></i>
                                            <p class="wrap">Student Administration</p>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Account</h4>
                        </li>

                        <li class="nav-item <?php if ( $route == 'industry_profile' ) { echo "active"; } ?>">
                            <a href="index.php?page=industry_profile" class="collapsed" aria-expanded="false">
                                <i class="fas fa-user"></i>
                                <p>Profile</p>
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


            <?php echo $__env->yieldContent('main-content'); ?>

            <footer class="footer">
                <div class="container">
                    <nav class="pull-left">
                        <ul class="nav">
                            
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(base_url('/security-policy')); ?>">
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

    <!--   Core JS Files   -->
    <!-- <script src="./assets/js/core/jquery.3.2.1.min.js"></script> -->
    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery UI -->
    <script src="./assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="./assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

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

    <?php echo Notification::renderFooter(); ?> 

    <?php echo $__env->make('partials.core-functionality', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.notifications', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldPushContent('footer-scripts'); ?>



</body>

</html>
<?php /**PATH C:\xampp_new\htdocs\pblKita\views/templates/industry.blade.php ENDPATH**/ ?>