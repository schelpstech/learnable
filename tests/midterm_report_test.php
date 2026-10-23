<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__.'/../config/database.php';
require __DIR__.'/../classes/MidtermReportService.php';
function mid_check($ok,$message) { if (!$ok) throw new RuntimeException('FAIL: '.$message); echo 'PASS: '.$message."\n"; }
$manifest=__DIR__.'/../tasks/_midterm_preview.json';
if (in_array('--cleanup-preview',$argv,true)) {
    $data=json_decode(file_get_contents($manifest),true);
    if (!preg_match('/^qa-midterm-[a-f0-9]{16}\.jpg$/D',$data['passport'] ?? '')) throw new RuntimeException('Invalid preview owner.');
    @unlink(__DIR__.'/../learn/asset/img/passport/'.$data['passport']);
    @unlink(__DIR__.'/_midterm_preview.html'); @unlink(__DIR__.'/_midterm_individual_preview.html'); unlink($manifest); echo "Owned midterm preview files removed.\n"; exit;
}
$db=database_pdo(); $source=$db->query('SELECT DATABASE()')->fetchColumn();
$schema='qa_midterm_'.bin2hex(random_bytes(8));
$passport='qa-midterm-'.bin2hex(random_bytes(8)).'.jpg';
$preview=in_array('--preview',$argv,true); $passed=false;
if (is_file($manifest)) throw new RuntimeException('Clean up the previous preview before creating another.');
$db->exec('CREATE DATABASE `'.$schema.'`');
try {
    $tables=['lpterm','lhpclass','lhpuser','lhpsubject','lhpweekrecord','lhpschool','lhpresultconfig','lhpstaff','lhpclassalloc'];
    foreach($tables as $table) $db->exec('CREATE TABLE `'.$schema.'`.`'.$table.'` LIKE `'.$source.'`.`'.$table.'`');
    $copy=function($table,$values) use($db,$source,$schema) {
        $row=$db->query('SELECT * FROM `'.$source.'`.`'.$table.'` LIMIT 1')->fetch(PDO::FETCH_ASSOC);
        if (!$row) throw new RuntimeException('A fixture template is required for '.$table);
        foreach($db->query('SHOW COLUMNS FROM `'.$schema.'`.`'.$table.'`')->fetchAll(PDO::FETCH_ASSOC) as $column) if(strpos($column['Extra'],'auto_increment')!==false) unset($row[$column['Field']]);
        $row=array_replace($row,$values);
        $db->prepare('INSERT INTO `'.$schema.'`.`'.$table.'` (`'.implode('`,`',array_keys($row)).'`) VALUES ('.implode(',',array_fill(0,count($row),'?')).')')->execute(array_values($row));
    };
    $term='1st Term 2099/2100'; $class='990';
    $copy('lpterm',['tid'=>990,'term'=>$term,'status'=>1]);
    $copy('lhpclass',['classid'=>990,'classname'=>'QA Old Class']);
    $copy('lhpclass',['classid'=>991,'classname'=>'QA New Class']);
    $copy('lhpschool',['schname'=>'Midterm QA School','motto'=>'Learning with purpose','address'=>'QA School Road','phone'=>'','email'=>'','proprietor'=>'QA Head Teacher']);
    $copy('lhpresultconfig',['term'=>$term,'signature'=>'']);
    $copy('lhpstaff',['sname'=>'qa-mid-teacher','staffname'=>'QA Class Teacher']);
    $copy('lhpclassalloc',['term'=>$term,'classid'=>990,'tutorid'=>'qa-mid-teacher']);
    $copy('lhpuser',['uname'=>'qa-mid-a','fname'=>'Amara Test Learner','gender'=>'Female','dob'=>'2017-04-12','classid'=>991,'picture'=>'']);
    copy(__DIR__.'/../images/learnable-landing-classroom-v3.jpg',__DIR__.'/../learn/asset/img/passport/'.$passport);
    $copy('lhpuser',['uname'=>'qa-mid-b','fname'=>'Bayo Test Learner','gender'=>'Male','dob'=>'2016-02-05','classid'=>991,'picture'=>$passport]);
    $db->exec('USE `'.$schema.'`');
    $insert=$db->prepare('INSERT INTO lhpweekrecord (term,week,classid,subjid,lid,score) VALUES (?,?,?,?,?,?)');
    for($subject=1;$subject<=24;$subject++) {
        $copy('lhpsubject',['sbjid'=>9900+$subject,'sbjname'=>['Mathematics','Basic Science','English Language'][$subject-1] ?? 'Subject '.$subject,'classid'=>990,'classname'=>'QA Old Class']);
        for($week=1;$week<=6;$week++) $insert->execute([$term,'Week '.$week,$class,9900+$subject,'qa-mid-b',10]);
    }
    foreach([10,0,9,8,7,6] as $index=>$score) $insert->execute([$term,'Week '.($index+1),$class,9901,'qa-mid-a',$score]);
    for($week=1;$week<=6;$week++) $insert->execute([$term,'Week '.$week,$class,9902,'qa-mid-a',10]);
    $insert->execute([$term,'Week 1',$class,9903,'qa-mid-a',0]);
    $insert->execute([$term,'Week 7',$class,9904,'qa-mid-a',10]);
    $service=new MidtermReportService($db,dirname(__DIR__),'/learnable');
    $report=$service->build('qa-mid-a',$term);
    mid_check($report['learner']['class_id']==='990' && $report['class_name']==='QA Old Class','reports use weekly-record class membership after a learner moves class');
    mid_check(count($report['subjects'])===3,'Week 7+ scores do not enter the midterm report');
    $bySubject=array_column($report['subjects'],null,'id');
    mid_check($bySubject[9901]['total']===20.0 && $bySubject[9901]['grade']==='B','weekly total and existing midterm grade boundaries are preserved');
    mid_check($bySubject[9903]['weeks'][1]===0.0 && $bySubject[9903]['weeks'][2]===null,'recorded zero and missing scores remain distinct');
    mid_check($report['missing']===5 && $report['total']===50.0 && $report['maximum']===90,'summary totals and unrecorded-entry counts are accurate');
    mid_check(str_ends_with($report['learner']['passport_url'],'student-placeholder.svg'),'blank student photos use an icon placeholder');
    $db->exec("UPDATE lhpuser SET picture='missing-file.jpg' WHERE uname='qa-mid-a'");
    mid_check(str_ends_with($service->build('qa-mid-a',$term)['learner']['passport_url'],'student-placeholder.svg'),'missing photo files also use the icon');
    $db->exec("UPDATE lhpuser SET picture='nopix.jpg' WHERE uname='qa-mid-a'");
    mid_check(str_ends_with($service->build('qa-mid-a',$term)['learner']['passport_url'],'student-placeholder.svg'),'legacy no-photo filenames use the icon');
    $reports=$service->buildClass($class,$term);
    mid_check(count($reports)===2 && $reports[0]['learner']['id']==='qa-mid-a','class export includes every recorded learner in stable name order');
    mid_check(str_ends_with($reports[1]['learner']['passport_url'],$passport),'uploaded student photographs are retained');
    mid_check(str_contains($report['logo_url'],'/learn/asset/img/school/'),'the school logo uses the profile image directory');
    mid_check($report['teacher']==='QA Class Teacher' && $report['population']===2,'teacher and recorded class population are included');
    $pages=''; foreach($reports as $midterm) { ob_start(); require __DIR__.'/../views/shared/midterm-sheet.php'; $pages.=ob_get_clean(); }
    mid_check(substr_count($pages,'data-midterm-sheet')===3,'long reports continue on another landscape sheet instead of losing subject rows');
    mid_check(str_contains($pages,'Subject 24') && str_contains($pages,'Sheet 2 of 2'),'the last subject and continuation page numbering survive pagination');
    $insert->execute([$term,'Week 1',$class,9901,'qa-mid-a',2]);
    try { $service->build('qa-mid-a',$term,$class); throw new LogicException('Duplicate scores were accepted.'); }
    catch(RuntimeException $e) { mid_check(str_contains($e->getMessage(),'Duplicate'),'duplicate weekly scores cannot silently produce an incorrect report'); }
    foreach([[22.5,'A'],[22.4,'B'],[19.5,'B'],[19.4,'C'],[15,'C'],[13.5,'D'],[12,'E'],[11.9,'F']] as $boundary) mid_check(MidtermReportService::grade($boundary[0])[0]===$boundary[1],'grade boundary '.$boundary[0].' maps to '.$boundary[1]);
    if ($preview) {
        $html='<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Midterm QA preview</title><link rel="stylesheet" href="../assets/css/midterm-report.css?v=1"><script src="../assets/js/html2pdf-loader.js?v=5"></script></head><body style="margin:0"><main class="midterm-workspace"><div class="midterm-toolbar midterm-no-print"><div><h1>Midterm QA preview</h1><p>Synthetic test learners - landscape export check</p></div><div class="midterm-actions"><button data-midterm-print>Print</button><button data-midterm-download>Download class PDF</button></div></div><p data-midterm-status class="midterm-status" role="status"></p><div class="midterm-scroll"><div class="midterm-stack" data-midterm-reports data-midterm-file="Midterm-QA-class.pdf">'.$pages.'</div></div></main><script src="../assets/js/midterm-report.js?v=1" defer></script></body></html>';
        file_put_contents(__DIR__.'/_midterm_preview.html',$html);
        file_put_contents($manifest,json_encode(['passport'=>$passport]));
        echo "Synthetic preview ready at tests/_midterm_preview.html.\n";
    }
    $passed=true;
} finally {
    $db->exec('USE `'.$source.'`');
    if (!preg_match('/^qa_midterm_[a-f0-9]{16}$/D',$schema)) throw new RuntimeException('Invalid test schema.');
    $db->exec('DROP DATABASE `'.$schema.'`');
    if (!$preview || !$passed) @unlink(__DIR__.'/../learn/asset/img/passport/'.$passport);
    echo "Disposable midterm database removed.\n";
}
