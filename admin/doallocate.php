<?php
require_once __DIR__ . '/conf.php';
if (!isset($_SESSION['unamed'])) { header('Location: ../admin.php'); exit; }
header('Location: index.php?route=allocations');
exit;
