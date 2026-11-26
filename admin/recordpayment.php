<?php
require __DIR__ . '/conf.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'])) {
    require __DIR__ . '/payrecord.php';
    exit;
}
header('Location: index.php?route=payments', true, 303);
exit;
