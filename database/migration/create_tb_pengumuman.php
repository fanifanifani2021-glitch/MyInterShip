<?php

$tb_migration = 'tb_migration';
$table_name = 'tb_pengumuman';

// Cek Table
$cekTable = mysqli_query( $koneksi_db , "SHOW tables LIKE '$table_name' ");

// var_dump( $cekTable ); exit;

if ( mysqli_num_rows( $cekTable ) != 0 ) {
    $drop = mysqli_query( $koneksi_db, "DROP TABLE $table_name ");
}


$sql = mysqli_query( $koneksi_db, "CREATE TABLE $table_name (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul_pengumuman VARCHAR(100) NOT NULL,
    isi_pengumuman VARCHAR(100) NOT NULL,
    lampiran_pengumuman VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_at VARCHAR(100) NOT NULL,
    created_by VARCHAR(100) NOT NULL
    )" );

if ( $sql === TRUE ) {
    $insert = mysqli_query( $koneksi_db, "INSERT INTO $tb_migration 
    VALUES ( NULL , 'create_$table_name' , NULL )");
}

if ( $sql === FALSE || $insert === FALSE ) {
    $hasil = FALSE;
}

?>