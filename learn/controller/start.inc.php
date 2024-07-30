<?php

// begin or resume session
session_start();

// Include necessary file
include_once 'user.class.php';
include_once 'model.class.php';
include_once 'utility.class.php';
include_once 'backup.class.php';

// database access parameters
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'lhp';

// connect to database
try {
    $db_conn = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
    $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $errors = [];
    array_push($errors, $e->getMessage());
}

// make use of database with users
$user = new User($db_conn);
$model = new Model($db_conn);
$back_up = new DBbackup($db_conn);
$utility =new Utility();
