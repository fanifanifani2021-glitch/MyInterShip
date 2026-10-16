<?php
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();

include 'ajax-init.php';

$id_company = base64_decode ( base64_decode($_POST['id_company'] ));


$sql_notif = "SELECT * FROM internship WHERE id_company = '$id_company' AND status IN ('WAITING','Waiting', 'waiting') ";
$exec_notif = $koneksi_db->query($sql_notif);


$total_notif = $exec_notif->num_rows;

$ss[] = array('total_notif' => $total_notif);


echo json_encode($ss);