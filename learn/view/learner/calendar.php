<?php
require_once dirname(__DIR__, 2) . '/controller/start.inc.php';
if (empty($_SESSION['active']) || ($_SESSION['user_type'] ?? '') !== 'Learner') {
    header('Location: ../index.php');
    exit;
}
header('Location: ../../app/router.php?pageid=calendar');
exit;
