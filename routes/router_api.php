<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

// semua API otomatis pakai token middleware
Router::group([
    'prefix'     => 'api',
    'middleware' => ['api']
], function () {

    Router::get('/docs', function () {
        Router::json(200, 'API Documentation', Router::apiDocs());
    });

    Router::post('/ping', function () {
        Router::json(200, 'API Alive', [
            'time' => date('Y-m-d H:i:s')
        ]);
    })->name('api.ping.post');

    Router::get('/ping', function () {
        Router::json(200, 'API Alive', [
            'time' => date('Y-m-d H:i:s')
        ]);
    })->name('api.ping.get')->meta(['description' => 'Check if the API is alive']);


    // API Stats
    Router::get('/stats/myinternship/overview', function() { 
        api('myinternship/MyInternshipStats', 'GET');
    })->name('api.stats.myinternship-overview');

    Router::get('/stats/company/count', function() {
        api('myinternship/stats/CountCompany', 'GET');
    })->name('api.stats.company-count');

    Router::get('/stats/internship/count', function() {
        api('myinternship/stats/CountInternship', 'GET');
    })->name('api.stats.internship-count');


    // Get Random Data
    Router::get('/random/company', function() {
	    api('company/random_company', 'GET');
    })->name('api.random.company');

    Router::get('/random/offer', function() {
	    api('company/random_company_offer', 'GET');
    })->name('api.random.offer');

    Router::get('/random/job', function() {
	    api('company/random_company_job', 'GET');
    })->name('api.random.job');


    // API Login
    Router::post('/auth/student', function() {
		api('myinternship/MyInternshipStudentLogin', 'POST');
    })->name('api.auth.student-login');

    Router::post('/auth/lecturer', function() {
		api('myinternship/MyInternshipLecturerLogin', 'POST');
    })->name('api.auth.lecturer-login');

    Router::post('/auth/industry', function() {
		api('myinternship/MyInternshipIndustryLogin', 'POST');
    })->name('api.auth.lecturer-login');

    Router::post('/auth/polibatam/login', function() {
		api('polibatam/LoginAPI', 'POST');
    })->name('api.auth.polibatam-login');

    Router::post('/auth/polibatam/old-login', function() {
		api('polibatam/OldLoginAPI', 'POST');
    })->name('api.auth.polibatam-old_login');

    Router::post('/auth/polibatam/biodata', function() {
	    api('polibatam/BiodataAPI', 'POST');
    })->name('api.auth.polibatam-biodata');



    // Internship Detail Data
    Router::post('/internship/company', function() {
		api('company/job', 'GET');
    })->name('api.intership.company');

    Router::post('/internship/logbook', function() {
    	api('logbook/LogbookByInternship', 'POST');
    })->name('api.intership.student-logbook');

    Router::post('/lecturer/logbook', function() {
	    api('logbook/LogbookByLecturer', 'POST');
    })->name('api.lecturer.student-logbook');

    Router::post('/lecturer/logbook/{id}', function() {
	    api('logbook/logbook_detail', 'POST');
    })->name('api.lecturer.logbook-detail');

    Router::post('/lecturer/attendance', function() {
	    api('attendance/AttendanceByLecturer', 'POST');
    })->name('api.lecturer.student-attendance');

    Router::post('/lecturer/attendance/{id}', function() {
	    api('attendance/attendance_detail', 'POST');
    })->name('api.lecturer.attendance-detail');


    // Company
    Router::post('/company/list', function() {
		api('company/company_list', 'POST');
    })->name('api.company.list');

    Router::post('/company/detail/{id_company}', function() {
		api('company/company_detail', 'GET');
    })->name('api.company.detail');


    Router::post('/company/{id_company}/user/list', function() {
		api('company/company_user', 'POST');
    })->name('api.company.detail');




    // Job
    Router::post('/job/list', function() {
		api('company/job_list', 'POST');
    })->name('api.job.list');

    Router::post('/job/detail/{id_company}', function() {
		api('company/job_detail', 'GET');
    })->name('api.job.detail');


    // Kampus
    Router::post('/campus/list', function() {
        controller('API/Campus/CampusController', ['method' => 'list']);
    })->meta([
        'name' => 'campus.list',
        'description' => 'Get list of campuses, supports DataTables filtering, pagination, and sorting',
        'method' => 'POST',
        'params' => [
            'draw' => 'integer, DataTables draw count',
            'start' => 'integer, DataTables start index for pagination',
            'length' => 'integer, number of records to fetch',
            'search[value]' => 'string, global search keyword',
            'order[0][column]' => 'integer, column index for ordering',
            'order[0][dir]' => 'string, ASC or DESC'
        ],
        'response' => [
            'draw' => 'integer, echo from request',
            'recordsTotal' => 'integer, total rows in tb_kampus',
            'recordsFiltered' => 'integer, total rows after filtering',
            'data' => 'array of campuses, each item contains id_kampus, nama_kampus, website, logo, created_at, updated_at'
        ]
    ]);

    Router::post('/campus/{id_kampus}/detail', function($params) {
      controller('API/Campus/CampusController', ['method' => 'detail', 'id_kampus' => $params['id_kampus']]);
    })->meta([
        'name' => 'campus.detail',
        'description' => 'Get detail of a specific campus by id_kampus',
        'method' => 'POST',
        'params' => [
            'id_kampus' => 'integer, required, ID of the campus'
        ],
        'response' => [
            'id_kampus' => 'integer',
            'nama_kampus' => 'string',
            'website' => 'string',
            'logo' => 'string',
        ]
    ]);


  // List jurusan
  Router::post('/campus/{id_kampus}/jurusan', function($params) {
      controller('API/Campus/MajorController', ['method' => 'list_prodi_kampus', 'id_kampus' => $params['id_kampus']]);
  })->meta([
      'description' => 'Get all departments (jurusan) for a specific campus',
      'params' => [
          'id_kampus' => 'int | required | ID of the campus'
      ],
      'response' => [
          'data' => 'Array of jurusan with id, en, major',
          'recordsTotal' => 'Total number of records',
          'recordsFiltered' => 'Total records after filtering (for DataTables)'
      ]
  ]);

  // List prodi
  Router::post('/campus/{id_kampus}/jurusan/{jurusan}/prodi', function($params) {
      controller('API/Campus/MajorController', ['method' => 'list_prodi_jurusan', 'id_kampus' => $params['id_kampus'], 'jurusan' => $params['jurusan']]);
  })->meta([
      'description' => 'Get all study programs (prodi) for a specific department in a campus',
      'params' => [
          'id_kampus' => 'int | required | ID of the campus',
          'jurusan' => 'string | required | Name of the department'
      ],
      'response' => [
          'data' => 'Array of prodi with id, en, prodi, jurusan, major',
          'recordsTotal' => 'Total number of records',
          'recordsFiltered' => 'Total records after filtering (for DataTables)'
      ]
  ]);

  Router::post('/campus/{id_kampus}/prodi', function($params) {
      controller('API/Campus/ProdiController', ['method' => 'list', 'id_kampus' => $params['id_kampus']]);
  })->meta([
      'description' => 'Get all study programs (prodi) for a campus without filtering by jurusan',
      'parameters' => [
          'id_kampus' => 'Campus ID (path parameter, required)'
      ],
      'response' => [
          'data' => 'Array of prodi',
          'recordsTotal' => 'Total records',
          'recordsFiltered' => 'Filtered records (same as total)'
      ]
  ]);

  Router::post('/campus/{id_kampus}/prodi/{kode_prodi}', function($params) {
      controller('API/Campus/ProdiController', [
          'method' => 'detail', 
          'id_kampus' => $params['id_kampus'], 
          'kode_prodi' => $params['kode_prodi']
      ]);
  })->meta([
      'description' => 'Get detail of a study program (prodi) for a campus by kode_prodi, including student internship statistics per angkatan',
      'parameters' => [
          'id_kampus'  => 'Campus ID (path parameter, required)',
          'kode_prodi' => 'Study program code (path parameter, required)'
      ],
      'response' => [
          'program_study' => [
              [
                  'id'          => 'Kode prodi',
                  'en'          => 'Study program name',
                  'prodi'       => 'Prodi name',
                  'jurusan'     => 'Jurusan name',
                  'major'       => 'Major name',
                  'student_stat' => [
                      'angkatan' => [
                          'belum_magang' => 'Number of students who have not started internship',
                          'sudah_magang' => 'Number of students who have completed internship',
                          'total'        => 'Total number of students for this angkatan'
                      ]
                  ]
              ]
          ]
      ]
  ]);


});