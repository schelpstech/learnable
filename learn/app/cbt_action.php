<?php

require_once '../controller/start.inc.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$role = isset($_SESSION['user_type']) ? (string) $_SESSION['user_type'] : '';
$actorId = isset($_SESSION['active']) ? (string) $_SESSION['active'] : '';
if ($actorId === '' || !in_array($role, array('Learner', 'Instructor'), true)) {
    header('Location: ../view/index.php');
    exit;
}

$redirect = 'router.php?pageid=cbt';
$flashKey = 'cbt_flash';

try {
    CbtSecurity::requireCsrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null, 'portal');
    $action = isset($_POST['cbt_action']) ? (string) $_POST['cbt_action'] : '';
    $service = new CbtService($db_conn);
    $attempts = new CbtAttemptService($db_conn);
    $results = new CbtResultService($db_conn);

    if ($action === 'start_attempt') {
        if ($role !== 'Learner') {
            throw new RuntimeException('Only learners may start an assessment.');
        }
        $started = $attempts->startAttempt(
            isset($_POST['assessment_id']) ? $_POST['assessment_id'] : null,
            $actorId,
            isset($_POST['device_fingerprint']) ? $_POST['device_fingerprint'] : ''
        );
        header('Location: ../exam.php');
        exit;
    }

    if ($role !== 'Instructor') {
        throw new RuntimeException('This assessment action is available only to instructors.');
    }

    if ($action === 'create_assessment') {
        $assessmentId = $service->createAssessment($_POST, $actorId, false);
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => 'Assessment draft created. Add questions, preview the paper, then submit it for approval.');
        $redirect = 'router.php?pageid=cbt_builder&assessment_id=' . rawurlencode((string) $assessmentId);
    } elseif ($action === 'duplicate_assessment') {
        $copyId = $service->duplicateAssessment((int) $_POST['assessment_id'], $actorId, false);
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => 'A new draft copy was created with its schedule moved forward by one week.');
        $redirect = 'router.php?pageid=cbt_builder&assessment_id=' . rawurlencode((string) $copyId);
    } elseif ($action === 'create_question') {
        $questionId = $service->createQuestion($_POST, $actorId, false);
        if (!empty($_POST['assessment_id'])) {
            $service->addQuestionToAssessment((int) $_POST['assessment_id'], $questionId, $actorId, false);
            $redirect = 'router.php?pageid=cbt_builder&assessment_id=' . rawurlencode((string) $_POST['assessment_id']);
            $message = 'Question saved to your bank and added to the assessment.';
        } else {
            $redirect = 'router.php?pageid=cbt_bank';
            $message = 'Question saved to your reusable question bank.';
        }
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => $message);
    } elseif ($action === 'duplicate_question') {
        $copyId = $service->duplicateQuestion((int) $_POST['question_id'], $actorId, false);
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => 'Question copied as a private draft. The original remains unchanged.');
        $redirect = 'router.php?pageid=cbt_bank';
    } elseif ($action === 'import_questions') {
        if (!isset($_FILES['question_file']) || !is_array($_FILES['question_file'])
            || $_FILES['question_file']['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Choose a valid CSV question file.');
        }
        if ((int) $_FILES['question_file']['size'] > 2 * 1024 * 1024) {
            throw new InvalidArgumentException('The question file must not exceed 2 MB.');
        }
        $extension = strtolower(pathinfo($_FILES['question_file']['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv' || !is_uploaded_file($_FILES['question_file']['tmp_name'])) {
            throw new InvalidArgumentException('Only the approved CSV question template is accepted.');
        }
        $outcome = $service->importQuestionsCsv($_FILES['question_file']['tmp_name'], $actorId);
        $message = $outcome['created'] . ' question(s) imported.';
        if ($outcome['errors']) $message .= ' ' . count($outcome['errors']) . ' row(s) need attention: ' . implode(' ', array_slice($outcome['errors'], 0, 3));
        $_SESSION[$flashKey] = array('type' => $outcome['created'] > 0 ? 'success' : 'error', 'message' => $message);
        $redirect = 'router.php?pageid=cbt_bank';
    } elseif ($action === 'add_question') {
        $service->addQuestionToAssessment((int) $_POST['assessment_id'], (int) $_POST['question_id'], $actorId, false);
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => 'Question added to the assessment paper.');
        $redirect = 'router.php?pageid=cbt_builder&assessment_id=' . rawurlencode((string) $_POST['assessment_id']);
    } elseif ($action === 'remove_question') {
        $service->removeQuestionFromAssessment((int) $_POST['assessment_id'], (int) $_POST['question_id'], $actorId, false);
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => 'Question removed from this draft. It remains available in your question bank.');
        $redirect = 'router.php?pageid=cbt_builder&assessment_id=' . rawurlencode((string) $_POST['assessment_id']);
    } elseif ($action === 'submit_approval') {
        $status = $service->submitForApproval((int) $_POST['assessment_id'], $actorId);
        $message = $status === 'pending_approval'
            ? 'Assessment submitted for administrative approval.'
            : 'Assessment scheduled and learner notices created.';
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => $message);
        $redirect = 'router.php?pageid=cbt';
    } elseif ($action === 'pause_assessment') {
        $service->setAssessmentStatus((int) $_POST['assessment_id'], 'paused', $actorId, false, 'Paused by the assigned teacher.');
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => 'Assessment paused and affected learners notified.');
        $redirect = 'router.php?pageid=cbt';
    } elseif ($action === 'mark_answer') {
        $attempts->markAnswer(
            (int) $_POST['answer_id'],
            isset($_POST['marks']) ? $_POST['marks'] : null,
            isset($_POST['comment']) ? $_POST['comment'] : '',
            isset($_POST['reason']) ? $_POST['reason'] : '',
            $actorId,
            false
        );
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => 'Mark and feedback saved with a permanent marking-history entry.');
        $redirect = 'router.php?pageid=cbt_marking&assessment_id=' . rawurlencode((string) $_POST['assessment_id'])
            . '&attempt_id=' . rawurlencode((string) $_POST['attempt_id']);
    } elseif ($action === 'publish_results') {
        $count = $attempts->publishResults((int) $_POST['assessment_id'], $actorId, false);
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => $count . ' completed script(s) published to learners.');
        $redirect = 'router.php?pageid=cbt_marking&assessment_id=' . rawurlencode((string) $_POST['assessment_id']);
    } elseif ($action === 'transfer_scores') {
        $outcome = $results->transferAssessment((int) $_POST['assessment_id'], $actorId, false);
        $_SESSION[$flashKey] = array('type' => 'success', 'message' => $outcome['transferred'] . ' score(s) transferred; ' . $outcome['skipped'] . ' duplicate(s) safely skipped.');
        $redirect = 'router.php?pageid=cbt_marking&assessment_id=' . rawurlencode((string) $_POST['assessment_id']);
    } else {
        throw new InvalidArgumentException('Unknown assessment action.');
    }
} catch (Throwable $exception) {
    $_SESSION[$flashKey] = array('type' => 'error', 'message' => $exception->getMessage());
    if (!empty($_POST['assessment_id']) && ctype_digit((string) $_POST['assessment_id'])) {
        $page = isset($_POST['cbt_action']) && $_POST['cbt_action'] === 'mark_answer' ? 'cbt_marking' : 'cbt_builder';
        $redirect = 'router.php?pageid=' . $page . '&assessment_id=' . rawurlencode((string) $_POST['assessment_id']);
        if ($page === 'cbt_marking' && !empty($_POST['attempt_id']) && ctype_digit((string) $_POST['attempt_id'])) {
            $redirect .= '&attempt_id=' . rawurlencode((string) $_POST['attempt_id']);
        }
    }
}

header('Location: ' . $redirect);
exit;
