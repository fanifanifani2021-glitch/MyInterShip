<?php

function random_company($limit = 5)
{
    $data = array();

    $sql = "SELECT * FROM company WHERE `status` = 'Verified' ORDER BY rand() LIMIT :int_limit";
    $stmt = $_SERVER['PDO']->prepare($sql);

    $stmt->bindParam(':int_limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() < 1) {
        $response = ['status' => 0, 'message' => 'No data found !'];
        return $response;
    }

    $row = $stmt->fetchAll(PDO::FETCH_OBJ);

    // $data[] = $row;


    $response =
        array(
            'status' => 1,
            'message' => 'Success',
            'data' => $row
        );

    return $response;

}


$limit = $_GET['limit'] ?? 5;
$result = random_company($limit);

if (!$result) {
    response_json(400, array( 'status' => 0, 'message' => 'No data found !', 'error_code' => '400'));
}

response_json(200, $result);
