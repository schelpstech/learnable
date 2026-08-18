<?php

require_once dirname(__DIR__) . '/classes/autoload.php';
require_once dirname(__DIR__) . '/config/database.php';

header('Cache-Control: no-store, private');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; media-src 'self' https:; style-src 'self'; script-src 'self'; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'");

try {
    $pdo = database_pdo();
    $attemptService = new CbtAttemptService($pdo);
    $cookie = isset($_COOKIE['learnable_cbt_attempt']) ? (string) $_COOKIE['learnable_cbt_attempt'] : '';
    if (!preg_match('/^(\d+)\.([a-f0-9]{64})$/', $cookie, $match)) {
        throw new RuntimeException('Your secure examination session is unavailable.');
    }
    $state = $attemptService->examState((int) $match[1], $match[2]);
} catch (Throwable $exception) {
    http_response_code(401);
    $examError = $exception->getMessage();
    $state = null;
}

$autosaveInterval = max(3000, (int) app_env('CBT_AUTOSAVE_INTERVAL', 8000));
$initials = '';
if ($state) {
    foreach (preg_split('/\s+/', trim($state['attempt']['fname'])) as $namePart) {
        if ($namePart !== '') $initials .= mb_strtoupper(mb_substr($namePart, 0, 1));
        if (mb_strlen($initials) >= 2) break;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex,nofollow">
    <title><?php echo $state ? htmlspecialchars($state['attempt']['title'], ENT_QUOTES, 'UTF-8') : 'Secure assessment'; ?> · LearnAble</title>
    <link rel="stylesheet" href="../assets/css/cbt.css?v=1">
</head>
<body class="cbt-exam">
<?php if (!$state): ?>
    <main class="cbt-exam-error"><div class="cbt-exam-seal">L</div><h1>Examination session unavailable</h1><p><?php echo htmlspecialchars($examError, ENT_QUOTES, 'UTF-8'); ?></p><a class="cbt-btn cbt-btn--primary" href="app/router.php?pageid=cbt">Return to my assessments</a></main>
<?php else: ?>
    <header class="cbt-exam-header">
        <a class="cbt-exam-brand" href="#" aria-label="LearnAble assessment"><span>L</span><div><strong>LearnAble</strong><small>Secure assessment</small></div></a>
        <div class="cbt-exam-title"><small><?php echo htmlspecialchars($state['attempt']['sbjname'] . ' · ' . $state['attempt']['classname'], ENT_QUOTES, 'UTF-8'); ?></small><strong><?php echo htmlspecialchars($state['attempt']['title'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
        <div class="cbt-exam-candidate"><span class="cbt-candidate-avatar"><?php echo htmlspecialchars($initials ?: 'ST', ENT_QUOTES, 'UTF-8'); ?></span><div><strong><?php echo htmlspecialchars($state['attempt']['fname'], ENT_QUOTES, 'UTF-8'); ?></strong><small><?php echo htmlspecialchars($state['attempt']['learner_id'], ENT_QUOTES, 'UTF-8'); ?></small></div></div>
    </header>
    <main class="cbt-exam-shell" data-cbt-exam>
        <aside class="cbt-exam-sidebar">
            <div class="cbt-timer" data-cbt-timer aria-live="polite"><small>Time remaining</small><strong>--:--</strong><span>Server controlled</span></div>
            <div class="cbt-save-indicator" data-cbt-save-status><i></i><span>All answers saved</span></div>
            <div class="cbt-question-legend"><span><i class="is-answered"></i> Answered</span><span><i class="is-current"></i> Current</span><span><i class="is-flagged"></i> Flagged</span></div>
            <nav class="cbt-question-nav" data-cbt-question-nav aria-label="Question navigation"></nav>
            <button type="button" class="cbt-exam-tool" data-cbt-fullscreen><span>Enter fullscreen</span><small>Optional focus mode</small></button>
            <button type="button" class="cbt-submit-exam" data-cbt-submit>Review & submit</button>
        </aside>
        <section class="cbt-exam-paper">
            <div class="cbt-connectivity" data-cbt-connectivity hidden><strong>You are offline.</strong> Your answer is held safely on this device and will sync when the connection returns. The server timer continues.</div>
            <div class="cbt-exam-receipt" data-cbt-receipt hidden></div>
            <article class="cbt-exam-question" data-cbt-question>
                <header><div><span data-cbt-question-number>Question 1</span><small data-cbt-question-marks>1 mark</small></div><label><input type="checkbox" data-cbt-flag><span>Flag for review</span></label></header>
                <div class="cbt-exam-prompt" data-cbt-prompt></div>
                <div class="cbt-exam-media" data-cbt-media></div>
                <div class="cbt-exam-answer" data-cbt-answer></div>
            </article>
            <footer class="cbt-exam-controls"><button type="button" class="cbt-btn cbt-btn--paper" data-cbt-previous><span aria-hidden="true">←</span> Previous</button><span data-cbt-progress>1 of 1</span><button type="button" class="cbt-btn cbt-btn--primary" data-cbt-next>Next <span aria-hidden="true">→</span></button></footer>
        </section>
    </main>
    <script type="application/json" id="cbt-exam-state"><?php echo json_encode(array('state' => $state, 'autosave_interval' => $autosaveInterval), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
    <script src="../assets/js/cbt-exam.js?v=1" defer></script>
<?php endif; ?>
</body>
</html>
