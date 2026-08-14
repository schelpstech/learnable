<?php

require __DIR__ . '/../config/database.php';

$baseUrl = rtrim((string) app_env('APP_URL', 'http://localhost/learnable'), '/');
$pdo = database_pdo();

function audit_login($url, array $fields)
{
    $cookieFile = tempnam(sys_get_temp_dir(), 'learnable-audit-');
    $handle = curl_init($url);
    curl_setopt_array($handle, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($fields),
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_USERAGENT => 'LearnAble local route audit',
    ));
    $body = curl_exec($handle);
    $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
    $error = curl_error($handle);
    curl_close($handle);

    if ($body === false || $error !== '' || $status >= 500) {
        @unlink($cookieFile);
        throw new RuntimeException('Unable to authenticate the local portal audit.');
    }
    return $cookieFile;
}

function audit_request($url, $cookieFile)
{
    $handle = curl_init($url);
    curl_setopt_array($handle, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_USERAGENT => 'LearnAble local route audit',
    ));
    $body = curl_exec($handle);
    $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
    $effectiveUrl = (string) curl_getinfo($handle, CURLINFO_EFFECTIVE_URL);
    $error = curl_error($handle);
    curl_close($handle);

    if ($body === false || $error !== '') {
        return 'transport error: ' . $error;
    }
    if ($status >= 500 || $status === 0) {
        return 'HTTP ' . $status;
    }
    if (preg_match('#/(?:admin\.php|learn/view/index\.php)(?:\?|$)#', $effectiveUrl)) {
        return 'authentication redirected to ' . $effectiveUrl;
    }
    if (preg_match('/(?:Fatal error|Uncaught (?:Error|Exception)|Parse error|Warning:|Notice:|Deprecated:)/i', $body)) {
        $plainBody = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        preg_match('/(?:Fatal error|Uncaught (?:Error|Exception)|Parse error|Warning:|Notice:|Deprecated:)/i', $plainBody, $match, PREG_OFFSET_CAPTURE);
        $position = isset($match[0][1]) ? (int) $match[0][1] : 0;
        return substr($plainBody, $position, 700);
    }
    return null;
}

$demoPassword = (string) app_env('E2E_DEMO_PASSWORD', '');
$admin = $pdo->query("SELECT dname FROM `123admin` WHERE dname = 'codex_demo_admin' LIMIT 1")->fetch();
$learner = $pdo->query("SELECT uname, classid FROM lhpuser WHERE status = 1 AND uname = 'codex_demo_student' LIMIT 1")->fetch();
$instructor = $pdo->query("SELECT sname FROM lhpstaff WHERE status = 1 AND role = 't' AND sname = 'codex_demo_instructor' LIMIT 1")->fetch();
$activeTerm = $pdo->query('SELECT term FROM lpterm WHERE status = 1 LIMIT 1')->fetchColumn();

if (!$admin || !$learner || !$instructor || !$activeTerm || strlen($demoPassword) < 12) {
    throw new RuntimeException('Run tests/demo_accounts.php and configure E2E_DEMO_PASSWORD before route auditing.');
}

$subjectStatement = $pdo->prepare('SELECT sbjid FROM lhpalloc WHERE classid = ? AND term = ? LIMIT 1');
$subjectStatement->execute(array($learner['classid'], $activeTerm));
$subjectId = $subjectStatement->fetchColumn();

$schemeStatement = $pdo->prepare('SELECT schmid FROM lhpscheme WHERE classname = ? AND term = ? AND status = 1 LIMIT 1');
$schemeStatement->execute(array($learner['classid'], $activeTerm));
$schemeId = $schemeStatement->fetchColumn();

$noteStatement = $pdo->prepare('SELECT noteid FROM lhpnote WHERE term = ? AND status = 1 LIMIT 1');
$noteStatement->execute(array($activeTerm));
$noteId = $noteStatement->fetchColumn();

$taskStatement = $pdo->prepare('SELECT questid FROM lhpquestion WHERE term = ? AND status = 1 LIMIT 1');
$taskStatement->execute(array($activeTerm));
$taskId = $taskStatement->fetchColumn();

$sessions = array(
    'admin' => audit_login($baseUrl . '/entadmin.php', array(
        'but_admn' => '1',
        'aaname' => $admin['dname'],
        'aapwd' => $demoPassword,
    )),
    'learner' => audit_login($baseUrl . '/learn/app/useracces.php', array(
        'log_in' => 'Log in',
        'userid' => $learner['uname'],
        'userpwd' => $demoPassword,
    )),
    'instructor' => audit_login($baseUrl . '/learn/app/useracces.php', array(
        'log_in' => 'Log in',
        'userid' => $instructor['sname'],
        'userpwd' => $demoPassword,
    )),
    'legacy-learner' => audit_login($baseUrl . '/enter.php', array(
        'but_submit' => '1',
        'uname' => $learner['uname'],
        'upass' => $demoPassword,
    )),
    'legacy-instructor' => audit_login($baseUrl . '/entstaff.php', array(
        'but_submit' => '1',
        'uname' => $instructor['sname'],
        'upass' => $demoPassword,
    )),
);

$requests = array();
foreach (array_keys(require __DIR__ . '/../config/admin_routes.php') as $route) {
    $requests[] = array('admin', '/admin/index.php?route=' . rawurlencode($route));
}

$learnerRoutes = array(
    '/learn/app/router.php?pageid=index',
    '/learn/app/router.php?pageid=overview',
    '/learn/app/router.php?pageid=subject',
    '/learn/app/router.php?pageid=payment&instance=bill',
    '/learn/app/router.php?pageid=payment&instance=transaction',
    '/learn/app/router.php?pageid=payment&instance=payment',
    '/learn/app/router.php?pageid=result&instance=select',
    '/learn/app/router.php?pageid=midterm_result&ref=' . rawurlencode($activeTerm),
);
if ($subjectId) {
    $learnerRoutes[] = '/learn/app/router.php?pageid=note&subjectid=' . rawurlencode($subjectId);
    $learnerRoutes[] = '/learn/app/router.php?pageid=task&subjectid=' . rawurlencode($subjectId);
    $learnerRoutes[] = '/learn/app/router.php?pageid=work&subjectid=' . rawurlencode($subjectId);
}
if ($schemeId) {
    $learnerRoutes[] = '/learn/app/router.php?pageid=scheme&ref=' . rawurlencode($schemeId);
}
if ($noteId) {
    $learnerRoutes[] = '/learn/app/router.php?pageid=note&ref=' . rawurlencode($noteId);
}
if ($taskId) {
    $learnerRoutes[] = '/learn/app/router.php?pageid=task&ref=' . rawurlencode($taskId);
}
foreach ($learnerRoutes as $route) {
    $requests[] = array('learner', $route);
}

$instructorRoutes = array(
    '/learn/app/router.php?pageid=index',
    '/learn/app/router.php?pageid=overview',
    '/learn/app/router.php?pageid=subject',
    '/learn/app/router.php?pageid=class_manager',
    '/learn/app/router.php?pageid=scoresheet',
    '/learn/app/router.php?pageid=resources&item=add_topic',
    '/learn/app/router.php?pageid=resources&item=add_note',
    '/learn/app/router.php?pageid=resources&item=add_task',
    '/learn/app/router.php?pageid=resources&item=add_cbt',
    '/learn/app/router.php?pageid=manage_learner&instance=' . rawurlencode($learner['uname']),
);
foreach ($instructorRoutes as $route) {
    $requests[] = array('instructor', $route);
}
$requests[] = array('legacy-learner', '/learn/app/router.php?pageid=index');
$requests[] = array('legacy-instructor', '/learn/app/router.php?pageid=index');

$failures = array();
try {
    foreach ($requests as $request) {
        $problem = audit_request($baseUrl . $request[1], $sessions[$request[0]]);
        if ($problem !== null) {
            $failures[] = $request[0] . ' ' . $request[1] . ' -> ' . $problem;
        }
    }
} finally {
    foreach ($sessions as $cookieFile) {
        @unlink($cookieFile);
    }
}

if ($failures) {
    echo "Live route failures:\n" . implode("\n", $failures) . "\n";
    exit(1);
}

echo 'Live portal audit passed: ' . count($requests) . " authenticated pages checked.\n";
