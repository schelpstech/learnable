<?php
// Only named local QA fixtures are created; no real learner, teacher, or academic setting is changed.
if (PHP_SAPI !== 'cli') exit;
require __DIR__.'/../classes/autoload.php';require __DIR__.'/../config/database.php';
$db=database_pdo();$password=(string)app_env('E2E_DEMO_PASSWORD','');
if(strlen($password)<12) throw new RuntimeException('Configure E2E_DEMO_PASSWORD in .env.');
$hash=password_hash($password,PASSWORD_DEFAULT);$term=(new DiscountService($db))->activeTerm();
$q=$db->prepare('INSERT INTO `123admin` (dname,dpwd) VALUES (?,?) ON DUPLICATE KEY UPDATE dpwd=VALUES(dpwd)');$q->execute(array('codex_demo_admin',$hash));
$q=$db->prepare("INSERT INTO lhpstaff (sname,staffname,spwd,sfone,semail,status,role) VALUES (?,?,?,'00000000000','teacher@example.test',1,'t') ON DUPLICATE KEY UPDATE spwd=VALUES(spwd),status=1");$q->execute(array('codex_demo_teacher','Demo Teacher',$hash));
$db->exec("INSERT INTO lhpclass (classname) SELECT 'Codex Demo' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM lhpclass WHERE classname='Codex Demo')");
$class=$db->query("SELECT classid FROM lhpclass WHERE classname='Codex Demo' ORDER BY classid LIMIT 1")->fetchColumn();
$q=$db->prepare("INSERT INTO lhpsubject (sbjname,classid,classname) SELECT 'Demo Mathematics',?,'Codex Demo' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM lhpsubject WHERE classid=? AND sbjname='Demo Mathematics')");$q->execute(array($class,$class));
$q=$db->prepare("SELECT sbjid FROM lhpsubject WHERE classid=? AND sbjname='Demo Mathematics' LIMIT 1");$q->execute(array($class));$subject=$q->fetchColumn();
$q=$db->prepare("INSERT INTO lhpalloc (term,classname,subject,staffid,supro,classid,sbjid) SELECT ?,'Codex Demo','Demo Mathematics','codex_demo_teacher','',?,? FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM lhpalloc WHERE term=? AND classid=? AND sbjid=? AND staffid='codex_demo_teacher')");$q->execute(array($term,$class,$subject,$term,$class,$subject));
$q=$db->prepare("INSERT INTO lhpclassalloc (term,classid,tutorid) SELECT ?,?,'codex_demo_teacher' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM lhpclassalloc WHERE term=? AND classid=? AND tutorid='codex_demo_teacher')");$q->execute(array($term,$class,$term,$class));
$q=$db->prepare("INSERT INTO lhpuser (uname,gender,dob,upwd,email,classid,fname,status,picture,numb) VALUES ('codex_demo_std','Male','2012-01-01',?,'student@example.test',?,'Demo Learner',1,'','00000000000') ON DUPLICATE KEY UPDATE upwd=VALUES(upwd),classid=VALUES(classid),status=1");$q->execute(array($hash,$class));
$q=$db->prepare("INSERT INTO lhpscheme (term,classname,subject,week,topic,staffid,status) SELECT ?,?,?,'Week 1','Demo · Fractions in everyday life','codex_demo_teacher',1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM lhpscheme WHERE term=? AND classname=? AND subject=? AND staffid='codex_demo_teacher')");$q->execute(array($term,$class,$subject,$term,$class,$subject));
$q=$db->prepare("SELECT schmid FROM lhpscheme WHERE term=? AND classname=? AND subject=? AND staffid='codex_demo_teacher' LIMIT 1");$q->execute(array($term,$class,$subject));$topic=$q->fetchColumn();
$q=$db->prepare("SELECT noteid FROM lhpnote WHERE topicid=? AND staffid='codex_demo_teacher' AND status=1 LIMIT 1");$q->execute(array($topic));
if(!$q->fetchColumn()) (new NoteService($db))->save(array('topicid'=>$topic,'type'=>'text','content'=>'<h2>What is a fraction?</h2><p>A fraction describes part of a whole. Think of sharing one orange equally between two friends.</p><h3>Worked example</h3><p>Two quarters make one half: <strong>2/4 = 1/2</strong>.</p><h3>Try it yourself</h3><ol><li>Draw a rectangle and shade one quarter.</li><li>Write two fractions equal to one half.</li></ol>'),'codex_demo_teacher');
$q=$db->prepare("INSERT INTO lhpassignedfee (feeid,classid,stdid,term,type,due,amount,discount,status) SELECT 'PreviousBalance',?,'codex_demo_std',?,'Demo fee',CURRENT_DATE,1000,100,1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM lhpassignedfee WHERE stdid='codex_demo_std' AND term=? AND type='Demo fee')");$q->execute(array($class,$term,$term));
echo "Demo accounts and isolated class ready. Class: $class; subject: $subject; topic: $topic. Password remains in .env.\n";
