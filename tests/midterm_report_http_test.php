<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__.'/../config/database.php'; require __DIR__.'/../classes/MidtermReportService.php';
$db=database_pdo(); $base=rtrim((string)app_env('APP_URL','http://localhost/learnable'),'/');
if (!in_array(parse_url($base,PHP_URL_HOST),['localhost','127.0.0.1','::1'],true)) throw new RuntimeException('Use a local development site.');
$secret=(string)app_env('E2E_DEMO_PASSWORD',''); if(strlen($secret)<12) throw new RuntimeException('Configure the existing demo administrator.');
function mid_http_check($ok,$message) { if(!$ok) throw new RuntimeException('FAIL: '.$message);echo 'PASS: '.$message."\n"; }
function mid_http($path,$cookie=null,$fields=null) { global $base;$c=curl_init($base.$path);$options=[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>30];if($cookie){$options[CURLOPT_COOKIEFILE]=$cookie;$options[CURLOPT_COOKIEJAR]=$cookie;}if($fields!==null){$options[CURLOPT_POST]=true;$options[CURLOPT_POSTFIELDS]=http_build_query($fields);}curl_setopt_array($c,$options);$body=curl_exec($c);$status=curl_getinfo($c,CURLINFO_RESPONSE_CODE);$url=curl_getinfo($c,CURLINFO_REDIRECT_URL);$error=curl_error($c);curl_close($c);if($body===false || $error)throw new RuntimeException('HTTP failed: '.$error);mid_http_check($status<500,'report endpoint responds without server errors');return [$status,$body,$url]; }
$sample=$db->query("SELECT w.term,w.classid,w.lid FROM lhpweekrecord w WHERE w.lid='codex_demo_std' AND LOWER(TRIM(w.week)) IN ('week 1','week 2','week 3','week 4','week 5','week 6') ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if(!$sample)throw new RuntimeException('The existing demo weekly score is required.');
$fingerprint=function()use($db,$sample){$q=$db->prepare('SELECT COUNT(*),SUM(score),SUM(CRC32(CONCAT_WS("|",id,term,week,classid,subjid,lid,score))) FROM lhpweekrecord WHERE term=? AND classid=?');$q->execute([$sample['term'],$sample['classid']]);return $q->fetch(PDO::FETCH_NUM);};$before=$fingerprint();
$cookie=tempnam(sys_get_temp_dir(),'midterm-admin-');$cookies=[$cookie];$staff=[];
$query=http_build_query(['term'=>$sample['term'],'class_id'=>$sample['classid']]);
try {
    [$status,,$url]=mid_http('/admin/viewmidterm.php?'.$query);mid_http_check($status===302 && str_ends_with($url,'/admin.php'),'anonymous class exports require authentication');
    mid_http('/entadmin.php',$cookie,['but_admn'=>'1','aaname'=>'codex_demo_admin','aapwd'=>$secret]);
    [$status,$body]=mid_http('/admin/index.php?route=midterm&'.$query,$cookie);mid_http_check($status===200 && str_contains($body,'Whole class') && str_contains($body,'Download PDF'),'management offers individual and whole-class export');
    [$status,$body]=mid_http('/admin/viewmidterm.php?'.http_build_query(['term'=>$sample['term'],'lid'=>$sample['lid']]),$cookie);mid_http_check($status===200 && substr_count($body,'data-midterm-sheet')===1,'legacy individual report links still render one sheet');
    mid_http_check(str_contains($body,'/learn/asset/img/school/') && str_contains($body,'student-placeholder.svg'),'the individual report includes the profile logo and missing-photo icon');
    mid_http_check(str_contains($body,'https://schelps.com.ng'),'the admin report page retains the SCHELPS backlink');
    $service=new MidtermReportService($db);
    $expected=$service->buildClass($sample['classid'],$sample['term']);
    [$status,$body]=mid_http('/admin/viewmidterm.php?'.$query,$cookie);mid_http_check($status===200 && substr_count($body,'data-midterm-sheet')===count($expected),'whole-class preview includes every learner with recorded midterm scores');
    [$status,$body]=mid_http('/admin/viewmidterm.php?'.$query.'&download=1',$cookie);mid_http_check($status===200 && str_contains($body,'data-midterm-auto-download'),'direct class download prepares automatic PDF export');
    [$status]=mid_http('/admin/viewmidterm.php?lid='.$sample['lid'],$cookie);mid_http_check($status===400,'missing report parameters are rejected clearly');
    [$status]=mid_http('/admin/viewmidterm.php?'.http_build_query(['term'=>'nonexistent','class_id'=>$sample['classid']]),$cookie);mid_http_check($status===404,'invalid term combinations do not expose another report');
    foreach(['r','b'] as $role){$name='qa-mid-'.bin2hex(random_bytes(5));$password=bin2hex(random_bytes(16));$staff[]=$name;$db->prepare('INSERT INTO lhpstaff (sname,staffname,spwd,semail,sfone,role,status) VALUES (?,?,?,?,?,?,1)')->execute([$name,'QA Midterm Staff',password_hash($password,PASSWORD_DEFAULT),'','',$role]);$staffCookie=tempnam(sys_get_temp_dir(),'midterm-staff-');$cookies[]=$staffCookie;mid_http('/entstaff.php',$staffCookie,['but_submit'=>'1','uname'=>$name,'upass'=>$password]);[$status]=mid_http('/admin/viewmidterm.php?'.$query,$staffCookie);mid_http_check($status===($role==='r'?200:403),$role==='r'?'Registry Admin retains class-report access':'bursary staff cannot export academic reports');}
    mid_http_check($fingerprint()===$before,'preview and export requests never change existing weekly scores');
}finally{foreach($staff as $name)$db->prepare('DELETE FROM lhpstaff WHERE sname=?')->execute([$name]);foreach($cookies as $path)@unlink($path);}
echo "Midterm authenticated HTTP checks passed.\n";
