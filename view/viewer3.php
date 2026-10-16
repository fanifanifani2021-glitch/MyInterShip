<?php


$filepath = './assets/img/profile.jpg';

if (file_exists($filepath)) {
    touch($filepath, filemtime($filepath), time()); // this will just record the time of access in file inode. you can write your own code to do whatever
    
    $path_parts = pathinfo($filepath);
    switch (strtolower($path_parts['extension'])) {
        case "gif":
            header("Content-type: image/gif");
            break;
        case "jpg":
        case "jpeg":
            header("Content-type: image/jpeg");
            break;
        case "png":
            header("Content-type: image/png");
            break;
        case "bmp":
            header("Content-type: image/bmp");
            break;
    }
    header("Accept-Ranges: bytes");
    header('Content-Length: ' . filesize($filepath));
    header("Last-Modified: Fri, 03 Mar 2004 06:32:31 GMT");
    readfile($filepath);
    
} else {
    header("HTTP/1.0 404 Not Found");
    header("Content-type: image/jpeg");
    header('Content-Length: ' . filesize("404_files.jpg"));
    header("Accept-Ranges: bytes");
    header("Last-Modified: Fri, 03 Mar 2004 06:32:31 GMT");
    readfile("404_files.jpg");
}