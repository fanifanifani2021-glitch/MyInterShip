<?php
session_start();


$id_discussion = $_POST['id_discussion'];
$user_id = $_SESSION['nim'] ?? $_SESSION['nim_nik_unit'] ?? $_SESSION['id_user_company'];


$sql = "SELECT * FROM discussion WHERE id_discussion = :id_discussion AND user_id = :user_id LIMIT 1";
$stmt = $_SERVER['PDO']->prepare( $sql );

$stmt->execute( [':id_discussion' => $id_discussion, ':user_id' => $user_id] );

if ( $stmt->rowCount() != 1 ) {

    http_response_code( 400 );
    make_snackbar('(01) Failed to remove Discussion Topic, please try again later');

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


$sql = "DELETE FROM discussion WHERE id_discussion = :id_discussion AND user_id = :user_id";
$stmt = $_SERVER['PDO']->prepare( $sql );

$stmt->execute( [':id_discussion' => $id_discussion, ':user_id' => $user_id] );

if ( $stmt->rowCount() < 1 ) {


    http_response_code( 401 );
    make_snackbar('(02) Failed to remove Discussion Topic, please try again later');

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
    make_snackbar('(03) Discussion Topic has been removed');

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
