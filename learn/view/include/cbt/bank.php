<?php
require __DIR__ . '/_common.php';
if (!$cbtIsInstructor) {
    throw new RuntimeException('Instructor access required.');
}
$allocations = $cbtService->teacherAllocations($cbtActor);
$selectedClass = $portalRoute->param('class_id');
$selectedSubject = $portalRoute->param('subject_id');
if (($selectedClass === null || $selectedSubject === null) && $allocations) {
    $selectedClass = $allocations[0]['class_id'];
    $selectedSubject = $allocations[0]['subject_id'];
}
$selectedAllocation = null;
foreach ($allocations as $allocation) {
    if ((int) $allocation['class_id'] === (int) $selectedClass && (int) $allocation['subject_id'] === (int) $selectedSubject) {
        $selectedAllocation = $allocation;
        break;
    }
}
$questionSchemeOptions = $selectedAllocation
    ? $cbtService->topics($cbtActor, (int) $selectedClass, (int) $selectedSubject, false)
    : array();
$questionSchemeId = $portalRoute->param('scheme_id');
if ($questionSchemeId === null && $questionSchemeOptions) $questionSchemeId = $questionSchemeOptions[0]['id'];
$questions = $selectedAllocation ? $cbtService->questionBank($cbtActor, false, array('class_id' => $selectedClass, 'subject_id' => $selectedSubject)) : array();
?>
<div class="main_content_iner overly_inner cbt-portal">
    <div class="container-fluid p-0">
        <?php if (!empty($cbtFlash)): ?><div class="cbt-alert cbt-alert--<?php echo $cbtFlash['type'] === 'error' ? 'error' : 'success'; ?>" role="alert"><i class="fas fa-info-circle"></i><span><?php echo cbt_h($cbtFlash['message']); ?></span></div><?php endif; ?>
        <header class="cbt-page-heading">
            <div><p class="cbt-kicker">Reusable teaching resource</p><h1>Question bank</h1><p>Build a dependable collection by topic, learning objective, question type and difficulty.</p></div>
            <div class="cbt-heading-actions"><a class="cbt-btn cbt-btn--paper" href="../../app/router.php?pageid=cbt">Assessment workspace</a><a class="cbt-btn cbt-btn--primary" href="#write-question">Write a question</a></div>
        </header>

        <section class="cbt-bank-filter cbt-board">
            <form method="get" action="../../app/router.php">
                <input type="hidden" name="pageid" value="cbt_bank">
                <label><span>Class and subject</span><select data-cbt-bank-allocation><option value="">Choose allocation</option><?php foreach ($allocations as $allocation): $value = $allocation['class_id'] . ':' . $allocation['subject_id']; ?><option value="<?php echo cbt_h($value); ?>" <?php echo (int) $allocation['class_id'] === (int) $selectedClass && (int) $allocation['subject_id'] === (int) $selectedSubject ? 'selected' : ''; ?>><?php echo cbt_h($allocation['classname'] . ' · ' . $allocation['sbjname']); ?></option><?php endforeach; ?></select></label>
                <input type="hidden" name="class_id" value="<?php echo cbt_h($selectedClass); ?>" data-cbt-class-id>
                <input type="hidden" name="subject_id" value="<?php echo cbt_h($selectedSubject); ?>" data-cbt-subject-id>
                <button class="cbt-btn cbt-btn--paper" type="submit">Load bank</button>
            </form>
        </section>

        <section class="cbt-import-strip">
            <div><strong>Bulk question import</strong><span>Use the approved CSV layout; every row is still checked against your allocations and scheme topics.</span></div>
            <a class="cbt-text-link" href="../../app/cbt_template.php">Download template</a>
            <form method="post" action="../../app/cbt_action.php" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>"><input type="hidden" name="cbt_action" value="import_questions"><label><span class="sr-only">Question CSV</span><input type="file" name="question_file" accept=".csv,text/csv" required></label><button class="cbt-btn cbt-btn--paper" type="submit">Import CSV</button></form>
        </section>

        <div class="cbt-builder-layout cbt-builder-layout--bank">
            <main>
                <section class="cbt-board">
                    <div class="cbt-board__title"><div><span class="cbt-section-number">A</span><h2><?php echo $selectedAllocation ? cbt_h($selectedAllocation['classname'] . ' · ' . $selectedAllocation['sbjname']) : 'Select a subject allocation'; ?></h2></div><p><?php echo count($questions); ?> questions</p></div>
                    <?php if (!$questions): ?><div class="cbt-empty cbt-empty--small"><i class="fas fa-book"></i><h3>No bank questions here yet</h3><p>Write the first question for an approved topic in this subject.</p></div><?php endif; ?>
                    <div class="cbt-bank-table">
                        <?php foreach ($questions as $index => $question): ?>
                            <article><div class="cbt-bank-index"><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></div><div><div class="cbt-card-meta"><span><?php echo cbt_h($question['week']); ?></span><span><?php echo cbt_h(cbt_label($question['question_type'])); ?></span><span><?php echo cbt_h($question['difficulty']); ?></span><span><?php echo cbt_h($question['visibility']); ?></span></div><h3><?php echo cbt_h(mb_substr(strip_tags($question['prompt_html']), 0, 180)); ?></h3><p><?php echo cbt_h($question['topic']); ?> · <?php echo cbt_h(number_format((float) $question['marks'], 1)); ?> marks · used <?php echo (int) $question['use_count']; ?> times</p><form method="post" action="../../app/cbt_action.php"><input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>"><input type="hidden" name="cbt_action" value="duplicate_question"><input type="hidden" name="question_id" value="<?php echo (int) $question['id']; ?>"><button class="cbt-text-link" type="submit">Copy as new draft</button></form></div><span class="cbt-status cbt-status--<?php echo cbt_h($question['status']); ?>"><?php echo cbt_h(cbt_label($question['status'])); ?></span></article>
                        <?php endforeach; ?>
                    </div>
                </section>
            </main>
            <aside id="write-question">
                <section class="cbt-board cbt-sticky-panel">
                    <div class="cbt-board__title"><div><span class="cbt-section-number">B</span><h2>Write a bank question</h2></div></div>
                    <?php if (!$selectedAllocation || !$questionSchemeOptions): ?><p class="cbt-panel-note">Choose an allocation with at least one approved scheme topic before writing a question.</p><?php else: ?>
                        <?php $questionClassId = $selectedClass; $questionSubjectId = $selectedSubject; $assessment = null; include __DIR__ . '/question-form.php'; ?>
                    <?php endif; ?>
                </section>
            </aside>
        </div>
    </div>
</div>
<script src="../../../assets/js/cbt-portal.js?v=1" defer></script>
