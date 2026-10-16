<?php
error_reporting( true ); session_start();

$title = 'Validate Candidate Data';
include "./controller/controller-init.php";



    set_rules( 'id_internship', '', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'student_nim', '', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'status_acceptance', '', ['required'], ['required' => 'This field is required !'] );
    
    set_rules( 'expected_start_date', '', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'expected_end_date', '', ['required'], ['required' => 'This field is required !'] );


    if ( $_POST['status_acceptance'] == 'Yes' ) {
        set_rules( 'start_date', '', ['required'], ['required' => 'This field is required !'] );
        set_rules( 'end_date', '', ['required'], ['required' => 'This field is required !'] );    
        set_rules( 'industry_supervisor', '', ['required'], ['required' => 'This field is required !'] );

    }


    $cek_form = form_validation(); 

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        make_sound('error');
        redirect_back(1); exit;
    }





    if ( $_POST['status_acceptance'] == 'Yes' ) {

        // Cek apakah sudah pernah mendaftar internship dan diterima sebelum nya
        $sql = "SELECT * FROM internship WHERE id_internship = '".$_POST['id_internship']."' AND `status` IN ('YES', 'Yes', 'yes')";
        if ( $_SERVER['KONEKSI_DB']->query($sql)->num_rows != 0 ){
            make_swal2('error', 'Validating Data Failed', 'Intern Student cannot have more than one active internship !');
            error('status_acceptance', 'Intern Student have been accepted in another Internship !');
            redirect_back(1); exit;
        }



        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $internship_period = date('n', strtotime($end_date) - strtotime($start_date) );


        // Cek Industri Feedback
        $sql = "SELECT * FROM industry_feedback WHERE id_internship = '".$_POST['id_internship']."'";
        $industri_feedback = $_SERVER['KONEKSI_DB']->query( $sql );        
        if ( $industri_feedback->num_rows != 0 ) {
            table_delete2('industry_feedback' , ['id_internship' => $_POST['id_internship'] ] );
        }

        $student_feedback = $_SERVER['KONEKSI_DB']->query("SELECT * FROM student_feedback WHERE id_internship = '".$_POST['id_internship']."' ");
        if ( $student_feedback->num_rows != 0 ) {
            table_delete2('student_feedback' , ['id_internship' => $_POST['id_internship'] ] );
        }



        // Insert Ke tb
        $industry_feedback= array(
            'id_industry_feedback' => NULL, 
            'id_internship' => $_POST['id_internship'], 
            'nim' => $_POST['student_nim'], 
            'catatan_utk_mahasiswa' => '-', 'catatan_utk_poltek' => '-', 
            'layak_diterima' => '-', 'langsung_diterima' => '-', 
            'nilai_akhir' => '-', 
            
            'etika' => 0, 'keahlian_kompetensi' => 0, 'keahlian_bahasa' => 0, 
            'penggunaan_ti' => 0, 'komunikasi' => 0, 'kerjasama' => 0, 'pengembangan_diri' => 0, 
            'date' => date('Y-m-d h:i:s')
        );


        $insert_i_feedback = table_insert('industry_feedback', $industry_feedback);


        if ( $insert_i_feedback === false ) {
            make_swal2('error', 'Validating Data Failed', 'Please re-check the data you entered and try again later !');
            redirect_back(1); exit; 
        }


        $student_feedback= array(
            'id_student_feedback' => NULL, 
            'id_internship' => $_POST['id_internship'], 
            'nim' => $_POST['student_nim'], 
            'date' => date('Y-m-d'), 
            'posisi' => '-', 
            'ilmu_dari_poltek' => '-', 
            'ilmu_baru' => '-', 
            'data_magang' => '-', 
            'kesan' => '-', 
            'kendala' => '-', 'masukan' => '-',
        );

        $insert_s_feedback = table_insert('student_feedback', $student_feedback);


        if ( $insert_s_feedback === false ) {
            table_delete2('industry_feedback' , ['id_internship' => $_POST['id_internship'] ] );
            make_swal2('error', 'Validating Data Failed', 'Please re-check the data you entered and try again later !');
            redirect_back(1); exit; 
        }

        if ( $_POST['registered_via'] == 'Offer' ) {

            $where_offer = array(
                'id_internship' => $_POST['id_internship']
            );
            $data_offer = array(
                'status' => 'Accepted'
            );
            $sql2 = table_update( 'applicant' , $data_offer, $where_offer );

            if ( $sql2 === false ) {
                table_delete2('student_feedback' , ['id_internship' => $_POST['id_internship'] ] );
                table_delete2('industry_feedback' , ['id_internship' => $_POST['id_internship'] ] );
                make_swal2('error', 'Validating Data Failed', 'Please re-check the data you entered and try again later !');
                redirect_back(1); exit; 
            }
        }


    } else {
        $start_date = $_POST['expected_start_date'];
        $end_date = $_POST['expected_end_date'];
        $internship_period = 0;

        if ( $_POST['registered_via'] == 'Offer' ) {

            $where_offer = array(
                'id_internship' => $_POST['id_internship']
            );
    
            $data_offer = array(
                'status' => 'Rejected'
            );
    
            $sql2 = table_update( 'applicant' , $data_offer, $where_offer );

            if ( $sql2 === false ) {
                table_delete2('student_feedback' , ['id_internship' => $_POST['id_internship'] ] );
                table_delete2('industry_feedback' , ['id_internship' => $_POST['id_internship'] ] );
                make_swal2('error', 'Validating Data Failed', 'Please re-check the data you entered and try again later !');
                redirect_back(1); exit; 
            }
    
        }
    }





    $where_internship = array(
        'id_internship' => $_POST['id_internship'],
        'nim' => $_POST['student_nim'],
    );

    $data_internship = array(
        'status' => $_POST['status_acceptance'],
        'id_user_company' => $_POST['industry_supervisor'],

        'start_date' => $start_date,
        'end_date' => $end_date,
        'internship_period' => $internship_period,
    );


    $validate_intern = table_update( 'internship', $data_internship, $where_internship );

    if ( $validate_intern === TRUE ) {
        // make_sound('success');
        make_snackbar('Candidate Data has been validated');

        // make_swal2('success', 'Validating Data Success', 'Candidate data has been validated successfully !');
        redirect('intern_candidate',1); exit;
    } else {

        table_delete2('industry_feedback' , ['id_internship' => $_POST['id_internship'] ] );
        table_delete2('student_feedback' , ['id_internship' => $_POST['id_internship'] ] );

        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }




?>