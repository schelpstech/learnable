<?php
require __DIR__ . '/workflow_bootstrap.php';
$service = new AcademicStructureService($workflowDb);
$edit = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', 'admin');
        $service->saveClass($_POST, $_SESSION['unamed']);
        workflow_redirect('classes', (!empty($_POST['id']) ? 'Class name updated everywhere it is displayed.' : 'Class created.') . ' You can create another class below.');
    } catch (Throwable $error) { $workflowError = workflow_error($error); }
}
try {
    $classes = $service->classes();
    if (!empty($_GET['id'])) {
        foreach ($classes as $row) if ((int)$row['classid'] === (int)$_GET['id']) $edit = $row;
        if (!$edit) throw new InvalidArgumentException('Class not found.');
    }
} catch (Throwable $error) { $classes = array(); $workflowError = workflow_error($error); }
$form = $workflowError && $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : ($edit ?: array());
$workflowTitle = 'Classes';
$workflowIntro = 'Maintain class names while keeping the permanent class IDs used by learners, fees, results and teaching records.';
require __DIR__ . '/workflow_header.php';
?>
<div class="workspace-stats"><div><span>Classes</span><strong><?php echo count($classes); ?></strong></div><div><span>Active learners</span><strong><?php echo number_format(array_sum(array_map(function($row){return (int)$row['population'];},$classes))); ?></strong></div><div><span>Active term</span><strong style="font-size:18px"><?php echo wh($service->activeTerm()); ?></strong></div></div>
<div class="workspace-grid">
    <section class="workspace-card"><p class="workspace-eyebrow"><?php echo $edit ? 'Edit class' : 'New class'; ?></p><h2><?php echo $edit ? wh($edit['classname']) : 'Create a class'; ?></h2><p class="workspace-muted">Renaming keeps the same class ID and updates the readable copies used in subjects and allocations.</p>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>"><input type="hidden" name="id" value="<?php echo (int)($form['id'] ?? $form['classid'] ?? 0); ?>"><div class="workspace-fields"><label class="workspace-wide">Class name<input name="name" maxlength="16" required value="<?php echo wh($form['name'] ?? $form['classname'] ?? ''); ?>" placeholder="e.g. Basic 4"></label></div><div class="workspace-actions"><button class="workspace-button"><?php echo $edit ? 'Save class name' : 'Create class'; ?></button><?php if($edit): ?><a class="workspace-button secondary" href="index.php?route=classes">New class</a><?php endif; ?></div></form>
    </section>
    <section class="workspace-card"><div class="fee-section-heading"><div><p class="workspace-eyebrow">Academic structure</p><h2>Class register</h2><p class="workspace-muted">Edit a name without changing any linked student or academic record.</p></div></div><label class="no-print">Find a class<input type="search" data-table-search="#class-table" placeholder="Search class or teacher"></label><div class="workspace-table-wrap"><table class="workspace-table" id="class-table"><thead><tr><th>Class</th><th>Learners</th><th>Class teacher</th><th></th></tr></thead><tbody><?php foreach($classes as $row): ?><tr><td><strong><?php echo wh($row['classname']); ?></strong><small>ID <?php echo (int)$row['classid']; ?></small></td><td><?php echo number_format((int)$row['population']); ?></td><td><?php echo wh($row['class_teacher'] ?: 'Not assigned'); ?></td><td><div class="workspace-actions"><a class="workspace-button secondary compact" href="index.php?route=classes&amp;id=<?php echo (int)$row['classid']; ?>">Edit name</a><a class="workspace-button secondary compact" href="allocateclass.php?classid=<?php echo (int)$row['classid']; ?>">Class teacher</a></div></td></tr><?php endforeach; ?></tbody></table></div><?php if(!$classes): ?><div class="workspace-empty">No classes have been created.</div><?php endif; ?></section>
</div>
<?php require __DIR__ . '/workflow_footer.php'; ?>
