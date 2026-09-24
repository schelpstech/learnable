<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../classes/autoload.php';
require __DIR__ . '/../config/database.php';

function academic_check($condition, $message) {
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
    echo 'PASS: ' . $message . "\n";
}
function academic_denied(callable $work, $message) {
    try { $work(); } catch (Throwable $error) { academic_check(true, $message); return; }
    throw new RuntimeException('FAIL: ' . $message);
}

$db = database_pdo();
$termTemplate = $db->query('SELECT * FROM lpterm WHERE status=1 ORDER BY tid DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
$staffTemplate = $db->query('SELECT * FROM lhpstaff WHERE status=1 LIMIT 1')->fetch(PDO::FETCH_ASSOC);
if (!$termTemplate || !$staffTemplate) throw new RuntimeException('Active term and staff templates are required for this isolated test.');
$tables = array('lpterm','lhpclass','lhpsubject','lhpalloc','lhpscheme','lhpuser','lhpclassalloc','lhpstaff','classact','schedule','school_workflow_audit');
foreach ($tables as $table) {
    $template = 'qa_academic_' . $table;
    $db->exec('CREATE TEMPORARY TABLE `' . $template . '` LIKE `' . $table . '`');
    $db->exec('CREATE TEMPORARY TABLE `' . $table . '` LIKE `' . $template . '`');
    $db->exec('DROP TEMPORARY TABLE `' . $template . '`');
}
$insert = function ($table, array $row) use ($db) {
    $columns = array_keys($row);
    $statement = $db->prepare('INSERT INTO `' . $table . '` (`' . implode('`,`', $columns) . '`) VALUES (' . implode(',', array_fill(0, count($columns), '?')) . ')');
    $statement->execute(array_values($row));
};
$termTemplate['term'] = 'QA Active Term'; $termTemplate['status'] = 1; $insert('lpterm', $termTemplate);
$staffTemplate['sname'] = 'qa_academic_teacher'; $staffTemplate['staffname'] = 'QA Academic Teacher'; $staffTemplate['status'] = 1; $insert('lhpstaff', $staffTemplate);
$db->exec("INSERT INTO lhpclass (classid,classname) VALUES (9911,'QA CLASS')");

$service = new AcademicStructureService($db);
$subjectId = $service->saveSubject(array('class_id'=>9911,'name'=>'QA Subject'), 'qa-admin');
academic_check($subjectId > 0, 'admin can create a subject with a stable ID');
$allocationId = $service->saveAllocation(array('class_id'=>9911,'subject_id'=>$subjectId,'teacher_id'=>'qa_academic_teacher'), 'qa-admin');
$allocation = $db->query('SELECT * FROM lhpalloc WHERE aid=' . (int)$allocationId)->fetch(PDO::FETCH_ASSOC);
academic_check($allocation && $allocation['supro'] === '', 'subject allocation no longer requires a supervisor');
academic_check(!array_key_exists('supro', $service->allocations()[0]), 'supervisor is absent from the admin allocation register');

$schemes = new SchemeService($db);
$topic = $schemes->save(array('class_id'=>9911,'subject_id'=>$subjectId,'week'=>1,'topic'=>'A connected topic'), 'qa_academic_teacher');
academic_check((int)$topic['schmid'] > 0, 'allocated teacher can create a current-term scheme topic');
academic_denied(function () use ($schemes, $subjectId) {
    $schemes->save(array('class_id'=>9911,'subject_id'=>$subjectId,'week'=>2,'topic'=>'Blocked topic'), 'unallocated_teacher');
}, 'unallocated teacher cannot create a scheme topic');

$service->saveClass(array('id'=>9911,'name'=>'QA RENAMED'), 'qa-admin');
academic_check($db->query("SELECT classname FROM lhpclass WHERE classid=9911")->fetchColumn() === 'QA RENAMED', 'class name can be edited without changing its ID');
academic_check($db->query('SELECT classname FROM lhpsubject WHERE sbjid=' . (int)$subjectId)->fetchColumn() === 'QA RENAMED', 'class rename synchronizes the subject display copy');
academic_check($db->query('SELECT classname FROM lhpalloc WHERE aid=' . (int)$allocationId)->fetchColumn() === 'QA RENAMED', 'class rename synchronizes the allocation display copy');

$service->saveSubject(array('id'=>$subjectId,'class_id'=>9911,'name'=>'QA Subject Renamed'), 'qa-admin');
academic_check($db->query('SELECT sbjname FROM lhpsubject WHERE sbjid=' . (int)$subjectId)->fetchColumn() === 'QA Subject Renamed', 'subject name can be edited without changing its ID');
academic_check($db->query('SELECT subject FROM lhpalloc WHERE aid=' . (int)$allocationId)->fetchColumn() === 'QA Subject Renamed', 'subject rename synchronizes the allocation display copy');

echo "Academic structure checks passed against connection-local temporary tables.\n";
