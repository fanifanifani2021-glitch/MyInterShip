<?php
error_reporting(true);
session_start();

$title = 'Intern Coordinator';
include "./controller/controller-init.php";

$action = $_GET['action'];

if ($action == 'add') {
    set_rules('lecturer_nik', 'Lecturer NIK', ['required', 'exists:lecturer.nim_nik_unit'], ['required' => 'This field is required !', 'exists' => 'Lecturer does not exist !']);
    set_rules('lecturer_status', 'Lecturer`s Status', ['required'], ['required' => 'This field is required !']);
    set_rules('program_study', 'Program Study', ['required'], ['required' => 'This field is required !']);

    set_rules('lecturer_name', '', ['allow_not_required']);
    set_rules('lecturer_email', '', ['allow_not_required']);

    $cek_form = form_validation();

    // Cek Koor untuk prodi saat ini
    $program_study = $_POST['program_study'];
    $query = "SELECT * FROM lecturer WHERE prodi_koor = '$program_study' AND is_koor = 1 ";
    $cek_koor  = $_SERVER['KONEKSI_DB']->query($query);

    // if (mysqli_num_rows($cek_koor) == 1) {
    //     error('program_study', 'Intern Coordinator for selected program study has been set !');
    //     $cek_form = false;
    // }


    if ($cek_form === false) {
        make_snackbar('Please check your submitted data and try again later');
        redirect_back(1);
        exit;
    }


    $lecturer_nik = $_POST['lecturer_nik'];
    $program_study = $_POST['program_study'];


    // Insert Data
    $where_lecturer = array(
        'nim_nik_unit' => $_POST['lecturer_nik'],
    );

    $data_lecturer = array(
        'is_koor' => 1,
        'prodi_koor' => $_POST['program_study'],
        'id_kampus' => $_SESSION['CDC_Data'] ['id_kampus'] ?? '1',
    );

    $insert_lecturer = table_update('lecturer', $data_lecturer, $where_lecturer);

    if ($insert_lecturer === TRUE) {
        // make_swal2('success', 'Adding Data Success', 'Supervisor has been successfully added !');
        make_snackbar('Intern Coordinator has been set successfully !');
        redirect('intern_co_management', 1);
        exit;
    } else {
        make_snackbar('Please check your submitted data and try again later');
        redirect_back(1);
        exit;
    }
} else 

if ($action == 'edit') {
    set_rules('old_lecturer_nik', 'Lecturer NIK', ['required', 'exists:lecturer.nim_nik_unit'], ['required' => 'This field is required !', 'exists' => 'Lecturer does not exist !']);
    set_rules('lecturer_nik', 'Lecturer NIK', ['required', 'exists:lecturer.nim_nik_unit'], ['required' => 'This field is required !', 'exists' => 'Lecturer does not exist !']);
    set_rules('lecturer_status', 'Lecturer`s Status', ['required'], ['required' => 'This field is required !']);
    set_rules('program_study', 'Program Study', ['required'], ['required' => 'This field is required !']);

    set_rules('lecturer_name', '', ['allow_not_required']);
    set_rules('lecturer_email', '', ['allow_not_required']);

    $cek_form = form_validation();

    // Cek Koor untuk prodi saat ini
    $program_study = $_POST['program_study'];
    $query = "SELECT * FROM lecturer WHERE prodi_koor = '$program_study' AND is_koor = 1 ";
    $cek_koor  = $_SERVER['KONEKSI_DB']->query($query);

    if (
        $_POST['old_lecturer_nik'] != $_POST['lecturer_nik'] &&
        $_POST['old_program_study'] != $_POST['program_study'] && $cek_koor->num_rows == 1
    ) {
        error('program_study', 'Intern Coordinator for selected program study has been set !');
        $cek_form = false;
    }


    if ($cek_form === false) {
        make_snackbar('Please check your submitted data and try again later');
        redirect_back(1);
        exit;
    }


    $lecturer_nik = $_POST['lecturer_nik'];
    $program_study = $_POST['program_study'];


    // Insert Data
    $where_lecturer = array(
        'nim_nik_unit' => $_POST['lecturer_nik'],
        'id_kampus' => $_SESSION['CDC_Data'] ['id_kampus'] ?? '1',
    );

    $data_lecturer = array(
        'is_koor' => 1,
        'prodi_koor' => $_POST['program_study'],
    );

    $insert_lecturer = table_update('lecturer', $data_lecturer, $where_lecturer);


    if ($_POST['old_lecturer_nik'] != $_POST['lecturer_nik']) {

        // Insert Data
        $where_lecturer = array(
            'nim_nik_unit' => $_POST['old_lecturer_nik'],
        );

        $data_lecturer = array(
            'is_koor' => 0,
            'prodi_koor' => NULL,
        );

        $insert_lecturer = table_update('lecturer', $data_lecturer, $where_lecturer);
    }

    if ($insert_lecturer === TRUE) {
        // make_swal2('success', 'Adding Data Success', 'Supervisor has been successfully added !');
        make_snackbar('Intern Coordinator has been set successfully !');
        redirect('intern_co_management', 1);
        exit;
    } else {
        make_snackbar('Please check your submitted data and try again later');
        redirect_back(1);
        exit;
    }
} else {
    make_snackbar('You do not have permission to perform this action !');
    redirect_back(1);
    exit;
}
