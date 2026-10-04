<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/database.php';
$admin = database_pdo()->query('SELECT dname FROM `123admin` ORDER BY dname LIMIT 1')->fetchColumn();
if (!$admin) throw new RuntimeException('An existing administrator is required for navigation rendering.');
$_SESSION = array('unamed' => $admin, 'auth_account_type' => 'admin', 'auth_username' => $admin);
$_SERVER['REQUEST_URI'] = '/learnable/admin/index.php?route=dashboard';
$adminRoute = 'dashboard';

ob_start();
include __DIR__ . '/../admin/nav.html';
$navigation = ob_get_clean();

foreach (array('admin-shell-sidebar', 'data-admin-nav-search', 'index.php?route=dashboard', 'csrf_token', 'is-active') as $marker) {
    if (strpos($navigation, $marker) === false) {
        throw new RuntimeException('Admin navigation is missing ' . $marker);
    }
}

echo "Admin navigation render test passed.\n";
