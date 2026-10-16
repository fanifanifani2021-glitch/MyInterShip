<?php header('Content-Type: application/json');
$koneksi = $_SERVER['KONEKSI_DB'];
$sql = "SELECT * FROM company";
$start = isset($_POST['start']) ? $_POST['start'] : 1;
$length = isset($_POST['length']) ? $_POST['length'] : 10;
$total_records = $_SERVER['PDO']->query($sql)->rowCount();
$column_order = array('id_company', 'name', 'type', 'city', 'access_type', 'status');

$search = "";
$search_query = "";
if (isset($_POST['search']) && $_POST['search']['value'] != '') {
  $search_val = $_POST['search']['value'];
  $where = " WHERE ( name LIKE '%$search_val%' OR city LIKE '%$search_val%' )";
  $sql .= $where;
}

$order = ' ORDER BY ';
if (isset($_POST['order'])) {
  $order_column_index = $_POST['order'][0]['column'];
  $order_dir = $_POST['order'][0]['dir'];
  $order_column = $column_order[$order_column_index];

    $order .= " $order_column $order_dir";

}
$sql .= " $order LIMIT $start, $length";
$result = $_SERVER['PDO']->query($sql);
$sql_count = "SELECT COUNT(*) AS count FROM company";
if (isset($_POST['search']) && $_POST['search']['value'] != '') {
  $search_val = $_POST['search']['value'];
  $sql_count .= $where;
}
$result_count = $_SERVER['PDO']->query($sql_count);
$row_count = $result_count->fetch( PDO::FETCH_ASSOC ) ['count'];
$data = array();


while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {

  if (in_array($row['status'], ['verified', 'Verified', 1])) {
    $status_label = '<button type="button" class="btn btn-sm btn-success">Verified</button>';
  } else

if (in_array($row['status'], [0, 'not verified', 'Not Verified'])) {
    $status_label = '<button type="button" class="btn btn-sm btn-danger">Not Verified</button>';
  } else

if (in_array($row['status'], [2, 'blacklist', 'Blacklist'])) {
    $status_label = '<button type="button" class="btn btn-sm btn-dark">Blacklist</button>';
  }

  $data[] = array( 'order' => $_POST['order'], "id" => $row['id_company'], "name" => $row['name'], "type" => $row['type'], "city" => $row['city'], "access_type" => $row['access_type'], 
  "status" => $row['status'], 
  
  "status_label" => $status_label,

  "aksi" => "<a aria-label='Detail Company" .$row['name']. "' href='index.php?page=company_list_detail&id_company=" .base64_encode( $row['id_company'] ). "' type='button' class='btn btn-secondary btn-sm'> <i class='fa fa-info-circle mr-1'></i> Detail</a>", 
  'draw' => intval($_POST['draw']), 'search_val' => $search_val);
}
$output = array( 'welcome_message' => $_SERVER['API_WELCOME'], "draw" => intval($_POST['draw']), "recordsTotal" => intval($total_records), "recordsFiltered" => intval($row_count), "sql" => $sql, "data" => $data);
echo json_encode($output, JSON_PRETTY_PRINT);
$conn->close();
