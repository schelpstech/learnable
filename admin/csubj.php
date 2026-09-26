<?php
require __DIR__ . '/workflow_bootstrap.php';
$service = new AcademicStructureService($workflowDb);
$edit = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', 'admin');
        $service->saveSubject($_POST, $_SESSION['unamed']);
        workflow_redirect('subjects', (!empty($_POST['id']) ? 'Subject name updated for its linked allocations.' : 'Subject created.') . ' You can create another subject below.');
    } catch (Throwable $error) { $workflowError = workflow_error($error); }
}
try {
    $classes = $service->classes(); $subjects = $service->subjects();
    if (!empty($_GET['id'])) {
        foreach ($subjects as $row) if ((int)$row['sbjid'] === (int)$_GET['id']) $edit = $row;
        if (!$edit) throw new InvalidArgumentException('Subject not found.');
    }
} catch (Throwable $error) { $classes = $subjects = array(); $workflowError = workflow_error($error); }
$form = $workflowError && $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : ($edit ?: array());
$workflowTitle = 'Subjects';
$workflowIntro = 'Create and rename class subjects without changing their IDs or disconnecting allocations, schemes and results.';
require __DIR__ . '/workflow_header.php';
?>
<div class="workspace-stats"><div><span>Subjects</span><strong><?php echo count($subjects); ?></strong></div><div><span>Classes covered</span><strong><?php echo count(array_unique(array_column($subjects,'classid'))); ?></strong></div><div><span>Active allocations</span><strong><?php echo number_format(array_sum(array_map(function($row){return (int)$row['allocation_count'];},$subjects))); ?></strong></div></div>
<div class="workspace-grid">
    <section class="workspace-card"><p class="workspace-eyebrow"><?php echo $edit ? 'Edit subject' : 'New subject'; ?></p><h2><?php echo $edit ? wh($edit['sbjname']) : 'Add a subject'; ?></h2><p class="workspace-muted"><?php echo $edit ? 'The class remains fixed so existing schemes and results stay connected.' : 'Subjects belong to one class and can then be allocated to a teacher each term.'; ?></p><form method="post"><input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>"><input type="hidden" name="id" value="<?php echo (int)($form['id'] ?? $form['sbjid'] ?? 0); ?>"><?php if($edit): ?><input type="hidden" name="class_id" value="<?php echo (int)$edit['classid']; ?>"><?php endif; ?><div class="workspace-fields"><label>Class<select name="<?php echo $edit?'class_display':'class_id'; ?>" required <?php echo $edit?'disabled':''; ?>><option value="">Choose class</option><?php foreach($classes as $row): ?><option value="<?php echo (int)$row['classid']; ?>" <?php echo (int)($form['class_id'] ?? $form['classid'] ?? 0)===(int)$row['classid']?'selected':''; ?>><?php echo wh($row['classname']); ?></option><?php endforeach; ?></select></label><label>Subject name<input name="name" maxlength="64" required value="<?php echo wh($form['name'] ?? $form['sbjname'] ?? ''); ?>" placeholder="e.g. Mathematics"></label></div><div class="workspace-actions"><button class="workspace-button"><?php echo $edit?'Save subject name':'Create subject'; ?></button><?php if($edit): ?><a class="workspace-button secondary" href="index.php?route=subjects">New subject</a><?php endif; ?></div></form></section>
    <section class="workspace-card"><div class="fee-section-heading"><div><p class="workspace-eyebrow">Curriculum directory</p><h2>Subject register</h2><p class="workspace-muted">Names can be corrected without replacing the subject record.</p></div></div><label class="no-print">Find a subject<input type="search" data-table-search="#subject-table" placeholder="Search class or subject"></label><div class="workspace-table-wrap"><table class="workspace-table" id="subject-table"><thead><tr><th>Class</th><th>Subject</th><th>Allocations</th><th></th></tr></thead><tbody><?php foreach($subjects as $row): ?><tr><td><?php echo wh($row['classname']); ?></td><td><strong><?php echo wh($row['sbjname']); ?></strong><small>ID <?php echo (int)$row['sbjid']; ?></small></td><td><?php echo number_format((int)$row['allocation_count']); ?></td><td><a class="workspace-button secondary compact" href="index.php?route=subjects&amp;id=<?php echo (int)$row['sbjid']; ?>">Edit name</a></td></tr><?php endforeach; ?></tbody></table></div><?php if(!$subjects): ?><div class="workspace-empty">No subjects have been created.</div><?php endif; ?></section>
</div>
<?php require __DIR__ . '/workflow_footer.php'; ?>
