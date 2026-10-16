<?php
session_start();

$id_internship = $_POST['id_internship'];
$id_discussion = $_POST['id_discussion'];

$sql = "SELECT * FROM discussion WHERE id_internship = :id_internship AND id_discussion = :id_discussion LIMIT 1";
$stmt = $_SERVER['PDO']->prepare( $sql );
$stmt->execute( [':id_internship' => $id_internship, ':id_discussion' => $id_discussion] );

if ( $stmt->rowCount() != 1 ) {
    
    http_response_code( 400 );
    make_snackbar('(01) Failed to report Comment, please try again later');

    whenAjaxRequest(function () {

        $output = array(
            'status' => 0,
            'validation' => $_SESSION['notifikasi'],
            'error' => $_SESSION['error'],
        );

        response_json(400, $output);
    });

    redirect_back(1); exit;
}

$id_comment = $_POST['id_comment'];

// Cek Comment dan Discussion

$sql = "SELECT * FROM discussion_comment WHERE id_discussion = :id_discussion AND id_comment = :id_comment LIMIT 1";
$stmt = $_SERVER['PDO']->prepare( $sql );
$stmt->execute( [':id_discussion' => $id_discussion, ':id_comment' => $id_comment] );

if ( $stmt->rowCount() != 1 ) {
    
    http_response_code( 400 );
    make_snackbar('(02) Failed to report Comment, please try again later');

    whenAjaxRequest(function () {

        $output = array(
            'status' => 0,
            'validation' => $_SESSION['notifikasi'],
            'error' => $_SESSION['error'],
        );

        response_json(400, $output);
    });

    redirect_back(1); exit;
}


$report = escape_string( $_POST['report'] );
$timestamp_add = date('Y-m-d H:i:s'); 

$user_id = $_SESSION['nim'] ?? $_SESSION['nim_nik_unit'] ?? $_SESSION['id_user_company'] ?? $_SESSION['username'];

$user_type = escape_string( $_POST['user_type'] ); 
$user_name = escape_string( $_POST['user_name'] );

$sql = "INSERT INTO `comment_report`(`id_report`, `id_comment`, `user_type`, `user_id`, `user_name`, `reason`, `timestamp_add`, `timestamp_update`) 
VALUES ( :id_report, :id_comment, :user_type, :user_id, :user_name, :report, :timestamp_add, :timestamp_update )";
$stmt = $_SERVER['PDO']->prepare( $sql );


$stmt->execute([ ':id_report' => NULL, ':id_comment' => $id_comment, ':user_type' => $user_type, ':user_id' => $user_id, ':user_name' => $user_name, ':report' => $report, 
':timestamp_add' => $timestamp_add, ':timestamp_update' => '-'] );



// $sql = mysqli_query( $_SERVER['KONEKSI_DB'] , "INSERT INTO `comment_report`(`id_report`, `id_comment`, `user_type`, `user_id`, `user_name`, `reason`, `timestamp_add`, `timestamp_update`) 
//     VALUES ( NULL, '$id_comment', '$user_type', '$user_id', '$user_name', '$report', '$timestamp_add', '-' )");

if ( $stmt->rowCount() != 1 ) {

    http_response_code( 400 );
    make_snackbar('(03) Failed to report comment, try again later');

    whenAjaxRequest(function () {

        $output = array(
            'status' => 0,
            'validation' => $_SESSION['notifikasi'],
            'error' => $_SESSION['error'],
        );

        response_json(400, $output);
    });

    redirect_back(1); exit;
        

} else {

    http_response_code( 200 );
    make_snackbar('(04) Comment has been reported');

    whenAjaxRequest(function () {

        $output = array(
            'status' => 0,
            'validation' => $_SESSION['notifikasi'],
            'error' => $_SESSION['error'],
        );

        response_json(200, $output);
    });

    redirect_back(1); exit;
}
