<?php
header('Content-Type: application/json');
error_reporting(false);
session_start();

$_SESSION['error'] = [];
$_SESSION['notifikasi'] = [];


http_response_code(200);