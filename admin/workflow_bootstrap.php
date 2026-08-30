<?php
require_once __DIR__ . '/conf.php';
require_once dirname(__DIR__) . '/classes/autoload.php';
$workflowDb = database_pdo();
$workflowCsrf = CbtSecurity::csrfToken('admin');
$workflowError = '';
$workflowNotice = $_SESSION['workflow_notice'] ?? '';
unset($_SESSION['workflow_notice']);
if (!function_exists('wh')) {
    function wh($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
    function workflow_redirect($route, $message, $id = 0) {
        $_SESSION['workflow_notice'] = $message;
        header('Location: index.php?route=' . rawurlencode($route) . ($id ? '&id=' . (int)$id : ''));
        exit;
    }
    function workflow_error(Throwable $error) {
        if ($error instanceof PDOException) { error_log($error->getMessage()); return 'We could not save this record. Please try again or contact your administrator.'; }
        return $error->getMessage();
    }
}
