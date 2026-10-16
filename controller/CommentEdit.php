<?php
session_start();


$id_comment = $_POST['id_comment'];
$user_id = $_SESSION['nim'] ?? $_SESSION['nim_nik_unit'] ?? $_SESSION['id_user_company'] ?? $_SESSION['username'];


$sql = "SELECT * FROM discussion_comment WHERE id_comment = :id_comment AND user_id = :user_id LIMIT 1";
$stmt = $_SERVER['PDO']->prepare( $sql );

$stmt->execute( [':id_comment' => $id_comment, ':user_id' => $user_id] );

if ( $stmt->rowCount() != 1 ) {

    http_response_code( 400 );
    make_snackbar('(01) Failed to edit Comment, please try again later');

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

$comment = $_POST['comment'];

$sql = "UPDATE discussion_comment SET comment = :comment, timestamp_update = :timestamp_update WHERE id_comment = :id_comment AND user_id = :user_id";
$stmt = $_SERVER['PDO']->prepare( $sql );

$stmt->execute( [':id_comment' => $id_comment, ':comment' => $comment, ':timestamp_update' => date('Y-m-d H:i:s'), ':user_id' => $user_id] );

if ( $stmt->rowCount() < 1 ) {


    http_response_code( 401 );
    make_snackbar('(02) Failed to edit Comment, please try again later');

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
    make_snackbar('(03) Comment has been edited');

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
