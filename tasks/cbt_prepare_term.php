<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/classes/autoload.php';
require dirname(__DIR__) . '/config/database.php';

$pdo = database_pdo();
$context = (new CbtService($pdo))->activeContext();
$guard = new ScorebookService($pdo);
$guard->locked('result-config:' . $context['term'], function () use ($pdo, $context, $argv) {
    $query = $pdo->prepare('SELECT * FROM lhpresultconfig WHERE term = ?');
    $query->execute(array($context['term']));
    if ($query->fetch()) { echo "Active-term result settings already exist; no settings changed.\n"; return; }
    $template = $pdo->query('SELECT * FROM lhpresultconfig ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if (!$template) throw new RuntimeException('Create the first result configuration in administration.');
    echo 'Reuse ' . $template['term'] . ' for ' . $context['term'] . ': CA ' . $template['ca_score'] . ', exam ' . $template['exam_score'] . ".\n";
    if (!in_array('--reuse-previous', $argv, true)) { echo "Preview only. Pass --reuse-previous to apply.\n"; return; }
    // The existing form requires calendar/signature fields. Preserve its template values;
    // the school can update them in Result Settings without affecting the score limits.
    unset($template['id']);
    $template['term'] = $context['term'];
    $template['status'] = 0;
    $template['midterm'] = 0;
    $pdo->prepare('INSERT INTO lhpresultconfig (`' . implode('`,`', array_keys($template)) . '`) VALUES (' . implode(',', array_fill(0, count($template), '?')) . ')')->execute(array_values($template));
    echo "Active-term settings created with score entry open. Calendar and signature retain the previous configuration values.\n";
});
