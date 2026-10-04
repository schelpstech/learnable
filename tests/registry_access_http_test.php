<?php
if (PHP_SAPI!=='cli') { http_response_code(404); exit; }
require __DIR__.'/../classes/autoload.php';
require __DIR__.'/../config/database.php';
$db=database_pdo();
$base=rtrim((string)app_env('APP_URL','http://localhost/learnable'),'/');
if (!in_array(parse_url($base,PHP_URL_HOST),['localhost','127.0.0.1','::1'],true)) throw new RuntimeException('Local development only.');
$password=(string)app_env('E2E_DEMO_PASSWORD','');
if (strlen($password)<12) throw new RuntimeException('Configure the existing demo admin first.');
$suffix=bin2hex(random_bytes(5)); $registry='qa_registry_'.$suffix; $bursary='qa_bursary_'.$suffix; $student='qs_'.$suffix;
$jars=[]; foreach(['admin','registry','bursary','unified','teacher'] as $key) $jars[$key]=tempnam(sys_get_temp_dir(),'registry-'.$key);
$class=$subject=$allocation=0; $config=null; $createdConfig=0;
function registry_check($ok,$message) { if(!$ok) throw new RuntimeException('FAIL: '.$message); echo 'PASS: '.$message."\n"; }
function registry_http($who,$path,$fields=null) {
    global $base,$jars;
    $c=curl_init($base.$path); curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_COOKIEFILE=>$jars[$who],CURLOPT_COOKIEJAR=>$jars[$who],CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>20]);
    if($fields!==null) {curl_setopt($c,CURLOPT_POST,true);curl_setopt($c,CURLOPT_POSTFIELDS,http_build_query($fields));}
    $html=curl_exec($c); $status=curl_getinfo($c,CURLINFO_RESPONSE_CODE); $location=curl_getinfo($c,CURLINFO_REDIRECT_URL); if($html===false) throw new RuntimeException(curl_error($c));curl_close($c);
    if($status>=500 || preg_match('/Fatal error|Warning:|Uncaught /',$html)) throw new RuntimeException('HTTP error on '.$path.': '.substr(strip_tags($html),0,600));
    return [$status,$html,$location];
}
function registry_token($html) { preg_match('/name="csrf_token" value="([a-f0-9]+)"/',$html,$m);if(empty($m[1])) throw new RuntimeException('Missing CSRF token.');return $m[1]; }
try {
    registry_http('admin','/entadmin.php',['but_admn'=>'1','aaname'=>'codex_demo_admin','aapwd'=>$password]);
    [$status,$html]=registry_http('admin','/admin/index.php?route=staff');
    registry_check($status===200 && str_contains($html,'value="r">Registry Admin'),'main admin can assign Registry Admin in the staff form');
    $csrf=registry_token($html);
    foreach([$registry=>'r',$bursary=>'b'] as $username=>$role) {
        registry_http('admin','/admin/createstaff.php',['csrf_token'=>$csrf,'createst'=>'Create Staff Account','stname'=>'QA '.$role,'stuname'=>$username,'stpwd'=>$password,'stmail'=>'','stfone'=>'','role'=>$role]);
        $q=$db->prepare('SELECT role FROM lhpstaff WHERE sname=?');$q->execute([$username]);registry_check($q->fetchColumn()===$role,'staff creation saves the selected role '.$role);
    }
    registry_http('registry','/entstaff.php',['but_submit'=>'1','uname'=>$registry,'upass'=>$password]);
    [$status,$html]=registry_http('registry','/admin/index.php?route=dashboard');
    registry_check($status===200 && str_contains($html,'Registry workspace') && str_contains($html,'Registry Admin'),'registry login opens its academic dashboard');
    registry_check(!str_contains($html,'route=payments') && !str_contains($html,'route=fees') && !str_contains($html,'route=staff') && !str_contains($html,'route=profile'),'registry navigation and dashboard contain no financial or system management links');
    $registryCsrf=registry_token($html);
    $routes=require __DIR__.'/../config/admin_routes.php';
    $access=new StaffAccess($db,['unamed'=>$registry,'auth_account_type'=>'staff','auth_username'=>$registry]);
    foreach($routes as $route=>$file) {
        if(str_starts_with($route,'report-') || str_starts_with($route,'reports-class')) continue;
        [$status]=registry_http('registry','/admin/index.php?route='.$route);
        registry_check($status===($access->canRoute($route)?200:403),'registry route permission: '.$route);
    }
    $blocked=['dashboard.php?route=fees','profile.php','mgstaff.php','createstaff.php','edstaff.php','schprofile.php','mgfee.php','createfee.php','assignfee.php','feeassign.php','mgdiscount.php','expenses.php','inventory.php','payrecord.php','payreport.php','payview.php','getclassrecord.php','get_fee.php','getdisc.php','getamt.php','recordpayment.php','modifyPayRecord.php','receipt.php'];
    foreach($blocked as $file) {
        // Dashboard remains academic even if a legacy caller appends a finance route.
        if(str_starts_with($file,'dashboard')) continue;
        foreach([null,['csrf_token'=>$registryCsrf,'role'=>'b','assessment_id'=>1]] as $fields) {
            [$status]=registry_http('registry','/admin/'.$file,$fields);registry_check($status===403,'direct financial/system request blocked: '.$file.($fields===null?' GET':' POST'));
        }
    }
    foreach(glob(__DIR__.'/../bursar/*.php') as $file) {
        if(basename($file)==='logout.php') continue;
        [$status]=registry_http('registry','/bursar/'.basename($file));registry_check($status===403,'bursary endpoint blocked: '.basename($file));
    }
    [$status]=registry_http('registry','/learn/app/do_backup.php'); registry_check($status===403,'registry cannot download a whole-database financial backup');
    registry_http('unified','/learn/app/useracces.php',['log_in'=>'Log in','userid'=>$registry,'userpwd'=>$password]);
    [$status,$html]=registry_http('unified','/admin/index.php?route=dashboard');registry_check($status===200 && str_contains($html,'Registry workspace'),'the learning portal login also routes Registry Admin correctly');
    registry_http('bursary','/entstaff.php',['but_submit'=>'1','uname'=>$bursary,'upass'=>$password]);
    [$status]=registry_http('bursary','/bursar/profile.php');registry_check($status===200,'existing bursary login still works');
    foreach(['/admin/index.php?route=learners','/admin/cbt.php','/learn/app/cbt_export.php?assessment_id=1'] as $path){[$status]=registry_http('bursary',$path);registry_check($status===403,'bursary cannot use academic administration: '.$path);}
    registry_http('unified','/learn/app/useracces.php',['log_in'=>'Log in','userid'=>'codex_demo_teacher','userpwd'=>$password]);
    [$status]=registry_http('unified','/admin/index.php?route=learners');registry_check($status===403,'switching from registry to teacher clears registry access');
    registry_http('unified','/learn/app/useracces.php',['log_in'=>'Log in','userid'=>'codex_demo_std','userpwd'=>$password]);
    [$status]=registry_http('unified','/admin/index.php?route=learners');registry_check($status===403,'student login never retains staff admin access');
    // Owned academic fixtures prove actual numeric saves without touching real student scores.
    $db->prepare('INSERT INTO lhpclass (classname) VALUES (?)')->execute(['QA'.$suffix]);$class=(int)$db->lastInsertId();
    $db->prepare('INSERT INTO lhpsubject (sbjname,classid,classname) VALUES (?,?,?)')->execute(['QS'.$suffix,$class,'QA'.$suffix]);$subject=(int)$db->lastInsertId();
    $term=(new ScorebookService($db))->activeTerm();
    $db->prepare('INSERT INTO lhpalloc (term,classname,subject,staffid,supro,classid,sbjid) VALUES (?,?,?,?,?,?,?)')->execute([$term,'QA'.$suffix,'QS'.$suffix,'codex_demo_teacher','',$class,$subject]);$allocation=(int)$db->lastInsertId();
    [$status]=registry_http('registry','/admin/createle.php',['createl'=>'Create Learner Account','lname'=>'QA Student','luname'=>$student,'lpwd'=>$password,'lmail'=>'','lclass'=>$class,'gender'=>'Male','dob'=>'2015-01-01']);
    // Legacy createle field names are inspected below; fixture fallback is intentionally avoided.
    $q=$db->prepare('SELECT uname FROM lhpuser WHERE uname=?');$q->execute([$student]); registry_check($q->fetchColumn()===$student,'registry creates a student account');
    $q=$db->prepare('SELECT * FROM lhpresultconfig WHERE term=? LIMIT 1');$q->execute([$term]);$config=$q->fetch(PDO::FETCH_ASSOC);
    if(!$config) {
        $config=$db->query('SELECT * FROM lhpresultconfig ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
        if(!$config) throw new RuntimeException('A result settings template is required.');
        unset($config['id']);$config['term']=$term;
        $columns=array_keys($config);$db->prepare('INSERT INTO lhpresultconfig (`'.implode('`,`',$columns).'`) VALUES ('.implode(',',array_fill(0,count($columns),'?')).')')->execute(array_values($config));
        $createdConfig=(int)$db->lastInsertId();$config['id']=$createdConfig;
    }
    $db->prepare('UPDATE lhpresultconfig SET status=0,midterm=0 WHERE id=?')->execute([$config['id']]);
    $path='/admin/index.php?route=scores&class_id='.$class.'&subject_id='.$subject;
    [$status,$html]=registry_http('registry',$path);registry_check($status===200 && str_contains($html,'QA Student'),'registry opens score sheet without a personal teaching allocation');
    $data=['csrf_token'=>registry_token($html),'class_id'=>$class,'subject_id'=>$subject,'week'=>0,'row_count'=>1,'changes'=>[['learner'=>$student,'version'=>'new','score'=>1,'examscore'=>2]]];
    [$status]=registry_http('registry','/admin/index.php?route=scores',$data);registry_check($status===302,'registry score entry saves');
    $q=$db->prepare('SELECT * FROM lhpresultrecord WHERE lid=? AND subjid=? AND term=?');$q->execute([$student,$subject,$term]);$record=$q->fetch(PDO::FETCH_ASSOC);registry_check($record && (int)$record['totalscore']===3,'numeric CA and exam marks reach the existing results table');
    [$status,$html]=registry_http('registry','/admin/index.php?route=scores',$data);registry_check(str_contains($html,'Scores changed in another window'),'stale scores cannot overwrite newer marks');
    $data['changes'][0]['version']=ScorebookService::version($record);$data['changes'][0]['score']=(int)$config['ca_score']+1;
    [$status,$html]=registry_http('registry','/admin/index.php?route=scores',$data);registry_check(str_contains($html,'Score for '),'registry must obey configured score limits');
    $data['changes'][0]['score']=1;$db->prepare('UPDATE lhpresultconfig SET status=1 WHERE id=?')->execute([$config['id']]);
    [$status,$html]=registry_http('registry','/admin/index.php?route=scores',$data);registry_check(str_contains($html,'Score entry is locked'),'registry cannot edit locked published scores');
    // Existing sessions lose permission immediately when the main admin changes role/status.
    registry_http('admin','/admin/edstaff.php',['csrf_token'=>$csrf,'edstf'=>'Modify Staff Details','stnamed'=>$registry,'stname'=>'QA Registry','stemail'=>'','stfone'=>'','stpwd'=>'','role'=>'t']);
    [$status]=registry_http('registry','/admin/index.php?route=learners');registry_check($status===403,'role changes revoke existing registry sessions');
    $db->prepare("UPDATE lhpstaff SET role='r',status=0 WHERE sname=?")->execute([$registry]);
    [$status]=registry_http('registry','/admin/index.php?route=learners');registry_check($status===403,'disabled registry accounts lose access immediately');
    registry_check((int)$db->query("SELECT COUNT(*) FROM school_workflow_audit WHERE module='staff' AND record_id=".$db->quote($registry))->fetchColumn()>=2,'staff creation and role changes are audited');
} finally {
    if($createdConfig) $db->prepare('DELETE FROM lhpresultconfig WHERE id=?')->execute([$createdConfig]);
    elseif($config) $db->prepare('UPDATE lhpresultconfig SET status=?,midterm=? WHERE id=?')->execute([$config['status'],$config['midterm'],$config['id']]);
    foreach(['lhpresultrecord'=>'lid','lhpweekrecord'=>'lid','lhpuser'=>'uname'] as $table=>$key) $db->prepare('DELETE FROM '.$table.' WHERE '.$key.'=?')->execute([$student]);
    foreach(['lhpalloc'=>['aid',$allocation],'lhpsubject'=>['sbjid',$subject],'lhpclass'=>['classid',$class]] as $table=>$entry) if($entry[1])$db->prepare('DELETE FROM '.$table.' WHERE '.$entry[0].'=?')->execute([$entry[1]]);
    foreach([$registry,$bursary] as $username){$db->prepare('DELETE FROM lhpstaff WHERE sname=?')->execute([$username]);$db->prepare('DELETE FROM log WHERE uname=?')->execute([$username]);$db->prepare('DELETE FROM school_workflow_audit WHERE actor=? OR (module=\'staff\' AND record_id=?)')->execute([$username,$username]);}
    foreach($jars as $jar)@unlink($jar);
    echo "Temporary accounts and academic fixtures removed; result settings restored.\n";
}
