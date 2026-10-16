<?php 

if ( !$_SESSION['is_koor'] ) {
    make_snackbar('Only Internship Coordinator can access this page !');
    redirect_back(1); exit;
}