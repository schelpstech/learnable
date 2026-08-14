<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

$database = database_config();
$host = $database['host'];
$user = $database['user'];
$password = $database['password'];
$dbname = $database['name'];
$con = database_mysqli();
