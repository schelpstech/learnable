<?php
require __DIR__ . '/_common.php';
if (!$cbtIsInstructor) {
    throw new RuntimeException('Instructor access required.');
}

$allocations = $cbtService->teacherAllocations($cbtActor);
$topicMap = array();
foreach ($allocations as $allocation) {
    $key = $allocation['class_id'] . ':' . $allocation['subject_id'];
    $topicMap[$key] = $cbtService->topics($cbtActor, (int) $allocation['class_id'], (int) $allocation['subject_id'], false);
}
$assessmentId = $portalRoute->param('assessment_id');
$assessment = null;
$paperQuestions = array();
$bankQuestions = array();
if ($assessmentId !== null) {
    $assessment = $cbtService->assessment((int) $assessmentId);
    $cbtService->assertAssessmentManager($assessment, $cbtActor, false);
    $paperQuestions = $cbtService->assessmentQuestions((int) $assessmentId, true);
    $bankQuestions = $cbtService->questionBank($cbtActor, false, array(
        'class_id' => $assessment['class_id'],
        'subject_id' => $assessment['subject_id'],
    ));
    $usedQuestionIds = array_map(function ($question) { return (int) $question['id']; }, $paperQuestions);
}
$defaultStart = date('Y-m-d\TH:i', strtotime('+1 hour'));
$defaultClose = date('Y-m-d\TH:i', strtotime('+1 day'));
?>
<div class="main_content_iner overly_inner cbt-portal">
    <div class="container-fluid p-0">
        <?php if (!empty($cbtFlash)): ?>
            <div class="cbt-alert cbt-alert--<?php echo $cbtFlash['type'] === 'error' ? 'error' : 'success'; ?>" role="alert"><i class="fas fa-info-circle"></i><span><?php echo cbt_h($cbtFlash['message']); ?></span></div>
        <?php endif; ?>

        <nav class="cbt-breadcrumb" aria-label="Breadcrumb"><a href="../../app/router.php?pageid=cbt">Assessment workspace</a><i class="fas fa-chevron-right"></i><span><?php echo $assessment ? cbt_h($assessment['title']) : 'New assessment'; ?></span></nav>

        <?php if (!$assessment): ?>
            <header class="cbt-page-heading cbt-page-heading--compact">
                <div><p class="cbt-kicker">Paper setup</p><h1>Plan a new assessment</h1><p>Start with the learning goal and timetable. Questions are added in the next step.</p></div>
            </header>
            <?php if (!$allocations): ?>
                <div class="cbt-empty"><i class="fas fa-user-lock"></i><h3>No active subject allocation</h3><p>An administrator must assign a class and subject to you before you can create a CBT assessment.</p></div>
            <?php else: ?>
                <form method="post" action="../../app/cbt_action.php" class="cbt-form cbt-paper-form" data-cbt-assessment-form>
                    <input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>">
                    <input type="hidden" name="cbt_action" value="create_assessment">
                    <section class="cbt-form-section">
                        <div class="cbt-form-section__heading"><span>1</span><div><h2>Academic context</h2><p>Only your current allocations and approved scheme topics are available.</p></div></div>
                        <div class="cbt-form-grid cbt-form-grid--3">
                            <label><span>Class & subject</span><select name="allocation" required data-cbt-allocation-select><option value="">Choose an allocation</option><?php foreach ($allocations as $allocation): ?><option value="<?php echo (int) $allocation['class_id']; ?>:<?php echo (int) $allocation['subject_id']; ?>"><?php echo cbt_h($allocation['classname'] . ' · ' . $allocation['sbjname']); ?></option><?php endforeach; ?></select></label>
                            <input type="hidden" name="class_id" data-cbt-class-id>
                            <input type="hidden" name="subject_id" data-cbt-subject-id>
                            <label><span>Scheme-of-work topic</span><select name="scheme_id" required data-cbt-topic-select><option value="">Choose class and subject first</option></select></label>
                            <label><span>Assessment type</span><select name="assessment_type" required><?php foreach (CbtService::assessmentTypes() as $type): ?><option value="<?php echo cbt_h($type); ?>"><?php echo cbt_h(cbt_label($type)); ?></option><?php endforeach; ?></select></label>
                        </div>
                    </section>
                    <section class="cbt-form-section">
                        <div class="cbt-form-section__heading"><span>2</span><div><h2>Purpose and score use</h2><p>Learner scores stay outside term results until an authorized transfer is confirmed.</p></div></div>
                        <div class="cbt-form-grid">
                            <label class="cbt-field-wide"><span>Assessment title</span><input type="text" name="title" maxlength="190" required placeholder="e.g. Fractions and equivalent values"></label>
                            <label class="cbt-field-wide"><span>Instructions</span><textarea name="instructions" rows="4" maxlength="5000" placeholder="Explain what learners need, how to answer, and any classroom rules."></textarea></label>
                            <label><span>Result treatment</span><select name="result_treatment" required><option value="practice">Practice only</option><option value="weekly">Weekly assessment (over 10)</option><option value="ca">Continuous assessment</option><option value="exam">Examination score</option><option value="temporary">Decide later</option><option value="excluded">Exclude from computation</option></select></label>
                            <label><span>Proposed total marks</span><input type="number" name="total_marks" value="20" min="1" max="10000" step="0.25" required></label>
                            <label><span>Pass mark</span><input type="number" name="pass_mark" value="10" min="0" max="10000" step="0.25" required></label>
                        </div>
                    </section>
                    <section class="cbt-form-section">
                        <div class="cbt-form-section__heading"><span>3</span><div><h2>Schedule and attempt rules</h2><p>The official countdown is enforced by the server and survives a refresh or reconnection.</p></div></div>
                        <div class="cbt-form-grid cbt-form-grid--3">
                            <label><span>Opens</span><input type="datetime-local" name="start_at" value="<?php echo cbt_h($defaultStart); ?>" required></label>
                            <label><span>Closes</span><input type="datetime-local" name="close_at" value="<?php echo cbt_h($defaultClose); ?>" required></label>
                            <label><span>Time allowed (minutes)</span><input type="number" name="duration_minutes" min="1" max="720" value="30" required></label>
                            <label><span>Attempts permitted</span><input type="number" name="max_attempts" min="1" max="5" value="1" required></label>
                            <label><span>Question navigation</span><select name="navigation_mode"><option value="free">Free navigation</option><option value="linear">One-way sequence</option></select></label>
                        </div>
                        <div class="cbt-check-grid">
                            <label><input type="checkbox" name="allow_backtrack" value="1" checked><span>Allow return to earlier questions</span></label>
                            <label><input type="checkbox" name="randomize_questions" value="1"><span>Randomize question order</span></label>
                            <label><input type="checkbox" name="shuffle_options" value="1"><span>Shuffle answer options</span></label>
                            <label><input type="checkbox" name="auto_submit" value="1" checked><span>Submit automatically at timeout</span></label>
                            <label><input type="checkbox" name="show_score" value="1"><span>Show score after publication</span></label>
                            <label><input type="checkbox" name="allow_review" value="1"><span>Allow script review after publication</span></label>
                            <label><input type="checkbox" name="show_correct_answers" value="1"><span>Release answers after the assessment closes</span></label>
                            <label><input type="checkbox" name="require_approval" value="1" checked><span>Require administrator approval</span></label>
                            <label><input type="checkbox" name="monitor_tab_switch" value="1" checked><span>Record tab switches for human review</span></label>
                            <label><input type="checkbox" name="fullscreen_mode" value="1"><span>Offer distraction-free fullscreen mode</span></label>
                        </div>
                    </section>
                    <footer class="cbt-form-footer"><a href="../../app/router.php?pageid=cbt">Cancel</a><button class="cbt-btn cbt-btn--primary" type="submit">Create draft & continue <i class="fas fa-arrow-right"></i></button></footer>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <header class="cbt-page-heading cbt-page-heading--compact">
                <div><p class="cbt-kicker"><?php echo cbt_h($assessment['classname'] . ' · ' . $assessment['sbjname']); ?></p><h1><?php echo cbt_h($assessment['title']); ?></h1><p><?php echo cbt_h($assessment['week'] . ' · ' . $assessment['topic']); ?></p></div>
                <div class="cbt-heading-actions"><span class="cbt-status cbt-status--<?php echo cbt_h($assessment['status']); ?>"><?php echo cbt_h(cbt_label($assessment['status'])); ?></span><a class="cbt-btn cbt-btn--paper" href="../../app/router.php?pageid=cbt_marking&amp;assessment_id=<?php echo (int) $assessment['id']; ?>">Scripts & analysis</a></div>
            </header>

            <section class="cbt-paper-summary">
                <div><span>Questions</span><strong><?php echo count($paperQuestions); ?></strong></div><div><span>Total marks</span><strong><?php echo cbt_h(number_format((float) $assessment['total_marks'], 1)); ?></strong></div><div><span>Time</span><strong><?php echo (int) $assessment['duration_minutes']; ?> min</strong></div><div><span>Opens</span><strong><?php echo date('j M, g:i a', strtotime($assessment['start_at'])); ?></strong></div>
            </section>

            <div class="cbt-builder-layout">
                <main>
                    <section class="cbt-board">
                        <div class="cbt-board__title"><div><span class="cbt-section-number">A</span><h2>Assessment paper</h2></div><p><?php echo count($paperQuestions); ?> question<?php echo count($paperQuestions) === 1 ? '' : 's'; ?></p></div>
                        <?php if (!$paperQuestions): ?><div class="cbt-empty cbt-empty--small"><i class="fas fa-file-alt"></i><h3>The paper is empty</h3><p>Create a new question below or reuse one from your bank.</p></div><?php endif; ?>
                        <ol class="cbt-paper-questions">
                            <?php foreach ($paperQuestions as $question): ?>
                                <li><span class="cbt-question-type"><?php echo cbt_h(cbt_label($question['question_type'])); ?></span><div class="cbt-question-copy"><?php echo $question['prompt_html']; ?><small><?php echo cbt_h($question['difficulty']); ?> · <?php echo cbt_h(number_format((float) ($question['marks_override'] !== null ? $question['marks_override'] : $question['marks']), 1)); ?> marks</small></div><?php if ($assessment['status'] === 'draft'): ?><form method="post" action="../../app/cbt_action.php"><input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>"><input type="hidden" name="cbt_action" value="remove_question"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><input type="hidden" name="question_id" value="<?php echo (int) $question['id']; ?>"><button class="cbt-icon-button" type="submit" title="Remove from paper"><i class="fas fa-times"></i></button></form><?php endif; ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </section>

                    <?php if ($assessment['status'] === 'draft'): ?>
                        <section class="cbt-board">
                            <div class="cbt-board__title"><div><span class="cbt-section-number">B</span><h2>Write a question</h2></div><p>Saved automatically to your bank</p></div>
                            <?php $questionClassId = $assessment['class_id']; $questionSubjectId = $assessment['subject_id']; $questionSchemeId = $assessment['scheme_id']; include __DIR__ . '/question-form.php'; ?>
                        </section>
                    <?php endif; ?>
                </main>

                <aside>
                    <section class="cbt-board cbt-bank-panel">
                        <div class="cbt-board__title"><div><span class="cbt-section-number">C</span><h2>Reuse from bank</h2></div></div>
                        <?php $availableBank = array_filter($bankQuestions, function ($question) use ($usedQuestionIds) { return !in_array((int) $question['id'], $usedQuestionIds, true); }); ?>
                        <?php if (!$availableBank): ?><p class="cbt-panel-note">No unused questions match this class and subject yet.</p><?php endif; ?>
                        <div class="cbt-bank-mini-list">
                            <?php foreach ($availableBank as $question): ?>
                                <article><span><?php echo cbt_h(cbt_label($question['question_type'])); ?> · <?php echo cbt_h($question['difficulty']); ?></span><h3><?php echo cbt_h(mb_substr(strip_tags($question['prompt_html']), 0, 100)); ?></h3><form method="post" action="../../app/cbt_action.php"><input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>"><input type="hidden" name="cbt_action" value="add_question"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><input type="hidden" name="question_id" value="<?php echo (int) $question['id']; ?>"><button type="submit" class="cbt-text-link">Add to paper <i class="fas fa-plus"></i></button></form></article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <?php if ($assessment['status'] === 'draft'): ?>
                        <section class="cbt-submit-panel"><i class="fas fa-stamp"></i><h2>Ready for review?</h2><p>Once submitted, the paper is locked while an administrator checks the schedule and assessment settings.</p><form method="post" action="../../app/cbt_action.php"><input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>"><input type="hidden" name="cbt_action" value="submit_approval"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><button class="cbt-btn cbt-btn--primary cbt-btn--block" type="submit" <?php echo count($paperQuestions) < 1 ? 'disabled' : ''; ?>>Submit for approval</button></form></section>
                    <?php endif; ?>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</div>
<script type="application/json" id="cbt-topic-data"><?php echo json_encode($topicMap, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<script src="../../../assets/js/cbt-portal.js?v=1" defer></script>
