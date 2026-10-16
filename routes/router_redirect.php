<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

/*
|--------------------------------------------------------------------------
| Redirect Routes (Moved Permanently 301)
|--------------------------------------------------------------------------
|
| File ini menangani URL lama (Legacy) yang struktur route-nya sudah berubah.
| Kita menampilkan halaman pemberitahuan (View 301) selama 5 detik,
| lalu otomatis redirect menggunakan helper Url::moved().
|
*/

// 1. Legacy Route: /login -> /auth/login
Router::get('/login', function () {
    $targetUrl = router('login.index');

    renderView('errors.error_301', [
        'newUrl'  => $targetUrl,
        'seconds' => 5 
    ], function() use ($targetUrl) {
        Url::moved($targetUrl, 5);
    });
});

// 2. Legacy Route: /login/industry -> /auth/login/industry
Router::get('/login/industry', function () {
    $targetUrl = router('login.industry');

    renderView('errors.error_301', [
        'newUrl'  => $targetUrl,
        'seconds' => 5 
    ], function() use ($targetUrl) {
        Url::moved($targetUrl, 5);
    });
});

// 3. Legacy Route: /login/student -> /auth/login/student
Router::get('/login/student', function () {
    $targetUrl = router('login.student');

    renderView('errors.error_301', [
        'newUrl'  => $targetUrl,
        'seconds' => 5 
    ], function() use ($targetUrl) {
        Url::moved($targetUrl, 5);
    });
});

// 4. Legacy Route: /login/cdc -> /auth/login/cdc
Router::get('/login/cdc', function () {
    $targetUrl = router('login.cdc');

    renderView('errors.error_301', [
        'newUrl'  => $targetUrl,
        'seconds' => 5 
    ], function() use ($targetUrl) {
        Url::moved($targetUrl, 5);
    });
});

// 5. Legacy Route: /login/lecturer -> /auth/login/lecturer
Router::get('/login/lecturer', function () {
    $targetUrl = router('login.lecturer');

    renderView('errors.error_301', [
        'newUrl'  => $targetUrl,
        'seconds' => 5 
    ], function() use ($targetUrl) {
        Url::moved($targetUrl, 5);
    });
});

// 6. Legacy Route: /register/student -> /auth/register/student
Router::get('/register/student', function () {
    $targetUrl = router('register.student.index');

    renderView('errors.error_301', [
        'newUrl'  => $targetUrl,
        'seconds' => 5 
    ], function() use ($targetUrl) {
        Url::moved($targetUrl, 5);
    });
});