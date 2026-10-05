<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
$pdo = database_pdo();
$base = rtrim((string) app_env('APP_URL', 'http://localhost/learnable'), '/');
$password = (string) app_env('E2E_DEMO_PASSWORD', '');
if (strlen($password) < 12) throw new RuntimeException('Configure the existing demo account password before this HTTP test.');
$learner = 'codex_demo_std';
$q = $pdo->prepare("SELECT a.id FROM cbt_assessments a WHERE (SELECT COUNT(*) FROM cbt_attempts t WHERE t.assessment_id=a.id AND t.learner_id=? AND t.status<>'cancelled') >= a.max_attempts AND NOT EXISTS (SELECT 1 FROM cbt_attempts t WHERE t.assessment_id=a.id AND t.learner_id=? AND t.status='in_progress') ORDER BY a.id LIMIT 1");
$q->execute(array($learner, $learner));
$id = $q->fetchColumn();
if (!$id) throw new RuntimeException('An exhausted demo assessment is required; this test never creates a school attempt.');
$cookie = tempnam(sys_get_temp_dir(), 'cbt-start-');
$request = function ($path, $fields = null) use ($base, $cookie) {
    $curl = curl_init($base . $path);
    curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER=>true, CURLOPT_FOLLOWLOCATION=>false, CURLOPT_CONNECTTIMEOUT=>5, CURLOPT_TIMEOUT=>25, CURLOPT_COOKIEFILE=>$cookie, CURLOPT_COOKIEJAR=>$cookie));
    if ($fields !== null) { curl_setopt($curl, CURLOPT_POST, true); curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($fields)); }
    $body = curl_exec($curl); $info = curl_getinfo($curl); $error = curl_error($curl); curl_close($curl);
    if ($body === false || $error !== '') throw new RuntimeException('HTTP request failed: ' . $error);
    return array($body, $info);
};
try {
    $request('/learn/app/useracces.php', array('log_in'=>'Log in','userid'=>$learner,'userpwd'=>$password));
    list($body, $info) = $request('/learn/app/router.php?pageid=cbt');
    if ($info['http_code'] !== 200 || !preg_match('/name="csrf_token" value="([a-f0-9]{64})"/', $body, $token)) throw new RuntimeException('The learner session or portal CSRF token was unavailable.');
    $count = $pdo->query('SELECT COUNT(*) FROM cbt_attempts')->fetchColumn();
    list($body, $info) = $request('/learn/app/cbt_action.php', array('cbt_action'=>'start_attempt','assessment_id'=>$id,'csrf_token'=>$token[1]));
    if ($info['http_code'] !== 302 || strpos($info['redirect_url'], 'pageid=cbt_builder') !== false || !str_ends_with($info['redirect_url'], 'router.php?pageid=cbt')) throw new RuntimeException('Failed learner start did not return to My Assessments.');
    list($body, $info) = $request('/learn/app/router.php?pageid=cbt');
    if ($info['http_code'] !== 200 || !str_contains($body, 'cbt-alert--error') || str_contains($body, 'Access denied')) throw new RuntimeException('The learner did not receive the actionable error on their assessment page.');
    if ($count !== $pdo->query('SELECT COUNT(*) FROM cbt_attempts')->fetchColumn()) throw new RuntimeException('The exhausted-start check created an unexpected attempt.');
    echo "PASS: failed learner start returns to My Assessments with its error, without creating an attempt.\n";
} finally { @unlink($cookie); }
