<?php
session_start();

$id_internship = $_POST['id_internship'];
$id_discussion = $_POST['id_discussion'];

$sql = "SELECT * FROM discussion WHERE id_internship = :id_internship AND id_discussion = :id_discussion LIMIT 1";
$stmt = $_SERVER['PDO']->prepare( $sql );
$stmt->execute( [':id_internship' => $id_internship, ':id_discussion' => $id_discussion] );

if ( $stmt->rowCount() != 1 ) {
    
    http_response_code( 400 );
    make_snackbar('(01) Failed to un-report Comment, please try again later');

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
    make_snackbar('(02) Failed to un-report Comment, please try again later');

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

$user_id = $_SESSION['nim'] ?? $_SESSION['nim_nik_unit'] ?? $_SESSION['id_user_company'] ?? $_SESSION['username'];

$sql = "SELECT * FROM comment_report WHERE id_comment = :id_comment AND user_id = :user_id LIMIT 1";
$stmt = $_SERVER['PDO']->prepare( $sql );
$stmt->execute( [':id_comment' => $id_comment, ':user_id' => $user_id] );

if ( $stmt->rowCount() != 1 ) {
    
    http_response_code( 400 );
    make_snackbar('(03) Failed to un-report Comment, please try again later');

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



$sql = "DELETE FROM comment_report WHERE id_comment = :id_comment AND user_id = :user_id LIMIT 1";
$stmt = $_SERVER['PDO']->prepare( $sql );
$stmt->execute( [':id_comment' => $id_comment, ':user_id' => $user_id] );

if ( $stmt->rowCount() != 1 ) {
    
    http_response_code( 400 );
    make_snackbar('(04) Failed to un-report Comment, please try again later');

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
    make_snackbar('(04) Report has been canceled');

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