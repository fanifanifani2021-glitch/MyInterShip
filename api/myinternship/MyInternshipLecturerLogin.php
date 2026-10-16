<?php

$username = $_POST['username'];
$password = $_POST['password'];

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
} else {
    response_json( 400, array('message' => 'Failed to connect to Login Services', 'error_code' => '108'));
}


// Hanya mahasiswa
if ($result->error_code == 0 && $polibatam_data->data->role == 'Mahasiswa') {
    response_json(400, array('message' => 'Only Lecturer can access this menu.', 'error_code' => '103'));
}



// Query menggunakan prepared statement
$query = $_SERVER['PDO']->prepare("SELECT * FROM lecturer WHERE nim_nik_unit = :nik OR nim_nik_unit = :nik_polibatam LIMIT 1");
$query->execute(array(':nik' => $username, ':nik_polibatam' => $polibatam_data->data->nik ?? $polibatam_data->data->id ?? ''));

// Ambil hasil query
$result2 = $query->fetch(PDO::FETCH_ASSOC);

// Count Siswa
$count_lecturer = $query->rowCount();

if ($count_lecturer == 0) {
    // Jika Password nya benar

    if ( $result->error_code == 0 ) {
        
        $logs = '';
        
        $logs .= "username : ";
        if ( is_string( $username ) ) {
            $logs .= $username. ". /n ";
        } else if ( is_array( $username ) ) {
            foreach( $username as $key => $value ) {
                $logs .= $key. " => " .$value. " /n ";
            }
        } else if ( is_null( $username ) ) {
            $logs .= " isi nya null. /n ";
        } else {
             $logs .= " isi nya unknown. /n ";
        }
        
        $logs .= "polibatam data : ";
        if ( is_array( $result->data ) ) {
            foreach( $result->data as $key => $value ) {
                $logs .= $key. " => " .$value. " /n ";
            }
        } else {
             $logs .= " isi nya unknown. /n ";
        }
        
        
        $logs .= " secretkey : $secretkey /n ";
        
        
        $logs .= "secretkey : ";
        if ( is_string( $secretkey ) ) {
            $logs .= $secretkey. ". /n ";
        } else if ( is_array( $secretkey ) ) {
            foreach( $secretkey as $key => $value ) {
                $logs .= $key. " => " .$value. " /n ";
            }
        } else if ( is_null( $secretkey ) ) {
            $logs .= " isi nya null. /n ";
        } else {
             $logs .= " isi nya unknown. /n ";
        }
        
        
        $logs .= "login pertama : ";
        if ( is_string( $result->data ) ) {
            $logs .= $result->data. ". /n ";
        } else if ( is_array( $result->data ) ) {
            foreach( $result->data as $key => $value ) {
                $logs .= $key. " => " .$value. " /n ";
            }
        } else if ( is_null( $result->data ) ) {
            $logs .= " isi nya null. /n ";
        } else {
             $logs .= " isi nya unknown. /n ";
        }
    
        
        dump_to_file('log_lecturer', "Timestamp : " . date("D, d - M - Y | H:i:s") . " \n" . " isi nya : " .$logs);
        response_json( 400, array('message' => 'Account is not registered', 'error_code' => '108'));
    } else

    if ($result->error_code != 0) {
        response_json( 400, array('message' => $result->message ?? $result->error_desc ?? 'Username or Password is incorrect.', 'error_code' => '102'));
    } else {
        response_json( 400, array('message' => $result->message ?? $result->error_desc ?? 'Unkwown Error, Please contact Admin', 'error_code' => '107'));
    }
}

// Cek Password
$data_lecturer = $result2;

if ($result->error_code != 0) {
    if (!password_verify($_POST['password'], $data_lecturer['password'])) {
        response_json( 400, array('message' => 'Username or Password is incorrect.', 'error_code' => '102'));
    }
}

unset( $data );
unset( $data_lecturer['password'] );

$data['role'] = 'Dosen';
$data['encrypt'] = $result2->data ?? NULL;
$data['POLIBATAM_LECTURER'] = ($result->error_code == 0) ? true : false;

$data['name'] = $data_lecturer['name'];
$data['nim_nik_unit'] = $data_lecturer['nim_nik_unit'];
$data['email_polibatam'] = $data_lecturer['email_polibatam'];
$data['Lecturer-Data'] = $data_lecturer;
$data['Lecturer_Data'] = $data_lecturer;

$data['is_koor'] = ($data_lecturer['is_koor'] == 1) ? true : false;
$data['prodi_koor'] = $data_lecturer['prodi_koor'];
$data['is_pembimbing'] = true;

$data['status'] = $data_lecturer['status'];
$data['program_study'] = $data_lecturer['program_study'];
$data['profile_picture'] = $data_lecturer['profile_picture'] ?? 'profile.jpg';

response_json( 200 , ['welcome_message' => $_SERVER['API_WELCOME'], 'message' => 'Login success', 'error_code' => '200', 'data' => $data ]  );