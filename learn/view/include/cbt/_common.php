<?php

if (!function_exists('cbt_h')) {
    function cbt_h($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('cbt_label')) {
    function cbt_label($value)
    {
        return ucwords(str_replace('_', ' ', (string) $value));
    }
}

if (!function_exists('cbt_datetime')) {
    function cbt_datetime($value)
    {
        return $value ? date('D, j M Y · g:i a', strtotime($value)) : 'Not set';
    }
}

$cbtService = new CbtService($db_conn);
$cbtAttemptService = new CbtAttemptService($db_conn);
$cbtResultService = new CbtResultService($db_conn);
$cbtActor = (string) $_SESSION['active'];
$cbtIsInstructor = $_SESSION['user_type'] === 'Instructor';
$cbtFlash = isset($_SESSION['cbt_flash']) && is_array($_SESSION['cbt_flash']) ? $_SESSION['cbt_flash'] : array();
unset($_SESSION['cbt_flash']);
$cbtCsrf = CbtSecurity::csrfToken('portal');
$cbtContext = $cbtService->activeContext();
