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
    'school_workflow_audit', 'lhpuser', 'lhpstaff', 'lhpnotice',
    'cbt_assessments', 'cbt_assessment_topics', 'cbt_assessment_assignments',
    'cbt_questions', 'cbt_question_options', 'cbt_assessment_questions', 'cbt_audit_log',
    'cbt_attempts', 'cbt_notification_targets', 'cbt_attempt_questions', 'cbt_attempt_answers',
    'cbt_integrity_events', 'cbt_marking_events', 'cbt_score_transfers', 'lhpresultrecord', 'lhpweekrecord',
);
// MySQL cannot reference a temporary table multiple times in assessment queries.
// A uniquely named disposable schema exercises the real queries without live data.
$sourceSchema=$db->query('SELECT DATABASE()')->fetchColumn();
$testSchema='qa_cbt_authoring_'.bin2hex(random_bytes(8));
$db->exec('CREATE DATABASE `'.$testSchema.'`');
try {
foreach ($tables as $table) {
    $db->exec('CREATE TABLE `'.$testSchema.'`.`'.$table.'` LIKE `'.$sourceSchema.'`.`'.$table.'`');
}
$db->exec('USE `'.$testSchema.'`');
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
    'pass_mark' => 1,
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

cbt_authoring_check((int)$service->assessment($assessmentId)['require_approval']===0, 'new drafts never require pre-approval, including old form submissions');
try { $service->publishAssessment($assessmentId,$teacher); throw new LogicException('An empty paper was published.'); }
catch (RuntimeException $e) { cbt_authoring_check(str_contains($e->getMessage(),'at least one question'), 'an incomplete paper cannot be published'); }
$service->addQuestionToAssessment($assessmentId,$questionId,$teacher,false);
$db->prepare('INSERT INTO lhpuser (uname,fname,classid,email,status) VALUES (?,?,?,?,1)')->execute(['qa-cbt-student','QA Student',$classId,'']);
cbt_authoring_check(count($service->learnerAssessments('qa-cbt-student'))===0,'a draft is hidden from students');
try { $service->publishAssessment($assessmentId,'qa-outsider'); throw new LogicException('An outsider published the paper.'); }
catch (RuntimeException $e) { cbt_authoring_check(true,'another teacher cannot publish the paper'); }
$db->prepare("UPDATE cbt_assessments SET status='pending_approval',require_approval=1 WHERE id=?")->execute([$assessmentId]);
cbt_authoring_check($service->submitForApproval($assessmentId,$teacher)==='scheduled','a legacy pending paper publishes directly without an administrator');
$paper=$service->assessment($assessmentId);
cbt_authoring_check(!$paper['approved_by'] && !$paper['approved_at'],'teacher publishing does not stamp results as approved');
cbt_authoring_check(count($service->learnerAssessments('qa-cbt-student'))===1,'the published paper is immediately visible to eligible students');
cbt_authoring_check($service->effectiveStatus($paper)==='scheduled','the future opening time still controls attempts');
try { (new CbtAttemptService($db))->publishResults($assessmentId,$teacher,false); throw new LogicException('Unapproved results were published.'); }
catch (RuntimeException $e) { cbt_authoring_check(str_contains($e->getMessage(),'approved'),'completed-result publication still requires approval'); }
cbt_authoring_check((int)$db->query('SELECT COUNT(*) FROM cbt_notification_targets')->fetchColumn()===1,'the eligible student receives a portal notice without duplicates');
cbt_authoring_check((int)$db->query('SELECT COUNT(*) FROM lhpnotice')->fetchColumn()===1,'one class notice is created when the teacher publishes');
$attemptService=new CbtAttemptService($db);
try { $attemptService->startAttempt($assessmentId,'qa-cbt-student','qa-device'); throw new LogicException('Early start succeeded.'); }
catch (RuntimeException $e) { cbt_authoring_check(str_contains($e->getMessage(),'not opened'),'students cannot start before the opening time'); }
$db->prepare('UPDATE cbt_assessments SET start_at=?,close_at=? WHERE id=?')->execute([date('Y-m-d H:i:s',strtotime('-1 minute')),date('Y-m-d H:i:s',strtotime('+1 hour')),$assessmentId]);
$attempt=$attemptService->startAttempt($assessmentId,'qa-cbt-student','qa-device');
cbt_authoring_check($attempt['attempt_id']>0,'an eligible student starts the teacher-published paper without admin pre-approval');
$_SESSION=['auth_account_type'=>'staff','auth_username'=>$teacher,'active'=>$teacher,'user_type'=>'Instructor'];
try { $service->approveResults($assessmentId,$teacher,'Reviewed'); throw new LogicException('Teacher approved results.'); }
catch (RuntimeException $e) { cbt_authoring_check(str_contains($e->getMessage(),'administration'),'teachers cannot approve their own completed results'); }
$db->prepare('INSERT INTO lhpstaff (sname,staffname,spwd,semail,sfone,role,status) VALUES (?,?,?,?,?,?,1)')->execute(['qa-registry','QA Registry','','','','r']);
$_SESSION=['unamed'=>'qa-registry','auth_account_type'=>'staff','auth_username'=>'qa-registry'];
try { $service->approveResults($assessmentId,'qa-registry','Reviewed'); throw new LogicException('In-progress scripts were approved.'); }
catch (RuntimeException $e) { cbt_authoring_check(str_contains($e->getMessage(),'mark all'),'in-progress scripts prevent result approval'); }
$receipt=$attemptService->submitAttempt($attempt['attempt_id'],$attempt['token'],false);
cbt_authoring_check($receipt['status']==='marked','an objective script reaches the completed marking state');
$service->approveResults($assessmentId,'qa-registry','Completed scripts reviewed.');
cbt_authoring_check($service->assessment($assessmentId)['approved_by']==='qa-registry','academic approval records the approving registry actor');
cbt_authoring_check($attemptService->publishResults($assessmentId,$teacher,false)===1,'the teacher can publish completed results after academic approval');

// Regression cases use the same disposable schema, including legacy MyISAM score tables.
function cbt_reject($callback, $contains, $message) {
    try { $callback(); } catch (RuntimeException $e) {
        cbt_authoring_check(stripos($e->getMessage(), $contains) !== false, $message . ': ' . $e->getMessage()); return;
    }
    throw new LogicException('FAIL: ' . $message);
}
$paperId = $service->duplicateAssessment($assessmentId, $teacher, false);
$db->prepare('UPDATE cbt_assessments SET start_at=?, close_at=? WHERE id=?')->execute([date('Y-m-d H:i:s', strtotime('-40 days')), date('Y-m-d H:i:s', strtotime('-39 days')), $paperId]);
$futureCopy = $service->duplicateAssessment($paperId, $teacher, false);
cbt_authoring_check(strtotime($service->assessment($futureCopy)['start_at']) > time(), 'copying an old paper gives the draft a usable future schedule');
$db->prepare('UPDATE cbt_assessments SET pass_mark=10 WHERE id=?')->execute([$futureCopy]);
cbt_reject(fn()=> $service->publishAssessment($futureCopy, $teacher), 'pass mark', 'publishing an impossible pass mark is blocked');
$service->updateAssessment($futureCopy, ['title'=>'Revised future paper', 'pass_mark'=>1, 'duration_minutes'=>45, 'shuffle_options'=>1], $teacher, false);
cbt_authoring_check($service->assessment($futureCopy)['title'] === 'Revised future paper', 'teachers can edit draft title, time and settings');
$paperId = $futureCopy;
$base = ['class_id'=>$classId,'subject_id'=>$subjectId,'scheme_id'=>$schemeId,'difficulty'=>'easy','negative_marks'=>0,'visibility'=>'private'];
$essayId = $service->createQuestion(array_merge($base, ['question_type'=>'essay','prompt_html'=>'Explain your answer.','marks'=>4,'model_answer'=>'Reasoning','marking_guide'=>'Award up to four marks.']), $teacher, false);
$matchId = $service->createQuestion(array_merge($base, ['question_type'=>'matching','prompt_html'=>'Match country and currency.','marks'=>3,'option_text'=>['Nigeria','USA','Europe'],'match_key'=>['NGN','USD','EUR']]), $teacher, false);
$service->addQuestionToAssessment($paperId, $essayId, $teacher, false);
$service->addQuestionToAssessment($paperId, $matchId, $teacher, false);
$service->updateAssessment($paperId, ['start_at'=>date('Y-m-d H:i:s',strtotime('-1 minute')), 'close_at'=>date('Y-m-d H:i:s',strtotime('+1 hour')), 'result_treatment'=>'ca'], $teacher, false);
$service->publishAssessment($paperId,$teacher);
$run = $attemptService->startAttempt($paperId,'qa-cbt-student','test-device');
$state = $attemptService->examState($run['attempt_id'],$run['token']);
cbt_reject(fn()=> $service->updateAssessment($paperId,['title'=>'Changed'], $teacher,false), 'before students start', 'settings cannot change after a student starts');
$map = ['A'=>'NGN','B'=>'USD','C'=>'EUR']; $objectiveAnswerId = null;
foreach ($state['questions'] as $q) {
    if ($q['question_type']==='true_false') {
        $objectiveQuestionId=$q['id'];
        $attemptService->saveAnswer($run['attempt_id'],$run['token'],$q['id'],'T',false,2);
        $attemptService->saveAnswer($run['attempt_id'],$run['token'],$q['id'],'F',false,1);
    } elseif ($q['question_type']==='matching') {
        $answer = array_map(fn($item)=>$map[$item['option_key']],$q['options']['items']);
        $attemptService->saveAnswer($run['attempt_id'],$run['token'],$q['id'],$answer,false,1);
    }
}
cbt_authoring_check($db->query('SELECT answer_json FROM cbt_attempt_answers WHERE attempt_question_id='.$objectiveQuestionId)->fetchColumn()==='"T"','an older answer save never replaces a newer version');
$service->setAssessmentStatus($paperId,'paused',$teacher,false,'Testing pause');
cbt_reject(fn()=> $attemptService->saveAnswer($run['attempt_id'],$run['token'],$objectiveQuestionId,'F',false,3), 'paused', 'paused papers reject answer writes');
cbt_reject(fn()=> $attemptService->submitAttempt($run['attempt_id'],$run['token'],false), 'paused', 'paused papers reject early submission');
$service->setAssessmentStatus($paperId,'scheduled',$teacher,false,'Resume');
$oldExpiry=$state['attempt']['expires_at_iso'];
$attemptService->addExtraTime($run['attempt_id'],5,'qa-registry',true,'Connection interruption');
$state=$attemptService->examState($run['attempt_id'],$run['token']);
cbt_authoring_check(strtotime($state['attempt']['expires_at_iso'])===strtotime($oldExpiry)+300,'live state exposes the updated extra-time deadline');
$receipt=$attemptService->submitAttempt($run['attempt_id'],$run['token'],false);
cbt_authoring_check($receipt['status']==='marked','an unanswered essay is zero and does not hold marking open');
$row=$db->query('SELECT * FROM cbt_attempts WHERE id='.$run['attempt_id'])->fetch(PDO::FETCH_ASSOC);
cbt_authoring_check((float)$row['total_score']===5.0,'shuffled matching answers and the latest objective answer receive full marks');
// Old snapshots are also interpreted in display order, without changing their evidence.
$scoreMethod=new ReflectionMethod(CbtAttemptService::class,'scoreObjective');
$legacy=['question_type'=>'matching','marks_available'=>3,'negative_marks'=>0,'allow_partial'=>0,'correct_answer_snapshot'=>'["NGN","USD","EUR"]','options_snapshot'=>json_encode(['items'=>[['option_key'=>'C','sort_order'=>3],['option_key'=>'A','sort_order'=>1],['option_key'=>'B','sort_order'=>2]]])];
cbt_authoring_check($scoreMethod->invoke($attemptService,$legacy,['EUR','NGN','USD'])===3.0,'legacy shuffled matching snapshots are scored correctly');
$db->prepare('INSERT INTO lhpuser (uname,fname,classid,email,status) VALUES (?,?,?,?,1)')->execute(['qa-abandoned','QA Abandoned',$classId,'']);
$abandoned=$attemptService->startAttempt($paperId,'qa-abandoned','test-device');
$db->prepare('UPDATE cbt_attempts SET expires_at=? WHERE id=?')->execute([date('Y-m-d H:i:s', strtotime('-1 minute')), $abandoned['attempt_id']]);
$attemptService->attemptsForAssessment($paperId,$teacher,false);
cbt_authoring_check($db->query('SELECT status FROM cbt_attempts WHERE id='.$abandoned['attempt_id'])->fetchColumn()==='marked','staff script listings finalize abandoned expired attempts');
$service->approveResults($paperId,'qa-registry','All scripts checked');
$attemptService->publishResults($paperId,$teacher,false);
$results=new CbtResultService($db);
$db->exec('UPDATE lhpresultconfig SET status=1,midterm=1');
cbt_reject(fn()=> $results->transferAssessment($paperId,$teacher,false),'locked','published term-result locks block CBT transfers');
$db->exec('UPDATE cbt_assessments SET result_treatment="weekly" WHERE id='.$paperId);
cbt_reject(fn()=> $results->transferAssessment($paperId,$teacher,false),'locked','published weekly-result locks block CBT transfers');
$db->exec('UPDATE cbt_assessments SET result_treatment="ca" WHERE id='.$paperId);
$db->exec('UPDATE lhpresultconfig SET status=0,midterm=0');
$outcome=$results->transferAssessment($paperId,$teacher,false);
cbt_authoring_check($outcome['transferred']===2,'approved published CBT results transfer to unlocked school records');
cbt_authoring_check($results->transferAssessment($paperId,$teacher,false)['skipped']===2,'repeated transfers safely skip unchanged scores');
$objectiveAnswerId=(int)$db->query('SELECT id FROM cbt_attempt_answers WHERE attempt_question_id='.$objectiveQuestionId)->fetchColumn();
$attemptService->markAnswer($objectiveAnswerId,0,'Correction','Incorrect key reviewed',$teacher,false);
cbt_authoring_check($service->assessment($paperId)['status']==='awaiting_approval' && !$service->assessment($paperId)['approved_at'],'mark corrections invalidate previous approval');
$corrected=$db->query('SELECT status,published_at FROM cbt_attempts WHERE id='.$run['attempt_id'])->fetch(PDO::FETCH_ASSOC);
cbt_authoring_check($corrected['status']==='marked' && !$corrected['published_at'],'correcting another answer does not revive a missing essay as pending');
cbt_reject(fn()=> $attemptService->learnerReview($run['attempt_id'],'qa-cbt-student'),'not been published','corrected results remain hidden until republished');
cbt_reject(fn()=> $attemptService->publishResults($paperId,$teacher,false),'approved','teachers cannot republish corrected marks without fresh approval');
$service->approveResults($paperId,'qa-registry','Correction checked');
$attemptService->publishResults($paperId,$teacher,false);
$preview=$results->previewAssessmentTransfer($paperId,$teacher,false);
$changed=array_values(array_filter($preview['attempts'],fn($row)=>$row['needs_amendment']));
cbt_authoring_check(count($changed)===1,'transfer preview identifies corrected scores awaiting amendment');
$transferId=$changed[0]['transfer_id'];
cbt_reject(fn()=> $results->transferAssessment($paperId,$teacher,false),'amend','changed transferred scores are not silently skipped');
$_SESSION=['auth_account_type'=>'staff','auth_username'=>$teacher,'active'=>$teacher,'user_type'=>'Instructor'];
cbt_reject(fn()=> $results->amendTransfer($transferId,$teacher,'Correction'), 'administration', 'teachers cannot amend official transferred scores');
$_SESSION=['unamed'=>'qa-registry','auth_account_type'=>'staff','auth_username'=>'qa-registry'];
$db->exec('UPDATE lhpresultconfig SET status=1');
cbt_reject(fn()=> $results->amendTransfer($transferId,'qa-registry','Correction'), 'locked', 'amendments respect official result locks');
$db->exec('UPDATE lhpresultconfig SET status=0');
$newScore=$results->amendTransfer($transferId,'qa-registry','Reapproved corrected marks');
cbt_authoring_check($newScore===round(3/9*(int)$resultConfigTemplate['ca_score']),'authorized amendment updates the official score after fresh approval');
$legacyId=$db->query('SELECT target_record_id FROM cbt_score_transfers WHERE id='.$transferId)->fetchColumn();
$db->exec('UPDATE lhpresultrecord SET score=score+1 WHERE id='.$legacyId);
cbt_reject(fn()=> $results->amendTransfer($transferId,'qa-registry','Correction'), 'changed since', 'amendments cannot overwrite intervening manual scorebook changes');
// Reopened attempts may resume after the original closing time.
$db->exec('UPDATE cbt_assessments SET close_at=DATE_SUB(NOW(), INTERVAL 1 HOUR) WHERE id='.$paperId);
$attemptService->reopenAttempt($run['attempt_id'],10,'qa-registry',true,'Approved recovery');
// A reopened script is available for resume while revised results await approval.
$resumed=$attemptService->startAttempt($paperId,'qa-cbt-student','test-device');
cbt_authoring_check($resumed['resumed'] && $resumed['attempt_id']===$run['attempt_id'],'the learner can resume a reopened attempt after the original closing time');
$visible=$service->learnerAssessments('qa-cbt-student');
$recoveryRows=array_values(array_filter($visible,fn($row)=>(int)$row['id']===$paperId));
cbt_authoring_check(count($recoveryRows)===1 && strtotime($recoveryRows[0]['attempt_expires_at'])>time(),'the learner dashboard receives the reopened deadline for its resume button');
// A flagged but blank essay remains unanswered and must not require manual marking.
$state=$attemptService->examState($resumed['attempt_id'],$resumed['token']);
foreach ($state['questions'] as $q) if ($q['question_type']==='essay') $attemptService->saveAnswer($resumed['attempt_id'],$resumed['token'],$q['id'],'',true,1);
$receipt=$attemptService->submitAttempt($resumed['attempt_id'],$resumed['token'],false);
cbt_authoring_check($receipt['status']==='marked','a flagged blank essay is recorded as zero instead of blocking result completion');


echo "CBT authoring checks passed against an isolated disposable schema.\n";

} finally {
    $db->exec('USE `'.$sourceSchema.'`');
    if (!preg_match('/^qa_cbt_authoring_[a-f0-9]{16}$/D',$testSchema)) throw new RuntimeException('Invalid test schema.');
    $db->exec('DROP DATABASE `'.$testSchema.'`');
    echo "Disposable CBT test schema removed.\n";
}
