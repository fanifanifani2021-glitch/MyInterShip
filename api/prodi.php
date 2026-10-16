<?php
$jurusan = base64_decode($_GET['jurusan']);
$id_kampus = 1 ?? base64_decode($_GET['id_kampus']) ?? 1;

if ($jurusan == '' || (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $_GET['jurusan']) === false) {

    response_json(400, array(
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 0,
        'error_code' => 101,
        'message' => 'Provide Valid Jurusan',
        'form_validation_status' => false,
        'form_notification' => array(
            'jurusan' => 'Jurusan is required !',
        ),
    ));
    exit;

}


$data = array();

$sql = "SELECT * FROM program_study WHERE id_kampus = :id_kampus AND (jurusan = :jurusan OR major = :jurusan)";
$stmt = $_SERVER['PDO']->prepare($sql);

$stmt->bindParam('jurusan', $jurusan, PDO::PARAM_STR);
$stmt->bindParam('id_kampus', $id_kampus, PDO::PARAM_STR);

$stmt->execute();


if ($stmt->rowCount() < 1) {

    
    response_json(400, array(
        'status' => 0,
        'error_code' => 102,
        'message' => 'Provide Valid Jurusan',
        'additional_message' => 'Maybe try different \'Jurusan\' ?',
        'data' => array(),
    ));
    exit;

} else {
    $response =
        array(
            'welcome_message' => $_SERVER['API_WELCOME'],
            'status' => 1,
            'message' => 'Success',
            // 'additional_message' => 'Maybe try different `Jurusan` ?',
            'data' => $stmt->fetchAll( PDO::FETCH_ASSOC ) 

        );
    echo json_encode($response, JSON_PRETTY_PRINT);
    http_response_code(200);
    exit;
}
