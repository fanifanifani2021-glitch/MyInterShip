<?php

function random_company_offer($offer_type = 'Internship', $limit = 5)
{
    $data = array();

    $sql = "SELECT DISTINCT company.id_company, company.name, offer.* FROM company
    JOIN offer ON offer.id_company = company.id_company
    WHERE offer_type = :offer_type 
    AND offer.status = 'Open'
    AND company.status = 'Verified'
    ORDER BY offer.timestamp_add DESC LIMIT :int_limit";
    $stmt = $_SERVER['PDO']->prepare($sql);

    $stmt->bindParam(':int_limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offer_type', $offer_type, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() < 1) {
        $response = ['status' => 0, 'message' => 'No data found !'];
        return $response;
    }

    $row = $stmt->fetchAll(PDO::FETCH_OBJ);

    $response =
        array(
            'status' => 1,
            'message' => 'Success',
            'data' => $row
        );

    return $response;

}


$limit = $_GET['limit'] ?? 5;
$offer_type = $_GET['offer'] ?? 'Internship';

$result = random_company_offer($offer_type, $limit);

if (!$result) {
    response_json(400, array( 'status' => 0, 'message' => 'No data found !', 'error_code' => '400'));
}

response_json(200, $result);
