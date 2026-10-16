<?php
header('Content-Type: application/json');

$pdo = $_SERVER['PDO'];

$id = decrypt($_POST['id_logbook']);

if ($id === false) {

  http_response_code( 400 );
  make_snackbar('(01) Failed to remove Logbook, please try again later');

  whenAjaxRequest(function () {

      $output = array(
          'status' => 0,
          'validation' => $_SESSION['notifikasi'],
          'error' => $_SESSION['error'],
      );

      response_json(400, $output);
  });

  redirect_back(1); exit;

} else {
  // $id = $pdo->quote($id);

  $sql = "SELECT * FROM logbook WHERE id_logbook = :id LIMIT 1";
  $statement = $pdo->prepare($sql);
  $statement->bindParam(':id', $id);
  $statement->execute();

  if ($statement->rowCount() != 1) {
    http_response_code( 400 );
    make_snackbar('(02) Failed to remove Logbook, please try again later');
  
    whenAjaxRequest(function () {
  
        $output = array(
            'status' => 0,
            'validation' => $_SESSION['notifikasi'],
            'error' => $_SESSION['error'],
        );
  
        response_json(400, $output);
    });
  
    redirect_back(1); exit;
  }

  $sql = "DELETE FROM logbook WHERE id_logbook = :id";
  $statement = $pdo->prepare($sql);
  $statement->bindParam(':id', $id);
  $statement->execute();

  if ($statement->rowCount() != 1) {

    http_response_code( 400 );
    make_snackbar('(03) Failed to remove Logbook, please try again later');
  
    whenAjaxRequest(function () {
  
        $output = array(
            'status' => 0,
            'validation' => $_SESSION['notifikasi'],
            'error' => $_SESSION['error'],
        );
  
        response_json(400, $output);
    });
  
    redirect_back(1); exit;

    // $output = array(
    //   'welcome_message' => $_SERVER['API_WELCOME'],
    //   'status' => 1,
    //   'data' => $result,
    //   'id' => $id
    // );

    // echo json_encode($output, JSON_PRETTY_PRINT);
    // http_response_code(200);
    // exit;
  } else {

    http_response_code( 400 );
    make_snackbar('(04) Logbook has been removed');
  
    whenAjaxRequest(function () {
  
        $output = array(
            'status' => 0,
            'validation' => $_SESSION['notifikasi'],
            'error' => $_SESSION['error'],
        );
  
        response_json(400, $output);
    });
  
    redirect_back(1); exit;


    // $output = array(
    //   'welcome_message' => $_SERVER['API_WELCOME'],
    //   'status' => 0,
    //   'data' => $result,
    //   'id' => $id
    // );

    // echo json_encode($output, JSON_PRETTY_PRINT);
    // http_response_code(400);
    // exit;
  }
}
