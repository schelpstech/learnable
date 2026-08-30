<?php

include __DIR__ . '/conf.php';
require_once dirname(__DIR__) . '/classes/autoload.php';
require_once dirname(__DIR__) . '/config/database.php';

if (!function_exists('admin_cbt_h')) {
    function admin_cbt_h($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
    function admin_cbt_label($value) { return ucwords(str_replace('_', ' ', (string) $value)); }
}

$pdo = database_pdo();
$service = new CbtService($pdo);
$attemptService = new CbtAttemptService($pdo);
$resultService = new CbtResultService($pdo);
$actorId = (string) $_SESSION['unamed'];
$csrf = CbtSecurity::csrfToken('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assessmentId = isset($_POST['assessment_id']) ? (int) $_POST['assessment_id'] : 0;
    $attemptId = isset($_POST['attempt_id']) ? (int) $_POST['attempt_id'] : 0;
    try {
        CbtSecurity::requireCsrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null, 'admin');
        $action = isset($_POST['cbt_action']) ? (string) $_POST['cbt_action'] : '';
        if ($action === 'change_status') {
            $status = isset($_POST['status']) ? (string) $_POST['status'] : '';
            $service->setAssessmentStatus($assessmentId, $status, $actorId, true, isset($_POST['reason']) ? $_POST['reason'] : 'Administrative moderation.');
            $message = 'Assessment status changed to ' . admin_cbt_label($status) . '.';
        } elseif ($action === 'publish_results') {
            $count = $attemptService->publishResults($assessmentId, $actorId, true);
            $message = $count . ' completed result(s) published.';
        } elseif ($action === 'transfer_scores') {
            $outcome = $resultService->transferAssessment($assessmentId, $actorId, true);
            $message = $outcome['transferred'] . ' score(s) transferred; ' . $outcome['skipped'] . ' duplicate(s) skipped.';
        } elseif ($action === 'add_time') {
            $attemptService->addExtraTime($attemptId, $_POST['minutes'], $actorId, true, $_POST['reason']);
            $message = 'Additional time granted and recorded in the audit trail.';
        } elseif ($action === 'reopen_attempt') {
            $attemptService->reopenAttempt($attemptId, $_POST['minutes'], $actorId, true, $_POST['reason']);
            $message = 'Attempt reopened. The learner can resume it from My Assessments.';
        } elseif ($action === 'mark_answer') {
            $attemptService->markAnswer($_POST['answer_id'], $_POST['marks'], $_POST['comment'], $_POST['reason'], $actorId, true);
            $message = 'Marking decision saved with its original value and reason.';
        } else {
            throw new InvalidArgumentException('Unknown CBT administration action.');
        }
        $_SESSION['admin_cbt_flash'] = array('type' => 'success', 'message' => $message);
    } catch (Throwable $exception) {
        $_SESSION['admin_cbt_flash'] = array('type' => 'error', 'message' => $exception->getMessage());
    }
    $target = 'index.php?route=cbt';
    if ($assessmentId > 0) $target .= '&assessment_id=' . rawurlencode((string) $assessmentId);
    if ($attemptId > 0 && (isset($_POST['cbt_action']) && $_POST['cbt_action'] === 'mark_answer')) $target .= '&attempt_id=' . rawurlencode((string) $attemptId);
    header('Location: ' . $target);
    exit;
}

$flash = isset($_SESSION['admin_cbt_flash']) && is_array($_SESSION['admin_cbt_flash']) ? $_SESSION['admin_cbt_flash'] : array();
unset($_SESSION['admin_cbt_flash']);
$assessmentId = isset($_GET['assessment_id']) && ctype_digit((string) $_GET['assessment_id']) ? (int) $_GET['assessment_id'] : null;
$attemptId = isset($_GET['attempt_id']) && ctype_digit((string) $_GET['attempt_id']) ? (int) $_GET['attempt_id'] : null;
$assessments = $service->adminAssessments();
$assessment = null;
$analytics = null;
$attemptRows = array();
$script = null;
$transferPreview = null;
$transferError = '';
if ($assessmentId) {
    $assessment = $service->assessment($assessmentId);
    $analytics = $service->analytics($assessmentId, $actorId, true);
    $attemptRows = $attemptService->attemptsForAssessment($assessmentId, $actorId, true);
    if ($attemptId) $script = $attemptService->scriptForMarking($attemptId, $actorId, true);
    try { $transferPreview = $resultService->previewAssessmentTransfer($assessmentId, $actorId, true); }
    catch (Throwable $exception) { $transferError = $exception->getMessage(); }
}
$pendingCount = count(array_filter($assessments, function ($item) { return $item['status'] === 'pending_approval'; }));
$activeCount = count(array_filter($assessments, function ($item) use ($service) { return $service->effectiveStatus($item) === 'active'; }));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CBT & assessments · LearnAble</title>
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/admin-modern.css?v=1">
    <link rel="stylesheet" href="../assets/css/cbt.css?v=2">
</head>
<body class="cbt-admin">
<?php include __DIR__ . '/nav.html'; ?>
<main class="cbt-admin-main">
    <?php if ($flash): ?><div class="cbt-alert cbt-alert--<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>"><i class="fa fa-info-circle"></i><span><?php echo admin_cbt_h($flash['message']); ?></span></div><?php endif; ?>
    <header class="cbt-page-heading">
        <div><p class="cbt-kicker">School assessments</p><h1>CBT & assessments</h1><p>Review teachers’ assessments, follow learner progress and publish results when they are ready.</p></div>
        <?php if ($assessment): ?><div class="cbt-heading-actions"><a class="cbt-btn cbt-btn--paper" href="index.php?route=cbt">All assessments</a></div><?php endif; ?>
    </header>

    <?php if (!$assessment): ?>
        <section class="cbt-stat-row"><article class="cbt-stat"><span class="cbt-stat__mark">01</span><div><strong><?php echo count($assessments); ?></strong><small>assessment records</small></div></article><article class="cbt-stat"><span class="cbt-stat__mark">02</span><div><strong><?php echo $pendingCount; ?></strong><small>awaiting approval</small></div></article><article class="cbt-stat"><span class="cbt-stat__mark">03</span><div><strong><?php echo $activeCount; ?></strong><small>active now</small></div></article></section>
        <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">A</span><h2>School assessment register</h2></div><p><?php echo count($assessments); ?> records</p></div><div class="cbt-admin-register"><div class="cbt-admin-register__head"><span>Assessment</span><span>Teacher</span><span>Schedule</span><span>Scripts</span><span>Status</span><span></span></div><?php foreach ($assessments as $item): ?><div><span><strong><?php echo admin_cbt_h($item['title']); ?></strong><small><?php echo admin_cbt_h($item['classname'] . ' · ' . $item['sbjname'] . ' · ' . $item['topic']); ?></small></span><span><?php echo admin_cbt_h($item['staffname'] ?: $item['teacher_id']); ?></span><span><?php echo date('j M Y, g:i a', strtotime($item['start_at'])); ?><small><?php echo (int) $item['duration_minutes']; ?> minutes</small></span><span><?php echo (int) $item['submitted_count']; ?> / <?php echo (int) $item['attempt_count']; ?></span><span><span class="cbt-status cbt-status--<?php echo admin_cbt_h($item['status']); ?>"><?php echo admin_cbt_h(admin_cbt_label($item['status'])); ?></span></span><span><a class="cbt-text-link" href="index.php?route=cbt&amp;assessment_id=<?php echo (int) $item['id']; ?>">Open record <i class="fa fa-arrow-right"></i></a></span></div><?php endforeach; ?></div><?php if (!$assessments): ?><div class="cbt-empty"><h3>No CBT records yet</h3><p>Teacher-created assessments will appear here for moderation and approval.</p></div><?php endif; ?></section>
    <?php else: ?>
        <nav class="cbt-breadcrumb"><a href="index.php?route=cbt">Assessment register</a><i class="fa fa-chevron-right"></i><span><?php echo admin_cbt_h($assessment['title']); ?></span></nav>
        <section class="cbt-marking-hero"><div><span class="cbt-status cbt-status--<?php echo admin_cbt_h($assessment['status']); ?>"><?php echo admin_cbt_h(admin_cbt_label($assessment['status'])); ?></span><h2><?php echo admin_cbt_h($assessment['title']); ?></h2><p><?php echo admin_cbt_h($assessment['classname'] . ' · ' . $assessment['sbjname'] . ' · ' . $assessment['topic'] . ' · ' . $assessment['staffname']); ?></p></div><div><a class="cbt-btn cbt-btn--paper" href="../learn/app/cbt_export.php?assessment_id=<?php echo (int) $assessment['id']; ?>">Export CSV</a><strong><?php echo admin_cbt_h(number_format((float) $assessment['total_marks'], 1)); ?></strong><span>marks</span></div></section>
        <section class="cbt-stat-row cbt-stat-row--six"><article class="cbt-stat"><div><strong><?php echo (int) $analytics['summary']['eligible']; ?></strong><small>eligible</small></div></article><article class="cbt-stat"><div><strong><?php echo (int) $analytics['summary']['attempts']; ?></strong><small>attempted</small></div></article><article class="cbt-stat"><div><strong><?php echo (int) $analytics['summary']['submitted']; ?></strong><small>submitted</small></div></article><article class="cbt-stat"><div><strong><?php echo (int) $analytics['summary']['absent']; ?></strong><small>absent</small></div></article><article class="cbt-stat"><div><strong><?php echo admin_cbt_h($analytics['summary']['average_score'] !== null ? $analytics['summary']['average_score'] : '—'); ?></strong><small>average</small></div></article><article class="cbt-stat"><div><strong><?php echo admin_cbt_h($analytics['summary']['pass_rate']); ?>%</strong><small>pass rate</small></div></article></section>
        <div class="cbt-marking-layout">
            <main>
                <?php if ($script): ?>
                    <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">S</span><h2><?php echo admin_cbt_h($script['attempt']['fname']); ?> · permanent script</h2></div><p><?php echo admin_cbt_h($script['attempt']['submission_ref']); ?></p></div><ol class="cbt-script-list"><?php foreach ($script['questions'] as $question): ?><li><header><span>Question <?php echo (int) $question['display_order']; ?> · <?php echo admin_cbt_h(admin_cbt_label($question['question_type'])); ?></span><strong><?php echo admin_cbt_h(number_format((float) $question['final_marks'], 1)); ?> / <?php echo admin_cbt_h(number_format((float) $question['marks_available'], 1)); ?></strong></header><div class="cbt-script-question"><?php echo $question['prompt_snapshot']; ?></div><div class="cbt-script-answer"><small>Learner answer</small><pre><?php echo admin_cbt_h(is_array($question['answer']) ? implode(', ', $question['answer']) : ($question['answer'] ?: 'Not answered')); ?></pre></div><?php if (!empty($question['answer_id'])): ?><form method="post" class="cbt-inline-mark-form"><input type="hidden" name="csrf_token" value="<?php echo admin_cbt_h($csrf); ?>"><input type="hidden" name="cbt_action" value="mark_answer"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><input type="hidden" name="attempt_id" value="<?php echo (int) $script['attempt']['id']; ?>"><input type="hidden" name="answer_id" value="<?php echo (int) $question['answer_id']; ?>"><label><span>Mark</span><input type="number" name="marks" min="0" max="<?php echo admin_cbt_h($question['marks_available']); ?>" step="0.25" value="<?php echo admin_cbt_h($question['final_marks']); ?>"></label><label><span>Feedback</span><input type="text" name="comment" value="<?php echo admin_cbt_h($question['marker_comment']); ?>"></label><label><span>Override reason</span><input type="text" name="reason" placeholder="Required when changed"></label><button class="cbt-btn cbt-btn--small cbt-btn--paper">Save</button></form><?php endif; ?></li><?php endforeach; ?></ol></section>
                <?php else: ?>
                    <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">S</span><h2>Candidate monitoring</h2></div><p><?php echo count($attemptRows); ?> attempts</p></div><div class="cbt-script-table"><div class="cbt-script-table__head"><span>Learner</span><span>Status</span><span>Score</span><span>Activity</span><span>Review</span></div><?php foreach ($attemptRows as $row): ?><div><span><strong><?php echo admin_cbt_h($row['fname']); ?></strong><small><?php echo admin_cbt_h($row['learner_id']); ?> · attempt <?php echo (int) $row['attempt_no']; ?></small></span><span><span class="cbt-status cbt-status--<?php echo admin_cbt_h($row['status']); ?>"><?php echo admin_cbt_h(admin_cbt_label($row['status'])); ?></span></span><span><?php echo $row['submitted_at'] ? admin_cbt_h(number_format((float) $row['total_score'], 1)) : '—'; ?></span><span><?php echo (int) $row['warning_count']; ?> flags</span><span><a class="cbt-text-link" href="index.php?route=cbt&amp;assessment_id=<?php echo (int) $assessment['id']; ?>&amp;attempt_id=<?php echo (int) $row['id']; ?>">Open</a></span></div><?php endforeach; ?></div></section>
                <?php endif; ?>
                <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">Q</span><h2>Question diagnostics</h2></div><p>Human review recommended below 30%</p></div><div class="cbt-question-analysis"><?php foreach ($analytics['questions'] as $index => $question): ?><article><span><?php echo $index + 1; ?></span><div><h3><?php echo admin_cbt_h(mb_substr(strip_tags($question['prompt_snapshot']), 0, 130)); ?></h3><p><?php echo admin_cbt_h(admin_cbt_label($question['question_type'])); ?> · <?php echo admin_cbt_h($question['responses']); ?> responses</p></div><strong><?php echo admin_cbt_h($question['full_mark_rate']); ?>%</strong></article><?php endforeach; ?></div></section>
            </main>
            <aside>
                <section class="cbt-board cbt-sticky-panel"><div class="cbt-board__title"><div><span class="cbt-section-number">M</span><h2>Moderation</h2></div></div><div class="cbt-admin-actions"><?php if ($assessment['status'] === 'pending_approval'): ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo admin_cbt_h($csrf); ?>"><input type="hidden" name="cbt_action" value="change_status"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><input type="hidden" name="status" value="approved"><label><span>Approval note</span><input name="reason" value="Academic schedule and paper reviewed." required></label><button class="cbt-btn cbt-btn--primary cbt-btn--block">Approve assessment</button></form><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo admin_cbt_h($csrf); ?>"><input type="hidden" name="cbt_action" value="change_status"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><label><span>Controlled status change</span><select name="status"><option value="paused">Pause assessment</option><option value="scheduled">Reschedule / activate</option><option value="cancelled">Cancel assessment</option><option value="archived">Archive record</option></select></label><label><span>Reason</span><textarea name="reason" rows="3" required></textarea></label><button class="cbt-btn cbt-btn--paper cbt-btn--block">Apply with notice</button></form></div></section>
                <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">P</span><h2>Publication</h2></div></div><p class="cbt-panel-note">Publish only after manual marking is complete. Learners never see correct answers before the assessment closes.</p><form method="post" class="cbt-admin-panel-form"><input type="hidden" name="csrf_token" value="<?php echo admin_cbt_h($csrf); ?>"><input type="hidden" name="cbt_action" value="publish_results"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><button class="cbt-btn cbt-btn--primary cbt-btn--block" <?php echo !in_array($assessment['status'], array('approved', 'published'), true) ? 'disabled' : ''; ?>>Publish checked results</button></form></section>
                <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">R</span><h2>Score transfer</h2></div></div><?php if ($transferPreview): ?><p class="cbt-panel-note"><?php echo admin_cbt_h($transferPreview['mapping']['formula']); ?></p><div class="cbt-transfer-preview"><span>Published</span><strong><?php echo count($transferPreview['attempts']); ?></strong><span>Transferred</span><strong><?php echo count(array_filter($transferPreview['attempts'], function ($row) { return !empty($row['transfer_id']); })); ?></strong></div><form method="post" class="cbt-admin-panel-form"><input type="hidden" name="csrf_token" value="<?php echo admin_cbt_h($csrf); ?>"><input type="hidden" name="cbt_action" value="transfer_scores"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><button class="cbt-btn cbt-btn--paper cbt-btn--block" <?php echo !in_array($assessment['status'], array('approved', 'published'), true) ? 'disabled' : ''; ?>>Transfer approved scores</button></form><?php else: ?><p class="cbt-panel-note"><?php echo admin_cbt_h($transferError); ?></p><?php endif; ?></section>
                <?php if ($script): ?><section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">E</span><h2>Exceptional case</h2></div></div><div class="cbt-admin-actions"><form method="post"><input type="hidden" name="csrf_token" value="<?php echo admin_cbt_h($csrf); ?>"><input type="hidden" name="cbt_action" value="<?php echo $script['attempt']['status'] === 'in_progress' ? 'add_time' : 'reopen_attempt'; ?>"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><input type="hidden" name="attempt_id" value="<?php echo (int) $script['attempt']['id']; ?>"><label><span>Minutes</span><input type="number" name="minutes" min="1" max="240" value="15" required></label><label><span>Reason</span><textarea name="reason" rows="3" required placeholder="Network interruption, device failure…"></textarea></label><button class="cbt-btn cbt-btn--paper cbt-btn--block"><?php echo $script['attempt']['status'] === 'in_progress' ? 'Grant extra time' : 'Reopen attempt'; ?></button></form></div></section><?php endif; ?>
            </aside>
        </div>
    <?php endif; ?>
</main>
<script src="../assets/js/cbt-portal.js?v=1"></script>
</body>
</html>
