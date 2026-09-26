<?php
require __DIR__ . '/conf.php';
require_once __DIR__ . '/../classes/SchoolProfile.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: profile.php'); exit; }
$uploaded = [];
try {
    if (!is_string($_POST['profile_csrf'] ?? null) || empty($_SESSION['profile_csrf']) || !hash_equals($_SESSION['profile_csrf'], $_POST['profile_csrf'])) {
        throw new InvalidArgumentException('The form expired or the upload exceeded the server limit. Refresh the page and try again.');
    }
    $data = SchoolProfile::validate($_POST);
    foreach (['schlogo' => ['logo', 'school-logo'], 'school_photo' => ['school_photo', 'school-photo']] as $field => [$column, $prefix]) {
        $name = SchoolProfile::upload($_FILES[$field] ?? [], $prefix);
        if ($name !== null) { $data[$column] = $name; $uploaded[] = $name; }
    }
    if (!isset($data['school_photo']) && ($_POST['remove_photo'] ?? '') === '1') { $data['school_photo'] = ''; }
    SchoolProfile::save(database_pdo(), $data);
    $_SESSION['school_profile_notice'] = ['success', 'School profile saved. Your school website is now up to date.'];
    unset($_SESSION['school_profile_input']);
} catch (Throwable $exception) {
    foreach ($uploaded as $name) { @unlink(__DIR__ . '/../learn/asset/img/school/' . $name); }
    $message = $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Unable to save the profile. Please try again or contact your administrator.';
    if (!($exception instanceof InvalidArgumentException)) { error_log('School profile save: ' . $exception->getMessage()); }
    $_SESSION['school_profile_notice'] = ['danger', $message];
    $_SESSION['school_profile_input'] = array_filter($_POST, static function ($value) { return is_string($value) || is_array($value); });
}
header('Location: profile.php');
exit;
