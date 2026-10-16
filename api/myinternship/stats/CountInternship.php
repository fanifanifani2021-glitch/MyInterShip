<?php

function count_internship()
{
    $data = array();

    $sql = "SELECT COUNT(id_offer) as CountInternship FROM offer WHERE offer_type = 'Internship' AND status = 'Open'";
    $stmt = $_SERVER['PDO']->prepare($sql);
    
    $stmt->execute();
    $CountCompany = $stmt->fetch( PDO::FETCH_ASSOC );

    $response = array(
        'CountInternship' => $CountCompany['CountInternship'] ?? 0
    );

    return $response;

}


$limit = $_GET['limit'] ?? 5;
$result = count_internship();

if (!$result) {
    response_json(400, array( 'status' => 0, 'message' => 'No data found !', 'error_code' => '400'));
}

response_json(200, $result);
