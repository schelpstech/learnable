<?php
require __DIR__ . '/conf.php';
$query = ['route' => 'payment-view'];
foreach (['term', 'class_id'] as $key) {
    if (is_string($_GET[$key] ?? null)) $query[$key] = $_GET[$key];
}
header('Location: ../admin/index.php?' . http_build_query($query), true, 303);
exit;
