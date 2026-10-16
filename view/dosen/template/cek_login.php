<?php

$_SESSION['REDIRECT'] = $_SERVER['QUERY_STRING'];
$_SESSION['REDIRECT'] = str_replace("page=","", $_SESSION['REDIRECT'] );

if ( $_SESSION['FIRST_TOLAK'] === NULL ) {
    $_SESSION['FIRST_TOLAK'] = 0;
} else {
    $_SESSION['FIRST_TOLAK'] = 1;
}

if ( $_SESSION['role'] != 'Dosen' ) {
    make_snackbar('Please login to your account !');
    // view('auth/login'); exit;
    redirect('lecturer_login',1); exit;
}