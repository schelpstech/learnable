<?php
require_once dirname(__DIR__) . '/conf.php';
require_once dirname(__DIR__) . '/classes/StaffAccess.php';
$staffAccess = new StaffAccess(database_pdo(), $_SESSION);
$staffAccess->requireAdminRequest();
