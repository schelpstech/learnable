<?php

include 'conf.php';

function admin_client_ip()
{
    return substr(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown', 0, 45);
}

function admin_record_login($con, $username, $status, $ip)
{
    $type = 'Administrator';
    $statement = mysqli_prepare($con, 'INSERT INTO log (uname, utype, stat, uip) VALUES (?, ?, ?, ?)');
    if ($statement) {
        mysqli_stmt_bind_param($statement, 'ssis', $username, $type, $status, $ip);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
}

function admin_login_fail($message)
{
    $_SESSION['messagef'] = $message;
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['but_admn'])) {
    admin_login_fail('Unauthorised access');
}

$username = isset($_POST['aaname']) ? trim((string) $_POST['aaname']) : '';
$password = isset($_POST['aapwd']) ? (string) $_POST['aapwd'] : '';
$ip = admin_client_ip();

if ($username === '' || $password === '' || strlen($username) > 100 || strlen($password) > 255) {
    admin_login_fail('Invalid login credentials');
}

$statement = mysqli_prepare($con, 'SELECT dpwd FROM `123admin` WHERE dname = ? LIMIT 2');
if (!$statement) {
    admin_login_fail('Login is temporarily unavailable');
}
mysqli_stmt_bind_param($statement, 's', $username);
mysqli_stmt_execute($statement);
mysqli_stmt_store_result($statement);
$accountCount = mysqli_stmt_num_rows($statement);
mysqli_stmt_bind_result($statement, $storedPassword);
$accountFound = mysqli_stmt_fetch($statement);
mysqli_stmt_close($statement);

if ($accountCount !== 1 || !$accountFound) {
    admin_record_login($con, $username, 4, $ip);
    admin_login_fail('Invalid username or password');
}

$storedPassword = (string) $storedPassword;
$passwordInfo = password_get_info($storedPassword);
$passwordMatches = !empty($passwordInfo['algo'])
    ? password_verify($password, $storedPassword)
    : hash_equals($storedPassword, $password);

if (!$passwordMatches) {
    admin_record_login($con, $username, 3, $ip);
    admin_login_fail('Invalid username or password');
}

admin_record_login($con, $username, 1, $ip);
session_regenerate_id(true);
$_SESSION['unamed'] = $username;
$_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
header('Location: admin/index.php?route=dashboard');
exit;
