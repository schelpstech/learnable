<?php
require __DIR__ . '/workflow_bootstrap.php';
$service = new AcademicStructureService($workflowDb);
$edit = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', 'admin');
        if (($_POST['action'] ?? 'save') === 'delete') {
            $service->deleteAllocation($_POST['id'] ?? 0, $_SESSION['unamed'], ($_POST['confirm'] ?? '') === 'yes');
            workflow_redirect('allocations', 'Subject allocation removed from the active term.');
        }
        $service->saveAllocation($_POST, $_SESSION['unamed']);
        // Return to a fresh form so another class/subject can be allocated immediately.
        workflow_redirect('allocations', (!empty($_POST['id']) ? 'Teacher allocation updated.' : 'Subject allocated to the teacher for the active term.') . ' You can allocate another subject below.');
    } catch (Throwable $error) { $workflowError = workflow_error($error); }
}
try {
    $classes = $service->classes(); $subjects = $service->subjects(); $staff = $service->staff(); $allocations = $service->allocations();
    if (!empty($_GET['id'])) {
        foreach ($allocations as $row) if ((int)$row['aid'] === (int)$_GET['id']) $edit = $row;
        if (!$edit) throw new InvalidArgumentException('Active-term allocation not found.');
    }
} catch (Throwable $error) { $classes=$subjects=$staff=$allocations=array(); $workflowError=workflow_error($error); }
// Availability is per class and active term, irrespective of the assigned teacher.
$allocatedSubjects = array();
foreach ($allocations as $allocation) {
    $allocatedSubjects[(int)$allocation['classid'] . ':' . (int)$allocation['sbjid']] = true;
}
$subjects = array_values(array_filter($subjects, static function ($subject) use ($allocatedSubjects, $edit) {
    $isCurrent = $edit && (int)$edit['classid'] === (int)$subject['classid'] && (int)$edit['sbjid'] === (int)$subject['sbjid'];
    return $isCurrent || !isset($allocatedSubjects[(int)$subject['classid'] . ':' . (int)$subject['sbjid']]);
}));
$form = $workflowError && $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : ($edit ?: array());
$workflowTitle = 'Subject allocation';
$workflowIntro = 'Assign each active-term class subject to its teacher. Scheme, e-note and CBT permissions follow this allocation automatically.';
$workflowScripts = array('../assets/js/academic-structure.js?v=2');
require __DIR__ . '/workflow_header.php';
?>
<div class="workspace-stats"><div><span>Active-term allocations</span><strong><?php echo count($allocations); ?></strong></div><div><span>Teachers represented</span><strong><?php echo count(array_unique(array_filter(array_column($allocations,'staffid')))); ?></strong></div><div><span>Academic term</span><strong style="font-size:18px"><?php echo wh($service->activeTerm()); ?></strong></div></div>
<div class="workspace-grid">
    <section class="workspace-card"><p class="workspace-eyebrow"><?php echo $edit?'Edit allocation':'New allocation'; ?></p><h2><?php echo $edit?wh($edit['classname'].' · '.$edit['sbjname']):'Allocate a subject'; ?></h2><p class="workspace-muted">One teacher owns the subject workflow for this class and term. No supervisor is required.</p><form method="post" data-allocation-form><input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>"><input type="hidden" name="id" value="<?php echo (int)($form['id'] ?? $form['aid'] ?? 0); ?>"><input type="hidden" name="action" value="save"><?php if($edit): ?><input type="hidden" name="class_id" value="<?php echo (int)$edit['classid']; ?>"><input type="hidden" name="subject_id" value="<?php echo (int)$edit['sbjid']; ?>"><?php endif; ?><div class="workspace-fields"><label>Class<select name="<?php echo $edit?'class_display':'class_id'; ?>" required data-allocation-class <?php echo $edit?'disabled':''; ?>><option value="">Choose class</option><?php foreach($classes as $row): ?><option value="<?php echo (int)$row['classid']; ?>" <?php echo (int)($form['class_id'] ?? $form['classid'] ?? 0)===(int)$row['classid']?'selected':''; ?>><?php echo wh($row['classname']); ?></option><?php endforeach; ?></select></label><label>Subject<select name="<?php echo $edit?'subject_display':'subject_id'; ?>" required data-allocation-subject <?php echo $edit?'disabled':''; ?>><option value="">Choose subject</option><?php foreach($subjects as $row): ?><option value="<?php echo (int)$row['sbjid']; ?>" data-class-id="<?php echo (int)$row['classid']; ?>" <?php echo (int)($form['subject_id'] ?? $form['sbjid'] ?? 0)===(int)$row['sbjid']?'selected':''; ?>><?php echo wh($row['sbjname']); ?></option><?php endforeach; ?></select><small><?php echo $edit?'Create a new allocation to change the class or subject.':'Only subjects not yet allocated for this class in the active term are shown.'; ?></small></label><label class="workspace-wide">Teacher<select name="teacher_id" required><option value="">Choose teacher</option><?php foreach($staff as $row): ?><option value="<?php echo wh($row['sname']); ?>" <?php echo (string)($form['teacher_id'] ?? $form['staffid'] ?? '')===(string)$row['sname']?'selected':''; ?>><?php echo wh($row['staffname']); ?></option><?php endforeach; ?></select></label></div><div class="workspace-actions"><button class="workspace-button"><?php echo $edit?'Save allocation':'Allocate subject'; ?></button><?php if($edit): ?><a class="workspace-button secondary" href="index.php?route=allocations">New allocation</a><?php endif; ?></div></form><?php if($edit): ?><hr><form method="post" data-confirm="Remove this teacher's active-term subject allocation? The teacher will lose authoring access; existing records remain stored."><input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$edit['aid']; ?>"><input type="hidden" name="confirm" value="yes"><button class="workspace-button danger">Remove allocation</button></form><?php endif; ?></section>
    <section class="workspace-card"><div class="fee-section-heading"><div><p class="workspace-eyebrow">Active term</p><h2>Teaching ownership</h2><p class="workspace-muted">This list controls who can plan topics, write e-notes, record scores and prepare CBT work.</p></div></div><label class="no-print">Find an allocation<input type="search" data-table-search="#allocation-table" placeholder="Search class, subject or teacher"></label><div class="workspace-table-wrap"><table class="workspace-table" id="allocation-table"><thead><tr><th>Class</th><th>Subject</th><th>Teacher</th><th>Term</th><th></th></tr></thead><tbody><?php foreach($allocations as $row): ?><tr><td><?php echo wh($row['classname']); ?></td><td><strong><?php echo wh($row['sbjname']); ?></strong></td><td><?php echo wh($row['staffname'] ?: $row['staffid']); ?></td><td><?php echo wh($row['term']); ?></td><td><a class="workspace-button secondary compact" href="index.php?route=allocations&amp;id=<?php echo (int)$row['aid']; ?>">Edit</a></td></tr><?php endforeach; ?></tbody></table></div><?php if(!$allocations): ?><div class="workspace-empty">No subjects are allocated in the active term.</div><?php endif; ?></section>
</div>
<?php require __DIR__ . '/workflow_footer.php'; ?>
