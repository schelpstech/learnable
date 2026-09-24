<?php
require_once __DIR__ . '/conf.php';
if (!isset($_SESSION['unamed'])) { header('Location: ../admin.php'); exit; }
$id = filter_input(INPUT_GET, 'ref', FILTER_VALIDATE_INT);
header('Location: index.php?route=allocations' . ($id ? '&id=' . $id : ''));
exit;
