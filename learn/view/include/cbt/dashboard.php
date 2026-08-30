<?php
require __DIR__ . '/_common.php';

$assessments = $cbtIsInstructor
    ? $cbtService->teacherAssessments($cbtActor)
    : $cbtService->learnerAssessments($cbtActor);

$now = new DateTimeImmutable('now');
$availableCount = 0;
$completedCount = 0;
foreach ($assessments as $assessment) {
    if ($cbtIsInstructor) {
        if (!empty($assessment['submitted_count'])) $completedCount += (int) $assessment['submitted_count'];
    } else {
        if ($assessment['attempt_status'] === 'published') $completedCount++;
        if ($now >= new DateTimeImmutable($assessment['start_at']) && $now <= new DateTimeImmutable($assessment['close_at'])) $availableCount++;
    }
}
?>
<div class="main_content_iner overly_inner cbt-portal">
    <div class="container-fluid p-0">
        <?php if (!empty($cbtFlash)): ?>
            <div class="cbt-alert cbt-alert--<?php echo $cbtFlash['type'] === 'error' ? 'error' : 'success'; ?>" role="alert">
                <i class="fas <?php echo $cbtFlash['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'; ?>"></i>
                <span><?php echo cbt_h($cbtFlash['message']); ?></span>
            </div>
        <?php endif; ?>

        <header class="cbt-page-heading">
            <div>
                <p class="cbt-kicker"><?php echo cbt_h($cbtContext['session']); ?> · <?php echo cbt_h($cbtContext['term']); ?></p>
                <h1><?php echo $cbtIsInstructor ? 'Assessment workspace' : 'My assessments'; ?></h1>
                <p><?php echo $cbtIsInstructor
                    ? 'Prepare your next test, review your class’s work and share feedback when it is ready.'
                    : 'See what is coming up, continue a test or read your teacher’s feedback.'; ?></p>
            </div>
            <?php if ($cbtIsInstructor): ?>
                <div class="cbt-heading-actions">
                    <a class="cbt-btn cbt-btn--paper" href="../../app/router.php?pageid=cbt_bank"><i class="fas fa-book-open"></i> Question bank</a>
                    <a class="cbt-btn cbt-btn--primary" href="../../app/router.php?pageid=cbt_builder"><i class="fas fa-plus"></i> New assessment</a>
                </div>
            <?php endif; ?>
        </header>

        <section class="cbt-stat-row" aria-label="Assessment summary">
            <article class="cbt-stat"><span class="cbt-stat__mark">01</span><div><strong><?php echo count($assessments); ?></strong><small><?php echo $cbtIsInstructor ? 'assessment records' : 'assigned this term'; ?></small></div></article>
            <article class="cbt-stat"><span class="cbt-stat__mark">02</span><div><strong><?php echo $cbtIsInstructor ? array_sum(array_map(function ($item) { return (int) $item['question_count']; }, $assessments)) : $availableCount; ?></strong><small><?php echo $cbtIsInstructor ? 'questions in papers' : 'available now'; ?></small></div></article>
            <article class="cbt-stat"><span class="cbt-stat__mark">03</span><div><strong><?php echo $completedCount; ?></strong><small><?php echo $cbtIsInstructor ? 'submitted scripts' : 'published results'; ?></small></div></article>
        </section>

        <section class="cbt-board">
            <div class="cbt-board__title">
                <div><span class="cbt-section-number">A</span><h2><?php echo $cbtIsInstructor ? 'Your assessment register' : 'Assessment timetable'; ?></h2></div>
                <p><?php echo count($assessments); ?> record<?php echo count($assessments) === 1 ? '' : 's'; ?></p>
            </div>

            <?php if (!$assessments): ?>
                <div class="cbt-empty">
                    <i class="fas fa-book-reader"></i>
                    <h3><?php echo $cbtIsInstructor ? 'Begin with one clear learning objective' : 'No assessments have been assigned yet'; ?></h3>
                    <p><?php echo $cbtIsInstructor
                        ? 'Create an assessment from an approved topic and build the paper from your reusable question bank.'
                        : 'When a teacher publishes a test for your class, its date, subject and instructions will appear here.'; ?></p>
                    <?php if ($cbtIsInstructor): ?><a class="cbt-btn cbt-btn--primary" href="../../app/router.php?pageid=cbt_builder">Create assessment</a><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="cbt-assessment-list">
                    <?php foreach ($assessments as $assessment):
                        $effective = $cbtIsInstructor ? $cbtService->effectiveStatus($assessment) : $cbtService->effectiveStatus($assessment);
                        $isOpen = $now >= new DateTimeImmutable($assessment['start_at']) && $now <= new DateTimeImmutable($assessment['close_at']);
                        $isUpcoming = $now < new DateTimeImmutable($assessment['start_at']);
                    ?>
                        <article class="cbt-assessment-card">
                            <div class="cbt-assessment-card__date">
                                <strong><?php echo date('d', strtotime($assessment['start_at'])); ?></strong>
                                <span><?php echo strtoupper(date('M', strtotime($assessment['start_at']))); ?></span>
                            </div>
                            <div class="cbt-assessment-card__body">
                                <div class="cbt-card-meta">
                                    <span><?php echo cbt_h($assessment['sbjname']); ?></span>
                                    <span><?php echo cbt_h(cbt_label($assessment['assessment_type'])); ?></span>
                                    <span class="cbt-status cbt-status--<?php echo cbt_h($effective); ?>"><?php echo cbt_h(cbt_label($effective)); ?></span>
                                </div>
                                <h3><?php echo cbt_h($assessment['title']); ?></h3>
                                <p><?php echo cbt_h($assessment['classname']); ?> · <?php echo cbt_h($assessment['topic']); ?> · <?php echo (int) $assessment['duration_minutes']; ?> minutes</p>
                                <div class="cbt-card-facts">
                                    <span><i class="far fa-clock"></i> <?php echo cbt_datetime($assessment['start_at']); ?></span>
                                    <span><i class="fas fa-list-ol"></i> <?php echo (int) $assessment['question_count']; ?> questions</span>
                                    <?php if ($cbtIsInstructor): ?>
                                        <span><i class="fas fa-file-signature"></i> <?php echo (int) $assessment['submitted_count']; ?> submitted</span>
                                    <?php elseif (!empty($assessment['attempt_status'])): ?>
                                        <span><i class="fas fa-pen-nib"></i> <?php echo cbt_h(cbt_label($assessment['attempt_status'])); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="cbt-assessment-card__actions">
                                <?php if ($cbtIsInstructor): ?>
                                    <?php if (in_array($assessment['status'], array('draft', 'pending_approval'), true)): ?>
                                        <a class="cbt-btn cbt-btn--small cbt-btn--primary" href="../../app/router.php?pageid=cbt_builder&amp;assessment_id=<?php echo (int) $assessment['id']; ?>">Build paper</a>
                                    <?php endif; ?>
                                    <a class="cbt-text-link" href="../../app/router.php?pageid=cbt_marking&amp;assessment_id=<?php echo (int) $assessment['id']; ?>">Scripts & analysis <i class="fas fa-arrow-right"></i></a>
                                    <form method="post" action="../../app/cbt_action.php" class="cbt-copy-form"><input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>"><input type="hidden" name="cbt_action" value="duplicate_assessment"><input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>"><button class="cbt-text-link" type="submit">Duplicate as draft</button></form>
                                <?php else: ?>
                                    <?php if ($assessment['attempt_status'] === 'published' && !empty($assessment['attempt_id'])): ?>
                                        <a class="cbt-btn cbt-btn--small cbt-btn--paper" href="../../app/router.php?pageid=cbt_review&amp;attempt_id=<?php echo (int) $assessment['attempt_id']; ?>">View result</a>
                                    <?php elseif ($isOpen && ((int) $assessment['attempts_used'] < (int) $assessment['max_attempts'] || $assessment['attempt_status'] === 'in_progress')): ?>
                                        <form method="post" action="../../app/cbt_action.php" class="cbt-start-form">
                                            <input type="hidden" name="csrf_token" value="<?php echo cbt_h($cbtCsrf); ?>">
                                            <input type="hidden" name="cbt_action" value="start_attempt">
                                            <input type="hidden" name="assessment_id" value="<?php echo (int) $assessment['id']; ?>">
                                            <input type="hidden" name="device_fingerprint" value="" data-cbt-fingerprint>
                                            <button class="cbt-btn cbt-btn--small cbt-btn--primary" type="submit"><?php echo $assessment['attempt_status'] === 'in_progress' ? 'Resume test' : 'Read & start'; ?></button>
                                        </form>
                                    <?php elseif ($isUpcoming): ?>
                                        <span class="cbt-muted-note">Opens <?php echo date('D, g:i a', strtotime($assessment['start_at'])); ?></span>
                                    <?php else: ?>
                                        <span class="cbt-muted-note"><?php echo !empty($assessment['attempt_status']) ? 'Awaiting publication' : 'Closed'; ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
<script src="../../../assets/js/cbt-portal.js?v=1" defer></script>
