<?php
error_reporting(false); session_start();


if (!array_key_exists($_POST['username'], $_SERVER['ADMIN_MAINTENANCE'])) {

    make_swal2('error', 'Not Authorize'); 
    redirect_back(1); exit;
} else

if (!password_verify($_POST['password'], $_SERVER['ADMIN_MAINTENANCE'][$_POST['username']])) {

    make_swal2('error', 'Not Authorize'); redirect_back(1); exit;

} else {


    session_start();
    $_SESSION['FLOGIN'] = TRUE;
    $_SESSION['FLOGIN-TIME'] = date('Y-m-d H:i:s');

    // make_snackbar('Welcome to ' . $_SERVER['APP_NAME'] . ' Admin !');
    make_swal2('success', 'Authorize', 'Welcome to ' . $_SERVER['APP_NAME'] . ' Admin !'); redirect_back(1); exit;

    redirect('express_login', 1); exit;

}
