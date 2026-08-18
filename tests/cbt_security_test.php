<?php

require __DIR__ . '/../classes/autoload.php';
require __DIR__ . '/../config/database.php';

if (PHP_SAPI !== 'cli') exit(1);
function security_assert($condition, $message) {
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
    echo "PASS: {$message}\n";
}

$pdo = database_pdo();
$cbt = new CbtService($pdo);
$attempts = new CbtAttemptService($pdo);
$assessmentId = (int) $pdo->query("SELECT id FROM cbt_assessments WHERE teacher_id = 'codex_demo_teacher' ORDER BY id LIMIT 1")->fetchColumn();
security_assert($assessmentId > 0, 'security fixture is available');

$denied = false;
try { $cbt->analytics($assessmentId, 'not_the_assigned_teacher', false); }
catch (RuntimeException $exception) { $denied = strpos($exception->getMessage(), 'only assessments assigned') !== false; }
security_assert($denied, 'instructors cannot inspect another teacher assessment');

$denied = false;
try { PortalRoute::fromRequest(array('pageid' => 'cbt_builder'), 'Learner'); }
catch (RuntimeException $exception) { $denied = true; }
security_assert($denied, 'learner route cannot open the assessment builder');

$denied = false;
try { PortalRoute::fromRequest(array('pageid' => 'cbt_review', 'attempt_id' => '1'), 'Instructor'); }
catch (RuntimeException $exception) { $denied = true; }
security_assert($denied, 'instructor route cannot impersonate learner result review');

$attemptId = (int) $pdo->query("SELECT id FROM cbt_attempts WHERE assessment_id = {$assessmentId} ORDER BY id LIMIT 1")->fetchColumn();
if ($attemptId) {
    $denied = false;
    try { $attempts->authenticate($attemptId, str_repeat('0', 64)); }
    catch (RuntimeException $exception) { $denied = strpos($exception->getMessage(), 'could not be verified') !== false; }
    security_assert($denied, 'invalid secure-attempt token is rejected');
}

$clean = CbtSecurity::safeHtml('<p onclick="steal()">Safe <strong>text</strong><script>alert(1)</script></p>', 500);
security_assert(stripos($clean, 'onclick') === false && stripos($clean, '<script') === false && strpos($clean, '<strong>') !== false, 'question HTML removes executable content while keeping academic formatting');

$legacyDefinitions = array(
    'lhpscheme' => array('schmid','term','classname','subject','week','topic','rectime','staffid','status'),
    'lhpweekrecord' => array('id','term','week','classid','subjid','lid','score','rectime'),
    'lhpresultrecord' => array('id','term','classid','subjid','lid','score','examscore','totalscore','rectime'),
);
foreach ($legacyDefinitions as $table => $expectedColumns) {
    $columns = $pdo->query('SHOW COLUMNS FROM `' . $table . '`')->fetchAll(PDO::FETCH_COLUMN);
    security_assert($columns === $expectedColumns, $table . ' legacy columns remain unchanged');
}

$auditCount = (int) $pdo->query('SELECT COUNT(*) FROM cbt_audit_log')->fetchColumn();
security_assert($auditCount > 0, 'important CBT actions have persisted audit entries');
echo "CBT security and compatibility test complete.\n";
