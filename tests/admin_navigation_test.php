<?php

$_SESSION = array('unamed' => 'Navigation QA');
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
