<?php
require __DIR__ . '/workflow_bootstrap.php';
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new InvalidArgumentException('Invalid staff management request.');
    CbtSecurity::requireCsrf($_POST['csrf_token'] ?? null,'admin');
    $service=new StaffAccountService($workflowDb);
    $actor=$staffAccess->username();
    if (($_POST['edstf'] ?? '') === 'Modify Staff Details') {
        $service->save($_POST,$actor,false);
        $message='Staff details and role updated.';
    } elseif (($_POST['chg'] ?? '') === 'Change Status') {
        $service->changeStatus($_POST['named'] ?? '',$_POST['status'] ?? '',$actor);
        $message='Staff status updated.';
    } elseif (($_POST['del'] ?? '') === 'Delete Staff Details') {
        $service->remove($_POST['stnamed'] ?? '',$actor);
        $message='Staff account deleted.';
    } else { throw new InvalidArgumentException('Unknown staff management action.'); }
    $_SESSION['ssmessaged']=$message;
} catch (Throwable $e) { $_SESSION['ssmessaged']=workflow_error($e); }
header('Location: mgstaff.php'); exit;
