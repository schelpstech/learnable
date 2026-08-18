<?php

require_once '../controller/start.inc.php';

if (!isset($_SESSION['active'], $_SESSION['user_type'])) {
    header('Location: ../view/index.php');
    exit;
}

try {
    $portalRoute = PortalRoute::fromRequest($_GET, $_SESSION['user_type']);
} catch (RuntimeException $exception) {
    http_response_code(403);
    exit('Access denied.');
} catch (InvalidArgumentException $exception) {
    http_response_code(404);
    exit('Page not found.');
}

// Mutating timetable requests are handled before any view output so CSRF,
// ownership checks and redirects remain reliable.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $portalRoute->page() === 'calendar') {
    if ($_SESSION['user_type'] !== 'Instructor') {
        http_response_code(405);
        exit('This action is not available for your account.');
    }
    if (empty($_SESSION['portal_csrf'])) {
        $_SESSION['portal_csrf'] = bin2hex(random_bytes(32));
    }
    $calendarService = new CalendarService(database_pdo());
    $token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    try {
        if (!hash_equals($_SESSION['portal_csrf'], $token)) {
            throw new RuntimeException('Your session token expired. Please try again.');
        }
        $term = $calendarService->activeTerm();
        if (($_POST['schedule_action'] ?? '') === 'create') {
            $calendarService->createClassSession($_POST, $_SESSION['active'], $term);
            $_SESSION['portal_calendar_flash'] = array('type' => 'notice', 'message' => 'Class session added to the timetable.');
        } elseif (($_POST['schedule_action'] ?? '') === 'delete') {
            $calendarService->deleteEvent($_POST['event_id'] ?? null, $_SESSION['active'], false);
            $_SESSION['portal_calendar_flash'] = array('type' => 'notice', 'message' => 'Your class session was removed.');
        }
    } catch (InvalidArgumentException $exception) {
        $_SESSION['portal_calendar_flash'] = array('type' => 'error', 'message' => $exception->getMessage());
    } catch (RuntimeException $exception) {
        $_SESSION['portal_calendar_flash'] = array('type' => 'error', 'message' => $exception->getMessage());
    }
    $redirect = 'router.php?pageid=calendar';
    if ($portalRoute->param('month')) $redirect .= '&month=' . rawurlencode($portalRoute->param('month'));
    if ($portalRoute->param('class_id')) $redirect .= '&class_id=' . rawurlencode($portalRoute->param('class_id'));
    header('Location: ' . $redirect);
    exit;
}

if ($portalRoute->view() === 'redirect') {
    $section = $_SESSION['user_type'] === 'Learner' ? 'learner' : 'instructor';
    $target = $portalRoute->page() === 'overview' ? 'notice.php' : 'index.php';
    header('Location: ../view/' . $section . '/' . $target);
    exit;
}

// The shared legacy views expect relative asset URLs from /learn/view/include/.
// A base element lets the front controller render them without changing every asset.
$portalBaseHref = '../view/include/';
$viewDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'include';
$previousDirectory = getcwd();

try {
    // Legacy view includes are relative to /learn/view/include. Keeping that
    // working directory makes their PHP includes deterministic while the URL
    // remains request-scoped through this front controller.
    if (!chdir($viewDirectory)) {
        throw new RuntimeException('Unable to load the requested portal view.');
    }
    require $portalRoute->view() . '.php';
} finally {
    if ($previousDirectory !== false) {
        chdir($previousDirectory);
    }
}
