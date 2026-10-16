<?php
error_reporting( true ); session_start();

$title = 'Jobdesk Management';
include "./controller/controller-init.php";

$action = $_GET['action'];


// Add Jobdesc
if ( $action == "add" ){


    set_rules('start_date','start_date' , ['required'], ['required' => 'This field is required !']);
    set_rules('end_date','end_date' , ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit();
    }


    // Insert Data Master Student Competence
    $data = array(
        'id_profile_student_jobdesc' => NULL, 'id_profile_jobdesc' => $_POST['id_profile_jobdesc'],
        'id_internship' => $_POST['id_internship'], 'nim' => $_POST['nim'],
        'start_date' => $_POST['start_date'], 'end_date' => $_POST['end_date'],
    );

    $sql = table_insert('profile_student_jobdesc', $data);


    if ( $sql === FALSE ) {
        make_snackbar('Check your submitted data and try again later');

        make_old('focus-add', 'open');
        redirect_back(1); exit;
    }

    $id_profile_student_jobdesc = $_SERVER['KONEKSI_DB']->insert_id;


    // Perulangan
    $sql2 = "SELECT * FROM profile_detail_comp WHERE id_profile_jobdesc = '" .$_POST['id_profile_jobdesc'] . "'";
    foreach ( $_SERVER['KONEKSI_DB']->query($sql2) as $i => $row ){
        $nilai = $_POST['nilai'][$row['id_detail_profile']];


        // Kalau tidak ada nilai nya
        if ( in_array($nilai, [NULL, ''] ) ) {
            continue;
        }

         // Insert Data Master Student Competence
        $data = array(
            'id_profile_student_detail' => NULL, 'id_profile_student_jobdesc' => $id_profile_student_jobdesc,
            'id_detail_profile' => $row['id_detail_profile'], 'nilai' => $nilai,
        );

        $sql3 = table_insert('profile_student_detail_comp', $data);
        if ( $sql3 === FALSE ){
            make_snackbar('Check your submitted data and try again later');

            table_delete2('profile_student_jobdesc', ['id_profile_student_jobdesc' => $id_profile_student_jobdesc ]);
            make_old('focus-add', 'open');
            redirect_back(1); exit;
        }
    }


    if ( $sql3 === true ) {
        make_snackbar('Student Competence has been saved');
        redirect('intern_list_competency&id_internship=' .base64_encode( $_POST['id_internship'] ),1); 
        exit;
    } else {
        make_snackbar('Check your submitted data and try again later');

        table_delete2('profile_student_jobdesc', ['id_profile_student_jobdesc' => $id_profile_student_jobdesc ]);
        make_old('focus-add', 'open');
        redirect_back(1); exit;
    }


} else

if ( $action == "edit" ){

    set_rules('start_date','start_date' , ['required'], ['required' => 'This field is required !']);
    set_rules('end_date','end_date' , ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit();
    }


    // Insert Data Master Student Competence
    $data = array(
        'id_profile_student_jobdesc' => NULL, 'id_profile_jobdesc' => $_POST['id_profile_jobdesc'],
        'id_internship' => $_POST['id_internship'], 'nim' => $_POST['nim'],
        'start_date' => $_POST['start_date'], 'end_date' => $_POST['end_date'],
    );

    $sql = table_insert('profile_student_jobdesc', $data);


    if ( $sql === FALSE ) {
        make_snackbar('Check your submitted data and try again later');

        make_old('focus-add', 'open');
        redirect_back(1); exit;
    }

    $id_profile_student_jobdesc = $_SERVER['KONEKSI_DB']->insert_id;


    // Perulangan
    $sql2 = "SELECT * FROM profile_detail_comp WHERE id_profile_jobdesc = '" .$_POST['id_profile_jobdesc'] . "'";
    foreach ( $_SERVER['KONEKSI_DB']->query($sql2) as $i => $row ){
        $nilai = $_POST['nilai'][$row['id_detail_profile']];


        // Kalau tidak ada nilai nya
        if ( in_array($nilai, [NULL, ''] ) ) {
            continue;
        }

         // Insert Data Master Student Competence
        $data = array(
            'id_profile_student_detail' => NULL, 'id_profile_student_jobdesc' => $id_profile_student_jobdesc,
            'id_detail_profile' => $row['id_detail_profile'], 'nilai' => $nilai,
        );

        $sql3 = table_insert('profile_student_detail_comp', $data);
        if ( $sql3 === FALSE ){
            make_snackbar('Check your submitted data and try again later');

            table_delete2('profile_student_jobdesc', ['id_profile_student_jobdesc' => $id_profile_student_jobdesc ]);
            make_old('focus-add', 'open');
            redirect_back(1); exit;
        }
    }


    if ( $sql3 === true ) {
        make_snackbar('Student Competence has been edited');

        table_delete2('profile_student_jobdesc', ['id_profile_student_jobdesc' => $_POST['old_id_profile_student_jobdesc'] ]);
        table_update('profile_student_jobdesc', ['id_profile_student_jobdesc' => $_POST['old_id_profile_student_jobdesc'] ], ['id_profile_student_jobdesc' => $id_profile_student_jobdesc ]);

        redirect('intern_list_competency&id_internship=' .base64_encode( $_POST['id_internship'] ),1); exit;

    } else {
        make_snackbar('Check your submitted data and try again later');

        table_delete2('profile_student_jobdesc', ['id_profile_student_jobdesc' => $id_profile_student_jobdesc ]);
        make_old('focus-add', 'open');
        redirect_back(1); exit;
    }









}

else {
    make_swal2('error', 'Not Allowed', 'You do not have permission to perform this action !');
    redirect_back(1); exit;
}


?>