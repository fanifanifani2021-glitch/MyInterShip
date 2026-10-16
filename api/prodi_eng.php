<?php
$prodi = base64_decode($_GET['prodi']);

if ($prodi == '' || (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $prodi) === false) {

    response_json(400, array(
        'welcome_message' => $_SERVER['API_WELCOME'],
        'status' => 0,
        'error_code' => 101,
        'message' => 'Provide Valid Prodi',
        'form_validation_status' => false,
        'form_notification' => array(
            'prodi' => 'Prodi is required !',
        )

    ));
    exit;
}



$data = array();

$sql = "SELECT * FROM program_study WHERE prodi LIKE :prodi OR kode_prodi LIKE :prodi LIMIT 1";
$stmt = $_SERVER['KONEKSI_PDO']->prepare($sql);
$prodi_like = '%' . $prodi . '%'; // Tambahkan wildcard % pada variabel prodi
$stmt->bindParam('prodi', $prodi_like, PDO::PARAM_STR);

$stmt->execute();

if ($stmt->rowCount() < 1) {

    response_json(400, array(
        'status' => 0,
        'error_code' => 102,
        'message' => 'Provide Valid Prodi',
        'additional_message' => 'Maybe try different \'Prodi\' ?',
        'data' => array()
    ));
    exit;
} else {

    $prodi = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1";
    $stmt = $_SERVER['KONEKSI_PDO']->prepare($sql);
    $stmt->execute([':id_kampus' => $prodi['id_kampus']]);

    if ($stmt->rowCount() != 1) {
        $prodi['university'] = [];
    } else {
        $prodi['university'] = $stmt->fetch(PDO::FETCH_ASSOC);
        unset($prodi['university']['created_at'], $prodi['university']['updated_at']);
    }

    response_json(200, array(
        'status' => 1,
        'error_code' => 200,
        'message' => 'Success',
        'data' => $prodi
    ));
    exit;
}

