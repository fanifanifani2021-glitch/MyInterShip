<?php

use Controller;
use Router;
defined('BASE_DIR') or die('Direct script access is Not Allowed');


/*
|--------------------------------------------------------------------------
| Industry Menu Routes Configuration
|--------------------------------------------------------------------------
|
| File ini mendefinisikan seluruh route yang berkaitan dengan modul
| khusus untuk pengguna Industry (Mitra Industri).
|
| Access Control:
| - Seluruh route diproteksi oleh CSRF Middleware.
| - Middleware tambahan memvalidasi $_SESSION['role'] == 'Industry'.
|
| Modules included:
| - Attendance Location (Lokasi Absensi Magang)
|
*/

/*
|--------------------------------------------------------------------------
| 1. Industry DataTables Source
|--------------------------------------------------------------------------
|
| Group ini khusus menangani request POST untuk mengambil data JSON
| DataTables (Server-side).
|
| - Access : Industry Only
| - Rate   : 100 req / minute
*/
Router::group([
    'prefix'     => 'datatable/industry',
    'middleware' => ['csrf'],
    'name' => 'datatable.industry.',
    'additionalMiddleware' => function () {
        session_status() === PHP_SESSION_NONE ? session_start() : null;
        !in_array( $_SESSION['role'] ?? null, ['Industry', 'Industri', 'industry', 'industri'], true)
            ? abort(401, 'You do not have permission to access this module.')
            : null;        
    }
], function () {


    // Data Lokasi Absensi (Active)
    Router::post('/attendance_location', function () {
        throttle('datatable/industry/attendance_location', 'data', 100, 60, 'seconds');
        controller('IndustryController/AttendanceLocationController', ['method' => 'data']);
    })
    ->name('attendance_location')
    ->meta([
        'title'       => 'Attendance Location Data Source',
        'description' => 'Fetch server-side JSON data for Attendance Location DataTable.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['Industry'],
        'tags'        => ['Industry', 'Datatable', 'Attendance']
    ]);


    // Data Lokasi Absensi (Recycle Bin)
    Router::post('/attendance_location/recycle', function () {
        throttle('datatable/industry/attendance_location', 'data_recycle', 100, 60, 'seconds');
        controller('IndustryController/AttendanceLocationController', ['method' => 'data_recycle']);
    })
    ->name('attendance_location.recycle')
    ->meta([
        'title'       => 'Attendance Location Trash Source',
        'description' => 'Fetch server-side JSON data for Soft-deleted Locations.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['Industry'],
        'tags'        => ['Industry', 'Datatable', 'Attendance', 'Trash']
    ]);

});



/*
|--------------------------------------------------------------------------
| 2. Industry Transactional Operations (Ajax CRUD)
|--------------------------------------------------------------------------
|
| Group ini menangani logika bisnis CRUD untuk modul Industry.
|
| - Access : Industry Only
| - Rate   : Adjusted based on operation risk (5-20 req/min)
*/
Router::group([
    'prefix'     => 'ajax/industry/attendance_location/',
    'middleware' => ['csrf'],
    'name' => 'ajax.industry.attendance_location.',
    'additionalMiddleware' => function () {
        session_status() === PHP_SESSION_NONE ? session_start() : null;
        !in_array( $_SESSION['role'] ?? null, ['Industry', 'Industri', 'industry', 'industri'], true)
            ? abort(401, 'You do not have permission to access this module.')
            : null;        
    }
], function () {

    // 1. Create Data (Submit)
    Router::post('/', function () {
        throttle('ajax/industry/attendance_location', 'submit', 10, 60, 'seconds');
        controller('IndustryController/AttendanceLocationController', ['method' => 'submit']);
    })
    ->name('submit')
    ->meta([
        'title'       => 'Create Attendance Location',
        'description' => 'Add new attendance location coordinate.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['Industry'],
        'tags'        => ['Industry', 'Attendance']
    ]);
    
    // 2. Soft Delete (Destroy)
    Router::delete('/', function ( ) {
        throttle('ajax/industry/attendance_location', 'destroy', 10, 60, 'seconds');
        controller('IndustryController/AttendanceLocationController', ['method' => 'destroy']);
    })
    ->name('destroy')
    ->meta([
        'title'       => 'Soft Delete Location',
        'description' => 'Move attendance location to trash.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['Industry'],
        'tags'        => ['Industry', 'Attendance']
    ]);



    // 3. Restore Data
    Router::post('/restore', function () {
        throttle('ajax/industry/attendance_location', 'restore', 10, 60, 'seconds');
        controller('IndustryController/AttendanceLocationController', ['method' => 'restore']);
    })
    ->name('restore')
    ->meta([
        'title'       => 'Restore Location',
        'description' => 'Restore attendance location from trash.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['Industry'],
        'tags'        => ['Industry', 'Attendance', 'Restore']
    ]);

    
    // 4. Permanent Delete (Force) - Strict Rate Limit
    Router::delete('/force', function () {
        throttle('ajax/industry/attendance_location', 'force_delete', 10, 60, 'seconds');
        controller('IndustryController/AttendanceLocationController', ['method' => 'force_delete']);
    })
    ->name('force_delete')
    ->meta([
        'title'       => 'Permanent Delete Location',
        'description' => 'Permanently remove attendance location. Irreversible.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['Industry'],
        'tags'        => ['Industry', 'Attendance', 'Permanent Delete']
    ]);


    // 5. Get Detail (For Edit Modal)
    Router::post('/detail', function () {
        throttle('ajax/industry/attendance_location', 'detail', 10, 60, 'seconds');
        controller('IndustryController/AttendanceLocationController', ['method' => 'detail']);
    })
    ->name('detail')
    ->meta([
        'title'       => 'Get Location Detail',
        'description' => 'Fetch single location data for editing.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['Industry'],
        'tags'        => ['Industry', 'Attendance']
    ]);

    // 6. Update Data
    Router::post('/update', function () {
        throttle('ajax/industry/attendance_location', 'update', 10, 60, 'seconds');
        controller('IndustryController/AttendanceLocationController', ['method' => 'update']);
    })
    ->name('update')
    ->meta([
        'title'       => 'Update Location',
        'description' => 'Update existing attendance location details.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['Industry'],
        'tags'        => ['Industry', 'Attendance']
    ]);

});



Router::group([
    'prefix'               => 'datatable/lecturer',
    'middleware'           => ['csrf'],
    'name'                 => 'datatable.lecturer.',
    'additionalMiddleware' => function () {
        session_status() === PHP_SESSION_NONE ? session_start() : null;
        !in_array($_SESSION['role'] ?? null, ['Lecturer', 'Dosen'], true)
            ? abort(401, 'You do not have permission to access this module.')
            : null;        
    }
], function () {

    Router::post('/my/student', function () {
        // Method ini harus ada di DatatableController
        throttle('LecturerController/MyStudentController', 'data', 100, 60, 'seconds');
        controller('LecturerController/MyStudentController', ['method' => 'data']);
    })
    ->name('my.student')
    ->meta([
        'title'       => 'My Supervised Student',
        'description' => 'Fetch server-side JSON data for Lecturer Supervised Student.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['Lecturer', 'Dosen'],
        'tags'        => ['Lecturer', 'Datatable', 'Internship']
    ]);

    Router::post('/company-list', function () {
        throttle('LecturerController/CompanyController', 'data', 100, 60, 'seconds');

        controller('LecturerController/CompanyController', [
            'method' => 'data'
        ]);
    })->name('company_list');


});

Router::group([
    'prefix'               => 'datatable/coordinator',
    'middleware'           => ['csrf'],
    'name'                 => 'datatable.coordinator.',
    'additionalMiddleware' => function () {
        session_status() === PHP_SESSION_NONE ? session_start() : null;
        !in_array($_SESSION['role'] ?? null, ['Lecturer', 'Dosen'], true)
            ? abort(401, 'You do not have permission to access this module.')
            : null;        
    }
], function () {

    // 7. Data Admisitrasi magang
    Router::post('/internship/administration', function () {
        // Method ini harus ada di DatatableController
        throttle('CoordinatorController/InternshipAdministration', 'data', 100, 60, 'seconds');
        controller('CoordinatorController/InternshipAdministration', ['method' => 'data']);
    })
    ->name('internship_administration')
    ->meta([
        'title'       => 'Internship Coordinator Internship Administration',
        'description' => 'Fetch server-side JSON data for Internship Administration Data.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['Lecturer', 'Dosen'],
        'tags'        => ['Lecturer', 'Coordinator', 'Datatable', 'Internship']
    ]);

});





# =========================================================================
# Student DataTables Endpoints
# =========================================================================
Router::group([
    'prefix'     => 'datatable/student',
    'middleware' => ['csrf'],
    'name'       => 'datatable.student.',
    'additionalMiddleware' => function () {
        session_status() === PHP_SESSION_NONE ? session_start() : null;
        if (!in_array($_SESSION['role'] ?? null, ['Mahasiswa', 'Student'], true)) {
            abort(401, 'Unauthorized access.');
        }
    }
], function () {

    Router::post('/internship_offer', function () {
        throttle('StudentController/InternshipOfferController', 'data', 100, 60, 'seconds');
        Controller::run('StudentController/InternshipOfferController', ['method' => 'data']);
    })->name('internship_offer');

    Router::post('/my_internship', function () {
        Controller::run('StudentController/InternshipController', ['method' => 'data']);
    })->name('my_internship');

    // 🎯 RUTE BARU: DataTables List Surat Pengantar Magang
    Router::post('/my_internship_letter', function () {
        throttle('StudentController/InternshipLetterController', 'data', 100, 60, 'seconds');
        Controller::run('StudentController/StudentInternshipLetterController', ['method' => 'data']);
    })->name('my_internship_letter');

});



/*
|--------------------------------------------------------------------------
| CDC Menu Routes Configuration
|--------------------------------------------------------------------------
|
| File ini mendefinisikan seluruh route yang berkaitan dengan modul
| Career Development Center (CDC) dan UPKPK.
|
| Struktur Route:
| 1. Datatable Sources : Endpoint untuk mengambil data JSON (Server-side).
| 2. CRUD Operations   : Endpoint untuk Create, Update, Delete, Restore.
| 3. Import Utilities  : Endpoint khusus untuk parsing dan import Excel.
|
| Security & Middleware:
| - CSRF Protection aktif di semua route.
| - Role Validation (CDC/UPKPK) diterapkan pada logic middleware.
| - Rate Limiting (Throttle) disesuaikan dengan beban endpoint.
|
*/

/*
|--------------------------------------------------------------------------
| 1. CDC DataTables Source
|--------------------------------------------------------------------------
|
| Group ini khusus menangani request POST untuk mengambil data JSON
| yang dibutuhkan oleh plugin DataTables (Server-side processing).
|
| - Access : UPKPK, CDC
| - Rate   : 100 req / minute (High Traffic allowed for fast browsing)
| - Method : POST (Secure param delivery)
*/
Router::group([
    'prefix'               => 'datatable/cdc',
    'middleware'           => ['csrf'],
    'name'                 => 'datatable.cdc.',
    'additionalMiddleware' => function () {
        session_status() === PHP_SESSION_NONE ? session_start() : null;
        !in_array($_SESSION['role'] ?? null, ['UPKPK', 'CDC'], true)
            ? abort(401, 'You do not have permission to access this module.')
            : null;        
    }
], function () {

    // 1. Data Kampus (Polibatam & Mitra)
    Router::post('/university', function () {
        throttle('Datatable/DatatableController', 'university_data_cdc', 100, 60, 'seconds');
        controller('Datatable/DatatableController', ['method' => 'university_data_cdc']);
    })
    ->name('university')
    ->meta([
        'title'       => 'CDC University Data Source',
        'description' => 'Fetch server-side JSON data for University DataTable.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Datatable', 'University']
    ]);

    // 2. Data Program Studi (Study Program)
    Router::post('/study_program', function () {
        throttle('CDCController/StudyProgramManagement', 'data', 100, 60, 'seconds');
        controller('CDCController/StudyProgramManagement', ['method' => 'data']);
    })
    ->name('study_program')
    ->meta([
        'title'       => 'CDC Study Program Data Source',
        'description' => 'Fetch server-side JSON data for Study Program DataTable.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Datatable', 'Study Program']
    ]);

    // 3. Data Dosen (Aktif)
    Router::post('/lecturer', function () {
        throttle('CDCController/LecturerManagement', 'data', 100, 60, 'seconds');
        controller('CDCController/LecturerManagement', ['method' => 'data']);
    })
    ->name('lecturer')
    ->meta([
        'title'       => 'CDC Lecturer Data Source',
        'description' => 'Fetch server-side JSON data for Active Lecturers DataTable.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Datatable', 'Lecturer']
    ]);
    

    // 4. Data Dosen (Sampah / Soft Deleted)
    Router::post('/lecturer/recycle', function () {
        throttle('CDCController/LecturerManagement', 'data_recycle', 100, 60, 'seconds');
        controller('CDCController/LecturerManagement', ['method' => 'data_recycle']);
    })
    ->name('lecturer.recycle')
    ->meta([
        'title'       => 'CDC Lecturer Trash Data Source',
        'description' => 'Fetch server-side JSON data for Soft-deleted Lecturers.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Datatable', 'Lecturer', 'Trash']
    ]);


    // 5. Data Magang (Aktif)
    Router::post('/internship', function () {
        // Method ini harus ada di DatatableController
        throttle('CDCController/InternshipManagement', 'data', 100, 60, 'seconds');
        controller('CDCController/InternshipManagement', ['method' => 'data']);
    })
    ->name('internship')
    ->meta([
        'title'       => 'CDC Internship Data Source',
        'description' => 'Fetch server-side JSON data for Active Internship DataTable.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Datatable', 'Internship']
    ]);

    // 6. Data Magang (Recycle Bin)
    Router::post('/internship/recycle', function () {
        // Method ini harus ada di DatatableController
        throttle('CDCController/InternshipManagement', 'data_recycle', 100, 60, 'seconds');
        controller('CDCController/InternshipManagement', ['method' => 'data_recycle']);
    })
    ->name('internship.recycle')
    ->meta([
        'title'       => 'CDC Internship Trash Source',
        'description' => 'Fetch server-side JSON data for Soft-deleted Internships.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Datatable', 'Internship', 'Trash']
    ]);

    // 7. Maintenance Schedule
    Router::post('/maintenance/schedule', function () {
        // Method ini harus ada di DatatableController
        throttle('CDCController/MaintenanceScheduleManagement', 'data', 100, 60, 'seconds');
        controller('CDCController/MaintenanceScheduleManagement', ['method' => 'data']);
    })
    ->name('maintenance')
    ->meta([
        'title'       => 'CDC Maintenance Schedule Management',
        'description' => 'Fetch server-side JSON data for Maintenance Schedule.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Datatable', 'Internship', 'Trash']
    ]);



    # =========================================================================
    # 2. CDC DataTables Endpoint
    # =========================================================================
    Router::group([
        'prefix'               => '/announcement',
        'middleware'           => ['csrf'],
        'name'                 => 'announcement.',
        'additionalMiddleware' => function () {
            session_status() === PHP_SESSION_NONE ? session_start() : null;
            !in_array($_SESSION['role'] ?? null, ['UPKPK', 'CDC'], true)
                ? abort(401, 'Unauthorized access.')
                : null;
        }
    ], function () {

        Router::post('/data', function () {
            throttle('CDCController/AnnouncementController', 'data', 100, 60, 'seconds');
            Controller::run('CDCController/AnnouncementController', ['method' => 'data']);
        })->name('data');

    });
    
    
    
    # -------------------------------------------------------------------------
    # CDC DataTables Source: Helpdesk Categories
    # -------------------------------------------------------------------------
    Router::group([
        'prefix'               => '/helpdesk',
        'middleware'           => ['csrf'],
        'name'                 => 'helpdesk.',
        'additionalMiddleware' => function () {
            session_status() === PHP_SESSION_NONE ? session_start() : null;
            !in_array($_SESSION['role'] ?? null, ['UPKPK', 'CDC'], true)
                ? abort(401, 'You do not have permission to access this module.')
                : null;        
        }
    ], function () {

        // Data Categories (Server-side JSON)
        Router::post('/categories', function () {
            throttle('CDCController/HelpdeskCategoryController', 'data', 100, 60, 'seconds');
            Controller::run('CDCController/HelpdeskCategoryController', ['method' => 'data']);
        })
        ->name('categories')
        ->meta([
            'title'       => 'CDC Helpdesk Categories Data Source',
            'description' => 'Fetch server-side JSON data for Helpdesk Categories DataTable.',
            'method'      => 'POST',
            'auth'        => true,
            'roles'       => ['UPKPK', 'CDC'],
            'tags'        => ['CDC', 'Datatable', 'Helpdesk']
        ]);

    });


});
/*
|--------------------------------------------------------------------------
| 2. CDC Transactional Operations (Ajax CRUD)
|--------------------------------------------------------------------------
|
| Group ini menangani logika bisnis utama seperti Create, Update, Delete.
|
| - Access : UPKPK, CDC
| - Rate   : 
|    > Single Action : 20 req/min (Standard Form Input speed)
|    > Bulk Action   : 10 req/min (Heavier DB load)
|    > Permanent Del : 5 req/min (Critical Action)
*/
Router::group([
    'prefix'               => 'ajax/cdc',
    'name'                 => 'ajax.cdc.',
    'middleware'           => ['csrf'],
    'additionalMiddleware' => function () {
        session_status() === PHP_SESSION_NONE ? session_start() : null;
        !in_array($_SESSION['role'] ?? null, ['UPKPK', 'CDC'], true)
            ? abort(401, 'You do not have permission to access this module.')
            : null;        
    }
], function () {





    # =========================================================================
    # 3. CDC Transactional AJAX Operations (CRUD)
    # =========================================================================
    Router::group([
        'prefix'               => 'cdc/announcement',
        'middleware'           => ['csrf'],
        'name'                 => 'announcement.',
        'additionalMiddleware' => function () {
            session_status() === PHP_SESSION_NONE ? session_start() : null;
            !in_array($_SESSION['role'] ?? null, ['UPKPK', 'CDC'], true)
                ? abort(401, 'Unauthorized access.')
                : null;
        }
    ], function () {

        // Save (Create & Update)
        Router::post('/save', function () {
            throttle('ajax/cdc/announcement', 'save', 20, 60, 'seconds');
            Controller::run('CDCController/AnnouncementController', ['method' => 'save']);
        })->name('save');

        // Fetch Detail
        Router::post('/detail', function () {
            throttle('ajax/cdc/announcement', 'detail', 20, 60, 'seconds');
            Controller::run('CDCController/AnnouncementController', ['method' => 'detail']);
        })->name('detail');

        // Toggle Active Status
        Router::post('/toggle-status', function () {
            throttle('ajax/cdc/announcement', 'toggleStatus', 20, 60, 'seconds');
            Controller::run('CDCController/AnnouncementController', ['method' => 'toggleStatus']);
        })->name('toggle_status');

        // Hard Delete
        Router::delete('/destroy', function () {
            throttle('ajax/cdc/announcement', 'destroy', 10, 60, 'seconds');
            Controller::run('CDCController/AnnouncementController', ['method' => 'destroy']);
        })->name('destroy');

    });
    
    
    # -------------------------------------------------------------------------
    # AJAX Operations for Helpdesk Category
    # -------------------------------------------------------------------------
    Router::group([
        'prefix'               => 'helpdesk/categories',
        'middleware'           => ['csrf'],
        'name'                 => 'helpdesk.categories.',
        'additionalMiddleware' => function () {
            session_status() === PHP_SESSION_NONE ? session_start() : null;
            !in_array($_SESSION['role'] ?? null, ['UPKPK', 'CDC'], true)
                ? abort(401, 'Unauthorized access.')
                : null;
        }
    ], function () {

        // Save Data (Create / Update)
        Router::post('/save', function () {
            throttle('ajax/cdc/helpdesk/categories', 'save', 20, 60, 'seconds');
            Controller::run('CDCController/HelpdeskCategoryController', ['method' => 'save']);
        })->name('save');

        // Get Single Detail for Modal Edit
        Router::post('/detail', function () {
            throttle('ajax/cdc/helpdesk/categories', 'detail', 20, 60, 'seconds');
            Controller::run('CDCController/HelpdeskCategoryController', ['method' => 'detail']);
        })->name('detail');

        // Delete Data
        Router::delete('/destroy', function () {
            throttle('ajax/cdc/helpdesk/categories', 'destroy', 10, 60, 'seconds');
            Controller::run('CDCController/HelpdeskCategoryController', ['method' => 'destroy']);
        })->name('destroy');

        // Toggle Active Status
        Router::post('/toggle-status', function () {
            throttle('ajax/cdc/helpdesk/categories', 'toggleStatus', 20, 60, 'seconds');
            Controller::run('CDCController/HelpdeskCategoryController', ['method' => 'toggleStatus']);
        })->name('toggle_status');

    });


    // Route for Personification AJAX search
    Router::post('personify/user/list', function () {
        throttle('CDCController/ImpersonateController', 'user_list', 100, 60, 'seconds');
        controller('CDCController/ImpersonateController', ['method' => 'user_list']);
    })->name('personify.user.list')
    ->meta([
        'title'       => 'Get Personify List',
        'description' => '',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC']
    ]);

    Router::post('personify/campus/list', function () {
        throttle('CDCController/ImpersonateController', 'campus_list', 100, 60, 'seconds');
        controller('CDCController/ImpersonateController', ['method' => 'campus_list']);
    })->name('personify.campus.list')
    ->meta([
        'title'       => 'Get Personify List',
        'description' => '',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC']
    ]);

    Router::post('personify/prodi/list', function () {
        throttle('CDCController/ImpersonateController', 'prodi_list', 100, 60, 'seconds');
        controller('CDCController/ImpersonateController', ['method' => 'prodi_list']);
    })->name('personify.prodi.list')
    ->meta([
        'title'       => 'Get Personify List',
        'description' => '',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC']
    ]);


    Router::post('personify/login', function () {
        throttle('CDCController/ImpersonateController', 'personify_login', 10, 1, 'minutes');
        controller('CDCController/ImpersonateController', ['method' => 'personify_login']);
    })->name('personify.login');

    Router::get('cdc/personify/exit', function() {
        controller('CDCController/ImpersonateController', ['method' => 'switch_back']);
    })->name('cdc.personify.switch_back');


    // =========================================================================
    // Maintenance Schedule
    // =========================================================================
    Router::post('/maintenance/schedule/save', function () {
        throttle('CDCController/MaintenanceScheduleManagement', 'save', 100, 60, 'seconds');
        controller('CDCController/MaintenanceScheduleManagement', ['method' => 'save']);
    })
    ->name('maintenance.save')
    ->meta([
        'title'       => 'Save Maintenance Schedule',
        'description' => 'Move a single lecturer to trash.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);

    Router::delete('/maintenance/schedule/delete', function () {
        throttle('CDCController/MaintenanceScheduleManagement', 'destroy', 100, 60, 'seconds');
        controller('CDCController/MaintenanceScheduleManagement', ['method' => 'destroy']);
    })
    ->name('maintenance.destroy')
    ->meta([
        'title'       => 'Save Maintenance Schedule',
        'description' => 'Move a single lecturer to trash.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);

    Router::post('/maintenance/schedule/detail', function () {
        throttle('CDCController/MaintenanceScheduleManagement', 'detail', 100, 60, 'seconds');
        controller('CDCController/MaintenanceScheduleManagement', ['method' => 'detail']);
    })
    ->name('maintenance.detail')
    ->meta([
        'title'       => 'Get Maintenance Schedule Detail',
        'description' => 'Move a single lecturer to trash.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);


    // =========================================================================
    // LECTURER MANAGEMENT
    // =========================================================================

    // 1. Soft Delete Single
    Router::delete('/lecturer/destroy', function () {
        throttle('CDCController/LecturerManagement', 'destroy', 20, 60);
        controller('CDCController/LecturerManagement', ['method' => 'destroy']);
    })
    ->name('lecturer.destroy')
    ->meta([
        'title'       => 'Soft Delete Lecturer',
        'description' => 'Move a single lecturer to trash.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);

    // 2. Soft Delete Bulk
    Router::delete('/lecturer/bulk_destroy', function () {
        throttle('CDCController/LecturerManagement', 'bulk_destroy', 10, 60);
        controller('CDCController/LecturerManagement', ['method' => 'bulk_destroy']);
    })
    ->name('lecturer.bulk_destroy')
    ->meta([
        'title'       => 'Bulk Soft Delete Lecturers',
        'description' => 'Move multiple selected lecturers to trash.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);

    Router::post('/lecturer/reset_password', function () {
        throttle('CDCController/LecturerManagement', 'reset_password', 20, 60);
        controller('CDCController/LecturerManagement', ['method' => 'reset_password']);
    })
    ->name('lecturer.reset_password')
    ->meta([
        'title'       => 'Reset Lecturer Password',
        'description' => 'Reset the password for a single lecturer.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);

    // 3. Permanent Delete Bulk (Critical)
    Router::delete('/lecturer/permanent/bulk_destroy', function () {
        throttle('CDCController/LecturerManagement', 'permanent_bulk_destroy', 5, 60);
        controller('CDCController/LecturerManagement', ['method' => 'permanent_bulk_destroy']);
    })
    ->name('lecturer.permanent_bulk_destroy')
    ->meta([
        'title'       => 'Bulk Permanent Delete Lecturers',
        'description' => 'Permanently remove multiple lecturers. Irreversible.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);

    // 4. Restore Bulk
    Router::post('/lecturer/bulk_restore', function () {
        throttle('CDCController/LecturerManagement', 'bulk_restore', 10, 60);
        controller('CDCController/LecturerManagement', ['method' => 'bulk_restore']);
    })
    ->name('lecturer.bulk_restore')
    ->meta([
        'title'       => 'Bulk Restore Lecturers',
        'description' => 'Restore multiple lecturers from trash.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);

    // 5. Restore Single
    Router::post('/lecturer/restore', function () {
        throttle('CDCController/LecturerManagement', 'restore', 20, 60);
        controller('CDCController/LecturerManagement', ['method' => 'restore']);
    })
    ->name('lecturer.restore')
    ->meta([
        'title'       => 'Restore Lecturer',
        'description' => 'Restore a single lecturer from trash.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);

    // 6. Permanent Delete Single (Critical)
    Router::delete('/lecturer/permanent/destroy', function () {
        throttle('CDCController/LecturerManagement', 'permanent_destroy', 5, 60);
        controller('CDCController/LecturerManagement', ['method' => 'permanent_destroy']);
    })
    ->name('lecturer.permanent.destroy')
    ->meta([
        'title'       => 'Permanent Delete Lecturer',
        'description' => 'Permanently remove a single lecturer. Irreversible.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'Lecturer']
    ]);


    // =========================================================================
    // UNIVERSITY MANAGEMENT
    // =========================================================================

    // 7. Create University
    Router::post('/university', function () {
        throttle('CDCController/UniversityManagement', 'submit', 20, 60);
        controller('CDCController/UniversityManagement', ['method' => 'submit']);
    })
    ->name('university.submit')
    ->meta([
        'title'       => 'Create University',
        'description' => 'Add new university data.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'University']
    ]);

    // 8. Delete University
    Router::delete('/university', function () {
        throttle('CDCController/UniversityManagement', 'destroy', 20, 60);
        controller('CDCController/UniversityManagement', ['method' => 'destroy']);
    })
    ->name('university.destroy')
    ->meta([
        'title'       => 'Delete University',
        'description' => 'Remove university data.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'University']
    ]);

    // 9. Update University
    Router::put('/university', function () {
        throttle('CDCController/UniversityManagement', 'update', 20, 60);
        controller('CDCController/UniversityManagement', ['method' => 'update']);
    })
    ->name('university.update')
    ->meta([
        'title'       => 'Update University',
        'description' => 'Update existing university details.',
        'method'      => 'PUT',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['CDC', 'University']
    ]);


    // =========================================================================
    // INTERNSHIP MANAGEMENT ACTIONS
    // =========================================================================

    // 1. Soft Delete Single
    Router::delete('/internship/destroy', function () {
        throttle('CDCController/InternshipManagement', 'destroy', 20, 60);
        controller('CDCController/InternshipManagement', ['method' => 'destroy']);
    })
    ->name('internship.destroy')
    ->meta([
        'title'       => 'Soft Delete Internship',
        'description' => 'Move a single internship record to trash.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Internship']
    ]);

    // 2. Soft Delete Bulk
    Router::delete('/internship/bulk_destroy', function () {
        throttle('CDCController/InternshipManagement', 'bulk_destroy', 10, 60);
        controller('CDCController/InternshipManagement', ['method' => 'bulk_destroy']);
    })
    ->name('internship.bulk_destroy')
    ->meta([
        'title'       => 'Bulk Soft Delete Internships',
        'description' => 'Move multiple selected internships to trash.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Internship']
    ]);

    // 3. Permanent Delete Bulk (Critical)
    Router::delete('/internship/permanent/bulk_destroy', function () {
        throttle('CDCController/InternshipManagement', 'permanent_bulk_destroy', 5, 60);
        controller('CDCController/InternshipManagement', ['method' => 'permanent_bulk_destroy']);
    })
    ->name('internship.permanent_bulk_destroy')
    ->meta([
        'title'       => 'Bulk Permanent Delete Internships',
        'description' => 'Permanently remove multiple internships. Irreversible.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Internship']
    ]);

    // 4. Restore Bulk
    Router::post('/internship/bulk_restore', function () {
        throttle('CDCController/InternshipManagement', 'bulk_restore', 10, 60);
        controller('CDCController/InternshipManagement', ['method' => 'bulk_restore']);
    })
    ->name('internship.bulk_restore')
    ->meta([
        'title'       => 'Bulk Restore Internships',
        'description' => 'Restore multiple internships from trash.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Internship']
    ]);

    // 5. Restore Single
    Router::post('/internship/restore', function () {
        throttle('CDCController/InternshipManagement', 'restore', 20, 60);
        controller('CDCController/InternshipManagement', ['method' => 'restore']);
    })
    ->name('internship.restore')
    ->meta([
        'title'       => 'Restore Internship',
        'description' => 'Restore a single internship from trash.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Internship']
    ]);

    // 6. Permanent Delete Single (Critical)
    Router::delete('/internship/permanent/destroy', function () {
        throttle('CDCController/InternshipManagement', 'permanent_destroy', 5, 60);
        controller('CDCController/InternshipManagement', ['method' => 'permanent_destroy']);
    })
    ->name('internship.permanent.destroy')
    ->meta([
        'title'       => 'Permanent Delete Internship',
        'description' => 'Permanently remove a single internship record. Irreversible.',
        'method'      => 'DELETE',
        'auth'        => true,
        'roles'       => ['UPKPK', 'CDC'],
        'tags'        => ['CDC', 'Internship']
    ]);

});


/*
|--------------------------------------------------------------------------
| 3. Data Import Utilities
|--------------------------------------------------------------------------
|
| Group ini khusus menangani proses Import data Excel (Bulk Insert).
|
| - Access : CDC Only (Strict)
| - Rate   : 3 req / minute (Very Strict to prevent server overload)
*/
Router::group([
    'prefix'     => 'ajax/import',
    'middleware' => ['csrf'],
    'name'       => 'ajax.import.'
], function () {

    // 1. Preview (Parsing Excel -> JSON)
    Router::post('/preview/lecturer', function () {
        throttle('ImportController/LecturerImport', 'preview_import_lecturer', 3, 60, 'seconds');
        controller('ImportController/LecturerImport', ['method' => 'preview_import_lecturer']);
    })
    ->name('preview.lecturer')
    ->meta([
        'title'       => 'Preview Lecturer Import',
        'description' => 'Parse uploaded Excel file and return JSON for validation.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['Import', 'Lecturer']
    ]);

    // 2. Store (Simpan Data Terpilih ke DB)
    Router::post('/store/lecturer', function () {
        throttle('ImportController/LecturerImport', 'import_lecturer', 3, 60, 'seconds');
        controller('ImportController/LecturerImport', ['method' => 'import_lecturer']);
    })
    ->name('store.lecturer')
    ->meta([
        'title'       => 'Execute Lecturer Import',
        'description' => 'Insert selected lecturers into database with transaction.',
        'method'      => 'POST',
        'auth'        => true,
        'roles'       => ['CDC'],
        'tags'        => ['Import', 'Lecturer']
    ]);

});




# =========================================================================
# Public AJAX Routes
# =========================================================================
Router::group([
    'prefix' => 'ajax/public/announcement',
    'name'   => 'ajax.public.announcement.'
], function () {

    Router::post('/list', function () {
        throttle('ajax/public/announcement', 'list', 100, 60, 'seconds');
        Controller::run('LandingController/PublicAnnouncementController', ['method' => 'list']);
    })->name('list');

});