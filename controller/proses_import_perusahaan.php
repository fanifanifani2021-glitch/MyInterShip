<?php
error_reporting(true);
session_start();


$gagal_insert = 0;
foreach ($_SESSION['IMPORT']['COMPANY'] as $i => $data) {

    if ($_POST['import_status'][$i] != 1) {
        continue;
        // echo "no import";
    }

    if ($_SESSION['IMPORT']['COMPANY'][$i]['nama_perusahaan'] === NULL) {
        continue;
    }


    $data = array(
        'id_company' => NULL,
        'name' => $_SESSION['IMPORT']['COMPANY'][$i]['nama_perusahaan'],
        'type' => $_SESSION['IMPORT']['COMPANY'][$i]['tipe_perusahaan'] ?? '-',
        'phone' => $_SESSION['IMPORT']['COMPANY'][$i]['no_telp'] ?? '-',
        'email' => $_SESSION['IMPORT']['COMPANY'][$i]['email'] ?? '-',
        'website' => $_SESSION['IMPORT']['COMPANY'][$i]['website'] ?? '-',
        'facebook' => $_SESSION['IMPORT']['COMPANY'][$i]['facebook'] ?? '-',
        'twitter' => $_SESSION['IMPORT']['COMPANY'][$i]['twitter'] ?? '-',
        'instagram' => $_SESSION['IMPORT']['COMPANY'][$i]['instagram'] ?? '-',
        'linkedin' => $_SESSION['IMPORT']['COMPANY'][$i]['linkedin'] ?? '-',
        'logo' => $_SESSION['IMPORT']['COMPANY'][$i]['logo'] ?? '-',
        'address' => $_SESSION['IMPORT']['COMPANY'][$i]['alamat'] ?? '-',
        'province' => $_SESSION['IMPORT']['COMPANY'][$i]['province'] ?? '-',
        'city' => $_SESSION['IMPORT']['COMPANY'][$i]['city'] ?? '-',
        'description' => $_SESSION['IMPORT']['COMPANY'][$i]['description'] ?? '-',
        'status' => $_SESSION['IMPORT']['COMPANY'][$i]['company_status'] ?? '-',
        'access_type' => $_SESSION['IMPORT']['COMPANY'][$i]['access_type'] ?? '-',
        'id_kampus' => $_SESSION['CDC-Data']['id_kampus'] ?? 1,
    );

    $insert = table_insert('company', $data);



    if (!$insert) {
        $_SESSION['IMPORT']['COMPANY'][$i]['import_status'] = false;
        $_SESSION['IMPORT']['COMPANY'][$i]['import_review'] = 'Failed to Import';

        $data_gagal_insert[] = $i;
        $gagal_insert = $gagal_insert + 1;
        continue;
    }

    $id_company = mysqli_insert_id($_SERVER['KONEKSI_DB']);
    // $_SESSION['IMPORT'] ['COMPANY'] [$id] ['username'] = 'HRD.' .sprintf( '%03s', $id_company);

    $data = array(
        'id_user_company' => NULL,
        'id_company' => $id_company,
        'user_fullname' => $_SESSION['IMPORT']['COMPANY'][$i]['nama_perusahaan'],
        'user_phone' => '-', 'user_email' => '-',
        'username' => $_SESSION['IMPORT']['COMPANY'][$i]['username'],
        'password' => $_SESSION['IMPORT']['COMPANY'][$i]['password_hash'],
        'user_type' => $_SESSION['IMPORT']['COMPANY'][$i]['user_type'] ?? 'HRD',
        'profile_picture' => 'profile.jpg',
    );

    $insert2 = table_insert('user_company', $data);
    // var_dump( $insert2, $_SERVER['KONEKSI_DB'] ); exit;


    if (!$insert2) {
        table_delete2('company', ['id_company' => $id_company]);
        $_SESSION['IMPORT']['COMPANY'][$i]['import_status'] = false;
        $data_gagal_insert[] = $i;
        $gagal_insert = $gagal_insert + 1;
        continue;
    }

    $_SESSION['IMPORT']['COMPANY'][$i]['import_status'] = true;
}

$_SESSION['IMPORT']['COMPANY_OVERALL_IMPORT'] = TRUE;

http_response_code(200);
view('upkpk/kelola_perusahaan/preview_data_perusahaan');


// $_SESSION['IMPORT'] ['COMPANY_OVERALL_IMPORT'] = true;
// $data_import = $_SESSION['IMPORT']['COMPANY'];
// $data_bermasalah = 0;

// if ($gagal_insert == 0) {

//     exit;
// } else {
//     http_response_code(200);
//     view('upkpk/kelola_perusahaan/preview_data_perusahaan', ['data_import' => $_SESSION['IMPORT']['COMPANY'], 'data_bermasalah' => '0', 'total_kosong' => '0']);
    
//     exit;
// }
