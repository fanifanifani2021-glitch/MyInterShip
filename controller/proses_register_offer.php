<?php
error_reporting(true);
session_start();
$title = "Register Offer";
include "./controller/controller-init.php";

$aksi = $_GET['aksi'];

if ($aksi == "register_offer") {

    set_rules('id_company', '', ['required'], ['required' => 'This field is required !']);
    set_rules('id_offer', '', ['required'], ['required' => 'This field is required !']);


    set_rules('nim', '', ['required'], ['required' => 'This field is required !']);
    set_rules('start_date', '', ['required'], ['required' => 'This field is required !']);
    set_rules('end_date', '', ['required'], ['required' => 'This field is required !']);

    set_rules('attachment_cv', '', ['required', 'valid_url'], ['required' => 'This field is required !', 'valid_url' => 'URL is not valid !']);
    // set_rules( 'attachment_cv', '', ['required', 'allowed_types:pdf', 'max_size: 2MB'], ['required' => 'This field is required !', 'allowed_types' => 'Allowed file type is pdf !', 'max_size' => 'Allowed file size is upto 2MB'] );

    if ($_FILES['attachment_transkrip']['error'] == '4') {
        set_rules('attachment_transkrip', '', ['allow_not_required'], []);
    } else {
        set_rules('attachment_transkrip', '', ['required', 'allowed_types:pdf', 'max_size: 500KB'], ['required' => 'This field is required !', 'allowed_types' => 'Allowed file type is pdf !', 'max_size' => 'Allowed file size is upto 500KB']);
    }


    if ($_POST['have_another'] == '0') {
        set_rules('another_attachment', '', ['allow_not_required']);
    } else {
        set_rules('another_attachment', '', ['required', 'allowed_types:pdf', 'max_size: 500KB'], ['required' => 'This field is required !', 'allowed_types' => 'Allowed file type is pdf !', 'max_size' => 'Allowed file size is upto 500KB']);
    }

    // Cek Internship. Jika ada masih ada yang status nya Yes, Maka batalkan registrasi
    $cek = mysqli_query($koneksi_db, "SELECT * FROM internship WHERE nim = '" . $_POST['nim'] . "' AND `status` NOT IN ('Waiting','No','Finish')");
    $count = mysqli_num_rows($cek);
    if ($count > 0) {
        make_swal2('error', 'Register Failed', 'You already registered in internship !');
        make_sound('error');

        redirect_back(1);
        exit;
    }

    $cek_form = form_validation();



    if ($cek_form === false) {
        make_snackbar('Please check your submitted data and try again later');
        make_sound('error');

        redirect_back(1);
        exit;
    }




    $nim = mysqli_real_escape_string($koneksi_db, $_POST['nim']);
    $id_company = mysqli_real_escape_string($koneksi_db, $_POST['id_company']);
    $start_date = mysqli_real_escape_string($koneksi_db, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($koneksi_db, $_POST['end_date']);
    $id_user_company = '-';
    $nik = '-';
    $status = 'Waiting';
    $status2 = 'Apply';
    $file_1 = '-';
    $file_2 = '-';
    $file_3 = '-';
    $internship_position = '-';
    $date_finalreport = '-';
    $final_report = '-';
    $internship_period = date('n', strtotime($end_date) - strtotime($start_date));
    $timestamp = date('Y-m-d H:i:s');

    $id_offer = mysqli_real_escape_string($koneksi_db, $_POST['id_offer']);

    // $cek = mysqli_query($koneksi_db, "SELECT * FROM internship WHERE nim = $nim AND id_company = $id_company");
    // $count = mysqli_num_rows($cek);
    // if($count > 0){
    //     make_swal2('error', 'Register Failed', 'You Already Register in this Company !');
    //     redirect_back(1); exit;
    // }

    $sql = mysqli_query($koneksi_db, "INSERT INTO `internship` VALUES
    ( NULL, '$nim', '$id_company', '$start_date', '$end_date', '$id_user_company', '$nik', 
    '$status', '$internship_position', '$file_1', '$file_2', '$file_3', '$date_finalreport', '$final_report', '$internship_period', '$timestamp')");

    if ($sql === TRUE) {
        $id_internship = mysqli_insert_id($koneksi_db);

        // Move Uploaded file
        $direkori = "upload/lampiran_internship/";

        // $upload_cv = upload( $direkori, 'attachment_cv');
        $upload_cv = $_POST['attachment_cv'];

        if ($_FILES['attachment_transkrip']['error'] == '0') {

            $upload_transkrip = upload($direkori, 'attachment_transkrip');
        } else {
            $upload_transkrip = '-';
        }


        if ($_POST['have_another'] == '0') {
            $upload_another = '';
        } else {
            $upload_another = upload($direkori, 'another_attachment');
        }

        if ($upload_cv === false || $upload_transkrip === false || $upload_another === false) {
            // $hapus = delete_dir("./upload/lampiran_internship/" . $id_internship);
            
            unlink( $direkori. $upload_transkrip );
            $delete = table_delete2('internship', ['id_internship' => $id_internship]);
            make_swal2('error', 'Register Failed', 'Failed when saving internship data !');
            redirect_back(1);
            exit;
        }

        $update_internship = array(
            'file_1' => $upload_cv,
            'file_2' => $upload_transkrip,
            'file_3' => $upload_another,
        );
        $update_internship = table_update('internship', $update_internship, ['id_internship' => $id_internship]);

        if ($update_internship === false) {
            make_sound('error');

            $hapus = delete_dir("./upload/lampiran_internship/" . $id_internship);
            $delete = table_delete2('internship', ['id_internship' => $id_internship]);
            make_swal2('error', 'Register Failed', 'Failed when saving internship data !');
            redirect_back(1);
            exit;
        }

        $sql2 = mysqli_query($koneksi_db, "INSERT INTO applicant VALUES (NULL, '$id_offer', '$nim', '$id_internship', '$id_company', '$status2', '$timestamp')");


        swal2('success', 'Register Success', 'Register Data have been successfully saved !');
        redirect('my_internship');
        exit();
    } else {
        make_sound('error');

        make_swal2('error', 'Register Failed', 'Failed when saving internship data !');
        redirect_back(1);
        exit;
    }
} else
if ($aksi == "validasi_offer") {
    $id = mysqli_real_escape_string($koneksi_db, $_POST['id_internship_validasi']);
    $status = mysqli_real_escape_string($koneksi_db, $_POST['status']);
    $status2 = mysqli_real_escape_string($koneksi_db, $_POST['status2']);
    $id_spv = mysqli_real_escape_string($koneksi_db, $_POST['supervisor']);
    $id_offer = mysqli_real_escape_string($koneksi_db, $_POST['id_offer']);

    $start_date = mysqli_real_escape_string($koneksi_db, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($koneksi_db, $_POST['end_date']);

    $timestamp = date('Y-m-d H:i:s');


    $internship_period = date('n', strtotime($end_date) - strtotime($start_date));



    $sql = mysqli_query($koneksi_db, "UPDATE `internship` SET `internship_period` = '$internship_period',
    `status`= '$status', `id_user_company`='$id_spv' WHERE `id_internship` = '$id' ;");

    if ($sql === TRUE) {
        $sql2 = mysqli_query($koneksi_db, "UPDATE `applicant` SET
            `status`= '$status2' WHERE `id_internship` = '$id' ;");

        if ($status == 'Yes') {
            $sql3 = mysqli_query($koneksi_db, "INSERT INTO industry_feedback VALUES(NULL, '$id', '$nim', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-','$timestamp')");
            $sql4 = mysqli_query($koneksi_db, "INSERT INTO student_feedback VALUES(NULL, '$id', '$nim', '$timestamp', '-', '-', '-', '-', '-', '-', '-')");
        }

        unset($_SESSION['error']);
        swal2('success', 'Update Validation Sucess', 'Data Validate!');
        redirect('offer_detail&id=' . $id_offer);
        exit();
    } else {
        make_swal2('error', 'Update Validation Fail', 'Cannot validate Intern Candidate !');
        redirect_back(1);
        exit;
    }
}
