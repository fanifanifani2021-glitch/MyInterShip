<?php

use Controller;
use Url;
use Redirect;
use Response;
use Router;
use Session;

defined('BASE_DIR') or die('Direct script access is Not Allowed');

// Halaman Dashboard
// Halaman Dashboard Dispatcher
Router::get('/dashboard', function () {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    // GUNAKAN INI: Session::get akan otomatis cek data personifikasi
    $role = Session::get('role'); 

    if (!$role) {
        Response::redirect(router('landing'));
    } elseif ($role === 'Mahasiswa') {
        // Redirect ke prefix agar Session::get tahu ini area mahasiswa
        Response::redirect(base_url('student'));
    } elseif ($role === 'Dosen') {
        Response::redirect(base_url('lecturer'));
    } elseif (in_array($role, ['UPKPK', 'CDC'])) {
        Response::redirect(base_url('cdc'));
    } elseif (in_array($role, ['industri', 'hrd', 'supervisor'])) {
        Response::redirect(base_url('industry'));
    }
})->name('dashboard')
->meta([
    'title'       => 'Dashboard',
    'description' => 'Main dashboard based on user role',
    'method'      => 'GET',
    'auth'        => true,
    'role'        => ['Mahasiswa', 'Dosen', 'UPKPK', 'industri'],
    'menu'        => true
]);

// Dashboard Mahasiswa
// ================= STUDENT AREA =================
Router::get('/student', function () {
    Controller::run('StudentController/StudentDashboardController', ['method' => 'index']);
})
->name('student.dashboard')
->additionalMiddleware(function() {
    session_status() === PHP_SESSION_NONE ? session_start() : null;
    if (($_SESSION['role'] ?? '') !== 'Mahasiswa') {
        Router::handleError(403, 'Restricted Area: Students Only');
    }
});

// ================= LECTURER AREA =================
Router::get('/lecturer', function () {
    renderView('lecturer.dashboard', ['title' => 'Lecturer Dashboard', 'route' => 'dashboard', 'routes' => 'dashboard']);
})->additionalMiddleware(function() {
    if (Session::get('role') !== 'Dosen') {
        abort(403, 'Restricted Area: Lecturers Only');
    }
});

// ================= INDUSTRY AREA =================
Router::get('/industry', function () {
    renderView('industry.dashboard', ['title' => 'Industry Dashboard', 'route' => 'dashboard', 'routes' => 'dashboard']);
})->additionalMiddleware(function() {
    $role = strtolower(Session::get('role'));
    if (!in_array($role, ['industri', 'hrd', 'supervisor'])) {
        abort(403, 'Restricted Area: Industry Only');
    }
});

// ================= CDC AREA =================
Router::get('/cdc', function () {
    renderView('cdc.dashboard', ['title' => 'CDC Dashboard', 'route' => 'dashboard', 'routes' => 'dashboard']);
})->additionalMiddleware(function() {
    // Khusus CDC, Session::get akan otomatis ambil role ASLI (UPKPK/CDC) 
    // karena URL-nya mengandung '/cdc'
    $role = Session::get('role');
    if (!in_array($role, ['UPKPK', 'CDC'])) {
        abort(403, 'Restricted Area: CDC Admin Only');
    }
});

// Halaman Landing
Router::get('/', function () {
    session_start();
    $role = $_SESSION['role'];

    if (!$role) {
        renderView('landing.home', ['title' => 'Home', 'route' => 'home', 'routes' => 'home']);
    } else {
        redirect( router('dashboard'), 100 );
    }
})
->name('landing')
->meta([
    'title'       => 'Landing Page',
    'description' => 'Public landing page',
    'method'      => 'GET',
    'auth'        => false,
    'menu'        => false
]);


Router::get('/home', function () {
    renderView('landing.home', ['title' => 'Home', 'routes' => 'home']);
})
->name('home')
->meta([
    'title'       => 'Landing Page',
    'description' => 'Public landing page',
    'method'      => 'GET',
    'auth'        => false,
    'menu'        => false
]);


/*
|--------------------------------------------------------------------------
| Authentication & Registration Routes (Unified)
|--------------------------------------------------------------------------
| Parent Group: 'auth'
| Middleware: Guest Check (Redirect ke dashboard jika sudah login)
*/
/*
|--------------------------------------------------------------------------
| Authentication & Registration Routes (Unified Restful)
|--------------------------------------------------------------------------
| Parent Group: 'auth'
| Middleware: Guest Check (Redirect ke dashboard jika sudah login)
*/
Router::group([
    'prefix' => 'auth',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Cek jika sudah login, alihkan ke dashboard
        if (!empty($_SESSION['role'])) {
            make_swal2('info', 'Already Logged In', 'Redirecting to dashboard page...');
            redirect(router('dashboard'), 100);
            exit;
        }   
    }
], function () {

    // =========================================================================
    // 1. LOGIN ROUTES (URL Prefix: auth/login | Name Prefix: login.)
    // =========================================================================
    Router::group(['prefix' => 'login', 'name' => 'login.'], function() {

        # Main Selection Page
        Router::get('/', function () {
            renderView('auth.login', ['title' => 'Login Selection']);
        })
        ->name('index')
        ->meta([
            'title'       => 'Login Selection',
            'description' => 'Main gateway to select user role.',
            'auth'        => false
        ]);

        # 1. Express Login (Admin / Dev Debug)
        Router::get('/express', function () {
            Controller::run('AuthController/ExpressAuthController', ['method' => 'index']);
        })->name('express');

        Router::post('/express/verification', function () {
            Controller::run('AuthController/ExpressAuthController', ['method' => 'verification']);
        })->name('express.verification')->middleware('csrf');

        Router::post('/express', function () {
            Controller::run('AuthController/ExpressAuthController', ['method' => 'login_process']);
        })->name('express.post')->middleware('csrf');


        # 2. Student Login
        Router::get('/student', function () {
            Controller::run('AuthController/StudentAuthController', ['method' => 'index']);
        })->name('student');

        Router::post('/student', function () {
            throttle('AuthController/StudentAuthController', 'login_process', 5, 60, "seconds");
            Controller::run('AuthController/StudentAuthController', ['method' => 'login_process']);
        })->name('student.post')->middleware('csrf');


        # 3. Lecturer Login
        Router::get('/lecturer', function () {
            Controller::run('AuthController/LecturerAuthController', ['method' => 'index']);
        })->name('lecturer');

        Router::post('/lecturer', function () {
            throttle('AuthController/LecturerAuthController', 'login_process', 5, 60, "seconds");
            Controller::run('AuthController/LecturerAuthController', ['method' => 'login_process']);
        })->name('lecturer.post')->middleware('csrf');


        # 4. Industry Login
        Router::get('/industry', function () {
            Controller::run('AuthController/IndustryAuthController', ['method' => 'index']);
        })->name('industry');

        Router::post('/industry', function () {
            throttle('AuthController/IndustryAuthController', 'login_process', 5, 60, "seconds");
            Controller::run('AuthController/IndustryAuthController', ['method' => 'login_process']);
        })->name('industry.post')->middleware('csrf');


        # 5. CDC Staff Login
        Router::get('/cdc', function () {
            Controller::run('AuthController/CDCAuthController', ['method' => 'index']);
        })->name('cdc');

        Router::post('/cdc', function () {
            throttle('AuthController/CDCAuthController', 'login_process', 5, 60, "seconds");
            Controller::run('AuthController/CDCAuthController', ['method' => 'login_process']);
        })->name('cdc.post')->middleware('csrf');

    }); // End Login Group


    // =========================================================================
    // 2. REGISTRATION ROUTES (URL Prefix: auth/register | Name Prefix: register.)
    // =========================================================================
    Router::group(['prefix' => 'register', 'name' => 'register.'], function() {

        Router::get('/student', function () {
            Controller::run('AuthController/StudentRegistrationController', ['method' => 'index']);
        })->name('student.index');

        Router::post('/student', function () {
            throttle('AuthController/StudentRegistrationController', 'submit', 5, 60, "seconds");
            Controller::run('AuthController/StudentRegistrationController', ['method' => 'submit']);
        })->name('student.submit')->middleware('csrf');

    }); // End Register Group

});

// Logout - GET Version (Mudah digunakan di Navbar)
Router::get('/logout', function () {
    throttle('AuthController/LogoutController', 'logout_action', 5, 60, "seconds");
    controller('AuthController/LogoutController', ['method' => 'logout_action']);
})
->name('logout.get')
->meta([
    'title' => 'Logout',
    'auth'  => true
]);

// Logout - POST Version (Direkomendasikan untuk keamanan)
Router::post('/logout', function () {
    throttle('AuthController/LogoutController', 'logout_action', 5, 60, "seconds");
    controller('AuthController/LogoutController', ['method' => 'logout_action']);
})
->middleware('csrf') // Proteksi CSRF
->name('logout.post')
->meta([
    'title'  => 'Logout Process',
    'method' => 'POST',
    'auth'   => true
]);


// Student Identity Routes
Router::group([
    'prefix' => 'student',
    'name'   => 'student.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Cek Login (Authentication)
        if (empty($_SESSION['role'])) {
            // Simpan URL saat ini
            $_SESSION['redirect_intended'] = Url::current();
            
            // Pesan Error
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            
            // Redirect ke Login Mahasiswa (Langsung)
            Redirect::to(router('login.student'), 0);
            exit;
        }

        // 2. Cek Role (Authorization)
        $allowedRoles = ['Mahasiswa', 'Student'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Students only.');
            exit;
        }
    }
], function () {


    # =========================================================================
    # Student Internship Letter Routes
    # =========================================================================
    Router::group([
        'prefix'               => 'internship-letter',
        'middleware'           => ['csrf'],
        'name'                 => 'internship_letter.',
        'additionalMiddleware' => function () {
            session_status() === PHP_SESSION_NONE ? session_start() : null;
            $allowedRoles = ['Mahasiswa', 'Student'];
            if (!in_array($_SESSION['role'] ?? '', $allowedRoles)) {
                Router::handleError(403, 'Access Denied: Restricted to Students only.');
            }
        }
    ], function () {

        // 1. List All Letters
        Router::get('/', function () {
            throttle('student/internship-letter', 'index', 10, 30, 'seconds');
            Controller::run('StudentController/StudentInternshipLetterController', ['method' => 'index']);
        })->name('index');

        // 2. Request New Letter Form
        Router::get('/create', function () {
            throttle('student/internship-letter', 'create', 10, 60, 'seconds');
            Controller::run('StudentController/StudentInternshipLetterController', ['method' => 'create']);
        })->name('create');

        // 3. Detail Letter Page
        Router::get('/detail/{id}', function ($params) {
            Controller::run('StudentController/StudentInternshipLetterController', [
                'method' => 'detail',
                'id'     => $params['id'] ?? null
            ]);
        })->name('detail');

        // 4. Submit New Request (Store)
        Router::post('/store', function () {
            throttle('student/internship-letter', 'store', 2, 60, 'seconds');
            Controller::run('StudentController/StudentInternshipLetterController', ['method' => 'store']);
        })->name('store');

        // 5. Acceptance Response Form
        Router::get('/acceptance/{id}', function ($params) {
            Controller::run('StudentController/StudentInternshipLetterController', [
                'method' => 'acceptanceForm',
                'id'     => $params['id'] ?? null
            ]);
        })->name('acceptance_form');

        // 6. Submit Acceptance Response Action
        Router::post('/acceptance/submit', function () {
            throttle('student/internship-letter', 'submitAcceptance', 5, 60, 'seconds');
            Controller::run('StudentController/StudentInternshipLetterController', ['method' => 'submitAcceptance']);
        })->name('submit_acceptance');

        // 7. Print Letter Document
        Router::get('/print/{id}', function ($params) {
            Controller::run('PrintController/InternshipLetterController', [
                'method' => 'print',
                'id'     => $params['id'] ?? null
            ]);
        })->name('print');

    });


    # =========================================================================
    # Student Helpdesk Routes
    # =========================================================================
    Router::group([
        'prefix'               => 'helpdesk',
        'middleware'           => ['csrf'],
        'name'                 => 'helpdesk.', // Menggunakan student.helpdesk. agar cocok dengan panggilan router('student.helpdesk.x')
        'additionalMiddleware' => function () {
            session_status() === PHP_SESSION_NONE ? session_start() : null;
            if (($_SESSION['role'] ?? null) !== 'Mahasiswa') {
                abort(401, 'Unauthorized access.');
            }
        }
    ], function () {

        // 1. List Tickets
        Router::get('/', function () {
            Controller::run('StudentController/StudentHelpdeskController', ['method' => 'index']);
        })->name('index');

        // 2. Create Ticket Page
        Router::get('/create', function () {
            Controller::run('StudentController/StudentHelpdeskController', ['method' => 'create']);
        })->name('create');

        // 3. Detail Ticket Page
        Router::get('/detail/{id}', function ($params) {
            Controller::run('StudentController/StudentHelpdeskController', [
                'method' => 'detail',
                'id'     => $params['id'] ?? null
            ]);
        })->name('detail');

        // 4. DataTables Endpoint
        Router::post('/datatable', function () {
            Controller::run('StudentController/StudentHelpdeskController', ['method' => 'data']);
        })->name('datatable');

        // 5. Submit Ticket Baru (Store)
        Router::post('/store', function () {
            throttle('student/helpdesk', 'store', 10, 60, 'seconds');
            Controller::run('StudentController/StudentHelpdeskController', ['method' => 'store']);
        })->name('store');

        // 6. Submit Reply / Balasan Diskusi Tiket (DITAMBAHKAN)
        Router::post('/reply', function () {
            throttle('student/helpdesk', 'reply', 30, 60, 'seconds');
            Controller::run('StudentController/StudentHelpdeskController', ['method' => 'reply']);
        })->name('reply');

    });


    # =========================================================================
    # Student Identity & Settings Routes (Dedicated Pages)
    # =========================================================================
    Router::group([
        'prefix'               => 'identity',
        'middleware'           => ['csrf'],
        'name'                 => 'identity.',
        'additionalMiddleware' => function () {
            session_status() === PHP_SESSION_NONE ? session_start() : null;
            $allowedRoles = ['Mahasiswa', 'Student'];
            if (!in_array($_SESSION['role'] ?? '', $allowedRoles)) {
                Router::handleError(403, 'Access Denied: Restricted to Students only.');
            }
        }
    ], function () {

        // 1. Identity Main Page (Data Diri & Dokumen Magang)
        Router::get('/', function () {
            Controller::run('StudentController/StudentIdentityController', ['method' => 'index']);
        })->name('index');

        Router::post('/save-identity', function () {
            throttle('student/identity', 'saveIdentity', 10, 60, 'seconds');
            Controller::run('StudentController/StudentIdentityController', ['method' => 'saveIdentity']);
        })->name('save_identity');

        Router::post('/save-document', function () {
            throttle('student/identity', 'saveDocument', 10, 60, 'seconds');
            Controller::run('StudentController/StudentIdentityController', ['method' => 'saveDocument']);
        })->name('save_document');


        // 2. Profile Picture Page
        Router::get('/avatar', function () {
            Controller::run('StudentController/StudentIdentityController', ['method' => 'avatar']);
        })->name('avatar');

        Router::post('/save-avatar', function () {
            throttle('student/identity', 'saveAvatar', 10, 60, 'seconds');
            Controller::run('StudentController/StudentIdentityController', ['method' => 'saveAvatar']);
        })->name('save_avatar');

        Router::delete('/delete-avatar', function () {
            Controller::run('StudentController/StudentIdentityController', ['method' => 'deleteAvatar']);
        })->name('delete_avatar');


        // 3. Local Account & Password Page
        Router::get('/security', function () {
            Controller::run('StudentController/StudentIdentityController', ['method' => 'security']);
        })->name('security');

        Router::post('/save-local-account', function () {
            Controller::run('StudentController/StudentIdentityController', ['method' => 'saveLocalAccount']);
        })->name('save_local_account');

        Router::post('/change-password', function () {
            throttle('student/identity', 'changePassword', 5, 60, 'seconds');
            Controller::run('StudentController/StudentIdentityController', ['method' => 'changePassword']);
        })->name('change_password');

    });
    # End Student Identity Management


    # =========================================================================
    # Student Internship Registration Routes (Cooperation)
    # =========================================================================
    Router::group([
        'prefix'               => 'register-internship',
        'middleware'           => ['csrf'],
        'name'                 => 'register_internship.',
        'additionalMiddleware' => function () {
            session_status() === PHP_SESSION_NONE ? session_start() : null;
            $allowedRoles = ['Mahasiswa', 'Student'];
            if (!in_array($_SESSION['role'] ?? '', $allowedRoles)) {
                Router::handleError(403, 'Access Denied: Restricted to Students only.');
            }
        }
    ], function () {

        // 1. Halaman Form Pendaftaran Magang Kerjasama
        Router::get('/cooperation', function () {
            throttle('student/register-cooperation', 'cooperation', 10, 60, 'seconds');
            Controller::run('StudentController/RegisterCooperationController', ['method' => 'create']);
        })->name('cooperation');

        // 2. Submit Pendaftaran Magang Kerjasama
        Router::post('/cooperation/store', function () {
            throttle('student/register-cooperation', 'store', 2, 60, 'seconds');
            Controller::run('StudentController/RegisterCooperationController', ['method' => 'store']);
        })->name('cooperation_store');

    });


    # Student Internship
    Router::get('/internship', function () {
        Controller::run('StudentController/StudentInternshipController', [
            'method' => 'index'
        ]);
    })
    ->name('internship')
    ->meta([
        'title'       => 'My Internship',
        'description' => 'Overview of student internship details and status.',
        'method'      => 'GET',
        'auth'        => true,
        'roles'       => ['Mahasiswa', 'Student']
    ]);

    Router::get('/internship/{id}', function ($params) {
        throttle('StudentController/StudentInternshipController', 'detail', 100, 60, 'seconds');
        Controller::run('StudentController/StudentInternshipController', [
            'method' => 'detail', 
            'id'     => $params['id']
        ]);
    })
    ->name('internship.detail')
    ->meta([
        'title'       => 'Internship Detail',
        'description' => 'Overview of student internship details and status.',
        'method'      => 'GET',
        'auth'        => true,
        'roles'       => ['Mahasiswa', 'Student']
    ]);

});


Router::group([
    'prefix' => 'student/internship/{id}/logbook',
    'name'   => 'student.logbook.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Cek Login (Authentication)
        if (empty($_SESSION['role'])) {
            // Simpan URL saat ini
            $_SESSION['redirect_intended'] = Url::current();
            
            // Pesan Error
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            
            // Redirect ke Login Mahasiswa (Langsung)
            Redirect::to(router('login.student'), 0);
            exit;
        }

        // 2. Cek Role (Authorization)
        $allowedRoles = ['Mahasiswa', 'Student'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Students only.');
            exit;
        }
    }
], function() {

    # Index Logbook (List)
    Router::get('/', function ($params) {
        controller('StudentController/LogbookController', ['method' => 'index', 'id' => $params['id']]);
    })->name('index');

    # Tambah Logbook (Form)
    Router::get('/add', function ($params) {
        controller('StudentController/LogbookController', ['method' => 'add', 'id' => $params['id']]);
    })->name('add');

    # Tambah Logbook (Process)
    Router::post('/store', function ($params) {
        throttle('StudentController/LogbookController', 'store', 10, 60, 'seconds');
        controller('StudentController/LogbookController', ['method' => 'store', 'id' => $params['id']]);
    })->name('store');


    # Edit Logbook (Form) - Menggunakan 2 ID terenkripsi
    Router::get('/edit/{logbook_id}', function ($params) {
        controller('StudentController/LogbookController', [
            'method' => 'edit', 
            'id' => $params['id'], 
            'logbook_id' => $params['logbook_id']
        ]);
    })->name('edit');

    Router::put('/update/{logbook_id}', function ($params) {
        controller('StudentController/LogbookController', [
            'method' => 'update', 
            'id' => $params['id'], 
            'logbook_id' => $params['logbook_id']
        ]);
    })->name('update');

    # Detail Logbook
    Router::get('/detail/{logbook_id}', function ($params) {
        controller('StudentController/LogbookController', [
            'method' => 'detail', 
            'id' => $params['id'], 
            'logbook_id' => $params['logbook_id']
        ]);
    })->name('detail');


    # Hapus Logbook (Proses POST)
    Router::post('/delete/{logbook_id}', function ($params) {

        throttle('StudentController/LogbookController', 'delete', 20, 60, 'seconds');
        controller('StudentController/LogbookController', [
            'method'     => 'delete', 
            'id'         => $params['id'], 
            'logbook_id' => $params['logbook_id']
        ]);
    })->name('delete');



});


Router::group([
    'prefix' => 'student/internship/{id}/attendance',
    'name'   => 'student.attendance.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Authentication Check
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            Redirect::to(router('login.student'), 0);
            exit;
        }

        // 2. Authorization Check
        $allowedRoles = ['Mahasiswa', 'Student'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Students only.');
            exit;
        }
    }
], function() {

    # Index Attendance (Calendar View)
    Router::get('/', function ($params) {
        controller('StudentController/AttendanceController', ['method' => 'index', 'id' => $params['id']]);
    })->name('index');

    # Check In (Form/Process)
    Router::get('/check-in', function ($params) {
        controller('StudentController/AttendanceController', ['method' => 'checkIn', 'id' => $params['id']]);
    })->name('checkIn');

    Router::post('/check-in', function ($params) {
        throttle('StudentController/AttendanceController', 'checkIn-store', 5, 60);
        controller('StudentController/AttendanceController', ['method' => 'checkIn-store', 'id' => $params['id']]);
    })->name('checkIn-store');


    # Check Out (Process)
    # Check Out (Process) dengan Optional ID
    Router::get('/check-out/{?attendance_id}', function ($params) {
        controller('StudentController/AttendanceController', [
            'method' => 'checkOut', 
            'id'     => $params['id'],
            'attendance_id' => $params['attendance_id'] ?? null // Ambil dari params router
        ]);
    })->name('checkOut');


    # Check Out (Process Store)
    Router::post('/check-out/{?attendance_id}', function ($params) {
        // Throttle untuk mencegah spam klik
        throttle('StudentController/AttendanceController', 'checkOut-store', 20, 60);
        
        controller('StudentController/AttendanceController', [
            'method' => 'checkOut-store', 
            'params' => $params,
            'id'     => $params['id'],
            'attendance_id' => $params['attendance_id'] ?? null // Tangkap ID opsional dari rute
        ]);
    })->name('checkOut-store');


    # Detail Attendance (Termasuk data lokasi GPS)
    Router::get('/detail/{attendance_id}', function ($params) {
        controller('StudentController/AttendanceController', [
            'method' => 'detail', 
            'id' => $params['id'], 
            'attendance_id' => $params['attendance_id']
        ]);
    })->name('detail');




    # 2. Real-time Check Out
    Router::post('/check-out/{attendance_id}', function ($params) {
        controller('StudentController/AttendanceController', ['method' => 'check_out', 'id' => $params['id'], 'attendance_id' => $params['attendance_id']]);
    })->name('check_out');

    # 3. Manual Add History
    Router::post('/store', function ($params) {
        controller('StudentController/AttendanceController', ['method' => 'store', 'id' => $params['id']]);
    })->name('store');


    # Add History (Manual Attendance)
    Router::get('/add-history', function ($params) {
        controller('StudentController/AttendanceController', ['method' => 'addHistory', 'id' => $params['id']]);
    })->name('addHistory');

    Router::post('/add-history', function ($params) {
        throttle('StudentController/AttendanceController', 'addHistory-store', 5, 60);
        controller('StudentController/AttendanceController', ['method' => 'addHistory-store', 'id' => $params['id']]);
    })->name('addHistory-store');

    
    /* # Hapus Attendance (Dinonaktifkan sesuai permintaan)
    Router::post('/delete/{attendance_id}', function ($params) {
        controller('StudentController/AttendanceController', [
            'method' => 'delete', 
            'id' => $params['id'], 
            'attendance_id' => $params['attendance_id']
        ]);
    })->name('delete');
    */

});


Router::group([
    'prefix' => 'student/internship/{id}/final-report', // Prefix dasar internship mahasiswa
    'name'   => 'student.final_report.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Authentication Check
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            Redirect::to(router('login.student'), 0);
            exit;
        }

        // 2. Authorization Check
        $allowedRoles = ['Mahasiswa', 'Student'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Students only.');
            exit;
        }
    }
], function() {
    // Rute untuk Final Report
    Router::get('/', function ( $params ) {
        controller('StudentController/FinalReportController', ['method' => 'index', 'id' => $params['id']]);
    })->name('index');

    Router::post('final-report', function ($params) {
        throttle('StudentController/FinalReportController', 'store', 5, 60);
        controller('StudentController/FinalReportController', ['method' => 'store', 'id' => $params['id']]);
    })->name('store');
});


Router::group([
    'prefix' => 'student/internship/{id}/student-feedback',
    'name'   => 'student.feedback.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Authentication & Authorization (Sesuai kode Gilang)
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            Redirect::to(router('login.student'), 0); exit;
        }

        // 2. Authorization Check
        $allowedRoles = ['Mahasiswa', 'Student'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Students only.');
            exit;
        }

    }
], function() {
    Router::get('/', function ($params) {
        controller('StudentController/FeedbackController', ['method' => 'index', 'id' => $params['id']]);
    })->name('index');
    Router::post('/store', function ($params) {
        throttle('StudentController/FeedbackController', 'store', 5, 60);
        controller('StudentController/FeedbackController', ['method' => 'store', 'id' => $params['id']]);
    })->name('store');

    Router::get('/download', function ($params) {
        throttle('StudentController/FeedbackController', 'download', 5, 60);
        controller('StudentController/FeedbackController', ['method' => 'download', 'id' => $params['id']]);
    })->name('download');
});



Router::group([
    'prefix' => 'student/internship/{id}/industry-feedback',
    'name'   => 'student.industry-feedback.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Authentication & Authorization (Sesuai kode Gilang)
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            Redirect::to(router('login.student'), 0); exit;
        }

                // 2. Authorization Check
        $allowedRoles = ['Mahasiswa', 'Student'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Students only.');
            exit;
        }
    }
], function() {
    Router::get('/', function ($params) {
        controller('StudentController/IndustryFeedbackController', ['method' => 'index', 'id' => $params['id']]);
    })->name('index');
    Router::post('/store', function ($params) {
        throttle('StudentController/IndustryFeedbackController', 'store', 5, 60);
        controller('StudentController/IndustryFeedbackController', ['method' => 'store', 'id' => $params['id']]);
    })->name('store');

    Router::get('/download/{language}', function ($params) {
        throttle('StudentController/IndustryFeedbackController', 'download_word', 5, 60);
        controller('StudentController/IndustryFeedbackController', ['method' => 'download_word', 'id' => $params['id'], 'language' => $params['language']]);
    })->name('download');
});



Router::group([
    'prefix' => 'student/internship/{id}/discussion',
    'name'   => 'student.discussion.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            Response::redirect(router('login.student'))->send(); exit;
        }

        // 2. Authorization Check
        $allowedRoles = ['Mahasiswa', 'Student'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Students only.');
            exit;
        }
    }
], function() {
    // 1. Halaman Daftar Topik Diskusi
    Router::get('/', function ($params) {
        controller('StudentController/DiscussionController', ['method' => 'index', 'id' => $params['id']]);
    })->name('index');

    // 2. Halaman Bergabung/Detail Diskusi
    Router::get('/join/{discussion_id}', function ($params) {
        controller('StudentController/DiscussionController', [
            'method' => 'join', 
            'id' => $params['id'], 
            'discussion_id' => $params['discussion_id']
        ]);
    })->name('join');

    // 3. Halaman Ringkasan Komentar
    Router::get('/summary/{comment_id}', function ($params) {
        controller('StudentController/DiscussionController', [
            'method' => 'summary', 
            'id' => $params['id'], 
            'comment_id' => $params['comment_id']
        ]);
    })->name('summary');


    // --- ACTION ROUTES ---
    Router::post('/store', function ($params) {
        throttle('StudentController/DiscussionController', 'store', 10, 60, 'seconds');
        controller('StudentController/DiscussionController', [
            'method' => 'store', 
            'id' => $params['id'],
        ]);
    })->name('store');

    Router::put('/update', function ($params) {
        throttle('StudentController/DiscussionController', 'update', 10, 60, 'seconds');
        controller('StudentController/DiscussionController', [
            'method' => 'update', 
            'id' => $params['id'],
        ]);
    })->name('update');

    Router::delete('/delete', function ($params) {
        throttle('StudentController/DiscussionController', 'destroy', 10, 60, 'seconds');
        controller('StudentController/DiscussionController', [
            'method' => 'destroy', 
            'id' => $params['id'],
        ]);
    })->name('delete');

    Router::put('/reply', function ($params) {
        throttle('StudentController/DiscussionController', 'reply', 10, 60, 'seconds');
        controller('StudentController/DiscussionController', [
            'method' => 'reply', 
            'id' => $params['id'],
        ]);
    })->name('reply');

});





// Announcement Routes
Router::group([
    'prefix' => 'announcement',
    'name'   => 'announcement.'
], function () {

    # 1. List Semua Pengumuman (Page View)
    Router::get('/', function () {
        Controller::run('LandingController/PublicAnnouncementController', ['method' => 'index']);
    })
    ->name('index')
    ->meta([
        'title'       => 'Announcement List',
        'description' => 'Browse all public announcements and news updates.',
        'method'      => 'GET',
        'auth'        => false, // Public Access
        'tags'        => ['Landing', 'Announcement']
    ]);

    # 2. Detail Pengumuman (Page View)
    Router::get('/detail/{id}', function ($params) {
        Controller::run('LandingController/PublicAnnouncementController', [
            'method' => 'detail',
            'id'     => $params['id'] ?? null
        ]);
    })
    ->name('detail')
    ->meta([
        'title'       => 'Announcement Detail',
        'description' => 'View full content and details of a specific announcement.',
        'method'      => 'GET',
        'auth'        => false, // Public Access
        'tags'        => ['Landing', 'Announcement']
    ]);

});

Router::group([
    'prefix' => 'lecturer/my/student/',
    'name'   => 'lecturer/my/student.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Cek Login
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            // Redirect ke Login Dosen
            Redirect::to(router('login.lecturer'), 0);
            exit;
        }

        // 2. Cek Role
        $allowedRoles = ['Dosen', 'Lecturer'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Lecturers/Coordinators only.');
            exit;
        }
    }
], function () {


    # 1. Index / List Surat
    Router::get('/', function () {
        controller('LecturerController/MyStudentController', ['method' => 'index']);
    })
    ->name('index')
    ->meta([
        'title'       => 'My Student',
        'description' => 'Manage student internship for Lecturer.',
        'method'      => 'GET',
        'auth'        => true,
        'roles'       => ['Dosen', 'Lecturer']
    ]);


    # 2. Add Supervised Student (POST)
    # URL: /lecturer/my/student/add
    Router::get('/add', function () {
        // Pindahkan throttle ke sini agar dieksekusi sebelum controller
        throttle('LecturerController/MyStudentController', 'add', 5, 5, "seconds");
        controller('LecturerController/MyStudentController', [
            'method' => 'add'
        ]);
    })->name('add');

    Router::post('/add', function () {
        // Pindahkan throttle ke sini agar dieksekusi sebelum controller
        throttle('LecturerController/MyStudentController', 'store', 5, 5, "seconds");
        controller('LecturerController/MyStudentController', [
            'method' => 'store'
        ]);
    })->name('store');



    Router::post('/details', function () {
        // Pindahkan throttle ke sini agar dieksekusi sebelum controller
        throttle('LecturerController/MyStudentController', 'details', 5, 5, "seconds");
        controller('LecturerController/MyStudentController', [
            'method' => 'details'
        ]);
    })->name('details');


    # 3. Student Detail (Profil + History)
    # URL: /lecturer/my/student/{nim}/detail
    Router::get('/{nim}/detail', function ($params) {
        controller('LecturerController/MyStudentController', [
            'method' => 'detail',
            'nim'    => $params['nim']
        ]);
    })->name('detail');

    # 5. Reset Password Mahasiswa
    Router::post('/reset-password', function () {
        controller('LecturerController/MyStudentController', ['method' => 'reset_password']);
    })->name('reset_password');

    # 6. Batalkan Bimbingan (Unassign)
    Router::post('/cancel-supervision', function () {
        controller('LecturerController/MyStudentController', ['method' => 'cancel_supervision']);
    })->name('cancel_supervision');


    # Form Tambah Magang
    Router::get('{nim}/internship/add', function ($params) {
        controller('LecturerController/InternshipController', ['method' => 'add_internship', 'nim' => $params['nim']]);
    })->name('internship.add');

    # Proses Simpan Magang
    Router::post('{nim}/internship/store', function ($params) {
        controller('LecturerController/InternshipController', ['method' => 'store_internship', 'nim' => $params['nim']]);
    })->name('internship.store');


    # Form Edit Magang (Halaman Terpisah)
    Router::get('/{nim}/internship/{id_internship}/edit', function ($params) {
        controller('LecturerController/InternshipController', [
            'method'        => 'edit_internship',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship']
        ]);
    })->name('internship.edit');

    # Proses Update Magang
    Router::put('/{nim}/internship/{id_internship}/update', function ($params) {
        controller('LecturerController/InternshipController', [
            'method'        => 'update_internship',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship']
        ]);
    })->name('internship.update');

    # 4. Specific Internship Detail
    Router::get('/{nim}/internship/{id_internship}', function ($params) {
        controller('LecturerController/InternshipController', [
            'method'        => 'internship_detail',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship']
        ]);
    })->name('internship.detail');


    # Discussion Forum Index
    Router::get('/{nim}/internship/{id_internship}/discussion', function ($params) {
        controller('LecturerController/DiscussionController', [
            'method'        => 'index',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship']
        ]);
    })->name('internship.discussion');

    # Discussion Forum Store
    Router::post('/{nim}/internship/{id_internship}/discussion/store', function ($params) {
        controller('LecturerController/DiscussionController', [
            'method'        => 'store',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship']
        ]);
    })->name('internship.discussion.store');

    # 3. Destroy Topic (Hapus Topik)
    Router::delete('/{nim}/internship/{id_internship}/discussion/{id_discussion}/destroy', function ($params) {
        controller('LecturerController/DiscussionController', [
            'method'        => 'destroy',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship'],
            'id_discussion' => $params['id_discussion']
        ]);
    })->name('internship.discussion.destroy');

    Router::put('/{nim}/internship/{id_internship}/discussion/{id_discussion}/update', function ($params) {
        controller('LecturerController/DiscussionController', [
            'method'        => 'update',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship'],
            'id_discussion' => $params['id_discussion']
        ]);
    })->name('internship.discussion.update');


    # -------------------------------------------------------------------------
    # DISCUSSION DETAIL & COMMENT ROUTES
    # -------------------------------------------------------------------------

    # 1. Join / View Discussion Detail
    Router::get('/{nim}/internship/{id_internship}/discussion/{id_discussion}/join', function ($params) {
        controller('LecturerController/DiscussionController', [
            'method'        => 'join',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship'],
            'id_discussion' => $params['id_discussion']
        ]);
    })->name('internship.discussion.join');

    # 2. Store New Comment (AJAX/POST)
    Router::post('/{nim}/internship/{id_internship}/discussion/{id_discussion}/comment', function ($params) {
        controller('LecturerController/DiscussionController', [
            'method'        => 'comment_store', // Pastikan method ini ada di controller
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship'],
            'id_discussion' => $params['id_discussion']
        ]);
    })->name('internship.discussion.comment_store');
    
    # 3. Destroy Comment
    Router::delete('/{nim}/internship/{id_internship}/discussion/{id_discussion}/comment/{id_comment}/destroy', function ($params) {
        controller('LecturerController/DiscussionController', [
            'method'        => 'comment_destroy',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship'],
            'id_discussion' => $params['id_discussion'],
            'id_comment'    => $params['id_comment']
        ]);
    })->name('internship.discussion.comment_destroy');


    # 4. Update Comment
    Router::put('/{nim}/internship/{id_internship}/discussion/{id_discussion}/comment/{id_comment}/update', function ($params) {
        controller('LecturerController/DiscussionController', [
            'method'        => 'comment_update',
            'nim'           => $params['nim'],
            'id_internship' => $params['id_internship'],
            'id_discussion' => $params['id_discussion'],
            'id_comment'    => $params['id_comment']
        ]);
    })->name('internship.discussion.comment_update');

});

# Group untuk fitur Company List (Lecturer Side)
Router::group([
    'prefix' => 'lecturer/company',
    'name'   => 'lecturer.company.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['role'])) {
            Redirect::to(router('login.lecturer'), 0); exit;
        }
        $allowedRoles = ['Dosen', 'Lecturer'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied'); exit;
        }
    }
], function() {
    
    # URL: /lecturer/company/list
    Router::get('/list', function () {
        controller('LecturerController/CompanyController', ['method' => 'index']);
    })->name('index');

    # URL: /lecturer/company/detail/{id}
    Router::get('/detail/{id}', function ($params) {
        controller('LecturerController/CompanyController', [
            'method' => 'detail',
            'id'     => $params['id']
        ]);
    })->name('detail');

});



/*
|--------------------------------------------------------------------------
| Internship Coordinator Routes
|--------------------------------------------------------------------------
|
| Kumpulan route yang hanya dapat diakses oleh pengguna
| dengan role Dosen atau Lecturer.  
| - Middleware utama melakukan validasi role user
| - Seluruh route di dalam group ini bersifat protected
| - Beberapa sub-module memiliki middleware tambahan
*/
# Internship Letter Management
Router::group([
    'prefix' => 'coordinator/internship_letter',
    'name'   => 'coordinator/internship-letter.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Cek Login
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            // Redirect ke Login Dosen
            Redirect::to(router('login.lecturer'), 0);
            exit;
        }

        // 2. Cek Role
        $allowedRoles = ['Dosen', 'Lecturer'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Lecturers/Coordinators only.');
            exit;
        }
    }
], function () {

    # 1. Index / List Surat
    Router::get('/', function () {
        controller('CoordinatorController/InternshipLetter', ['method' => 'index']);
    })
    ->name('management')
    ->meta([
        'title'       => 'Internship Letter Management',
        'description' => 'Manage student internship application letters.',
        'method'      => 'GET',
        'auth'        => true,
        'roles'       => ['Dosen', 'Lecturer']
    ]);

    # 2. Detail Surat
    Router::get('/detail/{id}', function ($params) {
        controller('CoordinatorController/InternshipLetter', [
            'method' => 'detail', 
            'id'     => $params['id']
        ]);
    })
    ->name('management.detail') // FIX: Nama dibedakan agar tidak bentrok
    ->meta([
        'title'       => 'Internship Letter Detail',
        'description' => 'View details of a specific internship letter.',
        'method'      => 'GET',
        'auth'        => true,
        'roles'       => ['Dosen', 'Lecturer']
    ]);

});


Router::group([
    'prefix' => 'coordinator/internship/administration',
    'name'   => 'coordinator/internship/administration.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Cek Login
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            // Redirect ke Login Dosen
            Redirect::to(router('login.lecturer'), 0);
            exit;
        }

        // 2. Cek Role
        $allowedRoles = ['Dosen', 'Lecturer'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Lecturers/Coordinators only.');
            exit;
        }
    }
], function () {

    # 1. Index / List Surat
    Router::get('/', function () {
        controller('CoordinatorController/InternshipAdministration', ['method' => 'index']);
    })
    ->name('index')
    ->meta([
        'title'       => 'Internship Administration',
        'description' => 'Manage student internship administration.',
        'method'      => 'GET',
        'auth'        => true,
        'roles'       => ['Dosen', 'Lecturer']
    ]);

});



/*
|--------------------------------------------------------------------------
| Student Internship Routes
|--------------------------------------------------------------------------
|
| Kumpulan route yang hanya dapat diakses oleh pengguna
| dengan role Mahasiswa atau Student.  
| - Middleware utama melakukan validasi role user
| - Seluruh route di dalam group ini bersifat protected
| - Beberapa sub-module memiliki middleware tambahan
*/
Router::group([
    'prefix' => 'student/internship_offer',
    'name'   => 'student/internship_offer.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Cek Login (Authentication)
        if (empty($_SESSION['role'])) {
            // Simpan URL saat ini
            $_SESSION['redirect_intended'] = Url::current();
            
            // Pesan Error
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            
            // Redirect ke Login Mahasiswa (Langsung)
            Redirect::to(router('login.student'), 0);
            exit;
        }

        // 2. Cek Role (Authorization)
        $allowedRoles = ['Mahasiswa', 'Student'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Students only.');
            exit;
        }
    }
], function() {

    # List Lowongan Magang
    Router::get('/', function () {
        Controller::run('StudentController/InternshipOfferController', ['method' => 'index']);
    })
    ->name('list')
    ->meta([
        'title'       => 'Internship Offers',
        'description' => 'Browse available internship opportunities for students.',
        'method'      => 'GET',
        'auth'        => true, // Protected
        'roles'       => ['Mahasiswa', 'Student']
    ]);


    Router::get('/detail/{id}', function ($params) {
    
        throttle('StudentController/InternshipOfferController', 'detail', 5, 60, "seconds");

        Controller::run('StudentController/InternshipOfferController', [
            'method' => 'detail',
            'id'     => $params['id']
        ]);
    })->name('detail')
    ->meta([
        'title'       => 'Internship Offers Detail',
        'description' => 'Detail of Selected Internship Offer for students.',
        'method'      => 'GET',
        'auth'        => true, // Protected
        'roles'       => ['Mahasiswa', 'Student']
    ]);

});



/*
|--------------------------------------------------------------------------
| CDC Access Menu Routes
|--------------------------------------------------------------------------
| 
| Kumpulan route yang hanya dapat diakses oleh pengguna
| dengan role UPKPK atau CDC.
|
| - Middleware utama melakukan validasi role user
| - Seluruh route di dalam group ini bersifat protected
| - Beberapa sub-module memiliki middleware tambahan
|   sesuai kebutuhan (contoh: kampus utama saja)
|
| Modules included:
| - My University
| - University Management (Polibatam Only)
| - CDC Management (Polibatam Only)
| - Internship Letter (Coordinator & CDC)
| - Study Program Management
|
*/
Router::group([
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Cek Login
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            // Redirect ke Login CDC
            Redirect::to(router('login.cdc'), 0);
            exit;
        }

        // 2. Cek Role
        $allowedRoles = ['UPKPK', 'CDC'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'You do not have permission to access this module.');
            exit;
        }
    }
], function() {


    // Maintenance Schedule
    Router::group([
        'prefix' => 'cdc/maintenance/schedule',
        'name' => 'cdc/maintenance/schedule.',
    ], function() {

        Router::get('/', function () {
            controller('CDCController/MaintenanceScheduleManagement', ['method' => 'index']);
        })->name('index');

    });
    
    
    # =========================================================================
    # Helpdesk & Category Management Routes (CDC Side)
    # =========================================================================
    Router::group([
        'prefix' => 'cdc/helpdesk',
        'name'   => 'cdc.helpdesk.',
    ], function () {

        # --- CATEGORY MANAGEMENT ---
        Router::group([
            'prefix' => 'categories',
            'name'   => 'categories.'
        ], function () {
            
            Router::get('/', function () {
                Controller::run('CDCController/HelpdeskCategoryController', ['method' => 'index']);
            })->name('index');

            Router::get('/add', function () {
                Controller::run('CDCController/HelpdeskCategoryController', ['method' => 'create']);
            })->name('add');

            Router::post('/store', function () {
                throttle('CDCController/HelpdeskCategoryController', 'store', 10, 60, 'seconds');
                Controller::run('CDCController/HelpdeskCategoryController', ['method' => 'store']);
            })->name('store')->middleware('csrf');

            Router::get('/edit/{id}', function ($params) {
                Controller::run('CDCController/HelpdeskCategoryController', [
                    'method' => 'edit',
                    'id'     => $params['id']
                ]);
            })->name('edit');

            Router::put('/update/{id}', function ($params) {
                throttle('CDCController/HelpdeskCategoryController', 'update', 10, 60, 'seconds');
                Controller::run('CDCController/HelpdeskCategoryController', [
                    'method' => 'update',
                    'id'     => $params['id']
                ]);
            })->name('update')->middleware('csrf');

            Router::post('/toggle-status/{id}', function ($params) {
                Controller::run('CDCController/HelpdeskCategoryController', [
                    'method' => 'toggleStatus',
                    'id'     => $params['id']
                ]);
            })->name('toggle_status')->middleware('csrf');

        }); // End Categories Group


        # --- TICKET MANAGEMENT (CDC) ---
        // 1. Main Dashboard List
        Router::get('/', function () {
            Controller::run('CDCController/HelpdeskTicketController', ['method' => 'index']);
        })->name('index');

        // 2. DataTables Server-side
        Router::post('/datatable', function () {
            Controller::run('CDCController/HelpdeskTicketController', ['method' => 'data']);
        })->name('datatable');

        // 3. View Single Ticket Thread & Replies
        Router::get('/detail/{id}', function ($params) {
            Controller::run('CDCController/HelpdeskTicketController', [
                'method' => 'detail',
                'id'     => $params['id']
            ]);
        })->name('detail');

        // 4. Post Reply to Ticket
        Router::post('/reply/{id}', function ($params) {
            throttle('CDCController/HelpdeskTicketController', 'reply', 10, 60, 'seconds');
            Controller::run('CDCController/HelpdeskTicketController', [
                'method' => 'reply',
                'id'     => $params['id']
            ]);
        })->name('reply')->middleware('csrf');

        // 5. Update Ticket Status or Priority
        Router::post('/update-status/{id}', function ($params) {
            Controller::run('CDCController/HelpdeskTicketController', [
                'method' => 'updateStatus',
                'id'     => $params['id']
            ]);
        })->name('update_status')->middleware('csrf');

    });




    # =========================================================================
    # 1. CDC Web View Route
    # =========================================================================
    Router::group([
        'prefix' => 'cdc/announcement',
        'name'   => 'cdc.announcement.',
    ], function () {

        Router::get('/management', function () {
            Controller::run('CDCController/AnnouncementController', ['method' => 'index']);
        })->name('management');


        // 2. Halaman Content Manager (Tambah & Edit dengan CKEditor)
        Router::get('/content-manager/{?id}', function ($params) {
            Controller::run('CDCController/AnnouncementController', [
                'method' => 'content_manager',
                'id'     => $params['id'] ?? null
            ]);
        })->name('content_manager');

    });


    

    
    // My University
    Router::group([
        'prefix' => 'cdc/my/university',
        'name' => 'cdc/my/university.',
    ], function() {

        Router::get('/', function () {
            renderView('cdc.university_management.my_university', ['title' => 'My University', 'route' => 'my_university']);
        })->name('index');

        Router::post('/', function () {
            throttle('MyUniversity', 'save', 5, 60, 'seconds');
            controller('MyUniversity',[]);
        })->name('post')->middleware('csrf');

    });


    // University Management
    Router::group([
        'prefix' => 'cdc/university',
        'name' => 'cdc/university.',
        'additionalMiddleware' => function () {
            if (session_status() === PHP_SESSION_NONE) session_start();

            // Cek Role & Kepemilikan Kampus (Polibatam ID = 1)
            // Pastikan user sudah login (dihandle parent), kita tinggal cek permission spesifik
            if (empty($_SESSION['CDC-Data']) || $_SESSION['CDC-Data']['id_kampus'] != 1) {
                Router::handleError(403, 'You do not have permission to access this module (Polibatam Only).');
                exit;
            }
        }
    ], function() {

        Router::get('/', function () {
            renderView('cdc.university_management.index', ['title' => 'University Management', 'route' => 'university_management']);
        })->name('index');

    });


    // CDC Management
    Router::group([
        'prefix' => 'cdc/management',
        'name' => 'cdc/management.',
    ], function() {

        Router::get('/', function () {
            renderView('cdc.cdc_management.index', ['title' => 'CDC Management', 'route' => 'cdc_management']);
        })->name('index');


        Router::get('/add', function () {
            renderView('cdc.cdc_management.add', ['title' => 'Add New CDC', 'route' => 'cdc_management']);
        })->name('add');

        Router::get('/edit/{id}', function ($params) {
            renderView('cdc.cdc_management.edit', ['title' => 'Edit CDC Account', 'route' => 'cdc_management']);
        })->name('edit');

    });

    # Internship Letter
    Router::group([
        'prefix' => 'cdc/internship_letter',
        'name'   => 'cdc/internship-letter.',
    ], function () {
        Router::get('/', function () {
            throttle('CDCController/InternshipLetter', 'index', 5, 60, "seconds");
            controller('CDCController/InternshipLetter', ['method' => 'index']);
        })->name('management');

        Router::get('/detail/{id}', function ($params) {
            throttle('CDCController/InternshipLetter', 'detail', 5, 60, "seconds");
            controller('CDCController/InternshipLetter', ['method' => 'detail', 'id_internship_letter' => $params['id']]);
        })->name('detail');

        
    });
    # End Internship Letter Management

    # Study Program Management
    Router::group([
        'prefix' => 'cdc/study_program',
        'name'   => 'study_program.',
    ], function () {
        Router::get('/management', function () {
            renderView('cdc.study_program_management.index', ['title' => 'Study Program Management', 'route' => 'study_program_management']);
        })->name('management');

        Router::get('/add', function () {
            renderView('cdc.study_program_management.add', ['title' => 'Add New Study Program', 'route' => 'study_program_management']);
        })->name('add');

        Router::POST('/add', function () {
            throttle('CDCController/StudyProgramManagement', 'store', 5, 60, 'seconds');
            controller('CDCController/StudyProgramManagement', ['method' => 'store']);
        })->name('store')->middleware('csrf');

        Router::get('/edit/{id}', function ($params) {
            renderView('cdc.study_program_management.edit', [
                'title' => 'Edit Existing Study Program',
                'route' => 'study_program_management',
                'params' => $params
            ]);
        })->name('edit');

    });
    # End Study Program Management

    # Lecturer Management
    Router::group([
        'prefix' => 'cdc/lecturer',
        'name'   => 'lecturer.',
    ], function () {

        // Main Page
        Router::get('/management', function () {
            renderView('cdc.lecturer_management.index', ['title' => 'Lecturer Management', 'route' => 'lecturer_management']);
        })->name('management');

        // Recycle Bin Page
        Router::get('/management/recycle', function () {
            renderView('cdc.lecturer_management.recycle', ['title' => 'Lecturer Management (Recycle)', 'route' => 'lecturer_management']);
        })->name('management.recycle');

        // Import Page
        Router::get('/import', function () {
            renderView('cdc.lecturer_management.import', ['title' => 'Import Lecturer Data', 'route' => 'lecturer_management']);
        })->name('import');

        // Add New Lecturer Page
        Router::get('/add', function () {
            renderView('cdc.lecturer_management.add', ['title' => 'Add New Lecturer', 'route' => 'lecturer_management']);
        })->name('add');

        Router::POST('/store', function () {
            throttle('CDCController/LecturerManagement', 'store', 5, 60, 'seconds');
            controller('CDCController/LecturerManagement', ['method' => 'store']);
        })->name('store')->middleware('csrf');

        Router::get('/edit/{id}', function ($params) {
            throttle('CDCController/LecturerManagement', 'edit', 5, 60, 'seconds');
            controller('CDCController/LecturerManagement', ['method' => 'edit', 'id_encoded' => $params['id']]);
        })->name('edit');

        Router::put('/update/{id}', function ($params) {
            throttle('CDCController/LecturerManagement', 'update', 5, 60, 'seconds');
            controller('CDCController/LecturerManagement', ['method' => 'update', 'id_encoded' => $params['id']]);
        })->name('update')->middleware('csrf');

    });
    # End Lecturer Management


    # =========================================================================
    # Internship Management Routes
    # =========================================================================
    Router::group([
        'prefix' => 'cdc/internship',
        'name'   => 'cdc.internship.',
    ], function () {

        // 1. Main Page (List Data)
        Router::get('/management', function () {
            renderView('cdc.internship_management.index', ['title' => 'Internship Management', 'route' => 'internship_management']);
        })
        ->name('management')
        ->meta([
            'title'       => 'Internship Management',
            'description' => 'Main dashboard to view and manage student internships.',
            'method'      => 'GET',
            'auth'        => true,
            'roles'       => ['CDC', 'UPKPK'],
            'tags'        => ['CDC', 'Internship', 'Page']
        ]);

        
        // 2. Add New Internship Page (Form)
        Router::get('/add/{?nim}', function ($params) {
            controller('CDCController/InternshipManagement', ['method' => 'add', 'nim' => $params['nim'] ?? null]);
        })
        ->name('add')
        ->meta([
            'title'       => 'Add Internship Form',
            'description' => 'Form to manually register a new internship.',
            'method'      => 'GET',
            'auth'        => true,
            'roles'       => ['CDC', 'UPKPK'],
            'tags'        => ['CDC', 'Internship', 'Page', 'Form']
        ]);

        // 3. Store Action (Process Add)
        Router::POST('/store', function () {
            throttle('CDCController/InternshipManagement', 'store', 100, 60, 'seconds');
            controller('CDCController/InternshipManagement', ['method' => 'store']);
        })
        ->name('store')
        ->middleware('csrf')
        ->meta([
            'title'       => 'Store Internship Data',
            'description' => 'Process the creation of a new internship record.',
            'method'      => 'POST',
            'auth'        => true,
            'roles'       => ['CDC', 'UPKPK'],
            'tags'        => ['CDC', 'Internship', 'Action', 'Create']
        ]);

        // 4. Recycle Bin Page
        Router::get('/management/recycle', function () {
            renderView('cdc.internship_management.recycle', ['title' => 'Internship Management (Recycle)', 'route' => 'internship_management']);
        })
        ->name('management.recycle')
        ->meta([
            'title'       => 'Internship Recycle Bin',
            'description' => 'View deleted internships and restore them.',
            'method'      => 'GET',
            'auth'        => true,
            'roles'       => ['CDC', 'UPKPK'],
            'tags'        => ['CDC', 'Internship', 'Page', 'Trash']
        ]);

        // 3. Import Page
        Router::get('/import', function () {
            renderView('cdc.internship_management.import', ['title' => 'Import Internship Data', 'route' => 'internship_management']);
        })
        ->name('import')
        ->meta([
            'title'       => 'Import Internship Data',
            'description' => 'Interface for bulk importing internship data via Excel.',
            'method'      => 'GET',
            'auth'        => true,
            'roles'       => ['CDC', 'UPKPK'],
            'tags'        => ['CDC', 'Internship', 'Page', 'Import']
        ]);

        // 6. Edit Internship Page (Form)
        Router::get('/edit/{id}', function ($params) {
            throttle('CDCController/InternshipManagement', 'edit', 5, 60, 'seconds'); // Optional: throttle view access
            controller('CDCController/InternshipManagement', ['method' => 'edit', 'id_encoded' => $params['id']]);
        })
        ->name('edit')
        ->meta([
            'title'       => 'Edit Internship Form',
            'description' => 'Form to modify existing internship details.',
            'method'      => 'GET',
            'auth'        => true,
            'roles'       => ['CDC', 'UPKPK'],
            'tags'        => ['CDC', 'Internship', 'Page', 'Form']
        ]);

        // 7. Update Action (Process Edit)
        Router::put('/update/{id}', function ($params) {
            throttle('CDCController/InternshipManagement', 'update', 5, 60, 'seconds');
            controller('CDCController/InternshipManagement', ['method' => 'update', 'id_encoded' => $params['id']]);
        })
        ->name('update')
        ->middleware('csrf')
        ->meta([
            'title'       => 'Update Internship Data',
            'description' => 'Process changes to an existing internship record.',
            'method'      => 'PUT',
            'auth'        => true,
            'roles'       => ['CDC', 'UPKPK'],
            'tags'        => ['CDC', 'Internship', 'Action', 'Update']
        ]);


        // 8. Detail Page
        Router::get('/detail/{id}', function ($params) {
            throttle('CDCController/InternshipManagement', 'detail', 5, 60, 'seconds'); // Optional: throttle view access
            controller('CDCController/InternshipManagement', ['method' => 'detail', 'id_encoded' => $params['id']]);
        })
        ->name('detail')
        ->meta([
            'title'       => 'Detail Internship Form',
            'description' => 'Form to view existing internship details.',
            'method'      => 'GET',
            'auth'        => true,
            'roles'       => ['CDC', 'UPKPK'],
            'tags'        => ['CDC', 'Internship', 'Page', 'Form']
        ]);

    });

});



// Industry - Attendance Location Routes
Router::group([
    'prefix' => 'industry/attendance_location',
    'name'   => 'industry/attendance_location.',
    'additionalMiddleware' => function () {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Cek Login
        if (empty($_SESSION['role'])) {
            $_SESSION['redirect_intended'] = Url::current();
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            // Redirect ke Login Industri
            Redirect::to(router('login.industry'), 0);
            exit;
        }

        // 2. Cek Role
        $allowedRoles = ['Industri', 'Industry', 'industri', 'industry'];
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            Router::handleError(403, 'Access Denied: Restricted to Industry Partners only.');
            exit;
        }
    }
], function() {

    # 1. List Locations
    Router::get('/', function () {
        renderView('industry.attendance_location.index', [
            'title'  => 'Attendance Location', 
            'routes' => 'attendance_location', 
            'route'  => 'attendance_location'
        ]);
    })
    ->name('index')
    ->meta([
        'title'       => 'Attendance Location List',
        'description' => 'Manage allowed locations for intern attendance.',
        'method'      => 'GET',
        'auth'        => true,
        'roles'       => ['Industri', 'Industry']
    ]);

    # 2. Recycle Bin Locations
    Router::get('/recycle', function () {
        renderView('industry.attendance_location.recycle', [
            'title'  => 'Recycle Bin - Attendance Location', 
            'routes' => 'attendance_location', 
            'route'  => 'attendance_location'
        ]);
    })
    ->name('recycle')
    ->meta([
        'title'       => 'Attendance Location Recycle Bin',
        'description' => 'View and restore deleted attendance locations.',
        'method'      => 'GET',
        'auth'        => true,
        'roles'       => ['Industri', 'Industry']
    ]);

});


# =========================================================================
    # Industry / Company Management Routes
    # =========================================================================
    Router::group([
        'prefix'               => 'industry',
        'middleware'           => ['csrf'],
        'name'                 => 'industry.',
        'additionalMiddleware' => function () {
            
            session_status() === PHP_SESSION_NONE ? session_start() : null;
            $allowedRoles = ['HRD', 'Supervisor', 'spv', 'Industry', 'industry', 'industri', 'Industri'];
            if (!in_array($_SESSION['role'] ?? ($_SESSION['user_type'] ?? ''), $allowedRoles)) {
                Router::handleError(403, 'Access Denied: Restricted to Industry personnel only.');
            }
        }
    ], function () {

        // 1. View My Company Details
        Router::get('/my-company', function () {
            throttle('industry/my-company', 'index', 20, 60, 'seconds');
            Controller::run('IndustryController/MyCompanyController', ['method' => 'index']);
        })->name('my_company');

        // 2. Update Company Details (HRD Only)
        Router::post('/my-company/update', function () {
            throttle('industry/my-company', 'update', 5, 60, 'seconds');
            Controller::run('IndustryController/MyCompanyController', ['method' => 'update']);
        })->name('my_company.update');

    });



/*
|--------------------------------------------------------------------------
| Sites Administration Routes
|--------------------------------------------------------------------------
*/
Router::get('/security-policy', function () {
    renderView('landing.security-policy', ['title' => 'Attendance Location', 'routes' => 'attendance_location', 'route' => 'attendance_location']);
})->name('security-policy');


Router::group([
    'additionalMiddleware' => function () {
        // Pastikan session start
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Cek Login (Authentication)
        if (empty($_SESSION['role'])) {
            // Simpan ke Session agar bisa diakses setelah login
            $_SESSION['redirect_intended'] = Url::current();

            // Titip Notifikasi ke Session (Akan muncul setelah redirect selesai)
            make_swal2('error', 'Access Denied', 'Please log in to continue.');
            
            // Gunakan Class Redirect (Timer 0 = Langsung Pindah)
            // Tidak perlu ->with() lagi karena sudah dihandle make_swal2
            Redirect::to(router('login.cdc'), 0);
            
            exit;
        }
            // 2. Cek Role (Authorization)
            if (!in_array($_SESSION['role'], ['UPKPK', 'CDC'], true)) {
                // 403 Forbidden (Tetap gunakan Error Page, lebih elegan untuk kasus Role)
                Router::handleError(403, 'You do not have permission to access this module.');
                exit;
            }
    }
], function () {

    Router::get('/router-analytics', function () {

        $basePath = BASE_DIR. '/logs/router';
        $channel  = 'Router_Error';

        if (!is_dir($basePath)) {
            @mkdir($basePath, 0777, true);
        }

        $channel = preg_replace('/[^A-Za-z0-9_\-]/', '_', $channel);
        $files = glob(rtrim($basePath, '/') . '/' . $channel . '_*.log');

        if (empty($files)) {
            renderView('analytics.index', ['groupedData' => [], 'totalLogs' => 0]);
            return;
        }

        // Sort file dari yang terbaru
        rsort($files, SORT_STRING);

        $allLogs = [];

        // Baca File
        foreach ($files as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES);
            $buffer = '';

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $buffer .= $line;

                if ($line === '}') {
                    $json = json_decode($buffer, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $allLogs[] = $json;
                    }
                    $buffer = '';
                }
            }
        }

        // ðŸ”¥ LOGIC GROUPING BERDASARKAN TANGGAL
        $groupedData = [];
        $totalLogs = count($allLogs);

        // Sort semua log dari jam terbaru ke terlama
        usort($allLogs, function ($a, $b) {
            return strtotime($b['timestamp'] ?? '') <=> strtotime($a['timestamp'] ?? '');
        });

        foreach ($allLogs as $log) {
            // Ambil Tanggal saja (Y-m-d) dari timestamp
            $dateKey = date('Y-m-d', strtotime($log['timestamp'] ?? 'now'));
            
            // Masukkan ke dalam grup
            $groupedData[$dateKey][] = $log;
        }

        renderView('analytics.index', [
            'title'      => 'Router Analytics',
            'groupedData' => $groupedData,
            'totalLogs'   => $totalLogs
        ]);

    })->name('router-analytics');
});