<?php

$username = $_POST['username'];
$password = $_POST['password'];


if ( $password == '' || $username == '' ) {
    response_json( 400, array('message' => 'Parameter yang diminta tidak lengkap !', 'error_code' => '110'));
}


// URL target
$url = base_url('api.php?request=polibatam_login');
// URL Target berhasil

// Inisialisasi CURL
$ch = curl_init();

$data = array(
    'username'        => $username,
    'password'        => $password,
    'token'           => env('APP_TOKEN'),
);

// Set URL
curl_setopt($ch, CURLOPT_URL, $url);

// Set untuk mengembalikan respons sebagai string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Set metode permintaan menjadi POST
curl_setopt($ch, CURLOPT_POST, true);

// Set data POST yang akan dikirim
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Eksekusi CURL dan ambil respons
$response = curl_exec($ch);

$result = json_decode($response);

// Tutup CURL
curl_close($ch);

if ($result->error_code == 0) {
    $secretkey = $result->data->secretkey;

    $url = base_url('api.php?request=polibatam_biodata');
    // URL Target berhasil

    // Inisialisasi CURL
    $ch2 = curl_init();

    $data2 = array(
        "secretkey"        => $secretkey,
        'token'           => env('APP_TOKEN'),

    );

    // Set URL
    curl_setopt($ch2, CURLOPT_URL, $url);

    // Set untuk mengembalikan respons sebagai string
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

    // Set metode permintaan menjadi POST
    curl_setopt($ch2, CURLOPT_POST, true);

    // Set data POST yang akan dikirim
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $data2);

    $code = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

    // Eksekusi CURL dan ambil respons
    $response2 = curl_exec($ch2);

    $polibatam_data = json_decode($response2);

    // Tutup CURL
    curl_close($ch2);
}

    if (is_string($polibatam_data)) {
        $payload .= $polibatam_data;
    } else if (is_null($polibatam_data)) {
        $payload .= "Response is null";
    } else if (is_array($polibatam_data)) {
        foreach ($polibatam_data as $key => $value) {
            $payload .= $key . " => " . $value . ", ";
        }
    } else {
        $payload .= " Reponse nya invalid ";
    }


dump_to_file('student_login_api', "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n" . " Status : Login API Log \n" . "From : " . getBrowser() . " \n Payload : $payload \n\n");


// Hanya mahasiswa
if ($result->error_code == 0 && !in_array(strtolower($polibatam_data->data->role), ['mahasiswa', 'student', 'students'])) {
    response_json( 400, array('message' => 'Only Student can access this menu. (1)', 'error_code' => '103'));
}



$nim = $username;
// Query menggunakan prepared statement
$query = $_SERVER['PDO']->prepare("SELECT * FROM student_internship WHERE nim = :nim OR nim = :polibatam_id LIMIT 1");

// Eksekusi query
$query->execute(array(':nim' => $nim, ':polibatam_id' => $polibatam_data->data->id ?? $polibatam_data->data->nim ?? ''));


// Ambil hasil query
$result2 = $query->fetch(PDO::FETCH_ASSOC);

// Count Siswa
$count_mhs = $query->rowCount();

if ($count_mhs == 0) {
    // Jika Password nya benar
    if ($result->error_code != 0) {
        response_json( 400, array('message' => $result->error_desc ?? 'Username or Password is incorrect.', 'error_code' => '102'));
    
    } else if ($result->error_code == 0 && $polibatam_data->data->role == 'Mahasiswa') {
        $prodi = $polibatam_data->data->prodi;

        // Get Kode Prodi
        $cek_prodi = $_SERVER['PDO']->prepare("SELECT * FROM program_study WHERE id_kampus = :id_kampus AND prodi LIKE :prodi LIMIT 1");
        $cek_prodi->execute(array(':id_kampus' => '1', ':prodi' => "%$prodi%"));

        if ($cek_prodi->rowCount() != 1) {
            response_json( 400, array('message' => 'Current Study Program is not available in MyInternship.', 'error_code' => '104'));
        } else {
            // Get Kode Prodi
            $prodi = $cek_prodi->fetch(PDO::FETCH_ASSOC);

            $data3 = array(
                'id_kampus' => 1,
                'nim' => $polibatam_data->data->id ?? $polibatam_data->data->nim,
                'name' => $polibatam_data->data->nama,
                'program_study' => $prodi['kode_prodi'],
                'email' => $polibatam_data->data->email,
                'other_email' => '-',
                'phone' => '-',
                'no_whatsapp' => '-',
                'password' => NULL,
                'profile_picture' => 'profile.jpg',
                'nik_dospem' => NULL,
            );

            $insert = table_insert('student_internship', $data3);

            if ($insert) {

                $query = $_SERVER['PDO']->prepare("SELECT * FROM student_internship WHERE nim = :nim OR nim = :polibatam_id LIMIT 1");

                // Eksekusi query
                $query->execute(array(':nim' => $nim, ':polibatam_id' => $polibatam_data->data->id ?? $polibatam_data->data->nim ));

                // Ambil hasil query
                $result2 = $query->fetch(PDO::FETCH_ASSOC);

            } else {
                response_json( 400, array('message' => 'Please contact your Internship Coordinator to register your Account.', 'error_code' => '106'));
            }
        }
    } else {
        response_json( 400, array( 'message' => $result->message ?? $result->error_desc ?? 'Failed to connect to login services.', 'error_code' => '107'));
    }
}

// Cek Password
$data_mhs = $result2;

if ($result->error_code != 0) {
    if (!password_verify($_POST['password'], $data_mhs['password'])) {
        response_json( 400, array('message' => 'Username or Password is incorrect.', 'error_code' => '102'));
    }
}


unset( $data );
unset( $data_mhs['password'] );

if ( $result->error_code == 0 ) {
    $polibatam_student = true;
} else {
    if ( $data_mhs['id_kampus'] == 1 ) {
        $polibatam_student = true;
    } else {
        $polibatam_student = false;
    }
}


$data['role'] = 'Mahasiswa';
$data['encrypt'] = $result2->data ?? NULL;
$data['POLIBATAM_STUDENT'] = $polibatam_student;
$data['POLIBATAM_DATA'] = $polibatam_data ?? [];
$data['POLIBATAM_SECRETKEY'] = $result->data->secretkey;


$data['name'] = $data_mhs['name'];
$data['nim'] = $data_mhs['nim'];
$data['email'] = $data_mhs['email'];
$data['other_email'] = $data_mhs['email'];
$data['phone'] = $data_mhs['phone'];
$data['no_whatsapp'] = $data_mhs['no_whatsapp'];
$data['profile_picture'] = $data_mhs['profile_picture'] ?? 'profile.jpg';

$data['Student_Data'] = $data_mhs;
$data['Student-Data'] = $data_mhs;

response_json( 200, array('message' => 'Login success', 'error_code' => '200', 'data' => $data));