<?php
error_reporting(true);
session_start();

$title = 'Approval Attendance';
include "./controller/controller-init.php";


$action = $_GET['action'];



if ($action == 'lecturer') {

    try {
        // Query SQL dengan placeholder
        $sql = "
            UPDATE attendance 
            SET approval_dpm = :approval_dpm, 
                timestamp_dpm = :timestamp_dpm
            WHERE id_internship = :id_internship 
            AND id_attendance = :id_attendance
        ";

        // Siapkan statement
        $stmt = $_SERVER['PDO']->prepare($sql);

        // Data yang akan di-bind ke query
        $params = [
            ':approval_dpm'   => $_POST['approval_dpm'],
            ':timestamp_dpm'  => date('Y-m-d'), // Waktu saat ini
            ':id_internship'  => $_POST['id_internship'],
            ':id_attendance'  => $_POST['id_attendance']
        ];

        $stmt->execute($params);

        // Cek apakah ada baris yang diperbarui
        if ($stmt->rowCount() > 0) {
            make_snackbar('Attendance approved');
            redirect_referrer(1);
            exit;
        } else {
            make_snackbar('Check your submitted data and try again later');
            make_old('ModalApproval', 'Open');
            redirect_back(1);
            exit;
        }
    } catch (PDOException $e) {

        make_snackbar('Check your submitted data and try again later');
        make_old('ModalApproval', 'Open');
        redirect_back(1);
        exit;
    }
} else 


if ($action == 'supervisor') {


    make_snackbar('Only Authorize Supervisor can perform this action');
    role_checker('user_type', ['Supervisor'], 'notifikasi');


    // Validasi Form
    if (validate_all() === false) {
        make_snackbar('Check your submitted data and try again later');

        make_old('ModalApproval', $_POST['loop']);
        redirect_back(1);
        exit;
    }


    $id_user_company = $_POST['id_user_company'];
    $id_internship = $_POST['id_internship'];
    // Cek NIK
    $sql = "SELECT * FROM internship WHERE id_user_company = '$id_user_company' AND id_internship = '$id_internship'";
    $exec = $_SERVER['KONEKSI_DB']->query($sql);


    if ($exec->num_rows != 1) {
        make_snackbar('Only Authorize Supervisor can perform this action');

        make_old('ModalApproval', $_POST['loop']);
        redirect_back(1);
        exit;
    }


    $approval_data = array(
        'approval_spv' => $_POST['approval_spv'],
        'timestamp_spv' => date('Y-m-d'),
        'id_user_company' => $id_user_company,
    );

    $where_attendance = array(
        'id_internship' => $_POST['id_internship'],
        'id_attendance' => $_POST['id_attendance'],
    );

    $approval_dpm = table_update('attendance', $approval_data, $where_attendance);

    if ($approval_dpm === true) {
        make_snackbar('Attendance approved');
        redirect_referrer(1);
        exit;
    } else {
        make_snackbar('Check your submitted data and try again later');

        make_old('ModalApproval', $_POST['loop']);
        redirect_back(1);
        exit;
    }
} else {
    make_snackbar('You do not have permission to perform this action');

    redirect_back(1);
    exit;
}
