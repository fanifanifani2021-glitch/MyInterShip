<?php 



// header("Content-type: image/jpeg");

// $image = imagecreatefromjpeg($file);
// imagejpeg($image);
// $file = 'assets/img/profile.jpg';
// header("Content-type: image/jpg");
// header("Expires: Mon, 1 Jan 2099 05:00:00 GMT");
// header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
// header("Cache-Control: no-store, no-cache, must-revalidate");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
$image = 'http://www.google.com/doodle4google/images/d4g_logo_global.jpg';
// $image = 'assets/img/profile.jpg';

// $image = base_url(). $file;
$imageData = base64_encode( file_get_contents($image));
// $imageData = base64_decode($image);

// $size = filesize($imageData);
header("Content-Length: $size bytes");
// readfile($imageData );

echo '<img src="data:image/jpeg;base64,' .$imageData. '"';

// header ("Content-type: image/jpeg"); //Output format gambar
// readfile($imageData);

// echo $file;
// var_dump( is_file( $file ) ); exit;





// readfile($file);
