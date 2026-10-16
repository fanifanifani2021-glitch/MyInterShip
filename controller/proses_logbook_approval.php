<?php
error_reporting(true);
session_start();

$title = 'Approval Logbook';
include "./controller/controller-init.php";


$action = $_GET['action'];

if ($action == 'lecturer') {

    // Validasi Form
    if (validate_all() === false) {
        make_old('ModalApproval', $_POST['loop']);
        redirect_back(1);
        exit;
    }


    $nik = $_POST['nik'];
    $id_internship = $_POST['id_internship'];
    // Cek NIK
    $sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
    $exec = $_SERVER['KONEKSI_DB']->query($sql);


    if ($exec->num_rows != 1) {
        make_soloalert('error', 'Not Allowed', 'You don`t have permission to complete this action !');
        make_old('ModalApproval', $_POST['loop']);
        redirect_back(1);
        exit;
    }


    $approval_data = array(
        'approval_dpm' => $_POST['approval_dpm']
    );

    $where_logbook = array(
        'id_internship' => $_POST['id_internship'],
        'id_logbook' => $_POST['id_logbook'],
    );

    $approval_dpm = table_update('logbook', $approval_data, $where_logbook);

    if ($approval_dpm === true) {
        reflash_all();
        make_swal2('success', 'Approval Success', 'Approval Data has been updated successfully !');
        // redirect('logbook_student_intern&nim='.base64_encode($_POST['nim']). '&id_internship=' .base64_encode($_POST['id_internship']), 1); exit;
        redirect_referrer(1);
        exit;
    } else {
        make_swal2('error', 'Approval Failed', 'Please try again later !');
        make_old('ModalApproval', $_POST['loop']);
        redirect_back(1);
        exit;
    }
} else 


if ($action == 'supervisor') {

    // Validasi Form
    if (validate_all() === false) {
        make_old('ModalApproval', $_POST['loop']);
        redirect_back(1);
        exit;
    }


    $id_user_company = $_POST['id_user_company'];
    $id_internship = $_POST['id_internship'];
    // Cek NIK
    $sql = "SELECT user_type FROM user_company WHERE id_user_company = :id_user_company LIMIT 1";
    $stmt = $_SERVER['PDO']->prepare($sql);
    $stmt->execute(['id_user_company' => $id_user_company]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    // Jika user_type adalah HRD, skip pemeriksaan, jika bukan HRD, lakukan pemeriksaan
    if (strtolower($user->user_type) != 'hrd') {
        $sql = "SELECT * FROM internship WHERE id_user_company = :id_user_company AND id_internship = :id_internship LIMIT 1";
        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->execute(['id_user_company' => $id_user_company, 'id_internship' => $id_internship]);

        // Cek apakah hasilnya tidak ada atau lebih dari satu baris
        if ($stmt->rowCount() != 1) {
            make_soloalert('error', 'Not Allowed', 'You don`t have permission to complete this action!');
            make_old('ModalApproval', $_POST['loop']);
            redirect_back(1);
            exit;
        }
    }


    $approval_data = array(
        'approval_spv' => $_POST['approval_spv']
    );

    $where_logbook = array(
        'id_internship' => $_POST['id_internship'],
        'id_logbook' => $_POST['id_logbook'],
    );

    $approval_dpm = table_update('logbook', $approval_data, $where_logbook);

    if ($approval_dpm === true) {
        make_swal2('success', 'Approval Success', 'Approval Data has been updated successfully !');
        // redirect('intern_list_logbook&id_internship=' .base64_encode($_POST['id_internship']), 1); exit;
        redirect_referrer(1);
        exit;
    } else {
        make_swal2('error', 'Approval Failed', 'Please try again later !');
        make_old('ModalApproval', $_POST['loop']);
        redirect_back(1);
        exit;
    }
} else {
    make_soloalert('error', 'Not Allowed', 'You don`t have permission to complete this action !');
    redirect_back(1);
    exit;
}
