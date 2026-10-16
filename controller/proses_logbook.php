<?php
error_reporting(true);
session_start();

$title = 'Logbook';
include "./controller/controller-init.php";


$action = $_GET['action'];
if ($action == 'add') {

    set_rules('id_internship', '', ['required'], ['required' => 'This field is required !']);
    set_rules('nim', '', ['required'], ['required' => 'This field is required !']);
    set_rules('week_num', '', ['required'], ['required' => 'This field is required !']);
    set_rules('start_date', '', ['required', 'valid_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !']);
    set_rules('end_date', '', ['required', 'valid_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !']);
    set_rules('description', '', ['required'], ['required' => 'This field is required !']);
    set_rules('documentation', '', ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();


    if ($cek_form === false) {
        make_swal2('error', 'Check your submitted data', 'Required coloumn cannot be empty ! (00)');
        // make_snackbar('Please check your submitted data !');
        redirect_back(1);
        exit;
    }

    $sql = "SELECT * FROM logbook WHERE id_internship = '" . $_POST['id_internship'] . "' AND week_num = '" . $_POST['week_num'] . "' ";
    $exec = $_SERVER['KONEKSI_DB']->query($sql);

    if ($exec->num_rows != 0) {
        // make_snackbar('Duplicate logbook data !');
        make_swal2('error', 'Duplicate logbook data', 'Logbook for current week num have been added ! (01)');

        error('week_num', 'Duplicate logbook data !');
        redirect_back(1);
        exit;
    }


    $logbook_data = array(
        'id_logbook' => NULL,
        'id_internship' => $_POST['id_internship'],
        'nim' => $_POST['nim'],

        'description' => $_POST['description'],
        'documentation' => $_POST['documentation'],

        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],

        'week_num' => $_POST['week_num'],

        'approval_spv' => 'Waiting',
        'approval_dpm' => 'Waiting',
    );

    $insert_logbook = table_insert2('logbook', $logbook_data);

    if ($insert_logbook === true) {
        make_swal2('success', 'Adding Data Success', 'Logbook data has been added successfully !');
        redirect('logbook_internship&id_internship=' . base64_encode($_POST['id_internship']), 1);
        exit;
    } else {

        if ($_POST['nim'] == '3311801013') {
            var_dump($_SERVER['KONEKSI_DB']->error);
            var_dump($insert_logbook);

            table_insert('logbook', $logbook_data, 'SHOW_SQL');
            exit;
        }

        dump_to_file('logbook_log', $_POST['nim'] . ' => ' . $_SERVER['KONEKSI_DB']->error . '\n');
        make_swal2('error', 'Failed to Add Logbook', 'Please try again later ! (02)');
        // make_snackbar('Please check your submitted data and try again later !');
        redirect_back(1);
        exit;
    }
} else


if ($action == 'edit') {

    set_rules('id_internship', '', ['required'], ['required' => 'This field is required !']);
    set_rules('nim', '', ['required'], ['required' => 'This field is required !']);
    set_rules('old_week', '', ['required'], ['required' => 'This field is required !']);

    set_rules('week_num', '', ['required'], ['required' => 'This field is required !']);
    set_rules('start_date', '', ['required', 'valid_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !']);
    set_rules('end_date', '', ['required', 'valid_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !']);
    set_rules('description', '', ['required'], ['required' => 'This field is required !']);
    set_rules('documentation', '', ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();


    if ($cek_form === false) {
        make_snackbar('Please check your submitted data !');
        redirect_back(1);
        exit;
    }

    // Gunakan statement prepared untuk menghindari SQL injection
    $sql = "SELECT * FROM logbook WHERE id_internship = :id_internship AND week_num = :week_num";
    $stmt = $_SERVER['PDO']->prepare($sql);

    // Bind parameter ke statement prepared
    $stmt->bindParam(':id_internship', $_POST['id_internship'], PDO::PARAM_STR);
    $stmt->bindParam(':week_num', $_POST['week_num'], PDO::PARAM_STR);

    // Eksekusi statement prepared
    $stmt->execute();

    // Periksa apakah week_num telah berubah dan hasil kueri menghasilkan hasil yang tidak kosong
    if ($_POST['old_week'] !== $_POST['week_num'] && $stmt->rowCount() > 0) {
        make_snackbar('Please check your submitted data !');
        error('week_num', 'Duplicate logbook data !');
        redirect_back(1);
        exit;
    }




    $where_logbook = array(
        'id_logbook' => $_POST['id_logbook'],
        'id_internship' => $_POST['id_internship'],
    );

    $logbook_data = array(
        'description' => $_POST['description'],
        'documentation' => $_POST['documentation'],

        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],

        'week_num' => $_POST['week_num'],

        'approval_spv' => 'Waiting',
        'approval_dpm' => 'Waiting',
    );

    $edit_logbook = table_update2('logbook', $logbook_data, $where_logbook);

    if ($edit_logbook === true) {
        make_swal2('success', 'Editing Data Success', 'Logbook data has been edited successfully !');
        redirect('logbook_internship&id_internship=' . base64_encode($_POST['id_internship']), 1);
        exit;
    } else {
        make_snackbar('Please check your submitted data and try again later !');
        redirect_back(1);
        exit;
    }
} else {
    make_snackbar('You do not have permission to perform this action !');
    redirect_back(1);
    exit;
}
