<?php

require __DIR__ . '/../classes/autoload.php';
require __DIR__ . '/../config/database.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

function cbt_test_assert($condition, $message)
{
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
    echo "PASS: {$message}\n";
}

$pdo = database_pdo();
$cbt = new CbtService($pdo);
$attempts = new CbtAttemptService($pdo);
$results = new CbtResultService($pdo);
$assessmentId = (int) $pdo->query(
    "SELECT id FROM cbt_assessments
     WHERE teacher_id = 'codex_demo_teacher' AND title = 'Codex Demo · Mixed Question Practice'
     ORDER BY id DESC LIMIT 1"
)->fetchColumn();
cbt_test_assert($assessmentId > 0, 'demo assessment exists');
$assessment = $cbt->assessment($assessmentId);
cbt_test_assert($assessment['teacher_id'] === 'codex_demo_teacher', 'assessment ownership is preserved');
cbt_test_assert((int) $assessment['question_count'] === 4, 'paper contains four versioned questions');

$started = null;
try {
    $started = $attempts->startAttempt($assessmentId, 'codex_demo_std', 'cbt-module-cli-test');
} catch (RuntimeException $exception) {
    if (strpos($exception->getMessage(), 'used all permitted attempts') === false) throw $exception;
    $latestAttempt = (int) $pdo->query(
        "SELECT id FROM cbt_attempts
         WHERE assessment_id = {$assessmentId} AND learner_id = 'codex_demo_std'
         ORDER BY attempt_no DESC LIMIT 1"
    )->fetchColumn();
    $reopenedToken = $attempts->reopenAttempt(
        $latestAttempt, 45, 'codex_demo_admin', true,
        'Automated regression rerun for the isolated Codex demo account.'
    );
    $started = array('attempt_id' => $latestAttempt, 'token' => $reopenedToken, 'resumed' => true);
}
cbt_test_assert(!empty($started['attempt_id']) && strlen($started['token']) === 64, 'secure attempt starts with a server token');
$state = $attempts->examState($started['attempt_id'], $started['token']);
$firstExpiry = $state['attempt']['expires_at'];
$refreshed = $attempts->examState($started['attempt_id'], $started['token']);
cbt_test_assert($firstExpiry === $refreshed['attempt']['expires_at'], 'refresh does not reset the official timer');
cbt_test_assert(count($state['questions']) === 4, 'attempt receives an immutable question snapshot');
$snapshotJson = json_encode($state['questions']);
cbt_test_assert(strpos($snapshotJson, 'is_correct') === false && strpos($snapshotJson, 'correct_answer') === false, 'exam state does not leak correct answers');

$answersByType = array(
    'single_choice' => 'B',
    'true_false' => 'T',
    'short_answer' => '10',
    'essay' => 'Reviewing my notes each evening helps me remember lessons and identify what I need to practise.',
);
foreach ($state['questions'] as $question) {
    $saved = $attempts->saveAnswer(
        $started['attempt_id'], $started['token'], $question['id'],
        $answersByType[$question['question_type']], false, 1
    );
    cbt_test_assert(empty($saved['submitted']), 'answer autosaves for ' . $question['question_type']);
}

$receipt = $attempts->submitAttempt($started['attempt_id'], $started['token'], false);
cbt_test_assert(strpos($receipt['submission_ref'], 'CBT-') === 0, 'final submission issues a unique receipt');
$repeated = $attempts->submitAttempt($started['attempt_id'], $started['token'], false);
cbt_test_assert($repeated['submission_ref'] === $receipt['submission_ref'] && !empty($repeated['idempotent']), 'repeated final submission is idempotent');

$script = $attempts->scriptForMarking($started['attempt_id'], 'codex_demo_teacher', false);
$essay = null;
foreach ($script['questions'] as $question) {
    if ($question['question_type'] === 'essay') $essay = $question;
}
cbt_test_assert($essay && !empty($essay['answer_id']), 'theory response enters the manual-marking queue');
$attempts->markAnswer($essay['answer_id'], 4, 'Clear habit and a sound explanation.', '', 'codex_demo_teacher', false);
$marked = $attempts->scriptForMarking($started['attempt_id'], 'codex_demo_teacher', false);
cbt_test_assert($marked['attempt']['status'] === 'marked', 'manual marking completes a partially marked script');

$published = $attempts->publishResults($assessmentId, 'codex_demo_teacher', false);
cbt_test_assert($published >= 1, 'approved scripts publish to the learner');
$review = $attempts->learnerReview($started['attempt_id'], 'codex_demo_std');
cbt_test_assert($review['attempt']['submission_ref'] === $receipt['submission_ref'], 'learner review returns the permanent submitted script');
if ($review['questions']) {
    cbt_test_assert($review['questions'][0]['correct_answer'] === null, 'correct answers remain hidden while the assessment window is open');
}

$analytics = $cbt->analytics($assessmentId, 'codex_demo_teacher', false);
cbt_test_assert((int) $analytics['summary']['submitted'] >= 1, 'analytics include submitted learner evidence');

$transferBlocked = false;
try { $results->previewAssessmentTransfer($assessmentId, 'codex_demo_teacher', false); }
catch (RuntimeException $exception) { $transferBlocked = strpos($exception->getMessage(), 'not configured') !== false; }
cbt_test_assert($transferBlocked, 'practice scores cannot enter academic results');

$otherLearner = $pdo->query(
    "SELECT uname FROM lhpuser WHERE status = 1 AND uname NOT IN ('codex_demo_std') ORDER BY id LIMIT 1"
)->fetchColumn();
if ($otherLearner) {
    $denied = false;
    try { $attempts->startAttempt($assessmentId, $otherLearner, 'unauthorized-test'); }
    catch (RuntimeException $exception) { $denied = strpos($exception->getMessage(), 'not assigned') !== false; }
    cbt_test_assert($denied, 'an unassigned learner cannot start the assessment');
}

echo "CBT module workflow test complete.\n";
