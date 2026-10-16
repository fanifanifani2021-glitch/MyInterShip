<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page Title -->
    <title><?php echo e($title ?? 'Lecturer Dashboard'); ?> | <?php echo env('APP_NAME'); ?></title>

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

    <!-- Fullcalendar -->
    <script src="./assets/js/plugin/fullcalendar/fullcalendar.min.js"></script>

    
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

    
    <?php echo $__env->yieldPushContent('head-scripts'); ?>

    
    <?php
      if(function_exists('show_notification')) {
          show_notification();
      }
    ?>

    <?php echo Notification::renderHead(); ?>


    <?php
    // Query pertama untuk menghitung jumlah attendance
    $notif_attendance = "SELECT COUNT(attendance.id_attendance) AS count_attendance
    FROM attendance
    INNER JOIN internship ON attendance.id_internship = internship.id_internship
    INNER JOIN student_internship ON internship.nim = student_internship.nim
    LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
    WHERE student_internship.nik_dospem = :nik_dospem
    AND attendance.approval_dpm = :approval_dpm";

    // Query kedua untuk menghitung jumlah logbook
    $notif_logbook = "SELECT COUNT(logbook.id_logbook) AS count_logbook
    FROM logbook
    INNER JOIN internship ON logbook.id_internship = internship.id_internship
    INNER JOIN student_internship ON internship.nim = student_internship.nim
    LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
    WHERE student_internship.nik_dospem = :nik_dospem
    AND logbook.approval_dpm = :approval_dpm";


    $notif_discussion = "SELECT COUNT(discussion.id_discussion) AS count_discussion
    FROM discussion
    INNER JOIN internship ON discussion.id_internship = internship.id_internship
    INNER JOIN student_internship ON internship.nim = student_internship.nim
    LEFT JOIN program_study ON program_study.kode_prodi = student_internship.program_study
    WHERE student_internship.nik_dospem = :nik_dospem
    AND internship.status = :internship_status AND discussion.discussion_status = :discussion_status";

    // Persiapan untuk eksekusi query pertama
    $stmt_attendance = $_SERVER['PDO']->prepare($notif_attendance);
    $stmt_attendance->execute([':nik_dospem' => $_SESSION['nim_nik_unit'], ':approval_dpm' => 'Waiting']);
    $result_attendance = $stmt_attendance->fetch( PDO::FETCH_ASSOC );
    $notif['count_attendance'] = $result_attendance['count_attendance'];

    // Persiapan untuk eksekusi query kedua
    $stmt_logbook = $_SERVER['PDO']->prepare($notif_logbook);
    $stmt_logbook->execute([':nik_dospem' => $_SESSION['nim_nik_unit'], ':approval_dpm' => 'Waiting']);
    $result_logbook = $stmt_logbook->fetch( PDO::FETCH_ASSOC );
    $notif['count_logbook'] = $result_logbook['count_logbook'];

    // Persiapan untuk eksekusi query ketiga
    $stmt_discussion = $_SERVER['PDO']->prepare($notif_discussion);
    $stmt_discussion->execute([':nik_dospem' => $_SESSION['nim_nik_unit'], ':discussion_status' => 'Open', 'internship_status' => 'Yes']);
    $result_discussion = $stmt_discussion->fetch( PDO::FETCH_ASSOC );
    $notif['count_discussion'] = $result_discussion['count_discussion'];


    # Get University Data
    $sql_university = "SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1";
    $stmt_university = $_SERVER['PDO']->prepare($sql_university);
    $stmt_university->execute( ['id_kampus' => $_SESSION['Lecturer-Data']['id_kampus']] );
    $lecturer_university = $stmt_university->fetch( PDO::FETCH_ASSOC );


    ?>
</head>
<body>
    <div class="wrapper <?php if( $routes == 'administration_student' ): ?> sidebar_minimize <?php endif; ?>">


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

                <a href="<?php echo e(base_url()); ?>" class="logo">
                    <img src="<?php echo e(base_url('/viewer/main/logo')); ?>" alt="navbar brand" class="navbar-brand" style="width: 180px; height: auto;">

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
                                        <a href="<?php echo e(base_url('index.php?page=logbook_student')); ?>">
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
                                        <a href="<?php echo e(base_url('index.php?page=attendance_student')); ?>">
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
                                        <a href="<?php echo e(base_url('index.php?page=discussion_student')); ?>">
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
                                    <img src="<?php echo e(base_url('/viewer/profile-picture')); ?>" alt="..." class="avatar-img rounded-circle">
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <div class="dropdown-user-scroll scrollbar-outer">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-lg"><img src="<?php echo e(base_url('/viewer/profile-picture')); ?>" alt="image profile" class="avatar-img rounded"></div>
                                            <div class="u-text">
                                                <h5><?= $_SESSION['name'] ?></h5>
                                                <p class="text-muted"><?= $_SESSION['role'] ?></p><a href="<?php echo e(base_url('index.php?page=lecturer_profile')); ?>" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?php echo e(base_url('index.php?page=lecturer_profile')); ?>">My Profile</a>
                                        
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
                            <img aria-label="User's Profile Picture" src="<?php echo e(base_url('/viewer/profile-picture')); ?>" alt="User's Profile Picture" class="avatar-img rounded-circle">
                        </div>
                        <div class="info">
                            <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                                <span>
                                    <span class="wrap2"><?= $_SESSION['name'] ?></span>
                                    <span class="wrap2">NIK : <?= $_SESSION['nim_nik_unit'] ?></span>
                                    <span class="user-level wrap2">Lecturer at <?php echo e($lecturer_university['nama_kampus']); ?></span>
                                </span>
                            </a>
                            <div class="clearfix"></div>

                        </div>
                    </div>
                    <ul class="nav nav-primary">
                        <li class="nav-item <?php checker( $routes , ['dashboard', ''], 'active' ) ?>">
                            <a aria-label="Go to Dashboard" href="<?php echo e(base_url()); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker( $routes , ['home', ''], 'active' ) ?>">
                            <a aria-label="Go to Home" href="<?php echo e(base_url('/home')); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-home"></i>
                                <p>Home</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker( $routes, ['company_list'], 'active' ) ?>">
                            <a aria-label="Go to Company List" href="<?php echo e(router('lecturer.company.index')); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-building"></i>
                                <p class="wrap">Company List</p>
                            </a>
                        </li>

                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Lecturer Menu</h4>
                        </li>
                        <li class="nav-item <?php if ( $routes == 'intern_student_management' ) { echo "active"; } ?>">
                            <a href="<?php echo e(router('lecturer/my/student.index')); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-users"></i>
                                <p class="wrap">My Supervised Students</p>
                            </a>
                        </li>


                        <li class="nav-item submenu <?php if( in_array($routes, ['attendance_student', 'logbook_student', 'administration_student', 'discussion_student']) ): ?> active <?php endif; ?>">

                            <a data-toggle="collapse" href="#internship_data" class="collapsed" aria-expanded="true">
                                <i class="fab fa-wpforms"></i>
                                <p class="wrap">My Student Internship Data</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse show" id="internship_data">
                                <ul class="nav nav-collapse open">
                                    <li class="nav-item <?php checker($routes, ['logbook_student'], 'active') ?>">
                                        <a aria-label="Go to Logbook" href="index.php?page=logbook_student" class="collapsed" aria-expanded="false">
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




                                    <li class="nav-item <?php checker($routes, ['attendance_student'], 'active') ?>">
                                        <a aria-label="Go to Attendance" href="index.php?page=attendance_student" class="collapsed" aria-expanded="false">
                                            <i class="fas fa-calendar notification-icon">
                                                <?php if($notif['count_attendance'] > 0): ?>
                                                <span class="custom-notification-badge bg-success"><?php echo e($notif['count_attendance']); ?></span>
                                                <?php endif; ?>
                                            </i>
                                            <p class="wrap">Student Attendance</p>
                                        </a>
                                    </li>


                                    <li class="nav-item <?php checker($routes, ['discussion_student'], 'active') ?>">
                                        <a aria-label="Go to Student Discussion" href="index.php?page=discussion_student" class="collapsed" aria-expanded="false">
                                            <i class="fas fa-comments notification-icon">
                                                <?php if($notif['count_discussion'] > 0): ?>
                                                <span class="custom-notification-badge bg-success"><?php echo e($notif['count_discussion']); ?></span>
                                                <?php endif; ?>
                                            </i>
                                            <p class="wrap">Student Discussion</p>
                                        </a>
                                    </li>





                                    <li class="nav-item <?php checker($routes, ['administration_student'], 'active') ?>">
                                        <a aria-label="Go to Administration" href="index.php?page=administration_student" class="collapsed" aria-expanded="false">
                                            <i class="fas fa-file"></i>
                                            <p class="wrap">Student Administration</p>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>


                        <li class="nav-item <?php if( $routes == 'jobdesk_management' ): ?> active <?php endif; ?>">
                            <a href="index.php?page=jobdesk_management" class="collapsed" aria-expanded="false">
                                <i class="fas fa-list"></i>
                                <p class="wrap">Jobdesk Management</p>
                            </a>
                        </li>



                        <?php if( in_array($_SESSION['is_koor'] , [true, 1] ) ): ?> :

                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Internship Coordinator</h4>
                        </li>

                        <li class="nav-item <?php if ( $routes == 'supervisor_management' ) { echo "active"; } ?>">
                            <a href="index.php?page=supervisor_management" class="collapsed" aria-expanded="false">
                                <i class="fas fa-users"></i>
                                <p class="wrap">Lecturer Management</p>
                            </a>
                        </li>


                        <li class="nav-item <?php if ( $routes == 'student_intern_management' ) { echo "active"; } ?>">
                            <a href="index.php?page=student_intern_management" class="collapsed" aria-expanded="false">
                                <i class="fas fa-user"></i>
                                <p class="wrap">Students Management</p>
                            </a>
                        </li>

                        <li class="nav-item submenu <?php if( in_array($routes, ['coordinator/internship_administration']) ): ?> active <?php endif; ?>">

                            <a data-toggle="collapse" href="#all_internship_data" class="collapsed" aria-expanded="true">
                                <i class="fab fa-wpforms"></i>
                                <p class="wrap">Students Internship Data</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse show" id="all_internship_data">
                                <ul class="nav nav-collapse open">
                                    <li class="nav-item <?php checker($routes, ['coordinator/internship_administration'], 'active') ?>">
                                        <a aria-label="Go to Administration" href="<?php echo e(router('coordinator/internship/administration.index')); ?>" class="collapsed" aria-expanded="false">
                                            <i class="fas fa-file"></i>
                                            <p class="wrap">Student Administration</p>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>


                        <li class="nav-item <?php if ( $routes == 'coordinator/internship_letter' ) { echo "active"; } ?>">
                            <a href="<?php echo e(base_url('/coordinator/internship_letter')); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-envelope"></i>
                                <p class="wrap">Internship Letter</p>
                            </a>
                        </li>

                        <?php if ( $_SESSION['prodi_koor'] == 'LPI' ) : ?>
                        <li class="nav-item <?php if ( $routes == 'profile_jobdesc' ) { echo "active"; } ?>">
                            <a href="index.php?page=profile_jobdesc" class="collapsed" aria-expanded="false">
                                <i class="fas fa-graduation-cap"></i>
                                <p class="wrap">Job Profile</p>
                            </a>
                        </li>                        
                        <?php endif;?>



                        <?php endif; ?>

                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Account</h4>
                        </li>


                        <li class="nav-item <?php checker( $routes, ['lecturer_profile'], 'active' ) ?>">
                            <a aria-label="Go to Lecturer Profile" href="index.php?page=lecturer_profile">
                                <i class="fas fa-user"></i>
                                <p>Lecturer Profile</p>
                            </a>
                        </li>



                        <li class="nav-item">
                            <a aria-label="Logout Button" href="javascript:void(0)" onclick="logout_confirm()">
                                <i class="fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->



        <!-- Modal Logout -->
        <div class="modal fade" id="ModalLogout2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Logout Confirmation</hh5>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to end current session?
                    </div>
                    <form action="index.php?form=logout" method="POST"><?php csrf(); ?>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Yes, take me out!</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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

    <?php echo Notification::renderFooter(); ?> 

    <?php echo $__env->make('partials.core-functionality', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.notifications', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


    <?php echo $__env->yieldPushContent('footer-scripts'); ?>


</body>
</html>
<?php /**PATH C:\laragon2\www\pblKita\views/templates/lecturer.blade.php ENDPATH**/ ?>