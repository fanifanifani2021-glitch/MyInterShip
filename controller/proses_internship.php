<?php

$action = $_GET['action'];




if ($action == 'assign_supervisor') {

    set_rules('id_internship', 'Internship', ['required', 'exists:internship.id_internship'], ['required' => 'This field is required !', 'exists' => 'Internship does not exist !']);
    set_rules('industry_supervisor', 'Industry Supervisor', ['required', 'exists:user_company.id_user_company'], ['required' => 'This field is required !', 'exists' => 'User Company does not exist !']);
    set_rules('university_supervisor', 'University Supervisor', ['required', 'exists:lecturer.nim_nik_unit'], ['required' => 'This field is required !', 'exists' => 'Lecturer does not exist !']);

    $form_validation = form_validation();

    if ($form_validation === FALSE) {
        make_snackbar('(1) Check your submitted data and try again later');
        redirect_referrer(1);
        exit;
    }
    
    // var_dump( $_POST ); exit;

    try {

        // GET Internship
        $sql = "SELECT * FROM internship WHERE id_internship = :id_internship LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_internship', $_POST['id_internship'], PDO::PARAM_INT);

        $stmt->execute();

        $data_internship = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        
        // var_dump( $e ); exit;
        make_swal2('error', 'Not Found', '(2) Internship data not found');
        redirect_referrer(1);
        exit;
    }


    try {
        $pdo->beginTransaction(); // Start the transaction

        // Prepare and execute the first update statement
        $updateStudentInternship = "UPDATE student_internship SET nik_dospem = :nik_dospem WHERE nim = :nim";
        $stmtStudentInternship = $pdo->prepare($updateStudentInternship);
        $stmtStudentInternship->bindParam(':nik_dospem', $_POST['university_supervisor']);
        $stmtStudentInternship->bindParam(':nim', $data_internship['nim']);
        $stmtStudentInternship->execute();

        // Prepare and execute the second update statement
        $updateInternship = "UPDATE internship SET id_user_company = :id_user_company WHERE id_internship = :id_internship";
        $stmtInternship = $pdo->prepare($updateInternship);
        $stmtInternship->bindParam(':id_user_company', $_POST['industry_supervisor']);
        $stmtInternship->bindParam(':id_internship', $_POST['id_internship']);
        $stmtInternship->execute();

        $pdo->commit(); // Commit the transaction if both updates succeed

        // Success message or further actions
        // echo "Both updates were successful.";
        make_swal2('success', 'Success', 'Success to assign supervisor');

        if ($_POST['redirect']) {
            redirect(base64_decode($_POST['redirect']), 1);
            exit;
        } else {
            redirect_referrer(1);
            exit;
        }
    } catch (PDOException $e) {
        $pdo->rollBack(); // Rollback the transaction if an error occurs
        make_swal2('error', 'Failed', '(3) Failed to assign supervisor');
        dump_to_file('proses_internship_error_log', 'Timestamp : ' . date('Y-m-d H:i:s') . ' /n Status : Assign Supervisor Access; /n From : ' . getBrowser() . ' ; /n Payload : ' . implode(';', $_POST) . '; /n Result = ' . $e->getMessage() . ' /n');

        redirect_back(1);
        exit;
    }
} else

if ($action == 'add') {

    set_rules('student_nim', 'Student NIM', ['required', 'exists:student_internship.nim'], ['required' => 'This field is required !', 'exists' => 'Student does not exist !']);
    set_rules('company', 'Company', ['required', 'exists:company.id_company'], ['required' => 'This field is required !', 'exists' => 'Company does not exist !']);
    set_rules('start_date', 'Start Date', ['required', 'valid_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !']);
    set_rules('end_date', 'End Date', ['required', 'valid_date', 'after_or_equal_date:start_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !', 'after_or_equal_date' => 'Date must be greater than or equal to ' . date('d M Y', strtotime($_POST['start_date'])) . ' ! ']);


    $form_validation = form_validation();

    if ($form_validation === FALSE) {
        make_snackbar('Check your submitted data and try again later');
        redirect_referrer(1);
        exit;
    }

    $data = array(
        'id_internship' => NULL,
        'nim' => $_POST['student_nim'],
        'id_company' => $_POST['company'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],

        'id_user_company' => '',
        'nik' => '', 'status' => 'Yes',

        'internship_position' => NULL,
        'file_1' => '', 'file_2' => '', 'file_3' => '',
        'date_finalreport' => '', 'final_report' => '',
        'internship_period' => date('n', strtotime($_POST['end_date']) - strtotime($_POST['start_date'])),
        'timestamp_register' => date('Y-m-d H:i:s')
    );

    $insert = table_insert2('internship', $data);

    if ($insert === true) {
        make_swal2('success', 'Success', 'Internship has been added successfully !');

        if ($_SESSION['role'] == 'Dosen') {
            redirect('student_intern_detail&nim=' . base64_encode($_POST['student_nim']), 1);
        } else {
            redirect('student_detail_intern_co&nim=' . base64_encode($_POST['student_nim']), 1);
        }
        exit;
    } else {
        make_swal2('error', 'Failed', 'Check your submitted data and try again later !');

        redirect_back(1);
        exit;
    }
} else

if ($action == 'edit') {

    set_rules('id_internship', 'Internship', ['required', 'exists:internship.id_internship'], ['required' => 'This field is required !', 'exists' => 'Internship does not exist !']);
    set_rules('student_nim', 'Student NIM', ['required', 'exists:student_internship.nim'], ['required' => 'This field is required !', 'exists' => 'Student does not exist !']);
    set_rules('company', 'Company', ['required', 'exists:company.id_company'], ['required' => 'This field is required !', 'exists' => 'Company does not exist !']);
    set_rules('start_date', 'Start Date', ['required', 'valid_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !']);
    set_rules('end_date', 'End Date', ['required', 'valid_date', 'after_or_equal_date:start_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !', 'after_or_equal_date' => 'Date must be greater than or equal to ' . date('d M Y', strtotime($_POST['start_date'])) . ' ! ']);
    set_rules('internship_status', 'Internship Status', ['required', 'allowed_values:Yes,No,Waiting,Finish'], ['required' => 'This field is required !', 'allowed_values' => 'Value is not valid !']);


    $form_validation = form_validation();

    if ($form_validation === FALSE) {
        make_snackbar('Check your submitted data and try again later');
        redirect_referrer(1);
        exit;
    }

    $where = array(
        'id_internship' => $_POST['id_internship'],
    );

    $data = array(
        'nim' => $_POST['student_nim'],
        'id_company' => $_POST['company'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'status' => $_POST['internship_status'],
    );

    $insert = table_update2('internship', $data, $where);

    if ($insert === true) {
        make_swal2('success', 'Success', 'Internship has been edited successfully !');

        if ($_POST['redirect']) {
            redirect(base64_decode($_POST['redirect']), 1); exit;
        }

        if ($_SESSION['role'] == 'Dosen') {
            redirect('student_intern_detail&nim=' . base64_encode($_POST['student_nim']), 1);
        } else {
            redirect('student_detail_intern_co&nim=' . base64_encode($_POST['student_nim']), 1);
        }
        exit;
    } else {
        make_swal2('error', 'Failed', 'Check your submitted data and try again later !');

        redirect_back(1);
        exit;
    }
} else {
    make_swal2('error', 'Forbidden', 'You do not have perform this action !');
    redirect_back(1);
    exit;
}
