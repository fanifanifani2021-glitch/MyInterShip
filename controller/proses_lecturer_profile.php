<?php
error_reporting( false ); session_start();
use Intervention\Image\ImageManager;

$title = 'Lecturer Profile';
include "./controller/controller-init.php";


$action = $_GET['action'];

if ( $action == 'identity' ) {
  set_rules('name' , 'Name' , ['required'], ['required' => 'This field is required !'] );
  set_rules('polibatam_email' , 'E-Mail' , ['required', 'valid_email'], ['required' => 'This field is required !', 'valid_email' => 'This email is not valid !'] );
  set_rules('other_email' , 'E-Mail' , ['required', 'valid_email'], ['required' => 'This field is required !', 'valid_email' => 'This email is not valid !'] );
  set_rules('phone' , 'Phone' , ['required', 'min_length:6'], ['required' => 'This field is required !', 'min_length' => 'This phone number is not valid !'] );

  $cek_form = form_validation();  


  if ( $cek_form === false ) {
    make_snackbar('Check your submitted data and try again later');

    make_old('open-form' , 'open');
    redirect_back(1); exit;
  }

  $nim_nik_unit = $_SESSION['nim_nik_unit'];

  $where_lecturer = array(
    'nim_nik_unit' => $_SESSION['nim_nik_unit'],
  );

  $data_lecturer = array(
    'name' => $_POST['name'],
    'email_polibatam' => $_POST['polibatam_email'],
  );


  // cek_lecturer_profile
  $cek = $_SERVER['KONEKSI_DB']->query("SELECT * FROM lecturer_profile WHERE nim_nik_unit = $nim_nik_unit ");

  if ( mysqli_num_rows($cek) == 0 ) {
    
    // insert
    $data_lecturer_profile = array(
      'id_lecturer_profile' => NULL,
      'nim_nik_unit' => $nim_nik_unit,
      'other_email' => $_POST['other_email'],
      'phone' => $_POST['phone'],
    );

    $insert = table_insert( 'lecturer_profile', $data_lecturer_profile );

    if ( $insert === false ) {
      make_old('open-form' , 'open');

      make_snackbar('Check your submitted data and try again later');

      // make_swal2('error' , 'Failed While Saving Profile', 'Please try again in a few minutes !');
      redirect_back(1); exit;
    }

  } else {

    $data_lecturer_profile = array(
      'other_email' => $_POST['other_email'],
      'phone' => $_POST['phone'],
    );
  
    $update = table_update('lecturer_profile', $data_lecturer_profile, $where_lecturer);
  
    if ( $update === false ) {
      make_snackbar('Check your submitted data and try again later');
      redirect_back(1); exit;
    }

  }


  $update = table_update('lecturer', $data_lecturer, $where_lecturer);
  
  if ( $update === false ) {
    make_snackbar('Check your submitted data and try again later');

    // make_swal2('error' , 'Failed While Saving Profile', 'Please try again in a few minutes !');
    redirect_back(1); exit;
  } else {

    $_SESSION['name'] = $_POST['name'];
    $_SESSION['email_polibatam'] = $_POST['email_polibatam'];
    make_snackbar('Lecturer Identity has been updated');

    // make_swal2('success' , 'Saving Profile Success', 'Profile had been saved successfully !');
    redirect('lecturer_profile',1); exit;
  }

} else


if ( $action == 'profile' ) {

  set_rules('new_profile', 'New Profile', ['required', 'allowed_types:png|jpg|jpeg'], ['required' => 'This field is required !', 'allowed_types' => 'Only .png, .jpg, .jpeg file are allowed !']);

  if ( form_validation() === false ) {
    make_snackbar('Check your submitted data and try again later');

    make_old('open-form' , 'open');
    redirect_back(1); exit;
  }

  // Get Data Profile
  $sql = "SELECT * FROM lecturer WHERE nim_nik_unit = '" .$_SESSION['nim_nik_unit']. "'";
  $exec = $_SERVER['KONEKSI_DB']->query($sql)->fetch_assoc();
        
  if ( !in_array( strtolower($exec['profile_picture']), ['profile.jpg', 'default.jpg'] ) ) {
    unlink('upload/profile_picture/'.$exec['profile_picture']);
  }


  // Upload file
  // move_uploaded_file( $_FILES['new_profile'] ['tmp_name'] , );
  // $new_name = escape( uniqid(). random_strings(15). ".". strtolower(end(explode('.', $_FILES['new_profile']['name']))));
  // $new_name = substr(escape( hash("sha512", random_strings(10) ). "." .pathinfo($_FILES['new_profile']['name'], PATHINFO_EXTENSION)), 100, 32 );

  // $upload = upload( 'upload/profile_picture/', 'new_profile', 'random' );

	// if ( $upload === FALSE) {

  $new_name = escape_string(random_strings(15) . "-" . random_strings(15) . "-" . random_strings(15) . "-" . uniqid() . ".jpg");

  $image = imagecreatefromstring(file_get_contents($_FILES['new_profile']['tmp_name']));
  if (!imagejpeg($image, './upload/profile_picture/' . $new_name, 80)) {

    make_snackbar('Please check your submitted data and try again later');

    make_old('open-form' , 'open');
    error('new_profile', 'Failed to upload Profile Picture');
    redirect_back(1); exit;
  }



  // Update Profile Picture
   $manager = new ImageManager(['driver' => 'gd']);

  // Membuka gambar menggunakan library php-image-editor
  $image = $manager->make( './upload/profile_picture/' . $new_name );

  // Menghapus data EXIF geolokasi
  $image->iptc([]);

  // Simpan kembali gambar setelah menghapus geolokasi
  $image->save($imagePath);




  $data = array(
    'profile_picture' => $new_name,
  );

  $where = array(
    'nim_nik_unit' => $_SESSION['nim_nik_unit']
  );

  $update = table_update('lecturer', $data, $where);

  if ( $update === false ) {
    unlink( "upload/profile_picture/$new_name" );

    make_snackbar('Failed to update Profile Picture, please try again later');

    // make_swal2('error', 'Update Profile Failed', 'Profile is not updated !');
    make_old('open-form' , 'open');
    error('new_profile', 'File failed to upload !');
    redirect_back(1); exit;
  } else {
    reflash_all();
    $_SESSION['profile_picture'] = $new_name;

    make_snackbar('Profile Picture has been updated');

    // make_swal2('success', 'Profile Updated', 'Profile data have been updated successfully !');
    redirect_referrer(1); exit;
  }

} else

if ( $action == 'password' ) {

  if ( $_SESSION['POLIBATAM_LECTURER'] ) {
    reflash_all();
    make_swal2('error', 'Not Allowed', 'Polibatam Lecturer cannot perform this action !');
    redirect_referrer(1); exit;
  }

  set_rules('current_password', 'Current Password', ['required'], ['required' => 'This field is required !']);
  set_rules('new_password', 'New Password', ['required', 'regex_match:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/u'], ['required' => 'This field is required !', 'regex_match' => 'Password did not match required format !']);
  set_rules('new_password2', 'Password Confirmation', ['required', 'matches:new_password'], ['required' => 'This field is required !', 'matches' => 'Password did not match !']);


  $cek_form = form_validation();

  if ($_POST['current_password'] != '') {

    // Cek User
    $sql = "SELECT `password` FROM lecturer WHERE nim_nik_unit = '" . $_SESSION['nim_nik_unit'] . "'";

    $exec = $_SERVER['KONEKSI_DB']->query($sql);


    if ($exec->num_rows != 1) {
      error('current_password', 'Password is incorrect !');
      $cek_form = false;
    } else {
      // Cek Password
      $password_hash = $exec->fetch_assoc()['password'];

      if (!password_verify($_POST['current_password'], $password_hash)) {
        error('current_password', 'Password is incorrect !');
        $cek_form = false;
      } else {
        success('current_password');
      }
    }
  }


  if ($cek_form === false) {
    make_old('open-form', 'open');
    make_snackbar('Check you submitted data and try again later');
    redirect_back(1);
    exit();
  }

  $where = array(
    'nim_nik_unit' => $_SESSION['nim_nik_unit'],
  );

  $data = array(
    'password' => password_hash($_POST['new_password'], PASSWORD_DEFAULT),
  );

  $update = table_update('lecturer', $where, $data);

  if ( $update === true ) {
    reflash_all();
    make_swal2('success', 'Success', 'Password has been changed successfully !');
    redirect_referrer(1); exit;
  } else {
    make_swal2('error', 'Change Password Failed', 'Please try again later !');
    redirect_back(1); exit;
  }
}


else {

  make_swal2('error' , 'Not Allowed', 'You do not have permission to complete this action !');
  redirect_back(1); exit;
}

?>