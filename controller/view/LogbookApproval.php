<?php
error_reporting(false);
session_start();

$data = $_GET['data'];
$data = JWTCustomData($data);

$id_logbook = $data['id_logbook'];

$sql = "SELECT * FROM logbook WHERE id_logbook = :id_logbook LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_logbook' => $id_logbook]);

if ($stmt->rowCount() != 1) {
    make_swal2('error', 'Not Found', 'Logbook data is not found !');
    redirect_back();
}

$logbook_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Student Data
$sql = "SELECT * FROM internship INNER JOIN student_internship ON student_internship.nim = internship.nim WHERE id_internship = :id_internship LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_internship' => $logbook_data['id_internship']]);

if ($stmt->rowCount() != 1) {
    $internship_data = [];
} else {
    $internship_data = $stmt->fetch(PDO::FETCH_ASSOC);
}


// Check Approver
$approver = [];
if ($data['role'] == 'Supervisor') {
    $approver['status'] = $logbook_data['approval_spv'];

    if (in_array($approver['status'], ['Waiting'])) {

        $approver['approval_date'] = "Waiting for Approval";
        $approver['approved_by'] = "Waiting for Approval";
    } else {
        $approver['approval_date'] = $logbook_data['timestamp_spv'];


        // Get Approved By
        $id_user_company = $logbook_data['id_user_company'] ?? $internship_data['id_user_company'];

        $sql = "SELECT * FROM user_company INNER JOIN company ON company.id_company = user_company.id_company WHERE id_user_company = :id_user_company LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_user_company' => $id_user_company]);

        if ($stmt->rowCount() != 1) {

            $user_company = [];
        } else {
            $user_company = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $approver['approved_by']['id_user_company'] = $id_user_company;
        $approver['approved_by']['supervisor_name'] = $user_company['user_fullname'] ?? '';
        $approver['approved_by']['company_name'] = $user_company['name'];
    }
} else if ($data['role'] == 'Lecturer') {
    $approver['status'] = $logbook_data['approval_dpm'];

    if (in_array($approver['status'], ['Waiting'])) {

        $approver['approval_date'] = "Waiting for Approval";
        $approver['approved_by'] = "Waiting for Approval";
    } else {
        $approver['approval_date'] = $logbook_data['timestamp_dpm'];

        // Get Approved By
        $nik_dospem = $logbook_data['nik_dospem'] ?? $internship_data['nik_dospem'];

        $sql = "SELECT * FROM lecturer INNER JOIN tb_kampus ON tb_kampus.id_kampus = lecturer.id_kampus WHERE nim_nik_unit = :nik_dospem LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nik_dospem' => $nik_dospem]);

        if ($stmt->rowCount() != 1) {

            $lecturer = [];
        } else {
            $lecturer = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $approver['approved_by']['id_user_company'] = $nik_dospem;
        $approver['approved_by']['supervisor_name'] = $lecturer['name'] ?? '';
        $approver['approved_by']['company_name'] = $lecturer['nama_kampus'];
    }
} else {
    $approver['status'] = 'Unknown';
    $approver['approval_date'] = 'Unknown';

    $approver['approved_by']['id_user_company'] = 'Unknown';
    $approver['approved_by']['supervisor_name'] = 'Unknown';
    $approver['approved_by']['company_name'] = 'Unknown';
}

// view('attendance/attendance_approval', ['title' => 'Attendance Approval', 'internship_data' => $internship_data, 'approver' => $approver, 'logbook_data' => $logbook_data, 'role' => $data['role']]);
renderView('landing.internship_data.logbook_approval', ['title' => 'Logbook Approval', 'internship_data' => $internship_data, 'approver' => $approver, 'logbook_data' => $logbook_data, 'role' => $data['role']]);