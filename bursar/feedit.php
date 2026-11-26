<?php
require __DIR__ . '/conf.php';
$query = ['route' => 'fees'];
foreach (['status'] as $key) {
    if (is_string($_GET[$key] ?? null)) $query[$key] = $_GET[$key];
}
$id = filter_input(INPUT_GET, 'ref', FILTER_VALIDATE_INT);
if ($id > 0) $query['id'] = $id;
header('Location: ../admin/index.php?' . http_build_query($query), true, 303);
exit;
