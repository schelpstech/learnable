<?php
if(PHP_SAPI!=='cli')exit;
require __DIR__.'/../classes/autoload.php';require __DIR__.'/../config/database.php';
$db=database_pdo();
function check($condition,$message){if(!$condition)throw new RuntimeException('FAIL: '.$message);echo 'PASS: '.$message."\n";}
function denied(callable $work,$message){try{$work();}catch(RuntimeException $e){check(true,$message);return;}catch(InvalidArgumentException $e){check(true,$message);return;}throw new RuntimeException('FAIL: '.$message);}
// Shadow the deployed schema with connection-local temporary copies. No school records are written.
$tables=array('lpterm','lhpresultconfig','lhpclass','lhpsubject','lhpalloc','lhpstaff','lhpuser','lhpscheme','lhpnote','lhpassignedfee','lhpresultrecord','lhpweekrecord','school_workflow_audit','school_expenses','school_inventory_items','school_inventory_movements');
$source=array();foreach($tables as $t)$source[$t]=$db->query('SELECT * FROM `'.$t.'`')->fetchAll();
foreach($tables as $t){
    $db->exec('CREATE TEMPORARY TABLE qa_schema_copy LIKE `'.$t.'`');
    $db->exec('CREATE TEMPORARY TABLE `'.$t.'` LIKE qa_schema_copy');
    $db->exec('DROP TEMPORARY TABLE qa_schema_copy');
}
$copy=function($table,$row)use($db){$columns=array_keys($row);$q=$db->prepare('INSERT INTO `'.$table.'` (`'.implode('`,`',$columns).'`) VALUES ('.implode(',',array_fill(0,count($columns),'?')).')');$q->execute(array_values($row));};
$termRow=array_values(array_filter($source['lpterm'],function($r){return (int)$r['status']===1;}))[0];$copy('lpterm',$termRow);$term=$termRow['term'];
$classRow=array_values(array_filter($source['lhpclass'],function($r){return $r['classname']==='Codex Demo';}))[0];$copy('lhpclass',$classRow);$class=$classRow['classid'];
$subjectRow=array_values(array_filter($source['lhpsubject'],function($r)use($class){return $r['classid']==$class;}))[0];$copy('lhpsubject',$subjectRow);$subject=$subjectRow['sbjid'];
foreach(array('lhpalloc','lhpstaff','lhpuser','lhpscheme','lhpnote','lhpassignedfee')as$table)foreach($source[$table]as$row){
    if(($row['staffid'] ?? $row['sname'] ?? '')==='codex_demo_teacher' || ($row['uname'] ?? $row['stdid'] ?? '')==='codex_demo_std')$copy($table,$row);
}
$config=end($source['lhpresultconfig']);$config['term']=$term;$config['status']=0;$config['midterm']=0;$config['ca_score']=30;$config['exam_score']=70;$copy('lhpresultconfig',$config);
$teacher='codex_demo_teacher';$student='codex_demo_std';$admin='codex_demo_admin';
$fee=$db->query("SELECT * FROM lhpassignedfee WHERE stdid='codex_demo_std' LIMIT 1")->fetch();
$discount=new DiscountService($db);$discount->change($fee['assid'],200,$fee['discount'],$admin);
check((int)$db->query('SELECT discount FROM lhpassignedfee LIMIT 1')->fetchColumn()===200,'discount edit updates only concession');
denied(function()use($discount,$fee,$admin){$discount->change($fee['assid'],100,100,$admin);},'stale discount rejected');
denied(function()use($discount,$fee,$admin){$discount->change($fee['assid'],2000,200,$admin);},'discount cannot exceed fee');
denied(function()use($discount,$fee,$admin){$discount->change($fee['assid'],0,200,$admin,true,false);},'discount removal requires confirmation');
denied(function()use($discount,$fee,$admin){$discount->change($fee['assid'],0,200,$admin);},'zero-value edit cannot bypass removal confirmation');
$discount->change($fee['assid'],0,200,$admin,true,true);
check((int)$db->query('SELECT COUNT(*) FROM lhpassignedfee')->fetchColumn()===1 && (int)$db->query('SELECT discount FROM lhpassignedfee')->fetchColumn()===0,'discount removal preserves assigned fee');
$expense=new ExpenseService($db);$expenseData=array('expense_date'=>date('Y-m-d'),'category'=>'Teaching materials','payee'=>'Demo supplier','description'=>'Demo books','amount'=>'120.50','method'=>'Cash','reference'=>'QA','request_key'=>bin2hex(random_bytes(16)));
$id=$expense->save($expenseData,$admin);check($expense->save($expenseData,$admin)===$id,'expense retry is duplicate-safe');
$expenseData['id']=$id;$expenseData['version']=1;$expenseData['amount']='150.00';$expense->save($expenseData,$admin);
check($expense->get($id)['amount']==='150.00','expense edit preserves decimal amount');
denied(function()use($expense,$expenseData,$admin){$expense->save($expenseData,$admin);},'stale expense edit rejected');
$expense->void($id,2,'Duplicate receipt',$admin,true);check($expense->get($id)['status']==='void','voided expense retains history');
$inventory=new InventoryService($db);$item=$inventory->save(array('sku'=>'QA-BOOK','name'=>'Exercise book','category'=>'Stationery','unit'=>'pieces','location'=>'Store','minimum_stock'=>5,'unit_cost'=>'12.50'),$admin);
$move=array('id'=>$item,'movement_type'=>'receive','quantity'=>10,'reason'=>'Opening stock','request_key'=>bin2hex(random_bytes(16)));$inventory->move($move,$admin);$inventory->move($move,$admin);
check((int)$inventory->get($item)['quantity']===10,'stock receipt retry cannot double-count');
$move['movement_type']='issue';$move['quantity']=4;$move['recipient']='Demo class';$move['request_key']=bin2hex(random_bytes(16));$inventory->move($move,$admin);
check((int)$inventory->get($item)['quantity']===6,'stock issue updates balance');
$move['quantity']=7;$move['request_key']=bin2hex(random_bytes(16));denied(function()use($inventory,$move,$admin){$inventory->move($move,$admin);},'negative stock prevented');
denied(function()use($inventory,$item,$admin){$inventory->archive($item,$admin,true);},'nonempty stock cannot be archived');
$scores=new ScorebookService($db);$sheet=$scores->sheet($teacher,$class,$subject);$change=array('learner'=>$student,'version'=>'new','score'=>'0','examscore'=>'45');
$scores->save($teacher,$class,$subject,0,array($change));$sheet=$scores->sheet($teacher,$class,$subject);$record=$sheet[0]['record'];
check((int)$record['score']===0 && (int)$record['totalscore']===45,'zero score accepted and total calculated');
denied(function()use($scores,$teacher,$class,$subject,$change){$scores->save($teacher,$class,$subject,0,array($change));},'concurrent score edit detected');
$change['version']=$sheet[0]['version'];$change['score']='20';$change['examscore']='';$scores->save($teacher,$class,$subject,0,array($change));$sheet=$scores->sheet($teacher,$class,$subject);
check((int)$sheet[0]['record']['examscore']===45 && (int)$sheet[0]['record']['totalscore']===65,'blank leaves recorded score unchanged');
$change['version']=$sheet[0]['version'];$change['score']='31';denied(function()use($scores,$teacher,$class,$subject,$change){$scores->save($teacher,$class,$subject,0,array($change));},'configured mark limit enforced');
$change['score']='25';$invalid=$change;$invalid['learner']='other_class';denied(function()use($scores,$teacher,$class,$subject,$change,$invalid){$scores->save($teacher,$class,$subject,0,array($change,$invalid));},'entire batch validated before any writes');
check((int)$scores->sheet($teacher,$class,$subject)[0]['record']['score']===20,'invalid batch leaves valid row unchanged');
$db->exec('UPDATE lhpresultconfig SET status=1');denied(function()use($scores,$teacher,$class,$subject,$change){$scores->save($teacher,$class,$subject,0,array($change));},'published term is locked server-side');
denied(function()use($scores,$class,$subject){$scores->sheet('unallocated_teacher',$class,$subject);},'unallocated teacher cannot access scores');
$scores->save($teacher,$class,$subject,2,array(array('learner'=>$student,'version'=>'new','score'=>'0')));check((int)$scores->sheet($teacher,$class,$subject,2)[0]['record']['score']===0,'weekly zero stored in legacy format');
$notes=new NoteService($db);$topics=$notes->topics($teacher);$noteId=$notes->save(array('topicid'=>$topics[0]['schmid'],'type'=>'text','content'=>'<h2>Fractions</h2><p onclick="alert(1)">one half</p><script>alert(1)</script><a href="javascript:alert(1)">bad link</a>'),$teacher);
$note=$notes->get($noteId,$student,'Learner');check(strpos($note['content'],'onclick')===false && strpos($note['content'],'<script')===false && strpos($note['content'],'javascript:')===false,'note content removes unsafe markup');
check(strpos($note['content'],'<h2>Fractions</h2>')!==false,'lesson headings preserved');
denied(function()use($notes,$noteId){$notes->get($noteId,'other_student','Learner');},'cross-class note access denied');
denied(function()use($notes,$noteId){$notes->get($noteId,'unallocated_teacher','Instructor',true);},'other teacher cannot edit note');
denied(function(){NoteService::safeUrl('javascript:alert(1)');},'unsafe resource URL rejected');
$version=NoteService::version($note);$notes->save(array('id'=>$noteId,'version'=>$version,'topicid'=>$note['topicid'],'type'=>'text','content'=>'<p>Updated lesson.</p>'),$teacher);
denied(function()use($notes,$noteId,$version,$teacher){$notes->remove($noteId,$version,$teacher,true);},'stale note deletion rejected');
$note=$notes->get($noteId,$teacher,'Instructor',true);$notes->remove($noteId,NoteService::version($note),$teacher,true);
check((int)$db->query('SELECT status FROM lhpnote WHERE noteid='.(int)$noteId)->fetchColumn()===0,'note removal retains original record');
$_SESSION=array('portal_csrf'=>'valid');denied(function(){CbtSecurity::requireCsrf('invalid','portal');},'forged CSRF token rejected');
echo "All school workflow checks passed against temporary copies of the deployed schema.\n";
