<?php
error_reporting(false);
session_start();

$aksi = $_GET['aksi'];


// Kalau Selain HRD, tidak bisa CUD Offer
if ($_SESSION['user_type'] != 'HRD') {
    make_snackbar('You do not have permission to perform this action !');
    // make_swal2('error', 'Not Allowed', 'You do not have permission to perform this action !');
    redirect_referrer(1);
    exit;
}

if ($aksi == "data") {

    $id_company = $_SESSION['id_company'];


    $dataTable = getDataTableData("SELECT * FROM offer WHERE id_company = $id_company ORDER BY id_offer DESC", function ($row) {
        return array(
            'aksi' =>
            '<button class="btn btn-primary btn-sm" onclick="editRow(\'' . encrypt($row['id_offer']) . '\')">Edit</button> <button class="btn btn-danger btn-sm btn-delete" data-id="' . encrypt($row['id_offer']) . '">Hapus</button>'
        );
    });

    response_json(200, $dataTable['response']);
} else 
if ($aksi == "tambah") {

    set_rules('id_company', 'id_company', ['required'], ['required' => 'This field is required !']);
    set_rules('position', 'position', ['required'], ['required' => 'This field is required !']);
    set_rules('description', 'description', ['required'], ['required' => 'This field is required !']);
    set_rules('requirement', 'requirement', ['required'], ['required' => 'This field is required !']);
    set_rules('employer_type', 'employer_type', ['required'], ['required' => 'This field is required !']);
    set_rules('city', 'city', ['required'], ['required' => 'This field is required !']);
    set_rules('job_type', 'job_type', ['required'], ['required' => 'This field is required !']);
    set_rules('start_date', 'start_date', ['required', 'valid_date'], ['required' => 'This field is required !']);
    set_rules('end_date', 'end_date', ['required', 'valid_date'], ['required' => 'This field is required !']);
    set_rules('offer_type', 'offer_type', ['required'], ['required' => 'This field is required !']);

    set_rules('salary_range', 'salary_range', ['allow_not_required'], ['required' => 'This field is required !']);

    // Cek Company Status
    $sql = "SELECT * FROM company WHERE id_company = '" . $_POST['id_company'] . "'";
    $company_data = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();

    if (!in_array($company_data['status'], ['Verified', 'verified', 1])) {
        make_swal2('error', 'Offer Not Added', 'Company is not verified !');

        whenAjaxRequest(function () {

            $output = array(
                'status' => 0,
                'validation' => $_SESSION['notifikasi'],
                'error' => $_SESSION['error'],
            );

            response_json(400, $output);
        });

        redirect_back(1);
        exit;
    }

    if (!in_array($company_data['access_type'], [1])) {
        make_swal2('error', 'Offer Not Added', 'Your Company do not have this access !');

        whenAjaxRequest(function () {

            $output = array(
                'status' => 0,
                'validation' => $_SESSION['notifikasi'],
                'error' => $_SESSION['error'],
            );

            response_json(400, $output);
        });


        redirect_back(1);
        exit;
    }


    $cek_form = form_validation();

    if ($cek_form === false) {
        make_snackbar('Check your submitted data and try again later');

        whenAjaxRequest(function () {

            $output = array(
                'status' => 0,
                'validation' => $_SESSION['notifikasi'],
                'error' => $_SESSION['error'],
            );

            response_json(400, $output);
        });

        make_old('notifikasi-error', 'Required columns cannot be left empty !');
        make_snackbar('Check your submitted data and try again later');
        make_old('old_tambah', TRUE);
        redirect_back(1);
        exit;
    }





    $offer_data = array(
        'id_offer' => NULL, 'id_company' => $_POST['id_company'],
        'position' => $_POST['position'], 'description' => $_POST['description'],
        'requirement' => $_POST['requirement'], 'employer_type' => $_POST['employer_type'],
        'salary_range' => $_POST['salary_range'], 'city' => $_POST['city'],
        'job_type' => $_POST['job_type'],

        'start_date' => $_POST['start_date'], 'end_date' => $_POST['end_date'],
        'offer_type' => $_POST['offer_type'], 'status' => 'Open',
        'timestamp_add' => date('Y-m-d H:i:s'), 'timestamp_update' => '-',
    );

    $sql = table_insert('offer', $offer_data);


    if ($sql === TRUE) {
        reflash_all();
        make_snackbar('Offer has been added');

        whenAjaxRequest(function () {

            $output = array(
                'status' => 1,
                'validation' => $_SESSION['notifikasi'],
                'error' => $_SESSION['error'],
            );

            response_json(200, $output);
        });

        // make_swal2('success', 'Offer Added', 'Offer Data have been successfully added !');
        redirect_referrer(1);
        exit();
    } else {
        make_old('old_tambah', TRUE);
        make_swal2('error', 'Offer Not Added', 'Failed to add New Offer !');

        whenAjaxRequest(function () {

            $output = array(
                'status' => 0,
                'validation' => $_SESSION['notifikasi'],
                'error' => $_SESSION['error'],
            );

            response_json(400, $output);
        });


        redirect_back(1);
        exit();
    }
} else


if ($aksi == "edit") {

    $id = mysqli_real_escape_string($koneksi_db, $_POST['id_offer']);
    $position = mysqli_real_escape_string($koneksi_db, $_POST['position']);
    $description = mysqli_real_escape_string($koneksi_db, $_POST['description']);
    $requirement = mysqli_real_escape_string($koneksi_db, $_POST['requirement']);
    $employer_type = mysqli_real_escape_string($koneksi_db, $_POST['employer_type']);
    $city = mysqli_real_escape_string($koneksi_db, $_POST['city']);
    $job_type = mysqli_real_escape_string($koneksi_db, $_POST['job_type']);
    $salary_range = mysqli_real_escape_string($koneksi_db, $_POST['salary_range']);
    $start_date = mysqli_real_escape_string($koneksi_db, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($koneksi_db, $_POST['end_date']);
    $offer_type = mysqli_real_escape_string($koneksi_db, $_POST['offer_type']);



    if (validate_all() === false) {
        make_snackbar('Please check your submitted data !');

        make_old('old_edit', TRUE);
        redirect_back(1);
        exit;
    }

    $where = array(
        'id_offer' => $_POST['id_offer'],
    );

    $data = array(
        'position' => $_POST['position'], 'description' => $_POST['description'],
        'requirement' => $_POST['requirement'], 'employer_type' => $_POST['employer_type'],
        'city' => $_POST['city'], 'job_type' => $_POST['job_type'],
        'salary_range' => $_POST['salary_range'], 'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'], 'offer_type' => $_POST['offer_type'],

        'timestamp_update' => date('Y-m-d H:i:s'),
    );

    $sql = table_update('offer', $data, $where);
    if ($sql === TRUE) {
        reflash_all();
        make_swal2('success', 'Update Offer Success', 'Offer Data have been updated successfully !');
        redirect('offer_detail&id=' . base64_encode($id), 1);
        exit();
    } else {
        make_old('old_edit', TRUE);

        make_snackbar('Please check your submitted data and try again later !');
        redirect_back(1);
        exit;
    }
} else


if ($aksi == "hapus") {


    $id = escape_string($_POST['id_hapus']);

    $sql = "DELETE FROM offer WHERE id_offer = :id_offer";
    $stmt = $pdo->prepare( $sql );
    $stmt->execute( [':id_offer' => $id ] );


    if ($stmt->rowCount() > 0) {
        make_swal2('success', 'Offer Data Deleted', 'Offer Data have been deleted successfully !');
        redirect('industry_offer', 1);
        exit();
    } else {
        make_snackbar('Server under heavy load please try again later !');

        // make_swal2('error', 'Delete Data Fail', 'Please try again later !');
        redirect_back(1);
        exit;
    }
} else


if ($aksi == "change_status") {


    set_rules('id_change', 'id_change', ['required'], ['required' => 'This field is required !']);
    set_rules('offer_status', 'offer_status', ['required', 'allowed_values:Open,Closed'], ['required' => 'This field is required !', 'allowed_values' => 'Selected value is not valid !']);

    if (form_validation() === false) {
        make_old('ModalChange', 'open');
        redirect_back(1);
        exit();
    }

    $where = array(
        'id_offer' => $_POST['id_change'],
    );

    $data = array(
        'status' => $_POST['offer_status'],
    );




    $sql = table_update('offer', $data, $where);

    if ($sql === TRUE) {
        reflash_all();
        make_swal2('success', 'Change Status Success', 'Offer Status have been updated successfully !');
        redirect_referrer(1);
        exit;
    } else {
        make_old('ModalChange', 'open');
        make_snackbar('Please check your submitted data and try again later !');

        // make_swal2('error', 'Change Status Failed', 'Please check your submitted data and try again later !');
        redirect_back(1);
        exit;
    }
    exit;
} else {
    make_snackbar('You do not have permission to perform this action !');
    // make_swal2('error', 'Not Allowed', 'You do not have permission to perform this action !');
    redirect_referrer(1);
    exit;
}
