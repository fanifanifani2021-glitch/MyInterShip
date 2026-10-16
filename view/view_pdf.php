<?php
  
// header("Content-Type: application/octet-stream");
  
$id = $_GET["id"];
$file = $_GET["file"];
$dir = './upload/lampiran_internship/'.$id.'/'.$file;

if ( !is_file( $dir ) ) {
    $dir = './upload/lampiran_internship/' .$file;
}
  
// header("Content-Disposition: attachment; filename=" . urlencode($dir));   
// header("Content-Type: application/download");
// header("Content-Description: File Transfer");            
// header("Content-Length: " . filesize($dir));
  
// flush(); // This doesn't really matter.
  
// $fp = fopen($dir, "r");
// while (!feof($fp)) {
//     echo fread($fp, 65536);
//     flush(); // This is essential for large downloads
// } 
  
// fclose($fp); 

// The location of the PDF file
// on the server
  
// Header content type
header("Content-type: application/pdf");
  
header("Content-Length: " . filesize($dir));
  
// Send the file to the browser.
readfile($dir);
?>