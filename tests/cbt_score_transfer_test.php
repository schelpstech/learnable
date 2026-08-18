<?php

require __DIR__ . '/../classes/autoload.php';
require __DIR__ . '/../config/database.php';

if (PHP_SAPI !== 'cli') exit(1);
function transfer_assert($condition, $message) {
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
    echo "PASS: {$message}\n";
}

$pdo = database_pdo();
$cbt = new CbtService($pdo);
$attempts = new CbtAttemptService($pdo);
$results = new CbtResultService($pdo);
$teacher = 'codex_demo_teacher';
$learner = 'codex_demo_std';
$context = $cbt->activeContext();
$source = $pdo->query(
    "SELECT q.id AS question_id, q.class_id, q.subject_id, q.scheme_id
     FROM cbt_questions q WHERE q.owner_teacher_id = 'codex_demo_teacher'
     AND q.question_type = 'single_choice' ORDER BY q.id LIMIT 1"
)->fetch();
transfer_assert((bool) $source, 'an isolated demo question is available');

$find = $pdo->prepare('SELECT id FROM cbt_assessments WHERE teacher_id = ? AND title = ? LIMIT 1');
$find->execute(array($teacher, 'Codex Demo · Weekly Score Transfer'));
$assessmentId = $find->fetchColumn();
if (!$assessmentId) {
    $assessmentId = $cbt->createAssessment(array(
        'teacher_id' => $teacher, 'class_id' => $source['class_id'], 'subject_id' => $source['subject_id'],
        'scheme_id' => $source['scheme_id'], 'title' => 'Codex Demo · Weekly Score Transfer',
        'instructions' => 'A one-question demo used to verify controlled weekly-score conversion.',
        'assessment_type' => 'weekly_test', 'result_treatment' => 'weekly',
        'total_marks' => 2, 'pass_mark' => 1,
        'start_at' => date('Y-m-d H:i:s', strtotime('-5 minutes')),
        'close_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
        'duration_minutes' => 10, 'max_attempts' => 1, 'navigation_mode' => 'free',
        'allow_backtrack' => 1, 'auto_submit' => 1, 'show_score' => 1,
        'allow_review' => 1, 'require_approval' => 1,
    ), 'codex_demo_admin', true);
    $pdo->prepare('DELETE FROM cbt_assessment_assignments WHERE assessment_id = ?')->execute(array($assessmentId));
    $pdo->prepare(
        'INSERT INTO cbt_assessment_assignments
         (assessment_id, assignment_type, class_id, learner_id, status)
         VALUES (?, \'student\', ?, ?, \'eligible\')'
    )->execute(array($assessmentId, $source['class_id'], $learner));
    $cbt->addQuestionToAssessment($assessmentId, $source['question_id'], $teacher, false);
    $cbt->setAssessmentStatus($assessmentId, 'approved', 'codex_demo_admin', true, 'Regression test approval.');
    $cbt->setAssessmentStatus($assessmentId, 'published', 'codex_demo_admin', true, 'Regression test publication.');
}

$publishedAttempt = $pdo->prepare(
    'SELECT id FROM cbt_attempts WHERE assessment_id = ? AND learner_id = ? AND published_at IS NOT NULL LIMIT 1'
);
$publishedAttempt->execute(array($assessmentId, $learner));
$attemptId = $publishedAttempt->fetchColumn();
if (!$attemptId) {
    $started = $attempts->startAttempt($assessmentId, $learner, 'score-transfer-test');
    $state = $attempts->examState($started['attempt_id'], $started['token']);
    $question = $state['questions'][0];
    $attempts->saveAnswer($started['attempt_id'], $started['token'], $question['id'], 'B', false, 1);
    $attempts->submitAttempt($started['attempt_id'], $started['token'], false);
    $attempts->publishResults($assessmentId, $teacher, false);
    $attemptId = $started['attempt_id'];
}

$preview = $results->previewAssessmentTransfer($assessmentId, $teacher, false);
transfer_assert($preview['mapping']['component'] === 'weekly' && (int) $preview['mapping']['target_max'] === 10, 'weekly target maps to the existing ten-mark component');
$first = $results->transferAssessment($assessmentId, $teacher, false);
$second = $results->transferAssessment($assessmentId, $teacher, false);
transfer_assert($first['transferred'] + $second['skipped'] >= 1, 'confirmed score transfers exactly once');
$transfer = $pdo->prepare(
    'SELECT converted_score, target_record_id FROM cbt_score_transfers
     WHERE assessment_id = ? AND learner_id = ? AND component = \'weekly\' LIMIT 1'
);
$transfer->execute(array($assessmentId, $learner));
$record = $transfer->fetch();
transfer_assert($record && (int) $record['converted_score'] === 10, 'raw CBT mark converts to the configured weekly maximum');
$legacy = $pdo->prepare('SELECT score FROM lhpweekrecord WHERE id = ? LIMIT 1');
$legacy->execute(array($record['target_record_id']));
transfer_assert((int) $legacy->fetchColumn() === 10, 'converted score is present in the legacy weekly record');
$duplicateCount = $pdo->prepare(
    'SELECT COUNT(*) FROM cbt_score_transfers WHERE attempt_id = ? AND component = \'weekly\''
);
$duplicateCount->execute(array($attemptId));
transfer_assert((int) $duplicateCount->fetchColumn() === 1, 'duplicate transfer protection is enforced by data and service layers');
echo "CBT score-transfer test complete.\n";
