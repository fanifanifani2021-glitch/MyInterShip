<?php
error_reporting( true ); session_start();

$title = 'Register Internship';
include "./controller/controller-init.php";


    set_rules( 'nim', '', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'expected_start', '', ['required'], ['required' => 'This field is required !'] );
    set_rules( 'expected_end', '', ['required'], ['required' => 'This field is required !'] );


    set_rules('attachment_cv', '', ['required', 'valid_url'], ['required' => 'This field is required !', 'valid_url' => 'URL is not valid !']);
    // set_rules( 'attachment_cv', '', ['required', 'allowed_types:pdf', 'max_size: 2MB'], ['required' => 'This field is required !', 'allowed_types' => 'Allowed file type is pdf !', 'max_size' => 'Allowed file size is upto 2MB'] );
    if ($_FILES['attachment_transkrip']['error'] == '4') {
        set_rules('attachment_transkrip', '', ['allow_not_required'], []);
    } else {
        set_rules('attachment_transkrip', '', ['required', 'allowed_types:pdf', 'max_size: 500KB'], ['required' => 'This field is required !', 'allowed_types' => 'Allowed file type is pdf !', 'max_size' => 'Allowed file size is upto 500KB']);
    }
    set_rules( 'company_name', '', ['required'], ['required' => 'This field is required !']);
    set_rules( 'company_type', '', ['required'], ['required' => 'This field is required !']);
    set_rules( 'company_description', '', ['required'], ['required' => 'This field is required !']);
    
    set_rules( 'company_phone', '', ['required'], ['required' => 'This field is required !']);
    set_rules( 'company_email', '', ['required', 'valid_email'], ['required' => 'This field is required !', 'valid_email' => 'Please enter a valid email address !']);
    set_rules( 'company_website', '', ['required', 'valid_url'], ['required' => 'This field is required !', 'valid_url' => 'Please enter a valid url !']);
    
    set_rules( 'company_address', '', ['required'], ['required' => 'This field is required !']);
    set_rules( 'company_city', '', ['required'], ['required' => 'This field is required !']);
    set_rules( 'company_province', '', ['required'], ['required' => 'This field is required !']);


    if ( $_POST['have_another'] == '0' ) {
        set_rules( 'another_attachment', '', ['allow_not_required'] );
    } else {
        set_rules( 'another_attachment', '', ['required', 'allowed_types:pdf', 'max_size: 500KB'], ['required' => 'This field is required !', 'allowed_types' => 'Allowed file type is pdf !', 'max_size' => 'Allowed file size is upto 500KB' ] );
    }

    // Cek Internship. Jika ada masih ada yang status nya Yes, Maka batalkan registrasi
    $cek = mysqli_query($koneksi_db, "SELECT * FROM internship WHERE nim = '" .$_POST['nim']. "' AND `status` NOT IN ('Waiting','No','Finish')");
    $count = mysqli_num_rows($cek);
    if($count > 0){
        make_swal2('error', 'Register Failed', 'You already registered in internship !');
        make_sound('error');
    
        redirect_back(1); exit;
    }



    $cek_form = form_validation(); 

    if ( $cek_form === false ) {
        make_snackbar('Please check your submitted data and try again later');
        redirect_back(1); exit;
    }



    $company_data = array(
        'id_company' => NULL,
        'name' => $_POST['company_name'],
        'type' => $_POST['company_type'],
        'phone' => $_POST['company_phone'],
        'email' => $_POST['company_email'],
        'website' => $_POST['company_website'],
        'facebook' => '-',
        'twitter' => '-',
        'instagram' => '-',
        'linkedin' => '-',
        'logo' => '-',
        'address' => $_POST['company_address'],
        'province' => $_POST['company_province'],
        'city' => $_POST['company_city'],
        'description' => $_POST['company_description'],
        'status' => 'Not Verified',
        'access_type' => 1,
    );



    $company_insert = table_insert( 'company', $company_data );
    $id_company = $_SERVER['KONEKSI_DB']->insert_id;





    if ( $company_insert !== TRUE ) {
        make_swal2('error', 'Register Failed', 'Failed when saving company data !');
        redirect_back(1); exit;
    }

    $internship_period = date('n' , strtotime( $end_date ) - strtotime( $start_date ));



    $internship_data = array(
        'id_internship' => NULL,
        'nim' => $_POST['nim'],
        'id_company' => $id_company,
        'start_date' => $_POST['expected_start'],
        'end_date' => $_POST['expected_end'],
        'id_user_company' => '',
        'nik' => '',
        'internship_position' => '-',
        'status' => 'Waiting',
        'file_1' => '',
        'file_2' => '',
        'file_3' => '',
        'date_finalreport' => '',
        'final_report' => '',
        'internship_period' => $internship_period,
        'timestamp_register' => date('Y-m-d H:i:s')
    );

    $internship_insert = table_insert('internship', $internship_data);
    $id_internship = $_SERVER['KONEKSI_DB']->insert_id;




    if ( $internship_insert !== TRUE ) {
        $delete = table_delete2('company', ['id_company' => $id_company]);
        make_swal2('error', 'Register Failed', 'Failed when saving internship data !');
        redirect_back(1); exit;
    }



    // Move Uploaded file
    $direkori = "./upload/lampiran_internship/";


    $upload_cv = $_POST['attachment_cv'];

    if ($_FILES['attachment_transkrip']['error'] == '0') {
        $upload_transkrip = upload($direkori, 'attachment_transkrip');
    } else {
        $upload_transkrip = '-';
    }


    if ($_POST['have_another'] == '0') {
        $upload_another = '-';
    } else {
        $upload_another = upload($direkori, 'another_attachment');
    }


    if ( $upload_cv === false || $upload_transkrip === false || $upload_another === false ) {
            unlink( $direktori. '/' .$upload_transkrip );
            if ($upload_another != '-' ) {
            
            unlink( $direktori. '/' .$upload_another );
        }
        $delete = table_delete2('company', ['id_company' => $id_company]);
        $delete = table_delete2('internship', ['id_internship' => $id_internship]);
        make_swal2('error', 'Register Failed', 'Failed when saving internship data !');
        redirect_back(1); exit;
    }



    $update_internship = array(
        'file_1' => $upload_cv,
        'file_2' => $upload_transkrip,
        'file_3' => $upload_another,
    );

    $update_internship = table_update( 'internship', $update_internship, ['id_internship' => $id_internship]);

    if ( $update_internship === false ) {

        $hapus = delete_dir( "./upload/lampiran_internship/" .$id_internship );
        $delete = table_delete2('company', ['id_company' => $id_company]);
        $delete = table_delete2('internship', ['id_internship' => $id_internship]);

        make_swal2('error', 'Register Failed', 'Failed when saving internship data !');
        redirect_back(1); exit;
    } else {
        make_swal2('success', 'Register Success', 'Register Data have been successfully saved !');
        redirect('my_internship',1); exit;
    }




?>