<?php
    session_start();

    if ($_SESSION['pkpk'] != '1'){
        swal2('Error', 'Access Denied!');
        back(); 
        exit();
    }
?>