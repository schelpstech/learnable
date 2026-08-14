<?php

include 'conf.php';

function legacy_learner_redirect($message)
{
    $_SESSION['messagef'] = $message;
    header('Location: student.php');
    exit;
}

function legacy_learner_log($connection, $username, $status)
{
    $type = 'Learner';
    $ip = substr(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown', 0, 45);
    $statement = mysqli_prepare($connection, 'INSERT INTO log (uname, utype, stat, uip) VALUES (?, ?, ?, ?)');
    if ($statement) {
        mysqli_stmt_bind_param($statement, 'ssis', $username, $type, $status, $ip);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['but_submit'])) {
    legacy_learner_redirect('Unauthorised access');
}

$username = isset($_POST['uname']) ? trim((string) $_POST['uname']) : '';
$password = isset($_POST['upass']) ? (string) $_POST['upass'] : '';
if ($username === '' || $password === '' || strlen($username) > 64 || strlen($password) > 255) {
    legacy_learner_redirect('Invalid login credentials');
}

$statement = mysqli_prepare($con, 'SELECT upwd, status, classid, fname FROM lhpuser WHERE uname = ? LIMIT 1');
mysqli_stmt_bind_param($statement, 's', $username);
mysqli_stmt_execute($statement);
mysqli_stmt_bind_result($statement, $storedPassword, $status, $classId, $fullName);
$found = mysqli_stmt_fetch($statement);
mysqli_stmt_close($statement);

if (!$found) {
    legacy_learner_log($con, $username, 4);
    legacy_learner_redirect('Invalid username or password');
}

$passwordInfo = password_get_info((string) $storedPassword);
$usesHash = !empty($passwordInfo['algo']);
$matches = $usesHash ? password_verify($password, $storedPassword) : hash_equals((string) $storedPassword, $password);
if (!$matches) {
    legacy_learner_log($con, $username, 3);
    legacy_learner_redirect('Invalid username or password');
}
if ((int) $status !== 1) {
    legacy_learner_log($con, $username, 2);
    legacy_learner_redirect('Your account is inactive. Contact the school.');
}

if (!$usesHash || password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $upgrade = mysqli_prepare($con, 'UPDATE lhpuser SET upwd = ? WHERE uname = ?');
    mysqli_stmt_bind_param($upgrade, 'ss', $newHash, $username);
    mysqli_stmt_execute($upgrade);
    mysqli_stmt_close($upgrade);
}

legacy_learner_log($con, $username, 1);
session_regenerate_id(true);
$_SESSION['classd'] = $classId;
$_SESSION['studnamed'] = $username;
$_SESSION['active'] = $username;
$_SESSION['user_type'] = 'Learner';
$_SESSION['portal_csrf'] = bin2hex(random_bytes(32));
$_SESSION['messagef'] = 'Welcome ' . $fullName;
header('Location: learn/view/learner/index.php');
exit;
