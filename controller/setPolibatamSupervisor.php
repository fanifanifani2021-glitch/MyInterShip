<?php
error_reporting( false ); session_start();

$title = 'Set Polibatam Supervisor';
include "./controller/controller-init.php";

if ( $_SESSION['is_koor'] !== TRUE ) {
  make_snackbar('Only Internship Coordinator can perform this action !'); 
  redirect_back(1); exit;
}


//   set_rules('lecturer_nik' , 'NIK' , ['required', 'exists:lecturer.nim_nik_uit'], ['required' => 'This field is required !', 'exists' => 'Lecturer not found !'] );
//   set_rules('student_nim' , 'NIM' , ['required', 'exists:student_internship.nim'], ['required' => 'This field is required !', 'exists' => 'Student not found !'] );

//   $cek_form = form_validation();

//   if ( $cek_form === false ) {
//     make_swal2('error', 'Failed', 'Failed to assign supervisor for current student ! (1)');
//     redirect_back(1); exit;
//   }

  $where = array(
    'nim' => $_POST['student_nim'],
  );

  $data = array(
    'nik_dospem' => $_POST['lecturer_nik'],
  );

  $assign = table_update( 'student_internship', $data, $where );

  if ( $assign !== TRUE ) {
    make_swal2('error', 'Failed', 'Failed to assign supervisor for current student ! (1)');
    redirect_back(1); exit;
  } else {
    make_swal2('success', 'Success', 'Supervisor has been assigned to current student !');
    redirect_back(1); exit;
  }


?>