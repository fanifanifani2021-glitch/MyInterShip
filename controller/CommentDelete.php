<?php
session_start();


$id_comment = $_POST['id_comment'];
$user_id = $_SESSION['nim'] ?? $_SESSION['nim_nik_unit'] ?? $_SESSION['id_user_company'] ?? $_SESSION['username'];


$sql = "SELECT * FROM discussion_comment WHERE id_comment = :id_comment AND user_id = :user_id LIMIT 1";
$stmt = $_SERVER['PDO']->prepare( $sql );

$stmt->execute( [':id_comment' => $id_comment, ':user_id' => $user_id] );

if ( $stmt->rowCount() != 1 ) {

    http_response_code( 400 );
    make_snackbar('(01) Failed to remove Comment, please try again later');

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


$sql = "DELETE FROM discussion_comment WHERE id_comment = :id_comment AND user_id = :user_id";
$stmt = $_SERVER['PDO']->prepare( $sql );

$stmt->execute( [':id_comment' => $id_comment, ':user_id' => $user_id] );

if ( $stmt->rowCount() < 1 ) {


    http_response_code( 401 );
    make_snackbar('(02) Failed to remove Comment, please try again later');

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
    make_snackbar('(03) Comment has been removed');

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
