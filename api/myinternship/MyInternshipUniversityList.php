<?php


function getUniversity()
{
    $result = array();

    $query = $_SERVER['PDO']->prepare("SELECT tb_kampus.* FROM tb_kampus");
    $query->execute();
    $kampus = $query->fetchAll(PDO::FETCH_OBJ);


    $query = $_SERVER['PDO']->prepare("SELECT tb_kampus.*, upkpk.* FROM tb_kampus, upkpk WHERE tb_kampus.id_kampus = upkpk.id_kampus");
    $query->execute();
    $with_cdc = $query->fetchAll(PDO::FETCH_OBJ);


    $result['university'] = $kampus;
    $result['university_with_cdc'] = $with_cdc;

    return $result;
}

// Memanggil fungsi untuk mendapatkan data berdasarkan nik
$result = getUniversity();

if (!$result) {
    response_json( 400, array('message' => 'University data not found.', 'error_code' => '400'));
}

response_json( 200, $result );