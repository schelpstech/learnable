<?php

// begin or resume session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Load all shared classes and database settings from the project root.
require_once dirname(__DIR__, 2) . '/classes/autoload.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

// connect to database
try {
    $db_conn = database_pdo();
} catch (PDOException $e) {
    $errors = [];
    array_push($errors, 'Database connection failed.');
}

// Resolve current permissions even for sessions created before a role/status change.
$portalAccess = new StaffAccess($db_conn, $_SESSION);
if (!empty($_SESSION['active']) && !in_array($portalAccess->role(), ['teacher','learner'], true)) {
    StaffAccess::clearAuthentication();
    http_response_code(403); exit('Your account access has changed. Please sign in again.');
}

// make use of database with users
$user = new User($db_conn);
$model = new Model($db_conn);
$back_up = new DBbackup($db_conn);
$utility =new Utility();
