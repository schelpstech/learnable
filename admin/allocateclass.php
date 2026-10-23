<?php
require __DIR__ . '/workflow_bootstrap.php';
$service = new AcademicStructureService($workflowDb);
$workflowError = $_SESSION['class_teacher_error'] ?? '';
unset($_SESSION['class_teacher_error']);
try {
    $allocation = $service->classTeacher($_GET['classid'] ?? 0);
    $teachers = $service->classTeachers();
} catch (Throwable $error) {
    http_response_code($error instanceof InvalidArgumentException ? 404 : 400);
    $workflowError = workflow_error($error);
    $allocation = null;
    $teachers = [];
}
$workflowTitle = 'Edit class teacher';
$workflowIntro = 'Choose the class teacher for the active term. Saving replaces the current assignment.';
require __DIR__ . '/workflow_header.php';
?>
<?php if ($allocation):
    $current = $allocation['assignments'][0] ?? null;
?>
<section class="workspace-card">
    <p class="workspace-eyebrow"><?= wh($allocation['term']) ?></p>
    <h2><?= wh($allocation['class']['classname']) ?></h2>
    <p>Current class teacher: <strong><?= wh($current ? ($current['staffname'] ?: $current['tutorid']) : 'Not assigned') ?></strong></p>
    <?php if (count($allocation['assignments']) > 1): ?><p class="workspace-muted">More than one teacher is recorded for this class. Saving will replace them with the teacher you select below.</p><?php endif; ?>
    <?php if (!$teachers): ?><p role="alert">No active teaching staff are available. Add or activate a teacher before assigning this class.</p><?php endif; ?>
    <form method="post" action="tutallocated.php">
        <input type="hidden" name="csrf_token" value="<?= wh($workflowCsrf) ?>">
        <input type="hidden" name="class_id" value="<?= (int)$allocation['class']['classid'] ?>">
        <input type="hidden" name="term" value="<?= wh($allocation['term']) ?>">
        <div class="workspace-fields">
            <label class="workspace-wide">Class teacher
                <select name="teacher_id" required>
                    <option value="">Select a teacher</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= wh($teacher['sname']) ?>" <?= $current && $current['tutorid'] === $teacher['sname'] ? 'selected' : '' ?>><?= wh($teacher['staffname'] ?: $teacher['sname']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div class="workspace-actions">
            <button type="submit" class="workspace-button" <?= !$teachers ? 'disabled' : '' ?>>Save class teacher</button>
            <a class="workspace-button secondary" href="index.php?route=classes">Back to classes</a>
        </div>
    </form>
</section>
<?php else: ?><p><a href="index.php?route=classes">Back to classes</a></p><?php endif; ?>
<?php require __DIR__ . '/foot.php'; require __DIR__ . '/workflow_footer.php'; ?>
