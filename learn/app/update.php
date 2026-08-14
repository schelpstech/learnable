<?php

include '../app/query.php';

function portal_update_message($class, $message)
{
    return '<div class="alert text-white bg-' . $class . ' d-flex align-items-center justify-content-between" role="alert">'
        . '<div class="alert-text">' . $message . '</div>'
        . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
        . '</div>';
}

function portal_update_redirect($model, $location)
{
    $model->redirect($location);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['active'], $_SESSION['user_type'])) {
    $_SESSION['msg'] = portal_update_message('danger', 'Access denied. Please sign in again.');
    portal_update_redirect($model, '../view/index.php');
}

$submittedToken = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
$sessionToken = isset($_SESSION['portal_csrf']) ? (string) $_SESSION['portal_csrf'] : '';
if ($sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
    $_SESSION['msg'] = portal_update_message('danger', 'The update request expired. Please try again.');
    $section = $_SESSION['user_type'] === 'Learner' ? 'learner' : 'instructor';
    portal_update_redirect($model, '../view/' . $section . '/index.php');
}

$action = isset($_POST['update']) ? (string) $_POST['update'] : '';
$phone = isset($_POST['phone']) ? trim((string) $_POST['phone']) : '';
$password = isset($_POST['password']) ? (string) $_POST['password'] : '';
$username = (string) $_SESSION['active'];
$userType = (string) $_SESSION['user_type'];

if (!preg_match('/^[0-9]{11}$/', $phone)) {
    $_SESSION['msg'] = portal_update_message('danger', 'Enter a valid 11-digit phone number.');
    $section = $userType === 'Learner' ? 'learner' : 'instructor';
    portal_update_redirect($model, '../view/' . $section . '/index.php');
}

if ($password !== '' && (strlen($password) < 8 || strlen($password) > 64)) {
    $_SESSION['msg'] = portal_update_message('danger', 'A new password must contain 8 to 64 characters.');
    $section = $userType === 'Learner' ? 'learner' : 'instructor';
    portal_update_redirect($model, '../view/' . $section . '/index.php');
}

if ($action === 'update_profile' && $userType === 'Learner') {
    $sql = 'UPDATE lhpuser SET numb = :phone';
    $parameters = array(':phone' => $phone, ':username' => $username);
    if ($password !== '') {
        $sql .= ', upwd = :password';
        $parameters[':password'] = password_hash($password, PASSWORD_DEFAULT);
    }
    $sql .= ' WHERE uname = :username';
    $statement = $db_conn->prepare($sql);
    $statement->execute($parameters);
    $_SESSION['portal_csrf'] = bin2hex(random_bytes(32));
    $_SESSION['msg'] = portal_update_message('success', 'Profile updated successfully.');
    portal_update_redirect($model, '../view/learner/index.php');
}

if ($action === 'update_staff_profile' && $userType === 'Instructor') {
    $sql = 'UPDATE lhpstaff SET sfone = :phone';
    $parameters = array(':phone' => $phone, ':username' => $username);
    if ($password !== '') {
        $sql .= ', spwd = :password';
        $parameters[':password'] = password_hash($password, PASSWORD_DEFAULT);
    }
    $sql .= ' WHERE sname = :username';
    $statement = $db_conn->prepare($sql);
    $statement->execute($parameters);
    $_SESSION['portal_csrf'] = bin2hex(random_bytes(32));
    $_SESSION['msg'] = portal_update_message('success', 'Profile updated successfully.');
    portal_update_redirect($model, '../view/instructor/index.php');
}

$_SESSION['msg'] = portal_update_message('danger', 'Invalid profile update request.');
$section = $userType === 'Learner' ? 'learner' : 'instructor';
portal_update_redirect($model, '../view/' . $section . '/index.php');
