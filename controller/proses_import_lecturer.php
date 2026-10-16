<?php
error_reporting( true ); session_start();


foreach ($_SESSION['IMPORT']['LECTURER'] as $i => $data) :

    if ( $_POST['import_status'] [$i] != 1 ) {
        continue;
    }

    $sql = "SELECT COUNT(*) FROM lecturer WHERE nim_nik_unit = :nim_nik_unit";
    $sql = $_SERVER['PDO']->prepare($sql);
    $sql->execute( ['nim_nik_unit' => $data['lecturer_nik'] ] );

    if ( $sql->fetchColumn() != 0 ) {
        $_SESSION['IMPORT']['LECTURER'] [$i]['import_status'] = FALSE;
        $_SESSION['IMPORT']['LECTURER'] [$i]['import_review'] = 'Lecturer NIK has been used !';

        continue;
    }

    $data = array(
        'nim_nik_unit' => $data['lecturer_nik'],
        'name' => $data['lecturer_name'],
        'email_polibatam' => $data['lecturer_email'] ?? '-',
        'is_koor' => 0, 'prodi_koor' => NULL, 
        'status' => 'active', 
        'password' => password_hash( $_SERVER['LECTURER_DEFAULT_PASSWORD'], PASSWORD_DEFAULT), 
        'profile_picture' => 'profile.jpg', 
        'id_kampus' => $_SESSION['CDC_Data'] ['id_kampus'] ?? '1',
    );

    $insert = table_insert( 'lecturer', $data );

    if ( $insert === TRUE ) {
        $_SESSION['IMPORT']['LECTURER'] [$i]['import_status'] = TRUE;
        $_SESSION['IMPORT']['LECTURER'] [$i]['import_review'] = 'Import Success';
    } else {
        $_SESSION['IMPORT']['LECTURER'] [$i]['import_status'] = FALSE;
        $_SESSION['IMPORT']['LECTURER'] [$i]['import_review'] = 'Failed to Import';
    }

endforeach;

$_SESSION['IMPORT'] ['LECTURER_OVERALL_IMPORT'] = TRUE;

view('upkpk/lecturer_management/preview_data_lecturer');
// echo "hello"; exit;