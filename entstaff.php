<?php

include 'conf.php';

function legacy_staff_redirect($message)
{
    $_SESSION['messagef'] = $message;
    header('Location: staff.php');
    exit;
}

function legacy_staff_log($connection, $username, $status)
{
    $type = 'Instructor';
    $ip = substr(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown', 0, 45);
    $statement = mysqli_prepare($connection, 'INSERT INTO log (uname, utype, stat, uip) VALUES (?, ?, ?, ?)');
    if ($statement) {
        mysqli_stmt_bind_param($statement, 'ssis', $username, $type, $status, $ip);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['but_submit'])) {
    legacy_staff_redirect('Unauthorised access');
}

$username = isset($_POST['uname']) ? trim((string) $_POST['uname']) : '';
$password = isset($_POST['upass']) ? (string) $_POST['upass'] : '';
if ($username === '' || $password === '' || strlen($username) > 64 || strlen($password) > 255) {
    legacy_staff_redirect('Invalid login credentials');
}

$statement = mysqli_prepare($con, 'SELECT spwd, status, role FROM lhpstaff WHERE sname = ? LIMIT 1');
mysqli_stmt_bind_param($statement, 's', $username);
mysqli_stmt_execute($statement);
mysqli_stmt_bind_result($statement, $storedPassword, $status, $role);
$found = mysqli_stmt_fetch($statement);
mysqli_stmt_close($statement);

if (!$found) {
    legacy_staff_log($con, $username, 4);
    legacy_staff_redirect('Invalid username or password');
}

$passwordInfo = password_get_info((string) $storedPassword);
$usesHash = !empty($passwordInfo['algo']);
$matches = $usesHash ? password_verify($password, $storedPassword) : hash_equals((string) $storedPassword, $password);
if (!$matches) {
    legacy_staff_log($con, $username, 3);
    legacy_staff_redirect('Invalid username or password');
}
if ((int) $status !== 1) {
    legacy_staff_log($con, $username, 2);
    legacy_staff_redirect('Your account is inactive. Contact the school.');
}

if (!$usesHash || password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $upgrade = mysqli_prepare($con, 'UPDATE lhpstaff SET spwd = ? WHERE sname = ?');
    mysqli_stmt_bind_param($upgrade, 'ss', $newHash, $username);
    mysqli_stmt_execute($upgrade);
    mysqli_stmt_close($upgrade);
}

legacy_staff_log($con, $username, 1);
session_regenerate_id(true);

if ($role === 'b') {
    $_SESSION['unamed'] = $username;
    header('Location: bursar/profile.php');
    exit;
}

if ($role === 't') {
    $_SESSION['stnamed'] = $username;
    $_SESSION['active'] = $username;
    $_SESSION['user_type'] = 'Instructor';
    $_SESSION['portal_csrf'] = bin2hex(random_bytes(32));
    header('Location: learn/view/instructor/index.php');
    exit;
}

legacy_staff_redirect('This staff role does not have portal access.');
