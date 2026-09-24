<?php
$schemeService = new SchemeService(database_pdo());
$schemeClasses = $schemeService->classes($_SESSION['active']);
$schemeEdit = null;
$schemeError = '';
try {
    if (!empty($itemRef)) $schemeEdit = $schemeService->get($itemRef, $_SESSION['active']);
} catch (Throwable $error) {
    $schemeError = $error->getMessage();
}
$schemeSelectedClass = (int)($schemeEdit['class_id'] ?? 0);
$schemeSelectedSubject = (int)($schemeEdit['subject_id'] ?? 0);
$schemeSubjects = $schemeSelectedClass ? $schemeService->subjects($_SESSION['active'], $schemeSelectedClass) : array();
$schemeTopics = ($schemeSelectedClass && $schemeSelectedSubject)
    ? $schemeService->topics($_SESSION['active'], $schemeSelectedClass, $schemeSelectedSubject)
    : array();
$schemeH = function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
?>
<div class="main_content_iner school-workspace scheme-workspace">
    <div class="container-fluid p-0">
        <header class="workspace-heading">
            <div>
                <p class="workspace-eyebrow">Teaching plan · <?php echo $schemeH($schemeService->activeTerm()); ?></p>
                <h1>Scheme of work</h1>
                <p>Plan the term by topic. Each saved topic becomes available for lesson notes, the question bank and CBT assessments.</p>
            </div>
            <div class="workspace-actions no-print">
                <a class="workspace-button secondary" href="../../app/router.php?pageid=resources&amp;item=add_note">Write e-note</a>
                <a class="workspace-button secondary" href="../../app/router.php?pageid=cbt_bank">Add questions</a>
                <a class="workspace-button" href="../../app/router.php?pageid=cbt_builder">Create assessment</a>
            </div>
        </header>

        <?php if ($schemeError): ?><div class="workspace-error" role="alert"><?php echo $schemeH($schemeError); ?></div><?php endif; ?>
        <div class="workspace-grid scheme-layout" data-scheme-workspace data-endpoint="../../app/scheme_action.php" data-actor="<?php echo $schemeH($_SESSION['active']); ?>">
            <section class="workspace-card">
                <p class="workspace-eyebrow"><?php echo $schemeEdit ? 'Edit topic' : 'Add a topic'; ?></p>
                <h2 data-scheme-form-title><?php echo $schemeEdit ? 'Update ' . $schemeH($schemeEdit['week']) : 'Plan a teaching week'; ?></h2>
                <p class="workspace-muted">Only classes and subjects allocated to you in the active term are available.</p>
                <div data-scheme-message role="status"></div>
                <form data-scheme-form>
                    <input type="hidden" name="csrf_token" value="<?php echo $schemeH($_SESSION['portal_csrf']); ?>">
                    <input type="hidden" name="scheme_action" value="save">
                    <input type="hidden" name="id" value="<?php echo (int)($schemeEdit['schmid'] ?? 0); ?>" data-scheme-id>
                    <div class="workspace-fields">
                        <label>Class
                            <select name="class_id" required data-scheme-class>
                                <option value="">Choose class</option>
                                <?php foreach ($schemeClasses as $row): ?><option value="<?php echo (int)$row['classid']; ?>" <?php echo $schemeSelectedClass === (int)$row['classid'] ? 'selected' : ''; ?>><?php echo $schemeH($row['classname']); ?></option><?php endforeach; ?>
                            </select>
                        </label>
                        <label>Subject
                            <select name="subject_id" required data-scheme-subject>
                                <option value="">Choose subject</option>
                                <?php foreach ($schemeSubjects as $row): ?><option value="<?php echo (int)$row['sbjid']; ?>" <?php echo $schemeSelectedSubject === (int)$row['sbjid'] ? 'selected' : ''; ?>><?php echo $schemeH($row['sbjname']); ?></option><?php endforeach; ?>
                            </select>
                        </label>
                        <label>Teaching week
                            <select name="week" required data-scheme-week><option value="">Choose week</option><?php for ($week=1;$week<=12;$week++): ?><option value="<?php echo $week; ?>" <?php echo (int)($schemeEdit['week_number'] ?? 0) === $week ? 'selected' : ''; ?>>Week <?php echo $week; ?></option><?php endfor; ?></select>
                        </label>
                        <label class="workspace-wide">Topic
                            <input name="topic" maxlength="254" required autocomplete="off" placeholder="e.g. Introduction to fractions" value="<?php echo $schemeH($schemeEdit['topic'] ?? ''); ?>" data-scheme-topic>
                        </label>
                    </div>
                    <div class="workspace-actions">
                        <button class="workspace-button" data-scheme-submit><?php echo $schemeEdit ? 'Save changes' : 'Add topic'; ?></button>
                        <button class="workspace-button secondary" type="button" data-scheme-cancel <?php echo $schemeEdit ? '' : 'hidden'; ?>>Cancel editing</button>
                    </div>
                </form>
            </section>

            <section class="workspace-card">
                <div class="fee-section-heading"><div><p class="workspace-eyebrow">Active-term plan</p><h2>Topics for the selected subject</h2><p class="workspace-muted">New topics appear here as soon as they are saved—no page refresh is needed.</p></div><span class="workspace-badge muted" data-scheme-count><?php echo count($schemeTopics); ?> topics</span></div>
                <div class="scheme-topic-list" data-scheme-list data-topics='<?php echo $schemeH(json_encode($schemeTopics, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>'></div>
                <div class="workspace-empty" data-scheme-empty <?php echo $schemeTopics ? 'hidden' : ''; ?>>Choose a class and subject to view its scheme, or add the first topic.</div>
            </section>
        </div>
    </div>
</div>
<script src="../../../assets/js/scheme-workspace.js?v=1"></script>
