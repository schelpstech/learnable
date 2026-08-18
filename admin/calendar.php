<?php

include __DIR__ . '/conf.php';
require_once dirname(__DIR__) . '/classes/autoload.php';
require_once dirname(__DIR__) . '/config/database.php';

if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}

$calendarService = new CalendarService(database_pdo());
$month = isset($_GET['month']) && is_string($_GET['month']) ? $_GET['month'] : date('Y-m');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!hash_equals($_SESSION['admin_csrf'], $token)) {
        $_SESSION['calendar_flash'] = array('type' => 'error', 'message' => 'Your session token expired. Please try again.');
    } else {
        try {
            if (($_POST['schedule_action'] ?? '') === 'create') {
                $calendarService->createAcademicEvent($_POST, $_SESSION['unamed']);
                $_SESSION['calendar_flash'] = array('type' => 'notice', 'message' => 'Academic event published successfully.');
            } elseif (($_POST['schedule_action'] ?? '') === 'delete') {
                $calendarService->deleteEvent($_POST['event_id'] ?? null, $_SESSION['unamed'], true);
                $_SESSION['calendar_flash'] = array('type' => 'notice', 'message' => 'Calendar entry removed.');
            }
        } catch (InvalidArgumentException $exception) {
            $_SESSION['calendar_flash'] = array('type' => 'error', 'message' => $exception->getMessage());
        } catch (RuntimeException $exception) {
            $_SESSION['calendar_flash'] = array('type' => 'error', 'message' => $exception->getMessage());
        }
    }
    header('Location: index.php?route=calendar&month=' . rawurlencode($month));
    exit;
}

$flash = isset($_SESSION['calendar_flash']) && is_array($_SESSION['calendar_flash']) ? $_SESSION['calendar_flash'] : array();
unset($_SESSION['calendar_flash']);
$term = $calendarService->activeTerm();
$calendarContext = array(
    'calendar' => $calendarService->month($month, null, $term),
    'timetable' => $calendarService->weeklyTimetable(null, $term),
    'term' => $term,
    'class_label' => 'Whole school schedule',
    'role' => 'admin',
    'can_manage' => true,
    'classes' => $calendarService->allClasses(),
    'csrf' => $_SESSION['admin_csrf'],
    'actor_id' => $_SESSION['unamed'],
    'form_action' => 'index.php?route=calendar&month=' . rawurlencode($month),
    'month_url' => 'index.php?route=calendar&month=',
    'notice' => ($flash['type'] ?? '') === 'notice' ? ($flash['message'] ?? '') : '',
    'error' => ($flash['type'] ?? '') === 'error' ? ($flash['message'] ?? '') : '',
);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Academic Calendar · LearnAble</title>
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/admin-modern.css?v=1">
    <link rel="stylesheet" href="../assets/css/academic-calendar.css?v=2">
</head>
<body>
    <?php include __DIR__ . '/nav.html'; ?>
    <?php require dirname(__DIR__) . '/views/shared/academic-calendar.php'; ?>
    <script src="../assets/js/academic-calendar.js?v=2"></script>
</body>
</html>
