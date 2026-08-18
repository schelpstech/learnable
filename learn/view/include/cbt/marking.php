<?php
require __DIR__ . '/_common.php';
if (!$cbtIsInstructor) throw new RuntimeException('Instructor access required.');
$assessmentId = $portalRoute->param('assessment_id');
$attemptId = $portalRoute->param('attempt_id');
$assessments = $cbtService->teacherAssessments($cbtActor);
$assessment = null;
$analytics = null;
$attemptRows = array();
$script = null;
$transferPreview = null;
$transferError = null;
if ($assessmentId !== null) {
    $assessment = $cbtService->assessment((int) $assessmentId);
    $cbtService->assertAssessmentManager($assessment, $cbtActor, false);
    $analytics = $cbtService->analytics((int) $assessmentId, $cbtActor, false);
    $attemptRows = $cbtAttemptService->attemptsForAssessment((int) $assessmentId, $cbtActor, false);
    if ($attemptId !== null) $script = $cbtAttemptService->scriptForMarking((int) $attemptId, $cbtActor, false);
    try { $transferPreview = $cbtResultService->previewAssessmentTransfer((int) $assessmentId, $cbtActor, false); }
    catch (Throwable $exception) { $transferError = $exception->getMessage(); }
}
?>
<div class="main_content_iner overly_inner cbt-portal">
    <div class="container-fluid p-0">
        <?php if (!empty($cbtFlash)): ?><div class="cbt-alert cbt-alert--<?php echo $cbtFlash['type'] === 'error' ? 'error' : 'success'; ?>" role="alert"><i class="fas fa-info-circle"></i><span><?php echo cbt_h($cbtFlash['message']); ?></span></div><?php endif; ?>
        <header class="cbt-page-heading cbt-page-heading--compact"><div><p class="cbt-kicker">Evidence before totals</p><h1>Marking & results</h1><p>Review scripts question by question, explain every override, publish deliberately, and transfer only approved scores.</p></div><div class="cbt-heading-actions"><?php if ($assessment): ?><a class="cbt-btn cbt-btn--paper" href="../../app/cbt_export.php?assessment_id=<?php echo (int) $assessment['id']; ?>"><i class="fas fa-file-csv"></i> Export scores</a><?php endif; ?><a class="cbt-btn cbt-btn--paper" href="../../app/router.php?pageid=cbt">Assessment workspace</a></div></header>

        <?php if (!$assessment): ?>
            <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">A</span><h2>Select an assessment</h2></div></div><div class="cbt-register-list"><?php foreach ($assessments as $item): ?><a href="../../app/router.php?pageid=cbt_marking&amp;assessment_id=<?php echo (int) $item['id']; ?>"><span><strong><?php echo cbt_h($item['title']); ?></strong><small><?php echo cbt_h($item['classname'] . ' · ' . $item['sbjname'] . ' · ' . cbt_label($item['status'])); ?></small></span><span><?php echo (int) $item['submitted_count']; ?> scripts <i class="fas fa-arrow-right"></i></span></a><?php endforeach; ?></div><?php if (!$assessments): ?><div class="cbt-empty"><h3>No assessment records yet</h3><p>Create and schedule an assessment before scripts can appear here.</p></div><?php endif; ?></section>
        <?php else: ?>
            <nav class="cbt-breadcrumb"><a href="../../app/router.php?pageid=cbt_marking">Marking register</a><i class="fas fa-chevron-right"></i><span><?php echo cbt_h($assessment['title']); ?></span></nav>
            <section class="cbt-marking-hero"><div><span class="cbt-status cbt-status--<?php echo cbt_h($assessment['status']); ?>"><?php echo cbt_h(cbt_label($assessment['status'])); ?></span><h2><?php echo cbt_h($assessment['title']); ?></h2><p><?php echo cbt_h($assessment['classname'] . ' · ' . $assessment['sbjname'] . ' · ' . $assessment['topic']); ?></p></div><div><strong><?php echo cbt_h(number_format((float) $assessment['total_marks'], 1)); ?></strong><span>marks</span></div></section>

            <section class="cbt-stat-row cbt-stat-row--six">
                <article class="cbt-stat"><div><strong><?php echo (int) $analytics['summary']['eligible']; ?></strong><small>eligible</small></div></article><article class="cbt-stat"><div><strong><?php echo (int) $analytics['summary']['submitted']; ?></strong><small>submitted</small></div></article><article class="cbt-stat"><div><strong><?php echo (int) $analytics['summary']['absent']; ?></strong><small>not started</small></div></article><article class="cbt-stat"><div><strong><?php echo cbt_h($analytics['summary']['average_score'] !== null ? $analytics['summary']['average_score'] : '—'); ?></strong><small>class average</small></div></article><article class="cbt-stat"><div><strong><?php echo cbt_h($analytics['summary']['highest_score'] !== null ? $analytics['summary']['highest_score'] : '—'); ?></strong><small>highest score</small></div></article><article class="cbt-stat"><div><strong><?php echo cbt_h($analytics['summary']['pass_rate']); ?>%</strong><small>pass rate</small></div></article>
            </section>

            <div class="cbt-marking-layout">
                <main>
                    <?php if ($script): ?>
                        <section class="cbt-board">
                            <div class="cbt-board__title"><div><span class="cbt-section-number">S</span><h2><?php echo cbt_h($script['attempt']['fname']); ?> · Script <?php echo (int) $script['attempt']['attempt_no']; ?></h2></div><div class="cbt-heading-actions"><button type="button" class="cbt-text-link" data-cbt-print>Print script</button><p><?php echo cbt_h(number_format((float) $script['attempt']['total_score'], 1)); ?> / <?php echo cbt_h(number_format((float) $script['attempt']['total_marks'], 1)); ?></p></div></div>
                            <div class="cbt-script-meta"><span>Started <?php echo cbt_datetime($script['attempt']['started_at']); ?></span><span>Submitted <?php echo cbt_datetime($script['attempt']['submitted_at']); ?></span><span>Reference <?php echo cbt_h($script['attempt']['submission_ref']); ?></span></div>
                            <ol class="cbt-script-list">
                                <?php foreach ($script['questions'] as $question): ?>
                                    <li><header><span>Question <?php echo (int) $question['display_order']; ?></span><strong><?php echo cbt_h(number_format((float) $question['final_marks'], 1)); ?> / <?php echo cbt_h(number_format((float) $question['marks_available'], 1)); ?></strong></header><div class="cbt-script-question"><?php echo $question['prompt_snapshot']; ?></div><div class="cbt-script-answer"><small>Learner answer</small><pre><?php echo cbt_h(is_array($question['answer']) ? implode(', ', $question['answer']) : ($question['answer'] === null || $question['answer'] === '' ? 'Not answered' : $question['answer'])); ?></pre></div><?php if ($question['model_answer_snapshot']): ?><details><summary>Model answer & marking guide</summary><p><?php echo nl2br(cbt_h($question['model_answer_snapshot'])); ?></p><p><?php echo nl2br(cbt_h($question['marking_guide_snapshot'])); ?></p></details><?php endif; ?>
                                        <?php if (!empty($question['answer_id'])): ?><form method="post" action="../../app/cbt_action.php" class="cbt-inline-mark-form"><input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>"><input type="hidden" name="cbt_action" value="mark_answer"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><input type="hidden" name="attempt_id" value="<?php echo (int) $script['attempt']['id']; ?>"><input type="hidden" name="answer_id" value="<?php echo (int) $question['answer_id']; ?>"><label><span>Awarded mark</span><input type="number" name="marks" min="0" max="<?php echo cbt_h($question['marks_available']); ?>" step="0.25" value="<?php echo cbt_h($question['final_marks']); ?>" required></label><label><span>Feedback</span><input type="text" name="comment" maxlength="5000" value="<?php echo cbt_h($question['marker_comment']); ?>"></label><label><span>Reason for override</span><input type="text" name="reason" maxlength="5000" placeholder="Required when changing a mark"></label><button class="cbt-btn cbt-btn--small cbt-btn--paper" type="submit">Save mark</button></form><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php else: ?>
                        <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">A</span><h2>Learner scripts</h2></div><p><?php echo count($attemptRows); ?> attempts</p></div><div class="cbt-script-table"><div class="cbt-script-table__head"><span>Learner</span><span>Status</span><span>Score</span><span>Time used</span><span>Review</span></div><?php foreach ($attemptRows as $row): ?><div><span><strong><?php echo cbt_h($row['fname']); ?></strong><small><?php echo cbt_h($row['learner_id']); ?> · attempt <?php echo (int) $row['attempt_no']; ?></small></span><span><span class="cbt-status cbt-status--<?php echo cbt_h($row['status']); ?>"><?php echo cbt_h(cbt_label($row['status'])); ?></span><?php if ((int) $row['warning_count'] > 0): ?><small><?php echo (int) $row['warning_count']; ?> activity flags</small><?php endif; ?></span><span><?php echo $row['submitted_at'] ? cbt_h(number_format((float) $row['total_score'], 1)) : '—'; ?></span><span><?php echo $row['submitted_at'] ? max(1, (int) round((strtotime($row['submitted_at']) - strtotime($row['started_at'])) / 60)) . ' min' : 'In progress'; ?></span><span><a class="cbt-text-link" href="../../app/router.php?pageid=cbt_marking&amp;assessment_id=<?php echo (int) $assessment['id']; ?>&amp;attempt_id=<?php echo (int) $row['id']; ?>">Open script</a></span></div><?php endforeach; ?></div><?php if (!$attemptRows): ?><div class="cbt-empty cbt-empty--small"><h3>No learner has started yet</h3><p>Eligible learner names will appear as attempts begin.</p></div><?php endif; ?></section>
                    <?php endif; ?>

                    <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">Q</span><h2>Question performance</h2></div><p>Lowest full-mark rate first</p></div><div class="cbt-question-analysis"><?php foreach ($analytics['questions'] as $index => $question): ?><article><span><?php echo $index + 1; ?></span><div><h3><?php echo cbt_h(mb_substr(strip_tags($question['prompt_snapshot']), 0, 120)); ?></h3><p><?php echo cbt_h(cbt_label($question['question_type'])); ?> · average <?php echo cbt_h($question['average_marks']); ?> / <?php echo cbt_h($question['marks_available']); ?></p></div><strong><?php echo cbt_h($question['full_mark_rate']); ?>%</strong></article><?php endforeach; ?></div></section>
                </main>

                <aside>
                    <section class="cbt-board cbt-sticky-panel"><div class="cbt-board__title"><div><span class="cbt-section-number">P</span><h2>Publication</h2></div></div><?php if ($assessment['status'] === 'pending_approval'): ?><p class="cbt-panel-note">This paper is waiting for an administrator to approve it. Results cannot be published before approval.</p><?php elseif (in_array($assessment['status'], array('approved', 'published'), true)): ?><p class="cbt-panel-note">Publish only after every subjective response has been marked and checked.</p><form method="post" action="../../app/cbt_action.php"><input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>"><input type="hidden" name="cbt_action" value="publish_results"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><button class="cbt-btn cbt-btn--primary cbt-btn--block" type="submit">Publish completed results</button></form><?php else: ?><p class="cbt-panel-note">Assessment status: <?php echo cbt_h(cbt_label($assessment['status'])); ?>.</p><?php endif; ?></section>
                    <section class="cbt-board"><div class="cbt-board__title"><div><span class="cbt-section-number">R</span><h2>Academic record</h2></div></div><?php if ($transferPreview): ?><p class="cbt-panel-note"><strong><?php echo cbt_h(cbt_label($transferPreview['mapping']['component'])); ?></strong><br><?php echo cbt_h($transferPreview['mapping']['formula']); ?></p><div class="cbt-transfer-preview"><span>Published scores</span><strong><?php echo count($transferPreview['attempts']); ?></strong><span>Already transferred</span><strong><?php echo count(array_filter($transferPreview['attempts'], function ($row) { return !empty($row['transfer_id']); })); ?></strong></div><form method="post" action="../../app/cbt_action.php"><input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>"><input type="hidden" name="cbt_action" value="transfer_scores"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><button class="cbt-btn cbt-btn--paper cbt-btn--block" type="submit" <?php echo !in_array($assessment['status'], array('approved', 'published'), true) ? 'disabled' : ''; ?>>Transfer approved scores</button></form><?php else: ?><p class="cbt-panel-note"><?php echo cbt_h($transferError); ?></p><?php endif; ?></section>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="../../../assets/js/cbt-portal.js?v=1" defer></script>
