<?php

$data = array();
$limit = $_GET['limit'] ?? 4;
$page = 1;
if($_POST['page'] > 1)
{
  $start = (($_POST['page'] - 1) * $limit);
  $page = $_POST['page'];
}
else
{
  $start = 0;
}

$query = "
SELECT * FROM `offer` INNER JOIN `company` ON offer.id_company = company.id_company WHERE offer.status = 'Open'
";

if($_POST['query'] != '' && $_POST['check'] != '')
{
  $cek = $_POST['check'];
  
  $query .= '
  AND offer.offer_type IN ( '.$cek.' ) AND offer.position LIKE "%'.str_replace(' ', '%', $_POST['query']).'%" OR company.name LIKE "%'.str_replace(' ', '%', $_POST['query']).'%" OR company.type LIKE "%'.str_replace(' ', '%', $_POST['query']).'%" OR company.city LIKE "%'.str_replace(' ', '%', $_POST['query']).'%"
  ';
}else

if($_POST['query'] == '' && $_POST['check'] != '')
{
    $cek = $_POST['check'];

  $query .= "
  AND offer.offer_type IN ( $cek )
  ";
}else
if($_POST['query'] != '' && $_POST['check'] == '')
{
  $query .= '
  AND offer.position LIKE "%'.str_replace(' ', '%', $_POST['query']).'%" OR company.name LIKE "%'.str_replace(' ', '%', $_POST['query']).'%" OR company.type LIKE "%'.str_replace(' ', '%', $_POST['query']).'%" OR company.city LIKE "%'.str_replace(' ', '%', $_POST['query']).'%"
  ';
}

$query .= 'ORDER BY offer.id_offer DESC ';

$filter_query = $query . 'LIMIT '.$start.', '.$limit.'';

$statement = $_SERVER['KONEKSI_PDO']->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();

$statement = $_SERVER['KONEKSI_PDO']->prepare($filter_query);
$statement->execute();
$result = $statement->fetchAll( PDO::FETCH_ASSOC );
$total_filter_data = $statement->rowCount();
foreach($result as $row){
    $data[] = $row;
}

            if($result) {
                $response = 
                    array(
                      'welcome_message' => $_SERVER['API_WELCOME'],
                        'status' => 1,
                        'message' =>'Success',
                        'data' => $data,
                        'total' => $total_data,
                    );               
                echo json_encode( $response, JSON_PRETTY_PRINT );  
                http_response_code(200); exit;      
            }else {
                $response=array(
                  'welcome_message' => $_SERVER['API_WELCOME'],
                'status' => 0,
                'message' =>'No Data Found'
                );
                echo json_encode( $response );  
                http_response_code(404); exit;
            }

