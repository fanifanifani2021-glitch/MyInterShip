<?php


$id_kampus = $_SESSION['CDC_Data'] ['id_kampus'];

$query = $_SERVER['PDO']->prepare("SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1");
$query->execute( array('id_kampus' => $id_kampus) );
$university_data = $query->fetch( PDO::FETCH_ASSOC );


view('upkpk/my_university', ['data' => $university_data] );