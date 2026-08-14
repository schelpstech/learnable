<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['log'], $_POST['csrf_token'], $_SESSION['admin_csrf'])
    || !hash_equals($_SESSION['admin_csrf'], (string) $_POST['csrf_token'])) {
    http_response_code(403);
    exit('Invalid logout request.');
}

$_SESSION = array();
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
header('Location: ../admin.php');
exit;
