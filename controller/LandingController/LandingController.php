<?php

switch ($method):
    default:
        view('exception/error_no_role');
        break;
    case 'home':
        renderView('landing.home', ['title' => 'Home', 'routes' => 'home']);
        break;


    case 'dashboard_mahasiswa':
        renderView('student.dashboard', ['title' => 'Student Dashboard', 'routes' => 'dashboard']);
        break;


endswitch;
