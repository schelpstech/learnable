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
