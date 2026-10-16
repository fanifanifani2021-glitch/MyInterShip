<?php
error_reporting(true);
session_start();

$title = 'Attendance';
include "./controller/controller-init.php";

$action = $_GET['action'];

make_snackbar('Only Student Internship can perform this action');
role_checker('role', ['Mahasiswa'], 'notifikasi');

if ($action == 'add_history') {

    set_rules('id_internship', '', ['required'], ['required' => 'This field is required !']);
    set_rules('nim', '', ['required'], ['required' => 'This field is required !']);
    // set_rules( 'week_num', '', ['required'], ['required' => 'This field is required !'] );
    set_rules('attendance_date', '', ['required', 'valid_date', 'before_date:' . date('Y-m-d')], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !', 'before_date' => 'Date must be before ' . date('d M Y') . ' !']);
    set_rules('attendance_type', '', ['required', 'allowed_values:Present,Abstaint,Permit,Sick Leave,National Holiday'], ['required' => 'This field is required !', 'allowed_values' => 'Selected value is not valid !']);
    set_rules('description', '', ['required'], ['required' => 'This field is required !']);
    // set_rules( 'validation', '', ['required'], ['required' => 'This field is required !'] );

    if ($_POST['attendance_type'] == 'Present') {
        set_rules('check_in', '', ['required'], ['required' => 'This field is required !']);
        set_rules('check_out', '', ['required'], ['required' => 'This field is required !']);
    }


    $cek_form = form_validation();


    if ($cek_form === false) {

        make_snackbar('Check your submitted data and try again later');

        redirect_back(1);
        exit;
    }


    try {
        // Mulai transaksi
        $_SERVER['PDO']->beginTransaction();

        // Cek apakah ada data attendance yang duplikat berdasarkan `id_internship` dan `date`
        $sql = "SELECT * FROM attendance WHERE id_internship = :id_internship AND `date` = :attendance_date";
        $stmt = $_SERVER['PDO']->prepare($sql);

        // Bind parameters
        $stmt->execute([':id_internship' => $_POST['id_internship'], ':attendance_date' => $_POST['attendance_date']]);

        if ($stmt->rowCount() != 0) {
            make_snackbar('Check your submitted data and try again later');
            error('attendance_date', 'Duplicate attendance data!');
            redirect_back(1);
            exit;
        }

        // Persiapkan data untuk insert
        $check_in = '-';
        $check_out = '-';

        if ($_POST['attendance_type'] == 'Present') {
            $check_in = $_POST['check_in'];
            $check_out = $_POST['check_out'];
        }

        $awal_bulan = date("Y-m-01", strtotime($_POST['attendance_date'])); // Mendapatkan awal bulan
        $week_number = ceil(date("j", strtotime($_POST['attendance_date'])) / 7);

        $logbook_data = array(
            'id_attendance' => NULL,
            'id_internship' => $_POST['id_internship'],
            'nim' => $_POST['nim'],
            'attendance_type' => $_POST['attendance_type'],
            'description' => $_POST['description'],
            'date' => $_POST['attendance_date'],
            'week' => $week_number,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'approval_spv' => 'Waiting',
            'approval_dpm' => 'Waiting',
            'is_history' => true,
        );

        // Insert ke tabel attendance
        $sql = "INSERT INTO attendance (id_attendance, id_internship, nim, attendance_type, `description`, `date`, week, check_in, check_out, approval_spv, approval_dpm, is_history) 
                VALUES (:id_attendance, :id_internship, :nim, :attendance_type, :description, :date, :week, :check_in, :check_out, :approval_spv, :approval_dpm, :is_history)";

        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->execute($logbook_data);

        // Ambil id_attendance yang baru saja diinsert
        $id_attendance = $_SERVER['PDO']->lastInsertId();

        // Split string untuk mendapatkan bagian yang berisi data gambar base64
        $base64_parts = explode(',', $_POST['validation']);

        // Ambil bagian data gambar base64
        $base64_image = isset($base64_parts[1]) ? $base64_parts[1] : '';

        // Decode base64 menjadi data gambar
        $image_data = base64_decode($base64_image);

        // Jika tidak ada image_data, rollback transaksi
        if (empty($image_data)) {
            $_SERVER['PDO']->rollBack();
            make_snackbar('No signature data provided');
            redirect_back(1);
            exit;
        }

        // Simpan Gambar Signature nya
        $direktori = "./upload/attendance/";

        if (!is_dir($direktori)) {
            mkdir($direktori, 0755);
        }

        $filename = $direktori . generateUUIDv4() . ".png";

        // Simpan gambar ke dalam file
        if (file_put_contents($filename, $image_data) === false) {
            throw new Exception('Failed to save signature image');
        }

        // Jika ada signature, simpan ke tabel attendance-signature
        $signature_data = array(
            'id_signature' => NULL,
            'id_attendance' => $id_attendance,
            'signature' => $filename,
        );

        $sql_signature = "INSERT INTO `attendance-signature` (id_signature, id_attendance, signature) 
                          VALUES (:id_signature, :id_attendance, :signature)";

        $stmt_signature = $_SERVER['PDO']->prepare($sql_signature);
        $stmt_signature->execute($signature_data);

        // Commit transaksi
        $_SERVER['PDO']->commit();

        make_snackbar('Attendance has been added');
        redirect('attendance_internship&id_internship=' . base64_encode($_POST['id_internship']), 1);
        exit;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $_SERVER['PDO']->rollBack();

        if (!empty($filename) && is_file($filename)) {
            // Hapus file gambar signature jika terjadi kesalahan
            unlink($filename);
        }

        var_dump($e->getMessage()); // Optional: untuk debugging

        make_snackbar('Check your submitted data and try again later');
        redirect_back(1);
        exit;
    }
} else
if ($action == 'add') {


    set_rules('id_internship', '', ['required'], ['required' => 'This field is required !']);
    set_rules('nim', '', ['required'], ['required' => 'This field is required !']);
    // set_rules( 'week_num', '', ['required'], ['required' => 'This field is required !'] );
    set_rules('attendance_date', '', ['required', 'valid_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !']);
    set_rules('attendance_type', '', ['required', 'allowed_values:Present,Abstaint,Permit,Sick Leave,National Holiday'], ['required' => 'This field is required !', 'allowed_values' => 'Selected value is not valid !']);
    set_rules('description', '', ['required'], ['required' => 'This field is required !']);

    if ($_POST['attendance_type'] == 'Present') {
        set_rules('check_in', '', ['required'], ['required' => 'This field is required !']);
        set_rules('check_out', '', ['required'], ['required' => 'This field is required !']);
    }


    $cek_form = form_validation();


    if ($cek_form === false) {
        make_snackbar('Check your submitted data and try again later');

        redirect_back(1);
        exit;
    }

    $sql = "SELECT * FROM attendance WHERE id_internship = '" . $_POST['id_internship'] . "' AND date = '" . $_POST['attendance_date'] . "' ";
    $exec = $_SERVER['KONEKSI_DB']->query($sql);

    if ($exec->num_rows != 0) {
        make_snackbar('Check your submitted data and try again later');

        error('attendance_date', 'Duplicate attendance data !');
        redirect_back(1);
        exit;
    }

    $check_in = '-';
    $check_out = '-';

    if ($_POST['attendance_type'] == 'Present') {
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
    } else {
        $check_in = '-';
        $check_out = '-';
    }

    // $week_num = '';

    $awal_bulan = date("Y-m-01", strtotime($_POST['attendance_date'])); // Mendapatkan awal bulan
    $week_number = ceil(date("j", strtotime($_POST['attendance_date'])) / 7);


    $logbook_data = array(
        'id_attendance' => NULL,
        'id_internship' => $_POST['id_internship'],
        'nim' => $_POST['nim'],

        'attendance_type' => $_POST['attendance_type'],
        'description' => $_POST['description'],

        'date' => $_POST['attendance_date'],
        'week' => $week_number,

        'check_in' => $check_in,
        'check_out' => $check_out,

        'approval_spv' => 'Waiting',
        'approval_dpm' => 'Waiting',

        'is_history' => FALSE,
        'signature' => '',

    );

    $insert_attendance = table_insert('attendance', $logbook_data);

    if ($insert_attendance === true) {

        make_snackbar('Attendance has been added');

        redirect('attendance_internship&id_internship=' . base64_encode($_POST['id_internship']), 1);
        exit;
    } else {
        make_snackbar('Check your submitted data and try again later !');
        redirect_back(1);
        exit;
    }
} else

if ($action == 'edit') {


    set_rules('id_attendance', '', ['required'], ['required' => 'This field is required !']);
    set_rules('id_internship', '', ['required'], ['required' => 'This field is required !']);
    set_rules('nim', '', ['required'], ['required' => 'This field is required !']);
    set_rules('week_num', '', ['required'], ['required' => 'This field is required !']);
    set_rules('attendance_date', '', ['required', 'valid_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !']);
    set_rules('attendance_type', '', ['required', 'allowed_values:Present,Abstaint,Permit,Sick Leave,National Holiday'], ['required' => 'This field is required !', 'allowed_values' => 'Selected value is not valid !']);
    set_rules('description', '', ['required'], ['required' => 'This field is required !']);

    if ($_POST['attendance_type'] == 'Present') {
        set_rules('check_in', '', ['required'], ['required' => 'This field is required !']);
        set_rules('check_out', '', ['required'], ['required' => 'This field is required !']);
    }


    // Pengecekan Form Validation
    $cek_form = form_validation();
    if ($cek_form === false) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1);
        exit;
    }

    $sql = "SELECT * FROM attendance WHERE id_internship = '" . $_POST['id_internship'] . "' AND date = '" . $_POST['attendance_date'] . "' ";
    $exec = $_SERVER['KONEKSI_DB']->query($sql);
    $dat = $exec->fetch_assoc();

    if ($dat['date'] != $_POST['attendance_date'] && $exec->num_rows != 0) {
        make_snackbar('Check your submitted data and try again later');
        error('attendance_date', 'Duplicate attendance data !');
        redirect_back(1);
        exit;
    }

    $check_in = '-';
    $check_out = '-';

    if ($_POST['attendance_type'] == 'Present') {
        $check_in = $dat['check_in'];
        $check_out = $dat['check_out'];
    } else {
        $check_in = '-';
        $check_out = '-';
    }

    $where_attendance = array(
        'id_attendance' => $_POST['id_attendance'],
        'id_internship' => $_POST['id_internship'],
    );


    $logbook_data = array(
        'attendance_type' => $_POST['attendance_type'],
        'description' => $_POST['description'],

        'date' => $dat['date'],
        'week' => $_POST['week_num'],

        'check_in' => $check_in,
        'check_out' => $check_out,

        'approval_spv' => 'Waiting',
        'approval_dpm' => 'Waiting',


    );

    $update_attendance = table_update('attendance', $logbook_data, $where_attendance);

    if ($update_attendance === true) {
        make_snackbar('Attendance has been edited');

        redirect('attendance_internship&id_internship=' . base64_encode($_POST['id_internship']), 1);
        exit;
    } else {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1);
        exit;
    }
} else 

if ($action == 'check_in') {

    /* ==============================
     * HELPER FUNCTION
     * ============================== */
    function distanceMeter($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /* ==============================
     * FORM VALIDATION
     * ============================== */
    set_rules('id_internship', '', ['required']);
    set_rules('nim', '', ['required']);
    set_rules('attendance_type', '', [
        'required',
        'allowed_values:Present,Abstaint,Permit,Sick Leave,National Holiday'
    ]);
    set_rules('attendance_date', '', ['required', 'valid_date']);

    if ($_POST['attendance_type'] === 'Present') {
        set_rules('check_in', '', ['required']);
    }

    if (form_validation() === false) {
        make_snackbar('Check your submitted data');
        redirect_back(1);
        exit;
    }

    /* ==============================
     * GEOLOCATION DATA
     * ============================== */
    $user_lat  = $_POST['latitude']  ?? null;
    $user_lng  = $_POST['longitude'] ?? null;
    $accuracy  = $_POST['accuracy']  ?? null;




    $stmt = $_SERVER['PDO']->prepare("
        SELECT id_company
        FROM internship
        WHERE id_internship = :id_internship AND nim = :nim LIMIT 1
    ");
    $stmt->execute([
        ':id_internship' => $_POST['id_internship'],
        ':nim' => $_SESSION['nim']
    ]);

    $internship_data = $stmt->fetch(PDO::FETCH_ASSOC);

    /* ==============================
     * GET LOCATION RULE
     * ============================== */
    $stmt = $_SERVER['PDO']->prepare("
        SELECT latitude, longitude, radius_meters
        FROM attendance_location
        WHERE id_company = :id_company
        AND is_deleted = 0
    ");
    $stmt->execute([
        ':id_company' => $internship_data['id_company']
    ]);

    $location_rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $is_inside    = null;
    $min_distance = null;

    if ($_POST['attendance_type'] === 'Present' && count($location_rules) > 0) {

        if (!$user_lat || !$user_lng) {
            make_snackbar('Location data is required');
            redirect_back(1);
            exit;
        }

        if ($accuracy !== null && $accuracy > 100) {
            make_snackbar('GPS accuracy too low');
            redirect_back(1);
            exit;
        }
    

        foreach ($location_rules as $loc) {

            $distance = distanceMeter(
                $user_lat,
                $user_lng,
                $loc['latitude'],
                $loc['longitude']
            );

            // Simpan lokasi TERDEKAT
            if ($min_distance === null || $distance < $min_distance) {
                $min_distance     = $distance;
                $nearest_location = $loc;
            }

            // Cek apakah user berada dalam radius salah satu lokasi
            if ($distance <= $loc['radius_meters']) {
                $is_inside = true;
            }
        }

        if (!$is_inside) {
            make_snackbar('You are outside the attendance area');
            redirect_back(1);
            exit;
        }

        if ($_POST['attendance_type'] === 'Present' && !$nearest_location) {
            make_snackbar('Attendance location not found');
            redirect_back(1);
            exit;
        }
    }




    /* ==============================
     * DUPLICATE CHECK
     * ============================== */
    $stmt = $_SERVER['PDO']->prepare("
        SELECT 1 FROM attendance
        WHERE id_internship = :id_internship
        AND date = CURDATE()
        LIMIT 1
    ");
    $stmt->execute([
        ':id_internship' => $_POST['id_internship']
    ]);

    if ($stmt->rowCount() > 0) {
        make_snackbar('Attendance already submitted today');
        redirect_back(1);
        exit;
    }

    /* ==============================
     * DATA PREPARATION
     * ============================== */
    $check_in  = ($_POST['attendance_type'] === 'Present')
        ? date('Y-m-d H:i:s')
        : '-';

    $check_out = '-';

    $week_number = (int) ceil(date('j') / 7);

    /* ==============================
     * TRANSACTION START
     * ============================== */
    $_SERVER['PDO']->beginTransaction();

    try {

        /* INSERT ATTENDANCE */
        $stmt = $_SERVER['PDO']->prepare("
            INSERT INTO attendance
            (id_internship, nim, attendance_type, description, date, week,
             check_in, check_out, approval_spv, approval_dpm, is_history)
            VALUES
            (:id_internship, :nim, :attendance_type, '-', CURDATE(), :week,
             :check_in, :check_out, 'Waiting', 'Waiting', 0)
        ");

        $stmt->execute([
            ':id_internship' => $_POST['id_internship'],
            ':nim'           => $_POST['nim'],
            ':attendance_type'=> $_POST['attendance_type'],
            ':week'          => $week_number,
            ':check_in'      => $check_in,
            ':check_out'     => $check_out
        ]);

        $id_attendance = $_SERVER['PDO']->lastInsertId();

        /* INSERT GEO LOCATION LOG */
        if ($_POST['attendance_type'] === 'Present' && count($location_rules) > 0) {

            $stmt = $_SERVER['PDO']->prepare("
                INSERT INTO attendance_record_location
                (id_attendance,
                latitude, longitude, accuracy,
                distance_meter,
                master_location, master_latitude, master_longitude,
                master_radius_meters,
                is_inside, is_fake_gps)
                VALUES
                (:id_attendance,
                :lat, :lng, :accuracy,
                :distance,
                :master_location, :master_latitude, :master_longitude,
                :master_radius,
                :inside, :fake)
            ");

            $stmt->execute([
                ':id_attendance'     => $id_attendance,
                ':lat'               => $user_lat,
                ':lng'               => $user_lng,
                ':accuracy'          => $accuracy ?? null,
                ':distance'          => $min_distance,

                // MASTER LOCATION SNAPSHOT
                ':master_location'   => 'Company Attendance Area',
                ':master_latitude'   => $nearest_location['latitude'],
                ':master_longitude'  => $nearest_location['longitude'],
                ':master_radius'     => $nearest_location['radius_meters'],

                ':inside'            => $is_inside ? 1 : 0,
                ':fake'              => ($accuracy !== null && $accuracy > 100) ? 1 : 0
            ]);

        }

        $_SERVER['PDO']->commit();

        make_snackbar('Thank you, Check In succeeded');
        redirect('attendance_internship&id_internship=' . base64_encode($_POST['id_internship']), 1);
        exit;

    } catch (Exception $e) {

        $_SERVER['PDO']->rollBack();

        make_snackbar('Failed to save attendance');
        redirect_back(1);
        exit;
    }
}

else 

if ($action == 'check_out') {


    set_rules('id_attendance', '', ['required'], ['required' => 'This field is required !']);
    set_rules('id_internship', '', ['required'], ['required' => 'This field is required !']);
    set_rules('nim', '', ['required'], ['required' => 'This field is required !']);
    set_rules('week_num', '', ['required'], ['required' => 'This field is required !']);
    set_rules('attendance_date', '', ['required', 'valid_date'], ['required' => 'This field is required !', 'valid_date' => 'Enter valid date !']);
    set_rules('attendance_type', '', ['required', 'allowed_values:Present,Abstaint,Permit,Sick Leave,National Holiday'], ['required' => 'This field is required !', 'allowed_values' => 'Selected value is not valid !']);
    set_rules('description', '', ['required'], ['required' => 'This field is required !']);

    if ($_POST['attendance_type'] == 'Present') {
        set_rules('check_out', '', ['required'], ['required' => 'This field is required !']);
    }
    $cek_form = form_validation();


    if ($cek_form === false) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1);
        exit;
    }



    // Check Attendance
    $sql = "SELECT * FROM attendance WHERE `id_attendance` = '" . $_POST['id_attendance'] . "' AND `id_internship` = '" . $_POST['id_internship'] . "'";
    $exec = $_SERVER['KONEKSI_DB']->query($sql);


    if ($exec->num_rows != 1) {
        make_snackbar('Check your submitted data and try again later');

        make_swal2('error', 'Not Allowed', 'Attendance data is not found !');
        redirect_back(1);
        exit;
    }

    $attendance_data = $exec->fetch_assoc();
    $_POST['attendance_type'] = $attendance_data['attendance_type'];



    $check_in = '-';
    $check_out = '-';

    if ($_POST['attendance_type'] == 'Present') {
        $check_out = date('Y-m-d H:i:s');
    } else {
        $check_in = '-';
        $check_out = '-';
    }


    $att_where = array(
        'id_attendance' => $_POST['id_attendance'],
        'id_internship' => $_POST['id_internship'],
    );


    $att_data = array(
        'description' => $_POST['description'],

        'check_out' => $check_out,

        'approval_spv' => 'Waiting',
        'approval_dpm' => 'Waiting',

    );

    $insert_attendance = table_update('attendance', $att_data, $att_where);



    if ($insert_attendance === true) {
        make_snackbar('Thank You, Check Out succeeded');
        redirect('attendance_internship&id_internship=' . base64_encode($_POST['id_internship']), 1);
        exit;
    } else {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1);
        exit;
    }
} else {
    make_snackbar('You do not have permission to perform this action !');
    redirect_back(1);
    exit;
}
