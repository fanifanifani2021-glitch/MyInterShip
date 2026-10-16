<?php
    session_start();

    if ($_SESSION['user_type'] != 'HRD'){
        swal2('Error', 'Access Denied!');
        back(); 
        exit();
    }
?>