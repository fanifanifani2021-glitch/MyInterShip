<?php

$_SESSION['REDIRECT'] = $_SERVER['QUERY_STRING'];
$_SESSION['REDIRECT'] = str_replace("page=","", $_SESSION['REDIRECT'] );


if ( $_SESSION['FIRST_TOLAK'] === NULL ) {
    $_SESSION['FIRST_TOLAK'] = 0;
} else {
    $_SESSION['FIRST_TOLAK'] = 1;
}

if ( $_SESSION['role'] != 'industri' ) {

    make_snackbar('Please login to your account !');
    redirect('login_industri', 1); exit;
}