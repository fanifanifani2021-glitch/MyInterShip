<?php

function count_company()
{
    $data = array();

    $sql = "SELECT COUNT(id_company) as CountCompany FROM company";
    $stmt = $_SERVER['PDO']->prepare($sql);
    
    $stmt->execute();
    $CountCompany = $stmt->fetch( PDO::FETCH_ASSOC );

    $response = array(
        'CountCompany' => $CountCompany['CountCompany'] ?? 0
    );

    return $response;

}


$limit = $_GET['limit'] ?? 5;
$result = count_company();

if (!$result) {
    response_json(400, array( 'status' => 0, 'message' => 'No data found !', 'error_code' => '400'));
}

response_json(200, $result);
