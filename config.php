<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

$database = database_config();
$servername = $database['host'];
$username = $database['user'];
$password = $database['password'];
$dbname = $database['name'];
$conn = database_mysqli();
