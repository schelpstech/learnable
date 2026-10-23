<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../classes/autoload.php';
$db = database_pdo();
$base = rtrim((string)app_env('APP_URL', 'http://localhost/learnable'), '/');
if (!in_array(parse_url($base, PHP_URL_HOST), ['localhost','127.0.0.1','::1'], true)) throw new RuntimeException('Use a local development site.');
$adminSecret = (string)app_env('E2E_DEMO_PASSWORD', '');
if (strlen($adminSecret) < 12) throw new RuntimeException('Configure the existing demo administrator.');
$suffix = bin2hex(random_bytes(5));
$names = ['teacher'=>'qpt'.$suffix, 'other'=>'qpo'.$suffix, 'registry'=>'qpr'.$suffix, 'bursary'=>'qpb'.$suffix, 'learner'=>'qpl'.$suffix, 'outside'=>'qpx'.$suffix];
$secret = bin2hex(random_bytes(16));
$classes = []; $pictures = []; $jars = []; $oversize = tempnam(sys_get_temp_dir(), 'passport-size-');
$browserManifest = null;
foreach (['admin','teacher','other','registry','bursary','learner','guest'] as $role) $jars[$role] = tempnam(sys_get_temp_dir(), 'passport-'.$role.'-');
function passport_check($ok, $message) { if (!$ok) throw new RuntimeException('FAIL: '.$message); echo 'PASS: '.$message."\n"; }
function passport_http($role, $path, $fields = null) {
    global $base, $jars;
    $c = curl_init($base.$path);
    $options = [CURLOPT_RETURNTRANSFER=>true, CURLOPT_FOLLOWLOCATION=>false, CURLOPT_COOKIEFILE=>$jars[$role], CURLOPT_COOKIEJAR=>$jars[$role], CURLOPT_CONNECTTIMEOUT=>5, CURLOPT_TIMEOUT=>30];
    if ($fields !== null) {
        $multipart = count(array_filter($fields, static function ($value) { return $value instanceof CURLFile; })) > 0;
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $multipart ? $fields : http_build_query($fields);
    }
    curl_setopt_array($c, $options);
    $body = curl_exec($c); $status = curl_getinfo($c, CURLINFO_RESPONSE_CODE);
    if ($body === false) throw new RuntimeException(curl_error($c));
    curl_close($c);
    if ($status >= 500 || preg_match('/Fatal error|Warning:|Uncaught /', $body)) throw new RuntimeException('Server error on '.$path.': '.substr(strip_tags($body),0,600));
    return [$status, $body];
}
function passport_token($body) {
    preg_match('/name="csrf_token" value="([a-f0-9]+)"/', $body, $m);
    if (empty($m[1])) throw new RuntimeException('Missing CSRF token.');
    return $m[1];
}
function passport_row($name) { global $db; $q=$db->prepare('SELECT * FROM lhpuser WHERE uname=?'); $q->execute([$name]); return $q->fetch(PDO::FETCH_ASSOC); }
function passport_upload($role, $token, $target, $file = null) {
    global $pictures;
    $fields = ['csrf_token'=>$token, 'learner_ref'=>$target];
    if ($file !== null) $fields['passport'] = new CURLFile($file, 'image/jpeg', 'passport.jpg');
    [$status, $body] = passport_http($role, '/learner_passport.php', $fields);
    $data = json_decode($body, true);
    passport_check(is_array($data) && isset($data['ok']), 'upload returns structured feedback');
    if (!empty($data['picture'])) $pictures[]=$data['picture'];
    return [$status, $data];
}
try {
    $term = (new LearnerProfileService($db))->activeTerm();
    foreach (['QP'.$suffix, 'QX'.$suffix] as $name) {
        $db->prepare('INSERT INTO lhpclass (classname) VALUES (?)')->execute([$name]); $classes[]=(int)$db->lastInsertId();
    }
    foreach (['teacher'=>'t','other'=>'t','registry'=>'r','bursary'=>'b'] as $key=>$role) {
        $db->prepare('INSERT INTO lhpstaff (sname,staffname,spwd,sfone,semail,status,role) VALUES (?,?,?,?,?,1,?)')
            ->execute([$names[$key], 'QA Passport '.$key, password_hash($secret, PASSWORD_DEFAULT), '', '', $role]);
    }
    foreach (['learner'=>0, 'outside'=>1] as $key=>$index) {
        $db->prepare('INSERT INTO lhpuser (uname,fname,upwd,email,classid,status,picture) VALUES (?,?,?,?,?,1,?)')
            ->execute([$names[$key], 'QA Passport Learner', password_hash($secret, PASSWORD_DEFAULT), '', $classes[$index], '']);
    }
    $db->prepare('INSERT INTO lhpclassalloc (classid,tutorid,term) VALUES (?,?,?)')->execute([$classes[0], $names['teacher'], $term]);
    $before = passport_row($names['learner']);
    $image = __DIR__.'/../images/learnable-landing-classroom-v3.jpg';
    [$status] = passport_upload('guest', 'invalid', $names['learner'], $image);
    passport_check($status === 403 && passport_row($names['learner']) === $before, 'anonymous upload is rejected without a data change');
    passport_http('admin', '/entadmin.php', ['but_admn'=>'1','aaname'=>'codex_demo_admin','aapwd'=>$adminSecret]);
    foreach (['teacher','other','learner'] as $role) passport_http($role, '/learn/app/useracces.php', ['log_in'=>'Log in', 'userid'=>$names[$role], 'userpwd'=>$secret]);
    foreach (['registry','bursary'] as $role) passport_http($role, '/entstaff.php', ['but_submit'=>'1', 'uname'=>$names[$role], 'upass'=>$secret]);
    $path='/learn/app/router.php?pageid=manage_learner&instance='.$names['learner'];
    [$status,$body] = passport_http('teacher', $path);
    passport_check($status===200 && str_contains($body,'learner-passport-form') && str_contains($body,'student-placeholder.svg'), 'class teacher sees a dedicated passport form and missing-photo icon');
    $teacherToken = passport_token($body);
    [$status] = passport_http('other', $path);
    passport_check($status===403, 'another teacher cannot open the learner editor');
    [$status] = passport_upload('teacher', 'invalid', $names['learner'], $image);
    passport_check($status===403 && passport_row($names['learner'])===$before, 'invalid CSRF preserves the complete learner record');
    [$status] = passport_upload('teacher', $teacherToken, $names['outside'], $image);
    passport_check($status===403 && passport_row($names['outside'])['picture']==='', 'class teacher cannot upload outside the assigned class');
    foreach (['other','learner','bursary'] as $role) {
        [$status] = passport_upload($role, $teacherToken, $names['learner'], $image);
        passport_check($status===403, $role.' cannot use another account\'s passport controls');
    }
    [$status] = passport_upload('teacher', $teacherToken, $names['learner']);
    passport_check($status===422, 'missing file has actionable feedback');
    [$status] = passport_upload('teacher', $teacherToken, $names['learner'], __FILE__);
    passport_check($status===422 && passport_row($names['learner'])===$before, 'a disguised PHP file is rejected without changing learner details');
    file_put_contents($oversize, file_get_contents($image).str_repeat('x', 6*1024*1024));
    [$status] = passport_upload('teacher', $teacherToken, $names['learner'], $oversize);
    passport_check(in_array($status,[403,422],true) && passport_row($names['learner'])===$before, 'oversized uploads preserve the previous record');
    [$status,$saved] = passport_upload('teacher', $teacherToken, $names['learner'], $image);
    passport_check($status===200 && $saved['ok'], 'class teacher uploads without completing phone, email or date of birth');
    $after = passport_row($names['learner']);
    $expected = $before; $expected['picture']=$saved['picture'];
    // The legacy rectime column automatically tracks row updates.
    $expected['rectime']=$after['rectime'];
    passport_check($after===$expected && is_file(__DIR__.'/../learn/asset/img/passport/'.$saved['picture']), 'only the picture field changes and its file exists in the shared report folder');
    [$status] = passport_http('guest','/'.$saved['path']); passport_check($status===200, 'saved image URL is accessible to profile and report pages');
    [$status,$body] = passport_http('teacher',$path);
    passport_check($status===200 && str_contains($body,$saved['picture']), 'teacher editor displays the uploaded photograph on reload');
    passport_check(str_contains($body,'Back to student list') && str_contains($body,'pageid=class_learners&amp;class_id='.$classes[0]), 'passport editor links back to the selected class student list');
    $listPath='/learn/app/router.php?pageid=class_learners&class_id='.$classes[0];
    [$status,$body]=passport_http('teacher',$listPath);
    passport_check($status===200 && str_contains($body,$names['learner']) && str_contains($body,$saved['picture']), 'returning to the student list displays the saved passport');
    [$status]=passport_http('other',$listPath);
    passport_check($status===403,'another teacher cannot open the student list');
    $details=['action'=>'modify_learner','learner_ref'=>$names['learner'],'fullname'=>'QA Updated Learner','email'=>'','phone'=>'','gender'=>'','date_of_birth'=>'','csrf_token'=>$teacherToken];
    [$status,$body]=passport_http('teacher','/learn/app/ajax_query.php',$details);
    passport_check($status===200 && (json_decode($body,true)['ok']??false) && passport_row($names['learner'])['picture']===$saved['picture'], 'separate details save accepts optional fields and preserves the passport');
    $details['learner_ref']=$names['outside'];
    [$status]=passport_http('teacher','/learn/app/ajax_query.php',$details);
    passport_check($status===403, 'legacy profile action also enforces class ownership');
    $details['learner_ref']=$names['learner']; $details['csrf_token']='invalid';
    [$status]=passport_http('teacher','/learn/app/ajax_query.php',$details);
    passport_check($status===403, 'legacy profile action requires CSRF');
    foreach (['admin','registry'] as $role) {
        [$status,$body] = passport_http($role,'/admin/stdedit.php?unam='.$names['learner']);
        passport_check($status===200 && str_contains($body,'learner-passport-form') && str_contains($body,'https://schelps.com.ng'), $role.' learner editor provides passport controls and keeps the SCHELPS backlink');
        $token=passport_token($body);
        [$status,$replacement]=passport_upload($role,$token,$names['learner'],$image);
        passport_check($status===200 && $replacement['ok'] && $replacement['picture']!==$saved['picture'], $role.' can replace a learner passport with a fresh image URL');
        $saved=$replacement;
    }
    if (in_array('--browser', $argv, true)) {
        $browserManifest = tempnam(sys_get_temp_dir(), 'passport-browser-');
        file_put_contents($browserManifest, json_encode(['base'=>$base, 'learner'=>$names['learner'], 'jars'=>$jars, 'image'=>$image, 'pictures'=>[], 'class_id'=>$classes[0], 'teacher'=>$names['teacher'], 'replacement'=>$names['other']]));
        $process = proc_open(['node', __DIR__.'/learner_passport_browser_test.js', $browserManifest], [0=>STDIN,1=>STDOUT,2=>STDERR], $pipes);
        if (!is_resource($process) || proc_close($process) !== 0) throw new RuntimeException('Passport browser checks failed.');
        $saved['picture']=passport_row($names['learner'])['picture'];
    }
    [$status,$body]=passport_http('admin','/admin/index.php?route=classes');
    passport_check($status===200 && str_contains($body,'>Edit class teacher</a>') && str_contains($body,'>Edit class name</a>'),'class register uses explicit edit button labels');
    $teacherPage='/admin/allocateclass.php?classid='.$classes[0];
    [$status,$body]=passport_http('admin',$teacherPage);
    passport_check($status===200 && str_contains($body,'name="teacher_id"') && str_contains($body,'value="'.$names['teacher'].'" selected'), 'class teacher editor selects the current teacher');
    $adminToken=passport_token($body);
    $assignment=['csrf_token'=>'invalid','class_id'=>$classes[0],'teacher_id'=>$names['other'],'term'=>$term];
    passport_http('admin','/admin/tutallocated.php',$assignment);
    [$status,$body]=passport_http('admin',$teacherPage);
    passport_check($status===200 && str_contains($body,'session token expired') && str_contains($body,'role="alert"'),'failed class teacher changes show feedback in the editor');
    // Reproduce duplicate rows left by the production-era insert-only controller.
    $db->prepare('INSERT INTO lhpclassalloc (classid,tutorid,term) VALUES (?,?,?)')->execute([$classes[0],$names['teacher'],$term]);
    $db->prepare('INSERT INTO lhpclassalloc (classid,tutorid,term) VALUES (?,?,?)')->execute([$classes[0],$names['teacher'],'QA prior term']);
    $assignment['csrf_token']=$adminToken;
    [$status]=passport_http('admin','/admin/tutallocated.php',$assignment);
    passport_check($status===302,'class teacher save redirects after success');
    [$status,$body]=passport_http('admin','/admin/index.php?route=classes');
    passport_check($status===200 && str_contains($body,'Class teacher updated successfully.') && str_contains($body,'QA Passport other'),'class register shows save feedback and the replacement teacher');
    $q=$db->prepare('SELECT tutorid FROM lhpclassalloc WHERE classid=? AND term=?');$q->execute([$classes[0],$term]);
    passport_check($q->fetchAll(PDO::FETCH_COLUMN)===[$names['other']],'teacher change leaves exactly one assignment for the active term');
    $q->execute([$classes[0],'QA prior term']);
    passport_check($q->fetchAll(PDO::FETCH_COLUMN)===[$names['teacher']],'teacher change preserves the previous term');
    [$status]=passport_http('other',$listPath);passport_check($status===200,'the replacement teacher can open the student list immediately');
    [$status]=passport_http('teacher',$listPath);passport_check($status===403,'the previous teacher loses class access immediately');
    [$status,$body]=passport_http('registry',$teacherPage);$assignment['csrf_token']=passport_token($body);$assignment['teacher_id']=$names['teacher'];
    passport_http('registry','/admin/tutallocated.php',$assignment);
    [$status,$body]=passport_http('registry','/admin/index.php?route=classes');
    passport_check($status===200 && str_contains($body,'Class teacher updated successfully.'),'Registry Admin can change the class teacher with visible feedback');
    $db->prepare('DELETE FROM lhpclassalloc WHERE tutorid=?')->execute([$names['teacher']]);
    [$status] = passport_upload('teacher',$teacherToken,$names['learner'],$image);
    passport_check($status===403, 'removing a class allocation immediately revokes passport upload access');
    passport_check(passport_row($names['learner'])['picture']===$saved['picture'], 'rejected uploads preserve the latest saved photograph');
    [$status] = passport_http('admin','/admin/stdedit.php?unam=missing-'.$suffix);
    passport_check($status===404, 'missing learner receives a clear not-found response');
} finally {
    if ($browserManifest && is_file($browserManifest)) {
        $browserData=json_decode(file_get_contents($browserManifest),true);
        $pictures=array_merge($pictures,$browserData['pictures']??[]);
        @unlink($browserManifest);
    }
    foreach (['learner','outside'] as $key) {
        $row=passport_row($names[$key]);
        if ($row && !empty($row['picture'])) $pictures[]=$row['picture'];
        $db->prepare('DELETE FROM lhpuser WHERE uname=?')->execute([$names[$key]]);
    }
    foreach (['teacher','other','registry','bursary'] as $key) {
        $db->prepare('DELETE FROM lhpclassalloc WHERE tutorid=?')->execute([$names[$key]]);
        $db->prepare('DELETE FROM lhpstaff WHERE sname=?')->execute([$names[$key]]);
    }
    foreach ($names as $name) $db->prepare('DELETE FROM log WHERE uname=?')->execute([$name]);
    foreach ($classes as $class) {
        $db->prepare("DELETE FROM school_workflow_audit WHERE module='class_teacher' AND record_id=?")->execute([(string)$class]);
        $db->prepare('DELETE FROM lhpclass WHERE classid=?')->execute([$class]);
    }
    foreach (array_unique($pictures) as $name) if (preg_match('/^learner-[a-f0-9]{24}\.(jpg|png)$/D',$name)) @unlink(__DIR__.'/../learn/asset/img/passport/'.$name);
    foreach ($jars as $jar) @unlink($jar);
    @unlink($oversize);
    echo "Temporary learners, staff, class allocations and photographs removed.\n";
}
echo "Learner passport HTTP checks passed.\n";
