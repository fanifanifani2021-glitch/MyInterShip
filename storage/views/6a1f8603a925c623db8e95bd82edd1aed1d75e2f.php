<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page Title -->
    <title><?php echo e($title ?? 'CDC Dashboard'); ?> | <?php echo env('APP_NAME'); ?></title>

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

    <!-- Fullcalendar -->
    <script src="./assets/js/plugin/fullcalendar/fullcalendar.min.js"></script>
    <link rel="stylesheet" href="component/vendor/fullcalendar/dist/fullcalendar.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    
    <?php echo $__env->yieldPushContent('head-scripts'); ?>

    
    <?php
      if(function_exists('show_notification')) {
          show_notification();
      }
    ?>

    <?php echo Notification::renderHead(); ?>


</head>

<body>

    <style>
        .fc-sun {
            color: red;
            /* Mengubah warna font menjadi merah pada hari Minggu */
        }

    </style>

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

                <a href="<?php echo e(router('landing')); ?>" class="logo" aria-label="My Internship Logo">
                    <img src="index.php?viewer=main_logo" alt="navbar brand" class="navbar-brand" style="width: 180px; height: auto;">
                </a>
                <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                        <i class="icon-menu"></i>
                    </span>
                </button>
                <button aria-label="Toggle Sidebar" class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
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


                    <ul class="navbar-nav navbar-left topbar-nav nav-search mr-md-3 align-items-center">


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

                                <span id="count_notification"></span>

                            </a>


                            <ul class='dropdown-menu messages-notif-box animated fadeIn' aria-labelledby='notifDropdown' id=''>

                                <li>
                                    <div class='dropdown-title'>New Notification</div>
                                </li>
                                <li>
                                    <div class='dropdown-title'>You don't have new notification</div>
                                </li>
                            </ul>

                        </li>

                        <!-- Profile -->
                        <li aria-label="Profile Toolbar" class="nav-item dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic form-inline" data-toggle="dropdown" href="#" aria-expanded="false">
                                <div class="avatar-sm">
                                    <!-- <img src="./upload/profile_picture/<?= $_SESSION['profile_picture'] ?>" alt="..." class="avatar-img rounded-circle"> -->
                                    <img src="index.php?viewer=profile_picture" alt="..." class="avatar-img rounded-circle">

                                    <!-- <img src="./assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle"> -->
                                </div>

                                <!-- <label class="float-right ml-2 text-white"><?= $_SESSION['name'] ?></label> -->
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <div class="dropdown-user-scroll scrollbar-outer">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-sm"><img src="./assets/img/profile.jpg" alt="image profile" class="avatar-img rounded"></div>
                                            <div class="u-text">
                                                <h5><?= $_SESSION['name'] ?></h5>
                                                <p class="text-muted">CDC</p><a href="index.php?page=profile" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="index.php">Dashboard</a>
                                        <div class="dropdown-divider"></div>

                                        <a class="dropdown-item" href="index.php?page=intern_student_list">Student Management</a>
                                        <a class="dropdown-item" href="index.php?page=intern_co_management">Coordinator Management</a>
                                        <a class="dropdown-item" href="index.php?page=company_management">Company Management</a>
                                        <a class="dropdown-item" href="index.php?page=announcement_management">Announcement Management</a>
                                        <a class="dropdown-item" href="<?php echo e(router('study_program.management')); ?>">Study Program Management</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="index.php?page=cdc_profile">Profile</a>
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
                            <img aria-label="User's Profile Picture" src="<?php echo e(router('user.profile-picture')); ?>" alt="User's Profile Picture" class="avatar-img rounded-circle">
                        </div>
                        <div class="info">
                            <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                                <span>
                                    <span class="wrap2"><?= $_SESSION['name'] ?></span>
                                    <span class="user-level">CDC</span>
                                </span>
                            </a>
                            <div class="clearfix"></div>

                        </div>
                    </div>
                    <ul class="nav nav-primary">

                        <!-- ================= DASHBOARD ================= -->
                        <li class="nav-item <?php checker($route, ['', 'dashboard'], 'active') ?>">
                            <a href="<?php echo e(router('landing')); ?>">
                                <i class="fas fa-tachometer-alt"></i>
                                <p class="wrap">Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item <?php if($route == 'home'): ?> active <?php endif; ?>">
                            <a href="<?php echo e(router('home')); ?>">
                                <i class="fas fa-home"></i>
                                <p class="wrap">Home</p>
                            </a>
                        </li>


                        <!-- ================= UNIVERSITY ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">University</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['my_university'], 'active') ?>">
                            <a href="<?php echo e(router('cdc/my/university.index')); ?>">
                                <i class="fas fa-building"></i>
                                <p class="wrap">My University</p>
                            </a>
                        </li>


                        <!-- ================= CDC MANAGEMENT ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">CDC Management</h4>
                        </li>

                        <?php if ($_SERVER['UNIVERSITY'] != 'POLIBATAM') : ?>
                            <?php if ($_SESSION['CDC_Data']['id_kampus'] == 1) : ?>

                                <li class="nav-item <?php checker($route, ['university_management'], 'active') ?>">
                                    <a href="<?php echo e(router('cdc/university.index')); ?>">
                                        <i class="fas fa-building"></i>
                                        <p class="wrap">University Management</p>
                                    </a>
                                </li>

                                <li class="nav-item <?php checker($route, ['cdc_management'], 'active') ?>">
                                    <a href="<?php echo e(router('cdc/management.index')); ?>">
                                        <i class="fas fa-user-secret"></i>
                                        <p class="wrap">CDC Account Management</p>
                                    </a>
                                </li>

                            <?php endif; ?>
                        <?php endif; ?>


                        <!-- ================= ACADEMIC ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Academic</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['study_program_management'], 'active') ?>">
                            <a href="<?php echo e(router('study_program.management')); ?>">
                                <i class="fas fa-graduation-cap"></i>
                                <p class="wrap">Study Program Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['lecturer_management'], 'active') ?>">
                            <a href="<?php echo e(router('lecturer.management')); ?>">
                                <i class="fas fa-user"></i>
                                <p class="wrap">Lecturer Management</p>
                            </a>
                        </li>


                        <!-- ================= STUDENT & INTERNSHIP ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Student & Internship</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['intern_student_list'], 'active') ?>">
                            <a href="index.php?page=intern_student_list">
                                <i class="fas fa-users"></i>
                                <p class="wrap">Student Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['internship_management'], 'active') ?>">
                            <a href="<?php echo e(router('cdc.internship.management')); ?>">
                                <i class="fas fa-briefcase"></i>
                                <p class="wrap">Internship Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['intern_co_management'], 'active') ?>">
                            <a href="index.php?page=intern_co_management">
                                <i class="fas fa-users"></i>
                                <p class="wrap">Coordinator Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['cdc/internship_letter'], 'active') ?>">
                            <a href="<?php echo e(router('cdc/internship-letter.management')); ?>">
                                <i class="fas fa-envelope"></i>
                                <p class="wrap">Internship Letter</p>
                            </a>
                        </li>


                        <!-- ================= COMPANY ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Company</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['company_management'], 'active') ?>">
                            <a href="index.php?page=company_management">
                                <i class="fas fa-building"></i>
                                <p class="wrap">Company Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['company_mou_management'], 'active') ?>">
                            <a href="index.php?page=company_mou_management">
                                <i class="fas fa-file-contract"></i>
                                <p class="wrap">Company MoU Management</p>
                            </a>
                        </li>

                        <li class="nav-item <?php checker($route, ['job_offer'], 'active') ?>">
                            <a href="index.php?page=job_offer">
                                <i class="fas fa-briefcase"></i>
                                <p class="wrap">Job Offers</p>
                            </a>
                        </li>


                        <!-- ================= INFORMATION ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Information</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['announcement_management'], 'active') ?>">
                            <a href="<?php echo e(router('cdc.announcement.management')); ?>">
                                <i class="fas fa-newspaper"></i>
                                <p class="wrap">Announcement Management</p>
                            </a>
                        </li>


                        <li class="nav-item <?php checker($route, ['maintenance_schedule_management'], 'active') ?>">
                            <a href="<?php echo e(router('cdc/maintenance/schedule.index')); ?>">
                                <i class="fas fa-calendar-check"></i>
                                <p class="wrap">Maintenance Schedule</p>
                            </a>
                        </li>


                        <!-- ================= SUPPORT & HELPDESK ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Support & Helpdesk</h4>
                        </li>

                        
                        <li class="nav-item <?php checker($route, ['helpdesk', 'helpdesk_tickets'], 'active') ?>">
                            <a href="<?php echo e(router('cdc.helpdesk.index')); ?>">
                                <i class="fas fa-headset"></i>
                                <p class="wrap">Support Tickets</p>
                            </a>
                        </li>

                        
                        <li class="nav-item <?php checker($route, ['helpdesk_categories'], 'active') ?>">
                            <a href="<?php echo e(router('cdc.helpdesk.categories.index')); ?>">
                                <i class="fas fa-tags"></i>
                                <p class="wrap">Ticket Categories</p>
                            </a>
                        </li>



                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Control Panel</h4>
                        </li>

                        <li class="nav-item">
                            <a href="javascript:void(0)" onclick="openPersonificationModal()">
                                <i class="fas fa-user-secret"></i>
                                <p class="wrap">User Personification</p>
                            </a>
                        </li>


                        <!-- ================= ACCOUNT ================= -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Account</h4>
                        </li>

                        <li class="nav-item <?php checker($route, ['cdc_profile'], 'active') ?>">
                            <a href="index.php?page=cdc_profile">
                                <i class="fas fa-user"></i>
                                <p class="wrap">CDC Profile</p>
                            </a>
                        </li>

                        

                        <li class="nav-item">
                            <a href="javascript:void(0)" onclick="logout_confirm()">
                                <i class="fas fa-sign-out-alt"></i>
                                <p class="wrap">Logout</p>
                            </a>
                        </li>

                    </ul>


                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">

            <?php echo $__env->yieldContent('main-content'); ?>



            <div class="modal fade" id="modalPersonification" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold"><i class="fas fa-user-secret mr-2"></i> User Personification</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form id="formPersonification">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="form-label">Personification Category</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="base_type" value="industry" class="selectgroup-input">
                                            <span class="selectgroup-button">Industry (DUDI)</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="base_type" value="campus" class="selectgroup-input">
                                            <span class="selectgroup-button">Campus (University)</span>
                                        </label>
                                    </div>
                                </div>

                                <div id="section-industry" style="display:none;">

                                    <div class="form-group">
                                        <label class="form-label">Industry Role</label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="ind_role" value="hrd" class="selectgroup-input">
                                                <span class="selectgroup-button">HRD</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="ind_role" value="supervisor" class="selectgroup-input">
                                                <span class="selectgroup-button">Supervisor</span>
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <div id="section-campus" style="display:none;">
                                    <div class="form-group">
                                        <label>Select University</label>
                                        <select id="personify-campus" class="form-control" style="width: 100%"></select>
                                    </div>
                                    <div class="form-group" id="campus-role-group" style="display:none;">
                                        <label>Campus Role</label>
                                        <select id="personify-role" class="form-control">
                                            <option value="">-- Select Role --</option>
                                            <option value="student">Student</option>
                                            <option value="lecturer">Lecturer</option>
                                            <option value="coordinator">Program Coordinator</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="section-target" style="display:none;">
                                    <div class="form-group" id="group-prodi" style="display:none;">
                                        <label>Study Program</label>
                                        <select id="personify-prodi" class="form-control" style="width: 100%"></select>
                                    </div>

                                    <div class="form-group">
                                        <label id="label-target">Select Target User</label>
                                        <select id="personify-target" class="form-control" style="width: 100%"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-border" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="btn-personify" disabled>Start Personification</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        
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

    <!-- Atlantis JS -->
    <script src="./assets/js/atlantis.min.js"></script>

    <!-- Fullcalendar -->
    <script src="./assets/js/plugin/fullcalendar/fullcalendar.min.js"></script>

    <!-- Bootstrap Toggle -->
    <script src="./assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            clock_run();

            show_calendar();

        });

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
                let hours2 = hours + 1;
                let minutes = d.getMinutes();
                let seconds = d.getSeconds();


                let time = ((hours < 10 ? "0" : "") + hours) + ' : ' + ((minutes < 10 ? "0" : "") + minutes) + ' : ' + ((seconds < 10 ? "0" : "") + seconds);
                let time2 = ((hours2 < 10 ? "0" : "") + hours2) + ' : ' + ((minutes < 10 ? "0" : "") + minutes) + ' : ' + ((seconds < 10 ? "0" : "") + seconds);

                $("#clock").text(time);
                $("#clock2").text(time);
                $("#clock3").text(time2);

            }, 1000);


        }


        function show_calendar() {
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            // var className = Array('fc-primary', 'fc-danger', 'fc-black', 'fc-success', 'fc-info', 'fc-warning', 'fc-danger-solid', 'fc-warning-solid', 'fc-success-solid', 'fc-black-solid', 'fc-success-solid', 'fc-primary-solid');

            $calendar = $('#calendar');
            $calendar.fullCalendar({
                aspectRatio: 2
            , });
        }


    </script>

    <script>
        $(document).ready(function() {
            // Reset modal on close
            $('#modalPersonification').on('hidden.bs.modal', function () {
                $('#formPersonification')[0].reset();
                $('#section-industry, #section-campus, #section-target, #campus-role-group, #group-prodi').hide();
                $('#personify-target, #personify-campus, #personify-prodi').val(null).trigger('change');
            });
        });

        window.openPersonificationModal = function() {
            $('#modalPersonification').modal('show');
        };

        // Handle Base Type Switch
        $('input[name="base_type"]').on('change', function() {
            const val = $(this).val();
            $('#section-target, #campus-role-group, #group-prodi').hide();
            $('#personify-target').val(null).trigger('change');

            if (val === 'industry') {
                $('#section-industry').fadeIn();
                $('#section-campus').hide();
            } else {
                $('#section-campus').fadeIn();
                $('#section-industry').hide();
                loadCampuses();
            }
        });

        // Handle Industry Role Change
        $('input[name="ind_role"]').on('change', function() {
            $('#section-target').fadeIn();
            $('#group-prodi').hide();
            initTargetSelect($(this).val(), null);
        });

        // Handle Campus Change
        $('#personify-campus').on('change', function() {
            if ($(this).val()) $('#campus-role-group').fadeIn();
            else $('#campus-role-group').hide();
        });

        // Handle Campus Role Change
        $('#personify-role').on('change', function() {
            const role = $(this).val();
            const campusId = $('#personify-campus').val();
            
            $('#section-target').fadeIn();
            $('#personify-target').val(null).trigger('change');

            if (role === 'coordinator') {
                $('#group-prodi').fadeIn();
                loadStudyPrograms(campusId);
            } else {
                $('#group-prodi').hide();
                initTargetSelect(role, campusId);
            }
        });

        // Load Study Programs (For Coordinator)
        function loadStudyPrograms(campusId) {
            $('#personify-prodi').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#modalPersonification'),
                ajax: {
                    url: "<?php echo e(router('ajax.cdc.personify.prodi.list')); ?>", // Create this route
                    type: 'POST',
                    data: params => ({ _token: $('meta[name="csrf-token"]').attr('content'), q: params.term, campus_id: campusId }),
                    processResults: data => ({ results: data })
                }
            });
        }

        // When Prodi selected, auto-load the coordinator in target select
        $('#personify-prodi').on('change', function() {
            const prodiId = $(this).val();
            if (prodiId) {
                initTargetSelect('coordinator', $('#personify-campus').val(), prodiId);
            }
        });


        function initTargetSelect(role, campusId, prodiId = null) {
            if ($('#personify-target').hasClass("select2-hidden-accessible")) {
                $('#personify-target').select2('destroy');
            }

            $('#personify-target').select2({
                theme: 'bootstrap4',
                placeholder: '-- Search User --',
                dropdownParent: $('#modalPersonification'),
                ajax: {
                    url: "<?php echo e(router('ajax.cdc.personify.user.list')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            q: params.term,
                            role: role,
                            campus_id: campusId,
                            prodi_id: prodiId
                        };
                    },
                    processResults: function (data) {
                        // Console log untuk melihat data yang datang dari server
                        return {
                            results: Array.isArray(data) ? data : []
                        };
                    },
                    // Tambahkan ini untuk melihat pesan error asli dari PHP (misal: syntax error)
                    error: function (xhr, status, error) {
                        console.error("AJAX Error: " + status + " - " + error);
                        console.log("Full Server Response:", xhr.responseText);
                    }
                }
            });
        }


        // Global Campus Loader
        function loadCampuses() {
            $.ajax({
                url: "<?php echo e(router('ajax.cdc.personify.campus.list')); ?>",
                type: 'POST',
                dataType: 'json',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    let opt = '<option value="">-- Select University --</option>';
                    
                    // Perbaikan: Pastikan res adalah Array sebelum forEach
                    if (res && Array.isArray(res)) {
                        res.forEach(item => {
                            opt += `<option value="${item.id_kampus}">${item.nama_kampus}</option>`;
                        });
                    } else if (typeof res === 'object' && res !== null) {
                        // Jika server mengirim Object (key-value), konversi manual
                        Object.values(res).forEach(item => {
                            opt += `<option value="${item.id_kampus}">${item.nama_kampus}</option>`;
                        });
                    }
                    
                    $('#personify-campus').html(opt);
                },
                error: function(xhr) {
                    console.error("Failed to load campuses");
                }
            });
        }

        $('#personify-target').on('change', function() {
            $('#btn-personify').prop('disabled', !$(this).val());
        });

        // Process Personification Form Submit
        $('#formPersonification').on('submit', function(e) {
            e.preventDefault();
            
            const targetId = $('#personify-target').val();
            const role     = $('#personify-role').val() || $('input[name="ind_role"]:checked').val();
            const campusId = $('#personify-campus').val();

            if (!targetId || !role) {
                Swal.fire('Error', 'Please select a target user first.', 'error');
                return;
            }

            Swal.fire({
                title: 'Enter Personification Mode?',
                text: "You will be logged in as this user. Your admin session will be saved.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Proceed',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: "<?php echo e(router('ajax.cdc.personify.login')); ?>", // Kita akan buat rute ini
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            target_id: targetId,
                            role: role,
                            campus_id: campusId
                        },
                        dataType: 'json'
                    }).catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error.responseJSON.message || error.statusText}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value.status) {
                    Swal.fire({
                        title: 'Success!',
                        text: result.value.message, // Gunakan pesan dari server
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // REDIRECT DINAMIS: Menggunakan URL dari JSON server
                        if (result.value.target) {
                            window.location.href = result.value.target;
                        } else {
                            window.location.href = "<?php echo e(base_url()); ?>"; // Fallback
                        }
                    });
                }
            });
        });
    </script>

    <?php echo Notification::renderFooter(); ?> 

    <?php echo $__env->make('partials.core-functionality', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.notifications', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldPushContent('footer-scripts'); ?>

</body>
</html><?php /**PATH C:\laragon\www\myinternship\views/templates/cdc.blade.php ENDPATH**/ ?>