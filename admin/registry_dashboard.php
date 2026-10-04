<?php
require __DIR__ . '/workflow_bootstrap.php';
$adminRoute='dashboard';
$workflowTitle='Registry workspace';
$workflowIntro='Manage student accounts, academic structure, scores and results.';
$counts=[];
foreach (['Active students'=>'lhpuser WHERE status=1','Classes'=>'lhpclass','Subjects'=>'lhpsubject'] as $label=>$table) {
    $counts[$label]=(int)$workflowDb->query('SELECT COUNT(*) FROM '.$table)->fetchColumn();
}
require __DIR__ . '/workflow_header.php';
?>
<div class="workspace-stats">
<?php foreach ($counts as $label=>$count): ?><section class="workspace-card"><p class="workspace-eyebrow"><?php echo wh($label); ?></p><h2><?php echo $count; ?></h2></section><?php endforeach; ?>
</div>
<section class="workspace-card"><h2>Registry</h2><p>Keep student accounts and class teaching assignments up to date.</p><div class="workspace-actions">
<?php foreach (['learners'=>'Students','classes'=>'Classes','subjects'=>'Subjects','allocations'=>'Subject allocation','terms'=>'Academic terms','calendar'=>'Calendar & timetable'] as $route=>$label): ?><a class="workspace-button secondary" href="index.php?route=<?php echo $route; ?>"><?php echo wh($label); ?></a><?php endforeach; ?>
</div></section>
<section class="workspace-card"><h2>Scores and results</h2><p>Enter scores, review results and manage their publication.</p><div class="workspace-actions">
<?php foreach (['scores'=>'Enter scores','records'=>'Academic records','reports'=>'Result reports','cbt'=>'CBT & assessments','promotions'=>'Promotions'] as $route=>$label): ?><a class="workspace-button secondary" href="index.php?route=<?php echo $route; ?>"><?php echo wh($label); ?></a><?php endforeach; ?>
</div></section>
<?php require __DIR__ . '/workflow_footer.php'; ?>
