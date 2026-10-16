<?php
error_reporting( true ); session_start();

$title = 'Internship Coordinator';
include "./controller/controller-init.php";

make_snackbar('You do not have permission to perform this action');

if ( $_SESSION['role'] == 'UPKPK' ) {
    role_checker( 'role' , ['UPKPK'], 'notifikasi' );

} else {
    role_checker( 'is_koor' , [true], 'notifikasi' );
}

$aksi = $_GET['aksi'];


if ( $aksi == 'add_supervisor' ){

    set_rules( 'lecturer_nik', 'Lecturer NIK', ['required' , 'is_unique:lecturer.nim_nik_unit'], ['required' => 'This field is required !', 'exact_value' => 'Please enter a valid Lecturer`s NIK !', 'is_unique' => 'Lecturer has been registered !'] );

    set_rules( 'lecturer_name', '', ['allow_not_required'] );
    set_rules( 'lecturer_email', '', ['allow_not_required'] );

    $cek_form = form_validation(); 

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }

    // Insert Data
    $data_lecturer = array(
        'nim_nik_unit' => $_POST['lecturer_nik'],
        'name' => $_POST['lecturer_name'],
        'email_polibatam' => $_POST['lecturer_email'],
        'is_koor' => 0,
        'prodi_koor' => NULL,
        'status' => 'Active',
        'profile_picture' => 'profile.jpg',
    );

    $insert_lecturer = table_insert( 'lecturer', $data_lecturer );

    if ( $insert_lecturer === TRUE ) {
        make_snackbar('Supervisor has been added');

        redirect('supervisor_management',1); exit;
    } else {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }


} else


if ( $aksi == 'edit_supervisor' ){


    $old_nik = $_POST['old_nik'];

    set_rules( 'lecturer_nik', 'Lecturer NIK', ['required' , "unique_except:$old_nik|lecturer.nim_nik_unit"], ['required' => 'This field is required !', 'exact_value' => 'Please enter a valid Lecturer`s NIK !', 'unique_except' => 'Lecturer has been registered !'] );
    set_rules( 'search_status', 'Search Status', ['required' , 'allowed_values: Success'], ['required' => 'This field is required !', 'allowed_values' => 'Value must be success !'] );
    set_rules( 'user_type', 'User Type', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'lecturer_status', 'Lecturer`s Status', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'program_study', 'Program Study', ['required'], ['required' => 'This field is required !'] );
    
    set_rules( 'lecturer_name', '', ['allow_not_required'] );
    set_rules( 'lecturer_email', '', ['allow_not_required'] );

    $cek_form = form_validation(); 

    if ( $_POST['lecturer_nik'] != '' && $_POST['search_status'] != 'Success' ) {
        error('lecturer_nik' , 'Please enter a valid Lecturer`s NIK !');
    }


    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');

        redirect_back(1); exit;
    }



    $where_lecturer = array (
        'nim_nik_unit' => $_POST['old_nik'],
    );
    $data_lecturer = array(
        'nim_nik_unit' => $_POST['lecturer_nik'],
        'name' => $_POST['lecturer_name'],
        'program_study' => $_POST['program_study'],
        'email_polibatam' => $_POST['lecturer_email'],
        'user_type' => $_POST['user_type'],
        'status' => $_POST['lecturer_status'],
    );

    $update_lecturer = table_update( 'lecturer', $data_lecturer, $where_lecturer );

    if ( $update_lecturer === TRUE ) {
        make_snackbar('Supervisor Data has been edited');
        redirect('supervisor_management',1); exit;
    } else {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }



} else


if ( $aksi == 'add_student_intern' ) {

     set_rules( 'student_nim', 'Student NIM', ['required' , 'is_unique:student_internship.nim'], ['required' => 'This field is required !', 'is_unique' => 'Student has been registered !'] );
    set_rules( 'kode_program_study', '', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'student_name', '', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'student_email', '', ['allow_not_required'] );
    set_rules( 'nik_dospem', '', ['allow_not_required'] );
    
    $cek_form = form_validation(); 


    
    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }
    
    
    // if ( $_SESSION['Lecturer-Data'] ['id_kampus'] != 1 && $_SESSION['CDC_Data'] ['id_kampus'] != 1 ) {
    //     // $password = password_hash( $_SERVER['STUDENT_DEFAULT_PASSWORD'], PASSWORD_DEFAULT );
    // }

    $password = password_hash($_POST['student_nim'], PASSWORD_DEFAULT);
    
    // Tangani nik_dospem
    $nik_dospem = $_POST['nik_dospem'] ?? null;
    if ($nik_dospem == 'Unassigned') {
        $nik_dospem = null;
    }
    
    // Siapkan data
    $data = [
        ':nim' => $_POST['student_nim'],
        ':name' => $_POST['student_name'],
        ':program_study' => $_POST['kode_program_study'],
        ':password' => $password,
        ':email' => $_POST['student_email'] ?: '-',
        ':other_email' => '-',
        ':phone' => '-',
        ':no_whatsapp' => '-',
        ':profile_picture' => 'profile.jpg',
        ':id_kampus' => $_SESSION['Lecturer-Data']['id_kampus'] ?? $_SESSION['CDC_Data']['id_kampus'],
        ':nik_dospem' => $nik_dospem
    ];
    
    // Query insert
    $sql = "INSERT INTO student_internship (
                nim, name, program_study, password,
                email, other_email, phone, no_whatsapp,
                profile_picture, id_kampus, nik_dospem
            ) VALUES (
                :nim, :name, :program_study, :password,
                :email, :other_email, :phone, :no_whatsapp,
                :profile_picture, :id_kampus, :nik_dospem
            )";
    
    $stmt = $_SERVER['PDO']->prepare($sql);
    $insertSuccess = $stmt->execute($data);
    
    // Redirect & feedback
    if ($insertSuccess) {
        make_snackbar('Student Internship has been added');
        redirect($_SESSION['role'] === 'UPKPK' ? 'intern_student_list' : 'student_intern_management', 1);
        exit;
    } else {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1);
        exit;
    }

} else


if ( $aksi == 'edit_student_intern' ) {

    $old_student_nim = $_POST['old_student_nim'];

    set_rules( 'old_student_nim', 'Student NIM', ['required'] );
    set_rules( 'student_nim', 'Student NIM', ['required' , "unique_except:$old_student_nim|student_internship.nim"], ['required' => 'This field is required !', 'unique_except' => 'Student has been registered !'] );

    set_rules( 'kode_program_study', '', ['required'] );
    set_rules( 'student_name', '', ['required'] );
    set_rules( 'student_email', '', ['required'] );

    $cek_form = form_validation(); 

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }



        // Insert Data
        $data_student = array(
            'nim' => $_POST['student_nim'], 'name' => $_POST['student_name'],
            'program_study' => $_POST['kode_program_study'],
        
            'email' => $_POST['student_email'],
            );

    $where = array(
        'nim' => $_POST['old_student_nim'],
        'id_kampus' => $_SESSION['Lecturer-Data'] ['id_kampus']
    );

    $insert_student = table_update( 'student_internship', $data_student, $where );

    if ( $insert_student === TRUE ) {
        make_snackbar('Student Internship Data has been edited');
        redirect('student_intern_management',1); exit;
    } else {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }


} else


if ( $aksi == 'assign_polibatam_supervisor' ){

    set_rules( 'id_internship', 'Internship ID', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'nim', 'Lecturer NIK', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'lecturer_nik', 'Lecturer NIK', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'search_status', 'Search Status', ['required' , 'allowed_values: Success'], ['required' => 'This field is required !', 'allowed_values' => 'Value must be success !'] );
    set_rules( 'program_study', 'Program Study', ['required'], ['required' => 'This field is required !'] );
    
    set_rules( 'lecturer_name', '', ['allow_not_required'] );
    set_rules( 'lecturer_email', '', ['allow_not_required'] );

    $cek_form = form_validation(); 

    if ( $_POST['lecturer_nik'] != '' && $_POST['search_status'] != 'Success' ) {
        error('lecturer_nik' , 'Please enter a valid Lecturer`s NIK !');
    }



    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        make_old('ModalAssignSupervisor', 'open');
        redirect_back(1); exit;
    }

    $where_student = array(
        'nim' => $_POST['nim'],
    );

    $data_update = array (
        'nik_dospem' => $_POST['lecturer_nik'],
    );

    $assign = table_update( 'student_internship', $data_update, $where_student );

    if ( $assign === TRUE ) {
        flash_session();
        make_snackbar('Lecturer has been assigned to current Student');
        redirect_referrer(1); exit;
    } else {
        make_old('ModalAssignSupervisor', 'open');

        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }


} else 

if ( $aksi == 'change_internship_status' ){

    if ( validate_all() === false ){
        redirect_referrer(1); exit;
    }

    if ( $_POST['old_internship_status'] == $_POST['internship_status'] ) {
        make_swal2('success', 'Internship Status Changed', 'Internship Status have been changed successfully !');
        redirect_referrer(1); exit;
    }

    if ( $_POST['internship_status'] == 'Yes' ) {
        make_swal2('error', 'Internship Status Not Changed', 'You cannot change internship status to Yes !');
        redirect_referrer(1); exit;
    }


    if ( $_POST['internship_status'] == 'No' ) {


        // Cek applicant
        $sql = "SELECT * FROM applicant WHERE id_internship = '" .$_POST['id_internship']. "'";

        if ( $_SERVER['KONEKSI_DB']->query($sql)->num_rows == 1 ) {
            $update = table_update('applicant', ['status' => 'Rejected'], ['id_internship' => $_POST['id_internship'] ]);
        } else {
            $update = true;
        }

        if ( $update === false ) {
            make_swal2('success', 'Internship Status Not Changed', 'Please try again later !');
            redirect_back(1); exit;
        }


        // Update
        $update = table_update('internship', ['status' => 'No', 'id_user_company' => '-', 'nik' => '-'], ['id_internship' => $_POST['id_internship'] ]);
        if ( $update === false ) {
            make_swal2('success', 'Internship Status Not Changed', 'Please try again later !');
            redirect_back(1); exit;
        } else {
            make_swal2('success', 'Internship Status Changed', 'Internship Status have been changed successfully !');
            redirect_back(1); exit;
            
        }

    }

    if ($_POST['internship_status'] == 'Finish' ) {

        if ( $_POST['old_internship_status'] != 'Yes' ) {
            make_swal2('error', 'Internship Status Not Changed', 'You cannot change internship status to Finish when Internship is Not Accepted !');
            redirect_referrer(1); exit;
        }


        // Cek applicant
        $sql = "SELECT * FROM applicant WHERE id_internship = '" .$_POST['id_internship']. "'";

        if ( $_SERVER['KONEKSI_DB']->query($sql)->num_rows == 1 ) {
            // $update = table_update('applicant', ['status' => 'Rejected'], ['id_internship' => $_POST['id_internship'] ]);
            $update = true;

        } else {
            $update = true;
        }

        if ( $update === false ) {
            make_swal2('success', 'Internship Status Not Changed', 'Please try again later !');
            redirect_back(1); exit;
        }


        // Update
        $update = table_update('internship', ['status' => 'Finish'], ['id_internship' => $_POST['id_internship'] ]);
        if ( $update === false ) {
            make_swal2('success', 'Internship Status Not Changed', 'Please try again later !');
            redirect_back(1); exit;
        } else {
            make_swal2('success', 'Internship Status Changed', 'Internship Status have been changed successfully !');
            redirect_back(1); exit;
            
        }

    }
}


else {
    make_snackbar('You do not have permission to perform this action !');
    redirect_referrer(1); exit;
}
