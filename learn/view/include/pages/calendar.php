<?php
$calendarService = new CalendarService(database_pdo());
$calendarRole = $_SESSION['user_type'] === 'Instructor' ? 'instructor' : 'learner';
$calendarMonth = $portalRoute->param('month', date('Y-m'));
$calendarTerm = (string) ($active_term['term'] ?? $calendarService->activeTerm());
$calendarActor = (string) $_SESSION['active'];
$calendarClasses = array();
$calendarClassId = null;
$calendarClassLabel = 'Whole school';

if ($calendarRole === 'learner') {
    $calendarClassId = (string) ($learner_profile['classid'] ?? '');
    $calendarClassLabel = $calendarService->className($calendarClassId);
} else {
    $calendarClasses = $calendarService->assignedClasses($calendarActor, $calendarTerm);
    $requestedClass = (string) $portalRoute->param('class_id', '');
    foreach ($calendarClasses as $assignedClass) {
        if ($requestedClass !== '' && (string) $assignedClass['classid'] === $requestedClass) {
            $calendarClassId = $requestedClass;
            $calendarClassLabel = $assignedClass['classname'];
            break;
        }
    }
    if ($calendarClassId === null && $calendarClasses) {
        $calendarClassId = (string) $calendarClasses[0]['classid'];
        $calendarClassLabel = $calendarClasses[0]['classname'];
    }
}

$calendarBaseQuery = 'pageid=calendar&month=' . rawurlencode($calendarMonth);
if ($calendarClassId !== null && $calendarRole === 'instructor') {
    $calendarBaseQuery .= '&class_id=' . rawurlencode($calendarClassId);
}

$calendarFlash = isset($_SESSION['portal_calendar_flash']) && is_array($_SESSION['portal_calendar_flash']) ? $_SESSION['portal_calendar_flash'] : array();
unset($_SESSION['portal_calendar_flash']);
$classViews = array();
if ($calendarRole === 'instructor' && count($calendarClasses) > 1) {
    foreach ($calendarClasses as $assignedClass) {
        $classViews[] = array(
            'label' => $assignedClass['classname'],
            'active' => (string) $assignedClass['classid'] === (string) $calendarClassId,
            'url' => '../../app/router.php?pageid=calendar&month=' . rawurlencode($calendarMonth) . '&class_id=' . rawurlencode($assignedClass['classid']),
        );
    }
}
$calendarContext = array(
    'calendar' => $calendarService->month($calendarMonth, $calendarClassId, $calendarTerm),
    'timetable' => $calendarService->weeklyTimetable($calendarClassId, $calendarTerm),
    'term' => $calendarTerm,
    'class_label' => $calendarClassLabel,
    'role' => $calendarRole,
    'can_manage' => $calendarRole === 'instructor' && !empty($calendarClasses),
    'classes' => $calendarClasses,
    'class_views' => $classViews,
    'csrf' => $_SESSION['portal_csrf'],
    'actor_id' => $calendarActor,
    'form_action' => '../../app/router.php?' . $calendarBaseQuery,
    'month_url' => '../../app/router.php?pageid=calendar' . ($calendarRole === 'instructor' && $calendarClassId !== null ? '&class_id=' . rawurlencode($calendarClassId) : '') . '&month=',
    'notice' => ($calendarFlash['type'] ?? '') === 'notice' ? ($calendarFlash['message'] ?? '') : '',
    'error' => ($calendarFlash['type'] ?? '') === 'error' ? ($calendarFlash['message'] ?? '') : '',
);
?>
<link rel="stylesheet" href="../../../assets/css/academic-calendar.css?v=2">
<?php require dirname(__DIR__, 4) . '/views/shared/academic-calendar.php'; ?>
<script src="../../../assets/js/academic-calendar.js?v=2"></script>
