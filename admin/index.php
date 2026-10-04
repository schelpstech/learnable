<?php

require_once __DIR__ . '/conf.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['unamed'])) {
    header('Location: ../admin.php');
    exit;
}

// Explicit allowlist: a route can never be turned into an arbitrary include.
$routes = require dirname(__DIR__) . '/config/admin_routes.php';

$route = isset($_GET['route']) && is_string($_GET['route']) ? $_GET['route'] : 'dashboard';
if (!isset($routes[$route])) {
    http_response_code(404);
    exit('Page not found.');
}

$adminRoute = $route;
if ($route === 'dashboard' && $staffAccess->role() === 'registry') {
    require __DIR__ . '/registry_dashboard.php';
} else {
    require __DIR__ . DIRECTORY_SEPARATOR . $routes[$route];
}
