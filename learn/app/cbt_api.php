<?php

require_once '../controller/start.inc.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');

function cbt_api_response($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_SERVER['HTTP_X_CBT_REQUEST'])
    || $_SERVER['HTTP_X_CBT_REQUEST'] !== '1') {
    cbt_api_response(405, array('ok' => false, 'message' => 'Request not allowed.'));
}

$contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
if ($contentLength > 100000) {
    cbt_api_response(413, array('ok' => false, 'message' => 'Request is too large.'));
}
$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    cbt_api_response(400, array('ok' => false, 'message' => 'Invalid request body.'));
}

$cookie = isset($_COOKIE['learnable_cbt_attempt']) ? (string) $_COOKIE['learnable_cbt_attempt'] : '';
if (!preg_match('/^(\d+)\.([a-f0-9]{64})$/', $cookie, $cookieMatch)) {
    cbt_api_response(401, array('ok' => false, 'message' => 'Secure examination session not found.'));
}
$attemptId = (int) $cookieMatch[1];
$plainToken = $cookieMatch[2];
if (isset($payload['attempt_id']) && (int) $payload['attempt_id'] !== $attemptId) {
    cbt_api_response(403, array('ok' => false, 'message' => 'Attempt mismatch.'));
}

try {
    $service = new CbtAttemptService($db_conn);
    $action = isset($payload['action']) ? (string) $payload['action'] : '';
    if ($action === 'state') {
        $data = $service->examState($attemptId, $plainToken);
    } elseif ($action === 'save') {
        $data = $service->saveAnswer(
            $attemptId,
            $plainToken,
            isset($payload['question_id']) ? $payload['question_id'] : null,
            array_key_exists('answer', $payload) ? $payload['answer'] : null,
            !empty($payload['flagged']),
            isset($payload['save_version']) ? $payload['save_version'] : 1
        );
    } elseif ($action === 'submit') {
        $data = $service->submitAttempt($attemptId, $plainToken, false);
    } elseif ($action === 'event') {
        $details = isset($payload['details']) && is_array($payload['details']) ? $payload['details'] : array();
        $service->recordIntegrityEvent($attemptId, $plainToken, isset($payload['event_type']) ? (string) $payload['event_type'] : '', $details);
        $data = array('recorded' => true);
    } else {
        throw new InvalidArgumentException('Unknown examination action.');
    }
    cbt_api_response(200, array('ok' => true, 'data' => $data));
} catch (InvalidArgumentException $exception) {
    cbt_api_response(422, array('ok' => false, 'message' => $exception->getMessage()));
} catch (RuntimeException $exception) {
    cbt_api_response(409, array('ok' => false, 'message' => $exception->getMessage()));
} catch (Throwable $exception) {
    cbt_api_response(500, array('ok' => false, 'message' => 'The examination service could not complete that request. Your saved answers remain protected.'));
}
