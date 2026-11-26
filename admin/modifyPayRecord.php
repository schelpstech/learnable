<?php
require __DIR__ . '/conf.php';
$id = filter_input(INPUT_GET, 'transref', FILTER_VALIDATE_INT);
header('Location: index.php?route=payments' . ($id > 0 ? '&id=' . $id : ''));
exit;
