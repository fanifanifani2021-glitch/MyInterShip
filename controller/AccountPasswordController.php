<?php
header('Access-Control-Allow-Origin: *');
error_reporting(false);
session_start();

$nim = base64_decode($_POST['nim']);
$password = password_hash($nim, PASSWORD_DEFAULT);

try {
    $pdo = $_SERVER['PDO'];
    $stmt = $pdo->prepare("UPDATE student_internship SET `password` = :password, password_reset = 1 WHERE nim = :nim");
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':nim', $nim);
    
    if ($stmt->execute()) {
        http_response_code(200);
        exit;
    } else {
        http_response_code(400);
        exit;
    }

} catch (PDOException $e) {
    http_response_code(500);
    // Log error jika perlu: error_log($e->getMessage());
    exit;
}