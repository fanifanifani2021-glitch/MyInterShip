<?php 

if ( !$_SESSION['is_pembimbing']) {
    make_snackbar('Only Internship Supervisor can access this page !');
    redirect_back(1); exit;
}