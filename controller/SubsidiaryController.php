<?php

$action = $_GET['action'];


if ($action == 'data') {

  $dataTable = getDataTableData("SELECT * FROM tb_customer", function ($row) {
    return array(
      'aksi' =>
      '<button class="btn btn-primary btn-sm" onclick="editRow(\'' . encrypt($row['id']) . '\')">Edit</button> <button class="btn btn-danger btn-sm btn-delete" data-id="' . encrypt($row['id']) . '">Hapus</button>'
    );
  });

  response_json(200, $dataTable['response']);
} else

if ($action == 'add') {

  header('Content-Type: application/json');

  set_rules('company', 'Company', ['required'], ['required' => 'Tidak boleh kosong !']);
  set_rules('name', 'Name', ['required'], ['required' => 'Tidak boleh kosong !']);
  set_rules('email', 'E-Mail', ['required', 'is_unique:tb_customer.email'], ['is_unique' => 'E-Mail sudah digunakan !', 'required' => 'Tidak boleh kosong !']);
  set_rules('phone', 'Phone', ['required', 'is_unique:tb_customer.phone'], ['is_unique' => 'No Telp sudah digunakan !', 'required' => 'Tidak boleh kosong !']);

  if (form_validation() === false) {

    $output = array(
      'welcome_message' => $_SERVER['API_WELCOME'],
      'status' => 0,
      'validation' => $_SESSION['notifikasi'],
      'error' => $_SESSION['error'],
    );

    response_json(400, $output);
  }


  $data = array(
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone'],
  );


  $insert = table_insert('tb_customer', $data);

  if ($insert) {
    http_response_code(200);

    $output = array(
      'welcome_message' => $_SERVER['API_WELCOME'],
      'status' => 1,
      'validation' => $_SESSION['notifikasi'],
      'error' => $_SESSION['error'],
    );

    echo json_encode($output, JSON_PRETTY_PRINT);
    exit;
  } else {
    http_response_code(400);

    $output = array(
      'welcome_message' => $_SERVER['API_WELCOME'],
      'status' => 0,
      'validation' => $_SESSION['notifikasi'],
      'error' => $_SESSION['error'],
    );

    echo json_encode($output, JSON_PRETTY_PRINT);

    exit;
  }
} else {
  return view('exception/error_404');
}
