<?php

require __DIR__ . '/../classes/autoload.php';
require __DIR__ . '/../config/database.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$pdo = database_pdo();
$service = new CbtService($pdo);
$context = $service->activeContext();
$teacher = 'codex_demo_teacher';
$learner = $pdo->query("SELECT uname, classid FROM lhpuser WHERE uname = 'codex_demo_std' AND status = 1 LIMIT 1")->fetch();
if (!$learner) {
    throw new RuntimeException('Run tests/demo_accounts.php before creating CBT demo data.');
}
$allocationStatement = $pdo->prepare(
    'SELECT a.classid, a.sbjid, s.sbjname, MIN(a.aid) AS first_allocation_id
     FROM lhpalloc a INNER JOIN lhpsubject s ON s.sbjid = a.sbjid
     WHERE a.staffid = ? AND a.term = ? AND a.classid = ?
     GROUP BY a.classid, a.sbjid, s.sbjname ORDER BY first_allocation_id LIMIT 1'
);
$allocationStatement->execute(array($teacher, $context['term'], $learner['classid']));
$allocation = $allocationStatement->fetch();
if (!$allocation) {
    throw new RuntimeException('The demo teacher needs an active allocation for the demo learner class.');
}
$topicStatement = $pdo->prepare(
    'SELECT schmid FROM lhpscheme
     WHERE term = ? AND classname = ? AND subject = ? AND status = 1 ORDER BY schmid LIMIT 1'
);
$topicStatement->execute(array($context['term'], $allocation['classid'], $allocation['sbjid']));
$schemeId = $topicStatement->fetchColumn();
if (!$schemeId) {
    $insertTopic = $pdo->prepare(
        'INSERT INTO lhpscheme (term, classname, subject, week, topic, staffid, status)
         VALUES (?, ?, ?, \'Week 1\', \'Codex Demo · Foundation Review\', ?, 1)'
    );
    $insertTopic->execute(array($context['term'], $allocation['classid'], $allocation['sbjid'], $teacher));
    $schemeId = (int) $pdo->lastInsertId();
}

$existing = $pdo->prepare('SELECT id FROM cbt_assessments WHERE teacher_id = ? AND title = ? LIMIT 1');
$existing->execute(array($teacher, 'Codex Demo · Mixed Question Practice'));
$assessmentId = $existing->fetchColumn();
if (!$assessmentId) {
    $assessmentId = $service->createAssessment(array(
        'teacher_id' => $teacher,
        'class_id' => $allocation['classid'],
        'subject_id' => $allocation['sbjid'],
        'scheme_id' => $schemeId,
        'title' => 'Codex Demo · Mixed Question Practice',
        'instructions' => 'Read each question carefully. Your answers save automatically. The final theory response will be marked by your teacher.',
        'assessment_type' => 'practice_test',
        'result_treatment' => 'practice',
        'total_marks' => 10,
        'pass_mark' => 5,
        'start_at' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
        'close_at' => date('Y-m-d H:i:s', strtotime('+2 days')),
        'duration_minutes' => 45,
        'max_attempts' => 2,
        'navigation_mode' => 'free',
        'allow_backtrack' => 1,
        'randomize_questions' => 0,
        'shuffle_options' => 1,
        'auto_submit' => 1,
        'show_score' => 1,
        'allow_review' => 1,
        'show_correct_answers' => 1,
        'require_approval' => 1,
        'monitor_tab_switch' => 1,
    ), 'codex_demo_admin', true);

    // Keep the demonstration isolated to the named demo learner instead of
    // notifying or assigning a real class in the local school dataset.
    $pdo->prepare('DELETE FROM cbt_assessment_assignments WHERE assessment_id = ?')->execute(array($assessmentId));
    $pdo->prepare(
        'INSERT INTO cbt_assessment_assignments
         (assessment_id, assignment_type, class_id, learner_id, status)
         VALUES (?, \'student\', ?, ?, \'eligible\')'
    )->execute(array($assessmentId, $allocation['classid'], $learner['uname']));

    $questions = array(
        array(
            'question_type' => 'single_choice', 'difficulty' => 'easy', 'marks' => 2,
            'prompt_html' => '<p>Which answer is equal to <strong>2 + 2</strong>?</p>',
            'option_text' => array('3', '4', '5', '6'), 'correct_option' => '1',
            'learning_objective' => 'Recall a basic addition fact.',
            'explanation' => 'Two plus two gives four.'
        ),
        array(
            'question_type' => 'true_false', 'difficulty' => 'easy', 'marks' => 2,
            'prompt_html' => '<p>A square has four equal sides.</p>',
            'true_false_answer' => 'true',
            'learning_objective' => 'Recognise the properties of a square.'
        ),
        array(
            'question_type' => 'short_answer', 'difficulty' => 'medium', 'marks' => 2,
            'prompt_html' => '<p>Write the next number: 2, 4, 6, 8, ___.</p>',
            'accepted_answer' => "10\nten", 'learning_objective' => 'Continue a simple number sequence.'
        ),
        array(
            'question_type' => 'essay', 'difficulty' => 'medium', 'marks' => 4,
            'prompt_html' => '<p>In two or three sentences, explain one good study habit and why it helps a learner.</p>',
            'model_answer' => 'A learner may review class notes every day. Regular review makes ideas easier to remember and reveals areas that need more practice.',
            'marking_guide' => 'Award 2 marks for a relevant habit and 2 marks for a clear explanation.',
            'learning_objective' => 'Explain a useful independent-learning habit.'
        ),
    );
    foreach ($questions as $question) {
        $question = array_merge(array(
            'class_id' => $allocation['classid'], 'subject_id' => $allocation['sbjid'],
            'scheme_id' => $schemeId, 'negative_marks' => 0, 'visibility' => 'private',
            'status' => 'active', 'model_answer' => '', 'marking_guide' => '', 'explanation' => '',
            'media_type' => '', 'media_url' => '', 'allow_partial' => 0,
        ), $question);
        $questionId = $service->createQuestion($question, $teacher, true);
        $service->addQuestionToAssessment($assessmentId, $questionId, $teacher, false);
    }
    $service->setAssessmentStatus($assessmentId, 'approved', 'codex_demo_admin', true, 'Demo assessment reviewed for automated testing.');
    $service->setAssessmentStatus($assessmentId, 'published', 'codex_demo_admin', true, 'Demo assessment published for role-based testing.');
}

echo "CBT demo data ready:\n";
echo "- assessment {$assessmentId}\n";
echo "- teacher {$teacher}\n";
echo "- learner {$learner['uname']}\n";
echo "- class {$allocation['classid']}, subject {$allocation['sbjid']} ({$allocation['sbjname']})\n";
