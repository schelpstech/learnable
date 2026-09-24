<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

require __DIR__ . '/../classes/autoload.php';
require __DIR__ . '/../config/database.php';

function cbt_authoring_check($condition, $message)
{
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
    echo 'PASS: ' . $message . "\n";
}

$db = database_pdo();
$resultConfigTemplate = $db->query('SELECT * FROM lhpresultconfig ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
if (!$resultConfigTemplate) throw new RuntimeException('A result configuration template is required for the isolated CBT authoring test.');

$tables = array(
    'lpterm', 'lhpsession', 'lhpclass', 'lhpsubject', 'lhpalloc', 'lhpscheme', 'lhpnote', 'lhpresultconfig',
    'school_workflow_audit',
    'cbt_assessments', 'cbt_assessment_topics', 'cbt_assessment_assignments',
    'cbt_questions', 'cbt_question_options', 'cbt_assessment_questions', 'cbt_audit_log',
);
foreach ($tables as $table) {
    $template = 'qa_authoring_' . $table;
    $db->exec('CREATE TEMPORARY TABLE `' . $template . '` LIKE `' . $table . '`');
    $db->exec('CREATE TEMPORARY TABLE `' . $table . '` LIKE `' . $template . '`');
    $db->exec('DROP TEMPORARY TABLE `' . $template . '`');
}
$db->exec('ALTER TABLE cbt_assessments AUTO_INCREMENT=990000001');

$term = '1st Term 2098/2099';
$teacher = 'qa-cbt-teacher';
$classId = 9901;
$subjectId = 9902;
$db->exec("INSERT INTO lpterm (tid,term,status) VALUES (9901,'{$term}',1)");
$db->exec("INSERT INTO lhpsession (sessionid,session,status) VALUES (9901,'2098/2099',1)");
$db->exec("INSERT INTO lhpclass (classid,classname) VALUES ({$classId},'QA CBT CLASS')");
$db->exec("INSERT INTO lhpsubject (sbjid,sbjname,classid,classname) VALUES ({$subjectId},'QA CBT SUBJECT',{$classId},'QA CBT CLASS')");
$allocation = $db->prepare('INSERT INTO lhpalloc (term,classname,subject,staffid,supro,classid,sbjid) VALUES (?,?,?,?,?,?,?)');
$allocation->execute(array($term, 'QA CBT CLASS', 'QA CBT SUBJECT', $teacher, '', $classId, $subjectId));
$schemes = new SchemeService($db);
$createdTopic = $schemes->save(array(
    'class_id' => $classId,
    'subject_id' => $subjectId,
    'week' => 1,
    'topic' => 'QA Week One Topic',
), $teacher);
$schemeId = (int)$createdTopic['schmid'];
cbt_authoring_check($schemeId > 0, 'an allocated teacher can add a scheme topic in the active term');
cbt_authoring_check(count($schemes->topics($teacher, $classId, $subjectId)) === 1, 'the saved topic is returned immediately without a page refresh');

$notes = new NoteService($db);
$noteId = $notes->save(array(
    'topicid' => $schemeId,
    'type' => 'text',
    'content' => '<h2>Week one lesson</h2><p>Teacher-authored e-note for the scheme topic.</p>',
), $teacher);
cbt_authoring_check($noteId > 0, 'the allocated teacher can attach an e-note to the scheme topic');

$resultConfigTemplate['term'] = $term;
if (array_key_exists('resumption', $resultConfigTemplate)) $resultConfigTemplate['resumption'] = date('Y-m-d', strtotime('+30 days'));
if (array_key_exists('status', $resultConfigTemplate)) $resultConfigTemplate['status'] = 0;
$columns = array_keys($resultConfigTemplate);
$insertConfig = $db->prepare(
    'INSERT INTO lhpresultconfig (`' . implode('`,`', $columns) . '`) VALUES (' . implode(',', array_fill(0, count($columns), '?')) . ')'
);
$insertConfig->execute(array_values($resultConfigTemplate));

$service = new CbtService($db);
$assessmentId = $service->createAssessment(array(
    'class_id' => $classId,
    'subject_id' => $subjectId,
    'scheme_id' => $schemeId,
    'title' => 'Week One Preparation Check',
    'instructions' => 'Prepared before resumption for an allocated class.',
    'assessment_type' => 'weekly_test',
    'result_treatment' => 'practice',
    'total_marks' => 10,
    'pass_mark' => 5,
    'start_at' => date('Y-m-d H:i:s', strtotime('+31 days')),
    'close_at' => date('Y-m-d H:i:s', strtotime('+32 days')),
    'duration_minutes' => 20,
    'max_attempts' => 1,
    'navigation_mode' => 'free',
    'allow_backtrack' => 1,
    'auto_submit' => 1,
    'require_approval' => 1,
), $teacher, false);
cbt_authoring_check($assessmentId > 0, 'an allocated teacher can prepare a Week 1 assessment before resumption');

$questionId = $service->createQuestion(array(
    'class_id' => $classId,
    'subject_id' => $subjectId,
    'scheme_id' => $schemeId,
    'question_type' => 'true_false',
    'difficulty' => 'easy',
    'prompt_html' => '<p>This Week 1 preparation question is valid.</p>',
    'marks' => 2,
    'negative_marks' => 0,
    'true_false_answer' => 'true',
    'learning_objective' => 'Prepare an assessment before the delivery week.',
    'visibility' => 'private',
), $teacher, false);
cbt_authoring_check($questionId > 0, 'the teacher can write a question for the same future teaching week');

cbt_authoring_check(
    (int)$db->query('SELECT COUNT(*) FROM cbt_questions WHERE id=' . $questionId . " AND status='draft'")->fetchColumn() === 1,
    'the question is retained as the teacher\'s reusable draft bank item'
);
cbt_authoring_check(
    (int)$db->query("SELECT COUNT(*) FROM cbt_audit_log WHERE actor_id='qa-cbt-teacher'")->fetchColumn() >= 2,
    'authoring actions remain recorded in the CBT audit log'
);

echo "CBT authoring checks passed against connection-local temporary tables.\n";
