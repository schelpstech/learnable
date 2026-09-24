<?php
require_once __DIR__ . '/../controller/start.inc.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new RuntimeException('Use the scheme-of-work page for this action.');
    }
    $actor = CbtSecurity::requirePortalRole(array('Instructor'));
    CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', 'portal');
    $service = new SchemeService($db_conn);
    $action = is_string($_POST['scheme_action'] ?? null) ? $_POST['scheme_action'] : '';

    if ($action === 'subjects') {
        $payload = array('subjects' => $service->subjects($actor, $_POST['class_id'] ?? 0));
    } elseif ($action === 'topics') {
        $payload = array('topics' => $service->topics($actor, $_POST['class_id'] ?? 0, $_POST['subject_id'] ?? 0));
    } elseif ($action === 'save') {
        $topic = $service->save($_POST, $actor);
        $payload = array(
            'message' => !empty($_POST['id']) ? 'Scheme topic updated.' : 'Scheme topic added and available immediately.',
            'topic' => $topic,
            'topics' => $service->topics($actor, $_POST['class_id'] ?? 0, $_POST['subject_id'] ?? 0),
        );
    } elseif ($action === 'archive') {
        $service->archive($_POST['id'] ?? 0, $actor, ($_POST['confirm'] ?? '') === 'yes');
        $payload = array(
            'message' => 'Topic removed from the active scheme. Existing learning records remain in the database.',
            'topics' => $service->topics($actor, $_POST['class_id'] ?? 0, $_POST['subject_id'] ?? 0),
        );
    } else {
        throw new InvalidArgumentException('Unknown scheme action.');
    }
    echo json_encode(array('ok' => true) + $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    if ($error instanceof PDOException) error_log($error->getMessage());
    http_response_code($error instanceof InvalidArgumentException ? 422 : 403);
    echo json_encode(array(
        'ok' => false,
        'message' => $error instanceof PDOException ? 'The topic could not be saved. Please try again.' : $error->getMessage(),
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
