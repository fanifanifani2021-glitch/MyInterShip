<?php
error_reporting( false ); session_start();

$title = 'Lecturer';
include "./controller/controller-init.php";


$action = $_GET['action'];

if ( $action == 'add' ) {

  set_rules('lecturer_nik' , 'NIK' , ['required', 'is_unique:lecturer.nim_nik_unit'], ['required' => 'This field is required !', 'is_unique' => 'NIK has been used !'] );
  set_rules('lecturer_name' , 'Nama' , ['required'], ['required' => 'This field is required !'] );
  set_rules('lecturer_email' , 'E-Mail' , ['required', 'valid_email'], ['required' => 'This field is required !', 'valid_email' => 'This email is not valid !'] );
  set_rules('lecturer_status' , 'Status' , ['required', 'allowed_values:Active,Not Active'], ['required' => 'This field is required !', 'allowed_values' => 'Value is not allowed !'] );

  $cek_form = form_validation();  


  if ( $cek_form === false ) {
    make_snackbar('Check your submitted data and try again later');
    redirect_back(1); exit;
  }

//   $data_lecturer = array(
//     'nim_nik_unit' => $_POST['lecturer_nik'],
//     'name' => $_POST['lecturer_name'],
//     'email_polibatam' => $_POST['lecturer_email'],
//     'is_koor' => 0,
//     'prodi_koor' => NULL,
//     'status' => $_POST['lecturer_status'] ?? 'Active',
//     'password' => password_hash( $_SERVER['LECTURER_DEFAULT_PASSWORD'], PASSWORD_DEFAULT ),
//     'profile_picture' => 'profile.jpg',
//     'id_kampus' => $_SESSION['CDC_Data'] ['id_kampus'] ?? '1',
//   );


//   $insert = table_insert('lecturer', $data_lecturer);
  
//   if ( $insert === false ) {
//     make_snackbar('Check your submitted data and try again later');

//     redirect_back(1); exit;
//   } else {
//     make_snackbar('New Lecturer has been added successfully');
//     redirect('lecturer_management',1); exit;
//   }


    try {
        // Koneksi database menggunakan PDO
        $pdo = $_SERVER['PDO'];
    
        // Data untuk di-insert
        $data_lecturer = array(
            'nim_nik_unit' => trim($_POST['lecturer_nik']),
            'name' => trim($_POST['lecturer_name']),
            'email_polibatam' => trim($_POST['lecturer_email']),
            'is_koor' => 0,
            'prodi_koor' => NULL,
            'status' => trim($_POST['lecturer_status'] ?? 'Active'),
            'password' => password_hash($_POST['lecturer_nik'] ?? $_SERVER['LECTURER_DEFAULT_PASSWORD'], PASSWORD_DEFAULT),
            'profile_picture' => 'profile.jpg',
            'id_kampus' => $_SESSION['CDC_Data']['id_kampus'] ?? '1',
        );
    
        // Query SQL untuk prepared statement
        $sql = "INSERT INTO lecturer (nim_nik_unit, name, email_polibatam, is_koor, prodi_koor, status, password, profile_picture, id_kampus)
                VALUES (:nim_nik_unit, :name, :email_polibatam, :is_koor, :prodi_koor, :status, :password, :profile_picture, :id_kampus)";
    
        // Prepare statement
        $stmt = $pdo->prepare($sql);
    
        // Bind parameter ke statement
        $stmt->bindParam(':nim_nik_unit', $data_lecturer['nim_nik_unit']);
        $stmt->bindParam(':name', $data_lecturer['name']);
        $stmt->bindParam(':email_polibatam', $data_lecturer['email_polibatam']);
        $stmt->bindParam(':is_koor', $data_lecturer['is_koor']);
        $stmt->bindParam(':prodi_koor', $data_lecturer['prodi_koor']);
        $stmt->bindParam(':status', $data_lecturer['status']);
        $stmt->bindParam(':password', $data_lecturer['password']);
        $stmt->bindParam(':profile_picture', $data_lecturer['profile_picture']);
        $stmt->bindParam(':id_kampus', $data_lecturer['id_kampus']);
    
        // Eksekusi statement
        $stmt->execute();
    
        // Jika berhasil
        make_snackbar('New Lecturer has been added successfully');
        redirect('lecturer_management', 1);
        exit;
    
    } catch (PDOException $e) {
        // Jika terjadi error
        error_log('Database Error: ' . $e->getMessage()); // Log error ke file log server
        make_snackbar('An error occurred while processing your request. Please try again later.');
        redirect_back(1);
        exit;
    }


} else

if ( $action == 'edit' ) {

  set_rules('old_lecturer_nik' , 'NIK' , ['required', 'exists:lecturer.nim_nik_unit'], ['required' => 'This field is required !', 'exists' => 'Lecturer not found !'] );
  set_rules('lecturer_nik' , 'NIK' , ['required', 'unique_except:lecturer.nim_nik_unit|' .$_POST['old_lecturer_nik']], ['required' => 'This field is required !', 'unique_except' => 'NIK has been used !'] );
  set_rules('lecturer_name' , 'Nama' , ['required'], ['required' => 'This field is required !'] );
  set_rules('lecturer_email' , 'E-Mail' , ['required', 'valid_email'], ['required' => 'This field is required !', 'valid_email' => 'This email is not valid !'] );
  set_rules('lecturer_status' , 'Status' , ['required', 'allowed_values:Active,Not Active'], ['required' => 'This field is required !', 'allowed_values' => 'Value is not allowed !'] );

  $cek_form = form_validation();  


  if ( $cek_form === false ) {
    make_snackbar('Check your submitted data and try again later');
    redirect_back(1); exit;
  }

  $where_lecturer = array(
    'nim_nik_unit' => $_POST['old_lecturer_nik'],
  );

  $data_lecturer = array(
    'nim_nik_unit' => $_POST['lecturer_nik'],
    'name' => $_POST['lecturer_name'],
    'email_polibatam' => $_POST['lecturer_email'],
    'status' => $_POST['lecturer_status'],
    'id_kampus' => $_SESSION['CDC_Data'] ['id_kampus'] ?? '1',
  );


  $insert = table_update('lecturer', $data_lecturer, $where_lecturer);
  
  if ( $insert === false ) {
    make_snackbar('Check your submitted data and try again later');

    redirect_back(1); exit;
  } else {
    make_snackbar('Lecturer has been edited successfully');
    redirect('lecturer_management',1); exit;
  }

}

else

if ( $action == 'reset_password' ) {

  set_rules('lecturer_nik' , 'NIK' , ['required', 'exists:lecturer.nim_nik_unit'], ['required' => 'This field is required !', 'exists' => 'Lecturer not found !'] );

  $cek_form = form_validation();  


  if ( $cek_form === false ) {
    make_snackbar('Check your submitted data and try again later');
    redirect_back(1); exit;
  }

  $where_lecturer = array(
    'nim_nik_unit' => $_POST['nim_nik_unit'],
  );

  $data_lecturer = array(
    'password' => password_hash( $_POST['nim_nik_unit'], PASSWORD_DEFAULT),
  );


  $insert = table_update('lecturer', $data_lecturer, $where_lecturer);

  if ( $insert === true ) {
    make_snackbar('Lecturer Password has been resetted successfully');
    redirect('lecturer_management',1); exit;
  }
  else {
    make_snackbar('Check your submitted data and try again later');

    redirect_back(1); exit;
  }

}

else {

  make_swal2('error' , 'Not Allowed', 'You do not have permission to complete this action !');
  redirect_back(1); exit;
}
