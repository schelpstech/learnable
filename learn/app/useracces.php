<?php

include '../app/query.php';

$valErr = '';

function portal_login_message($class, $message)
{
    return '<div class="alert text-white bg-' . $class . ' d-flex align-items-center justify-content-between" role="alert">'
        . '<div class="alert-text">' . $message . '</div>'
        . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
        . '</div>';
}

function portal_login_redirect($model, $location)
{
    $model->redirect($location);
    exit;
}

function portal_record_login($model, $username, $userType, $status)
{
    $model->insert_data('log', array(
        'uname' => $username,
        'utype' => $userType,
        'uip' => substr(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown', 0, 45),
        'stat' => $status,
    ));
}

if (isset($_POST['logout']) && $_POST['logout'] === 'logout') {
    $submittedToken = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    $sessionToken = isset($_SESSION['portal_csrf']) ? (string) $_SESSION['portal_csrf'] : '';
    if ($sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
        $_SESSION['msg'] = portal_login_message('danger', 'The logout request expired. Please try again.');
        portal_login_redirect($model, '../view/index.php');
    }
    $model->log_out_user();
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['msg'] = portal_login_message('info', 'Bye! <b>Log out successful</b>!');
    portal_login_redirect($model, '../view/index.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['log_in']) || $_POST['log_in'] !== 'Log in') {
    $_SESSION['msg'] = portal_login_message('danger', 'Invalid login request.');
    portal_login_redirect($model, '../view/index.php');
}

$userid = isset($_POST['userid']) ? trim((string) $_POST['userid']) : '';
$userpwd = isset($_POST['userpwd']) ? (string) $_POST['userpwd'] : '';

if ($userid === '' || $userpwd === '' || strlen($userid) > 64 || strlen($userpwd) > 255) {
    portal_record_login($model, $userid, 'unknown', 4);
    $_SESSION['msg'] = portal_login_message('danger', 'Invalid login credentials.');
    portal_login_redirect($model, '../view/index.php');
}

$loginDetails = false;
$userType = '';
$passwordColumn = '';
$usernameColumn = '';
$tableName = '';

$learnerStatement = $db_conn->prepare('SELECT * FROM lhpuser WHERE uname = :username LIMIT 1');
$learnerStatement->execute(array(':username' => $userid));
$loginDetails = $learnerStatement->fetch(PDO::FETCH_ASSOC);

if ($loginDetails) {
    $userType = 'Learner';
    $passwordColumn = 'upwd';
    $usernameColumn = 'uname';
    $tableName = 'lhpuser';
} else {
    $staffStatement = $db_conn->prepare('SELECT * FROM lhpstaff WHERE sname = :username LIMIT 1');
    $staffStatement->execute(array(':username' => $userid));
    $loginDetails = $staffStatement->fetch(PDO::FETCH_ASSOC);
    if ($loginDetails) {
        $userType = 'Instructor';
        $passwordColumn = 'spwd';
        $usernameColumn = 'sname';
        $tableName = 'lhpstaff';
    }
}

if (!$loginDetails) {
    portal_record_login($model, $userid, 'unknown', 4);
    $_SESSION['msg'] = portal_login_message('danger', 'Invalid login credentials.');
    portal_login_redirect($model, '../view/index.php');
}

$storedPassword = (string) $loginDetails[$passwordColumn];
$passwordInfo = password_get_info($storedPassword);
$usesPasswordHash = !empty($passwordInfo['algo']);
$passwordMatches = $usesPasswordHash
    ? password_verify($userpwd, $storedPassword)
    : hash_equals($storedPassword, $userpwd);

if (!$passwordMatches) {
    portal_record_login($model, $userid, $userType, 3);
    $_SESSION['msg'] = portal_login_message('danger', 'Invalid login credentials.');
    portal_login_redirect($model, '../view/index.php');
}

if ((int) $loginDetails['status'] !== 1) {
    portal_record_login($model, $userid, $userType, 2);
    $_SESSION['msg'] = portal_login_message('danger', 'Access denied. Contact the school administrator.');
    portal_login_redirect($model, '../view/index.php');
}

// Upgrade legacy plaintext passwords after a verified login without forcing a reset.
if (!$usesPasswordHash || password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
    $newHash = password_hash($userpwd, PASSWORD_DEFAULT);
    $upgradeStatement = $db_conn->prepare(
        'UPDATE ' . $tableName . ' SET ' . $passwordColumn . ' = :password WHERE ' . $usernameColumn . ' = :username'
    );
    $upgradeStatement->execute(array(':password' => $newHash, ':username' => $userid));
}

portal_record_login($model, $userid, $userType, 1);
session_regenerate_id(true);
$_SESSION['active'] = $userid;
$_SESSION['user_type'] = $userType;
$_SESSION['portal_csrf'] = bin2hex(random_bytes(32));
$_SESSION['msg'] = portal_login_message('success', 'Welcome! <b>Log in successful</b>!');

if ($userType === 'Learner') {
    portal_login_redirect($model, '../view/learner/index.php');
}

portal_login_redirect($model, '../view/instructor/index.php');
