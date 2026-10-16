<?php

if (!defined('DB_HOST')) {
    define('DB_HOST', env('DB_HOST') ?? 'localhost');
}


if (!defined('DB_PORT')) {
    define('DB_PORT', env('DB_PORT') ?? 3306);
}


if (!defined('DB_DATABASE')) {
    define('DB_DATABASE', env('DB_DATABASE') ?? 'myinternship');
}

if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', env('DB_USERNAME') ?? 'user');
}

if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', env('DB_PASSWORD') ?? 'password');
}





















function env($key = null)
{
    if ($key === null) {
        return null;
    } else {
        return null;
    }
}


