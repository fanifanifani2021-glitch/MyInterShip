<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');

Router::group([
    'prefix'     => 'manual/industry',
    'name' => 'manual/industry.'
], function () {

    Router::get('attendance_location', function() {
        renderView('manual.industry.attendance_location');
    })->name('attendance_location');

});


Router::group([
    'prefix'     => 'manual/cdc',
    'name' => 'manual/cdc.'
], function () {

    Router::get('lecturer_management', function() {
        renderView('manual.cdc.lecturer_management');
    })->name('lecturer_management');

});