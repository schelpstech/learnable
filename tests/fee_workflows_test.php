<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../classes/autoload.php';require __DIR__.'/../config/database.php';
$db=database_pdo();
function fee_check($ok,$message){if(!$ok)throw new RuntimeException('FAIL: '.$message);echo 'PASS: '.$message."\n";}
function fee_denied(callable $work,$message){try{$work();}catch(InvalidArgumentException $e){fee_check(true,$message);return;}catch(RuntimeException $e){fee_check(true,$message);return;}throw new RuntimeException('FAIL: '.$message);}
$tables=array('lpterm','lhpsession','lhpclass','lhpuser','lhpfeelist','lhpassignedfee','school_workflow_audit');$columns=array();
foreach($tables as $table){$columns[$table]=array_column($db->query('SHOW COLUMNS FROM `'.$table.'`')->fetchAll(),'Field');$db->exec('CREATE TEMPORARY TABLE qa_fee_schema LIKE `'.$table.'`');$db->exec('CREATE TEMPORARY TABLE `'.$table.'` LIKE qa_fee_schema');$db->exec('DROP TEMPORARY TABLE qa_fee_schema');}
$db->exec("INSERT INTO lpterm(tid,term,status) VALUES(901,'1st Term 2098/2099',1),(902,'2nd Term 2098/2099',0),(801,'1st Term 2097/2098',0)");
$db->exec("INSERT INTO lhpsession(sessionid,session,status) VALUES(900,'2098/2099',0),(901,'2098/2099',1),(801,'2097/2098',0)");
$db->exec("INSERT INTO lhpclass(classid,classname) VALUES(901,'QA ONE'),(902,'QA TWO')");
$users=array(array('qa-fee-1','Learner One',901),array('qa-fee-2','Learner Two',901),array('qa-fee-3','Learner Three',902));
$insertUser=$db->prepare("INSERT INTO lhpuser(uname,gender,dob,upwd,email,classid,fname,status) VALUES(?,'Male','2010-01-01','','qa@example.test',?,?,1)");foreach($users as $user)$insertUser->execute(array($user[0],$user[2],$user[1]));
$service=new FeeService($db);$actor='qa-admin';$term='1st Term 2098/2099';$term2='2nd Term 2098/2099';
$id=$service->save(array('association_mode'=>'class','class_id'=>901,'feename'=>'Tuition fee','amount'=>1000),$actor);
$definition=$service->definition($id);
fee_check((int)$definition['session']===901 && $definition['term']===null,'fee creation stores the active session without making the catalogue term-specific');
fee_check($definition['feename']==='TUITION FEE' && (string)$definition['classid']==='901','fee creation keeps a clear name and class association');
fee_check(count($service->definitions(901))===1 && count($service->definitionsForTerm($term2))===1,'one session catalogue is reused by another term and the active duplicate session row is preferred');
fee_denied(function()use($service,$actor){$service->save(array('association_mode'=>'class','class_id'=>901,'feename'=>'tuition fee','amount'=>1200),$actor);},'duplicate fee definition is rejected across the same session');
fee_denied(function()use($service,$id,$definition,$actor){$service->archive($id,$definition['version'],$actor,false);},'fee archiving requires explicit confirmation');

$assigned=$service->assign(array('term'=>$term,'audience'=>'learner','class_id'=>901,'learner_id'=>'qa-fee-1','fee_id'=>$id,'due'=>'2098-10-01'),$actor);
fee_check($assigned['created']===1 && $assigned['skipped']===0,'one learner can receive a fee');
$assigned=$service->assign(array('term'=>$term,'audience'=>'learner','class_id'=>901,'learner_id'=>'qa-fee-1','fee_id'=>$id,'due'=>'2098-10-01'),$actor);
fee_check($assigned['created']===0 && $assigned['skipped']===1,'repeated assignment is skipped without a duplicate charge');
$assigned=$service->assign(array('term'=>$term2,'audience'=>'learner','class_id'=>901,'learner_id'=>'qa-fee-1','fee_id'=>$id,'due'=>'2099-01-15'),$actor);
fee_check($assigned['created']===1 && $assigned['skipped']===0,'the reusable fee can be assigned again in another term');
fee_denied(function()use($service,$id,$actor){$service->assign(array('term'=>'1st Term 2097/2098','audience'=>'learner','class_id'=>901,'learner_id'=>'qa-fee-1','fee_id'=>$id,'due'=>'2097-10-01'),$actor);},'a fee cannot be assigned outside its academic session');
$assigned=$service->assign(array('term'=>$term,'audience'=>'class','class_id'=>901,'fee_id'=>$id,'due'=>'2098-10-01'),$actor);
fee_check($assigned['created']===1 && $assigned['skipped']===1,'class assignment creates missing charges and reports skipped duplicates');
fee_denied(function()use($service,$term,$id,$actor){$service->assign(array('term'=>$term,'audience'=>'school','fee_id'=>$id,'due'=>'2098-10-01'),$actor);},'class-specific fee cannot be assigned school-wide');
fee_denied(function()use($service,$term,$id,$actor){$service->assign(array('term'=>$term,'audience'=>'class','class_id'=>902,'fee_id'=>$id,'due'=>'2098-10-01'),$actor);},'class-specific fee cannot be assigned to another class');

$definition=$service->definition($id);$service->save(array('id'=>$id,'version'=>$definition['version'],'feename'=>'Tuition fee','amount'=>1100),$actor);
fee_check((int)$db->query('SELECT amount FROM lhpassignedfee ORDER BY assid LIMIT 1')->fetchColumn()===1000,'fee edit leaves existing learner charges unchanged by default');
$definition=$service->definition($id);$service->save(array('id'=>$id,'version'=>$definition['version'],'feename'=>'Tuition fee','amount'=>1200,'propagate'=>'yes'),$actor);
fee_check((int)$db->query('SELECT MIN(amount) FROM lhpassignedfee WHERE status=1')->fetchColumn()===1200,'explicit propagation updates active assigned charges');

$assignmentId=(int)$db->query("SELECT assid FROM lhpassignedfee WHERE stdid='qa-fee-1' AND term='1st Term 2098/2099' AND status=1 LIMIT 1")->fetchColumn();
fee_denied(function()use($service,$assignmentId,$actor){$service->changeAssignmentStatus($assignmentId,0,$actor,false);},'deactivation requires confirmation');
$service->changeAssignmentStatus($assignmentId,0,$actor,true);
fee_check((int)$db->query('SELECT status FROM lhpassignedfee WHERE assid='.$assignmentId)->fetchColumn()===0,'assigned fee is deactivated without deleting its record');
$service->assign(array('term'=>$term,'audience'=>'learner','class_id'=>901,'learner_id'=>'qa-fee-1','fee_id'=>$id,'due'=>'2098-10-01'),$actor);
fee_denied(function()use($service,$assignmentId,$actor){$service->changeAssignmentStatus($assignmentId,1,$actor);},'old assigned fee cannot be reactivated beside an active duplicate');

$schoolFee=$service->save(array('association_mode'=>'school','fee_group'=>'School-wide','feename'=>'Activity fee','amount'=>500),$actor);
$assigned=$service->assign(array('term'=>$term,'audience'=>'school','fee_id'=>$schoolFee,'due'=>'2098-10-01'),$actor);
fee_check($assigned['created']===3,'school-wide assignment reaches every active learner once');
$definition=$service->definition($id);$affected=$service->archive($id,$definition['version'],$actor,true);
fee_check($affected===3 && (int)$service->definition($id)['status']===0,'archiving deactivates active session charges while retaining records');
$service->assign(array('term'=>$term,'audience'=>'learner','class_id'=>902,'learner_id'=>'qa-fee-3','fee_id'=>'PreviousBalance','custom_amount'=>275,'due'=>'2098-10-01'),$actor);
fee_check((int)$db->query("SELECT amount FROM lhpassignedfee WHERE feeid='PreviousBalance'")->fetchColumn()===275,'previous balance uses the verified custom amount');
foreach($tables as $table)fee_check($columns[$table]===array_column($db->query('SHOW COLUMNS FROM `'.$table.'`')->fetchAll(),'Field'),$table.' schema remains unchanged');
fee_check((int)$db->query("SELECT COUNT(*) FROM school_workflow_audit WHERE module IN ('fee_definition','fee_assignment')")->fetchColumn()>0,'fee changes leave an audit trail');
echo "All fee workflow checks passed against temporary copies of the deployed schema.\n";
