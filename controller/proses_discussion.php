<?php
error_reporting(true);
session_start();

$title = 'Discussion';
include "./controller/controller-init.php";


$action = $_GET['action'];

if ($action == 'add_topic') {


    set_rules('id_internship', '', ['required'], ['required' => 'This field is required !']);
    set_rules('user_type', '', ['required'], ['required' => 'This field is required !']);
    set_rules('user_id', '', ['required'], ['required' => 'This field is required !']);
    set_rules('user_name', '', ['required'], ['required' => 'This field is required !']);
    set_rules('discussion_topic', '', ['required'], ['required' => 'This field is required !']);
    set_rules('discussion_status', '', ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();


    if ($cek_form === false) {
        make_snackbar('Check your submitted data and try again later');

        make_old('ModalAdd', 'open');
        redirect_back(1);
        exit;
    }

    $discussion_data = array(
        'id_discussion' => NULL,
        'id_internship' => $_POST['id_internship'],

        'user_type' => $_POST['user_type'],
        'user_id' => $_POST['user_id'],
        'user_name' => $_POST['user_name'],

        'discussion_topic' => mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $_POST['discussion_topic']),
        'discussion_status' => mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $_POST['discussion_status']),

        'timestamp_add' => date('Y-m-d H:i:s'),
        'timestamp_update' => '-',


    );

    $insert_discussion = table_insert2('discussion', $discussion_data);

    if ($insert_discussion === true) {
        flash_session();

        make_snackbar('Discussion Topic has been added');

        // make_swal2( 'success', 'Adding Data Success', 'Discussion Topic has been added successfully !' );
        redirect_referrer(1);
        exit;
    } else {
        make_old('ModalAdd', 'open');
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1);
        exit;
    }
} else 

if ($action == 'edit_topic') {


    set_rules('id_discussion', '', ['required'], ['required' => 'This field is required !']);
    set_rules('loop', '', ['required'], ['required' => 'This field is required !']);
    set_rules('discussion_topic', '', ['required'], ['required' => 'This field is required !']);
    set_rules('discussion_status', '', ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();


    if ($cek_form === false) {
        make_old('ModalEdit', 'open');
        make_old('LoopEdit', $_POST['loop']);

        make_snackbar('(01) Check your submitted data and try again later');

        redirect_back(1);
        exit;
    }

    $where = array(
        'id_discussion' => $_POST['id_discussion'],
    );

    $discussion_data = array(

        'discussion_topic' => $_POST['discussion_topic'],
        'discussion_status' => $_POST['discussion_status'],

        'timestamp_update' => date('Y-m-d H:i:s'),

    );

    $insert_discussion = table_update2('discussion', $discussion_data, $where);
    

    if ($insert_discussion) {
        flash_session();

        make_snackbar('(02) Discussion Topic has been edited');

        // make_swal2( 'success', 'Editing Data Success', 'Discussion Topic has been edited successfully !' );
        redirect_referrer(1);
        exit;
    } else {

        make_old('ModalEdit', 'open');
        make_old('LoopEdit', $_POST['loop']);
        make_snackbar('(03) Check your submitted data and try again later');
        redirect_back(1);
        exit;
    }
} else

if ($action == 'reply') {

    set_rules('id_discussion', '', ['required'], ['required' => 'This field is required !']);
    set_rules('user_type', '', ['required'], ['required' => 'This field is required !']);
    set_rules('user_id', '', ['required'], ['required' => 'This field is required !']);
    set_rules('user_name', '', ['required'], ['required' => 'This field is required !']);
    set_rules('comment', '', ['required'], ['required' => 'This field is required !']);

    $cek_form = form_validation();


    if ($cek_form === false) {
        make_snackbar('Check your submitted data and try again later');

        make_old('ModalAdd', 'open');
        redirect_back(1);
        exit;
    }


    $comment_data = array(
        'id_comment' => NULL,
        'id_discussion' => $_POST['id_discussion'],

        'user_type' => $_POST['user_type'],
        'user_id' => $_POST['user_id'],
        'user_name' => $_POST['user_name'],

        'comment' => $_POST['comment'],

        'timestamp_add' => date('Y-m-d H:i:s'),
        'timestamp_update' => '-',

    );

    $insert_comment = table_insert2('discussion_comment', $comment_data);


    if ($insert_comment === true) {
        reflash_all();
        make_snackbar('Comment has been added');
        redirect_referrer(1);
        exit;
    } else {
        make_snackbar('Failed to add comment, please try again later !');
        redirect_back(1);
        exit;
    }
} else {
    make_snackbar('(00) You do not have permission to perform this action !');

    // make_swal2('error','Not Allowed', 'You do not have permission to perform this action !');
    redirect_back(1);
    exit;
}
