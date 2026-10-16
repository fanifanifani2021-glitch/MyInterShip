<?php
error_reporting(false);
session_start();

// On Maintenance
$_SERVER['ON_MAINTENANCE'] = false;
$_SERVER['ADMIN_MAINTENANCE'] = array(
    'psteam' => password_hash('PSTEAM12379', PASSWORD_DEFAULT),
);

$_SERVER['SECURE_ACCESS'] = true;
$_SERVER['UNIVERSITY'] = 'UNIVERSAL';

$_SERVER['APP_NAME'] = 'MyInternship';
$_SERVER['APP_DESCRIPTION'] = 'Information System for Managing Internship Administration in Polibatam';
$_SERVER['APP_KEYWORDS'] = 'Information System, Internship, Internship Administration, Polibatam, Politeknik Negeri Batam';
$_SERVER['APP_AUTHOR'] = 'Gilang Bagus Ramadhan, Iqbal Afif, PST';

$_SERVER['API_WELCOME'] = $_SERVER['APP_NAME'] . ' API at your services !';
$_SERVER['API_ADDITIONAL_WELCOME'] = "Happy Eid al-Adha for All Muslim in the world !";

$_SERVER['NIK_EXCEPTION'] = ['122287'];

$_SERVER['CDC_DEFAULT_PASSWORD'] = 'CDC-login#2022';
$_SERVER['LECTURER_DEFAULT_PASSWORD'] = 'Lecturer-Login#2022';
$_SERVER['STUDENT_DEFAULT_PASSWORD'] = 'Student-Login#2022';
$_SERVER['INDUSTRY_DEFAULT_PASSWORD'] = 'password';

$_SERVER['MAIN_LOGO'] = './assets/img/my_internship_logo_grey5.png';
// $_SERVER['PROFILE_BACKGROUND'] = './assets/img/blogpost.jpg';
$_SERVER['PROFILE_BACKGROUND'] = './core/background.jpg';



$_SERVER['DISCUSSION_ACTOR'] = [
    'spv' => 'Industry Supervisor',
    'hrd' => 'Industry HRD',
    'dpm' => 'Lecturer',
    'dkm' => 'Internship Coordinator',
    'mhs' => 'Student',
    'cdc' => 'CDC',
    'anon' => 'Another',
    
    ];


date_default_timezone_set('Asia/Jakarta');
