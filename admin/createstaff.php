<?php
require __DIR__ . '/workflow_bootstrap.php';
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['createst'] ?? '') !== 'Create Staff Account') throw new InvalidArgumentException('Invalid staff creation request.');
    CbtSecurity::requireCsrf($_POST['csrf_token'] ?? null, 'admin');
    (new StaffAccountService($workflowDb))->save($_POST,$staffAccess->username(),true);
    $_SESSION['ssmessaged']='Staff account successfully created.';
} catch (Throwable $e) { $_SESSION['ssmessaged']=workflow_error($e); }
header('Location: mgstaff.php'); exit;
