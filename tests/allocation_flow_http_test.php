<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../classes/autoload.php';
$db = database_pdo();
$base = rtrim((string) app_env('APP_URL','http://localhost/learnable'),'/');
if (!in_array(parse_url($base,PHP_URL_HOST),['localhost','127.0.0.1','::1'],true)) { throw new RuntimeException('Local development only.'); }
$password = (string) app_env('E2E_DEMO_PASSWORD','');
if (strlen($password)<12) { throw new RuntimeException('Configure the existing demo admin first.'); }
$cookie = tempnam(sys_get_temp_dir(),'allocation-qa-');
$subjects=[]; $allocations=[];
function allocation_check($ok,$message) { if (!$ok) { throw new RuntimeException($message); } echo 'PASS: '.$message."\n"; }
function allocation_request($path,$fields=null) {
    global $base,$cookie;
    $curl=curl_init($base.$path);
    curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_COOKIEFILE=>$cookie,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>20]);
    if($fields!==null){curl_setopt($curl,CURLOPT_POST,true);curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($fields));}
    $html=curl_exec($curl);$status=curl_getinfo($curl,CURLINFO_RESPONSE_CODE);$url=curl_getinfo($curl,CURLINFO_REDIRECT_URL);
    if($html===false){throw new RuntimeException(curl_error($curl));}curl_close($curl);
    allocation_check($status<500,'Request succeeds: '.$path);
    return [$status,$html,$url];
}
function allocation_form($html) {
    $doc=new DOMDocument();@$doc->loadHTML($html);$xpath=new DOMXPath($doc);
    $class=$xpath->query('//form[@data-allocation-form]//select[@data-allocation-class]')->item(0);
    $subject=$xpath->query('//form[@data-allocation-form]//select[@data-allocation-subject]')->item(0);
    allocation_check($class && $subject && !$class->hasAttribute('disabled') && !$subject->hasAttribute('disabled'),'New form has editable class and subject');
    allocation_check($xpath->evaluate('string(//form[@data-allocation-form]//input[@name="id"]/@value)')==='0','New form does not retain a previous allocation ID');
    return $xpath->evaluate('string(//form[@data-allocation-form]//input[@name="csrf_token"]/@value)');
}
function allocation_has_subject($html, $id) {
    $doc=new DOMDocument();@$doc->loadHTML($html);$xpath=new DOMXPath($doc);
    return $xpath->query('//select[@data-allocation-subject]/option[@value="'.(int)$id.'"]')->length > 0;
}
try {
    allocation_request('/entadmin.php',['but_admn'=>'1','aaname'=>'codex_demo_admin','aapwd'=>$password]);
    [$status,$html]=allocation_request('/admin/index.php?route=allocations');
    allocation_check($status===200,'Allocation page is authenticated');
    $token=allocation_form($html);
    $service=new AcademicStructureService($db);
    $classes=$service->classes(); $staff=$service->staff();
    allocation_check(count($classes)>=2 && count($staff)>0,'Existing classes and teacher available for isolated fixture allocations');
    foreach (array_slice($classes,0,2) as $i=>$class) {
        $subject=$service->saveSubject(['class_id'=>$class['classid'],'name'=>'QA allocation '.bin2hex(random_bytes(5))],'qa-allocation-flow');
        $subjects[]=$subject;
        [, $html]=allocation_request('/admin/index.php?route=allocations');
        allocation_check(allocation_has_subject($html,$subject),'Unallocated subject is offered');
        [$status,,$redirect]=allocation_request('/admin/index.php?route=allocations',['action'=>'save','id'=>'0','csrf_token'=>$token,'class_id'=>$class['classid'],'subject_id'=>$subject,'teacher_id'=>$staff[0]['sname']]);
        $query=$db->prepare('SELECT aid FROM lhpalloc WHERE sbjid=? AND classid=?');$query->execute([$subject,$class['classid']]);
        $id=(int)$query->fetchColumn();if($id){$allocations[]=$id;}
        allocation_check($id>0,'Allocation '.($i+1).' saved for a different class and subject');
        allocation_check($status===302 && parse_url($redirect,PHP_URL_QUERY)==='route=allocations','Save returns to a fresh allocation URL');
        [, $html]=allocation_request('/admin/index.php?route=allocations');$token=allocation_form($html);
        allocation_check(!allocation_has_subject($html,$subject),'Allocated subject is removed from new allocation choices');
        allocation_check(str_contains($html,'You can allocate another subject below.'),'Success message invites the next allocation');
    }
    $id=$allocations[0];
    [, $html]=allocation_request('/admin/index.php?route=allocations&id='.$id);
    allocation_check(allocation_has_subject($html,$subjects[0]),'Current allocated subject remains visible when editing');
    allocation_check(str_contains($html,'>New allocation</a>'),'Edit mode offers an explicit New allocation link');
    allocation_check(str_contains($html,'name="class_display" required data-allocation-class disabled'),'Editing retains existing class identity protection');
    [$status,,$redirect]=allocation_request('/admin/index.php?route=allocations&id='.$id,['action'=>'save','id'=>$id,'csrf_token'=>$token,'class_id'=>$classes[0]['classid'],'subject_id'=>$subjects[0],'teacher_id'=>$staff[0]['sname']]);
    allocation_check($status===302 && parse_url($redirect,PHP_URL_QUERY)==='route=allocations','Saving an edit also returns to a new allocation form');
    [, $html]=allocation_request('/admin/index.php?route=allocations');allocation_form($html);
    allocation_request('/admin/index.php?route=allocations&id='.$id,['action'=>'delete','id'=>$id,'confirm'=>'yes','csrf_token'=>$token]);
    [, $html]=allocation_request('/admin/index.php?route=allocations');
    allocation_check(allocation_has_subject($html,$subjects[0]),'Removing an allocation makes its subject available again');
    $q=$db->prepare('UPDATE lhpalloc SET term=? WHERE aid=?');$q->execute(['QA previous term',$allocations[1]]);
    [, $html]=allocation_request('/admin/index.php?route=allocations');
    allocation_check(allocation_has_subject($html,$subjects[1]),'An allocation in another term does not hide the subject');
} finally {
    foreach($allocations as $id){$q=$db->prepare("DELETE FROM school_workflow_audit WHERE module='allocation' AND record_id=?");$q->execute([(string)$id]);}
    foreach($subjects as $subject){
        $query=$db->prepare('SELECT aid FROM lhpalloc WHERE sbjid=?');$query->execute([$subject]);
        foreach($query->fetchAll(PDO::FETCH_COLUMN) as $id){$q=$db->prepare("DELETE FROM school_workflow_audit WHERE module='allocation' AND record_id=?");$q->execute([(string)$id]);}
        $q=$db->prepare('DELETE FROM lhpalloc WHERE sbjid=?');$q->execute([$subject]);
        $q=$db->prepare("DELETE FROM school_workflow_audit WHERE module='subject' AND record_id=?");$q->execute([(string)$subject]);
        $q=$db->prepare('DELETE FROM lhpsubject WHERE sbjid=?');$q->execute([$subject]);
    }
    @unlink($cookie);
    echo "Temporary test subjects and allocations removed.\n";
}
echo "Allocation HTTP flow checks passed.\n";
