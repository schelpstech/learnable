<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../classes/autoload.php';
$db = database_pdo();
$base = rtrim((string) app_env('APP_URL','http://localhost/learnable'),'/');
if (!in_array(parse_url($base,PHP_URL_HOST),['localhost','127.0.0.1','::1'],true)) { throw new RuntimeException('Local development only.'); }
$password = (string) app_env('E2E_DEMO_PASSWORD','');
if (strlen($password)<12) { throw new RuntimeException('Configure the existing demo admin first.'); }
$cookie = tempnam(sys_get_temp_dir(),'creation-qa-');
$createdClasses=[]; $createdSubjects=[];
function creation_check($ok,$message) { if (!$ok) { throw new RuntimeException($message); } echo 'PASS: '.$message."\n"; }
function creation_request($path,$fields=null) {
    global $base,$cookie;
    $curl=curl_init($base.$path);
    curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_COOKIEFILE=>$cookie,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>20]);
    if($fields!==null){curl_setopt($curl,CURLOPT_POST,true);curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($fields));}
    $html=curl_exec($curl);$status=curl_getinfo($curl,CURLINFO_RESPONSE_CODE);$url=curl_getinfo($curl,CURLINFO_REDIRECT_URL);
    if($html===false){throw new RuntimeException(curl_error($curl));}curl_close($curl);
    creation_check($status<500,'Request succeeds: '.$path);
    return [$status,$html,$url];
}
function creation_form($html, $route) {
    $doc=new DOMDocument();@$doc->loadHTML($html);$x=new DOMXPath($doc);
    creation_check($x->evaluate('string(//form[.//input[@name="name"]]//input[@name="id"]/@value)')==='0', $route.': form resets to a new record');
    creation_check($x->evaluate('string(//form//input[@name="name"]/@value)')==='', $route.': name is cleared');
    if ($route==='subjects') {
        $select=$x->query('//select[@name="class_id"]')->item(0);
        creation_check($select && !$select->hasAttribute('disabled'), 'Subject class selection remains enabled');
    }
    return $x->evaluate('string(//form[.//input[@name="name"]]//input[@name="csrf_token"]/@value)');
}
try {
    creation_request('/entadmin.php',['but_admn'=>'1','aaname'=>'codex_demo_admin','aapwd'=>$password]);
    foreach(['classes','subjects'] as $route) {
        [, $html]=creation_request('/admin/index.php?route='.$route);
        $token=creation_form($html,$route);
        $table=$route==='classes'?'lhpclass':'lhpsubject';
        $key=$route==='classes'?'classid':'sbjid';
        $column=$route==='classes'?'classname':'sbjname';
        $ids=[];
        for($i=0;$i<2;$i++) {
            $name='QA'.bin2hex(random_bytes(5));
            $data=['csrf_token'=>$token,'id'=>'0','name'=>$name];
            if($route==='subjects'){$data['class_id']=$createdClasses[$i];}
            [$status,,$url]=creation_request('/admin/index.php?route='.$route,$data);
            $q=$db->prepare('SELECT '.$key.' FROM '.$table.' WHERE '.$column.'=?');$q->execute([$name]);$id=(int)$q->fetchColumn();
            if($id){$ids[]=$id;if($route==='classes'){$createdClasses[]=$id;}else{$createdSubjects[]=$id;}}
            creation_check($id>0, $route.': record '.($i+1).' saved separately');
            creation_check($status===302 && parse_url($url,PHP_URL_QUERY)==='route='.$route, $route.': save returns to creation URL');
            [, $html]=creation_request('/admin/index.php?route='.$route);$token=creation_form($html,$route);
        }
        creation_check($ids[0]!==$ids[1],$route.': second creation does not overwrite first');
        [, $html]=creation_request('/admin/index.php?route='.$route.'&id='.$ids[1]);
        creation_check(str_contains($html,$route==='classes'?'>New class</a>':'>New subject</a>'),$route.': editing offers a new-record link');
        $data['id']=$ids[1];$data['name']='QR'.bin2hex(random_bytes(5));
        [$status,,$url]=creation_request('/admin/index.php?route='.$route.'&id='.$ids[1],$data);
        creation_check($status===302 && parse_url($url,PHP_URL_QUERY)==='route='.$route,$route.': edit saves and returns to creation');
        $q=$db->prepare('SELECT '.$column.' FROM '.$table.' WHERE '.$key.'=?');$q->execute([$ids[1]]);
        creation_check($q->fetchColumn()===$data['name'],$route.': edit preserves the existing ID');
        [, $html]=creation_request('/admin/index.php?route='.$route);creation_form($html,$route);
        $data['id']='0';
        [$status,$html]=creation_request('/admin/index.php?route='.$route,$data);
        creation_check($status===200 && str_contains($html,'already exists'),$route.': duplicate creation is still rejected');
    }
} finally {
    foreach(['subject'=>$createdSubjects,'class'=>$createdClasses] as $module=>$ids){
        foreach($ids as $id){
            $q=$db->prepare('DELETE FROM school_workflow_audit WHERE module=? AND record_id=?');$q->execute([$module,(string)$id]);
            $q=$db->prepare($module==='subject'?'DELETE FROM lhpsubject WHERE sbjid=?':'DELETE FROM lhpclass WHERE classid=?');$q->execute([$id]);
        }
    }
    @unlink($cookie);
    echo "Temporary test classes and subjects removed.\n";
}
echo "Class and subject creation HTTP checks passed.\n";
