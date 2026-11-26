<?php
require __DIR__ . '/conf.php';
$query = ['route' => 'payment-view'];
foreach (['term', 'class_id'] as $field) {
    if (is_string($_GET[$field] ?? null)) $query[$field] = $_GET[$field];
}
header('Location: index.php?' . http_build_query($query));
exit;
