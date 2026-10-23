<?php
require __DIR__ . '/conf.php';
require_once dirname(__DIR__) . '/classes/MidtermReportService.php';
$db=database_pdo(); $midtermService=new MidtermReportService($db);
$terms=$midtermService->terms();
$term=isset($_GET['term']) && is_string($_GET['term']) ? trim($_GET['term']) : ($terms[0]['term'] ?? '');
$validTerms=array_column($terms,'term');
if (!in_array($term,$validTerms,true)) $term='';
$classes=$term !== '' ? $midtermService->classes($term) : [];
$classId=isset($_GET['class_id']) && is_string($_GET['class_id']) ? trim($_GET['class_id']) : '';
if (!in_array($classId,array_map('strval',array_column($classes,'classid')),true)) $classId='';
$learners=$classId !== '' ? $midtermService->learners($classId,$term) : [];
$adminRoute='midterm';
function midterm_select_escape($value) { return htmlspecialchars((string)$value,ENT_QUOTES | ENT_SUBSTITUTE,'UTF-8'); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Midterm report sheets | LearnAble</title><link rel="stylesheet" href="css/font-awesome.min.css"><link rel="stylesheet" href="../assets/css/midterm-report.css?v=1"></head><body>
<?php include __DIR__ . '/nav.html'; ?>
<main class="midterm-workspace"><header class="midterm-toolbar"><div><span class="midterm-kicker">Academic records</span><h1>Midterm report sheets</h1><p>Landscape reports for an individual learner or a whole class.</p></div></header>
<section class="midterm-selector"><h2>Choose the report</h2><p>Weekly assessments for Weeks 1-6, using the original midterm total of 30 marks per subject. Only learners with weekly records in the selected class and term are included.</p>
<?php if (!$terms): ?><div class="midterm-empty">No Week 1-6 scores have been recorded yet. Add weekly scores through the class scorebook first.</div><?php else: ?>
<form method="get" action="index.php" data-midterm-selector><input type="hidden" name="route" value="midterm"><div class="midterm-fields">
<label for="midterm-term">Academic term<select id="midterm-term" name="term" required data-midterm-term><option value="">Choose a term</option><?php foreach($terms as $item): ?><option value="<?php echo midterm_select_escape($item['term']); ?>" <?php echo $term===$item['term'] ? 'selected' : ''; ?>><?php echo midterm_select_escape($item['term']); ?></option><?php endforeach; ?></select></label>
<label for="midterm-class">Recorded class<select id="midterm-class" name="class_id" required data-midterm-class><option value="">Choose a class</option><?php foreach($classes as $item): ?><option value="<?php echo midterm_select_escape($item['classid']); ?>" <?php echo $classId===(string)$item['classid'] ? 'selected' : ''; ?>><?php echo midterm_select_escape($item['classname']); ?> (<?php echo (int)$item['learner_count']; ?> learners)</option><?php endforeach; ?></select></label>
<label for="midterm-learner">Learner<select id="midterm-learner" name="lid" data-midterm-learner><option value="">Whole class<?php echo $learners ? ' ('.count($learners).')' : ''; ?></option><?php foreach($learners as $item): ?><option value="<?php echo midterm_select_escape($item['uname']); ?>"><?php echo midterm_select_escape($item['fname'].' · '.$item['uname']); ?></option><?php endforeach; ?></select><small>Leave Whole class selected for a combined PDF. Select a learner for an individual report.</small></label>
</div><div class="midterm-actions"><button type="submit" formnovalidate>Update selection</button><button type="submit" class="is-primary" formaction="viewmidterm.php" <?php echo $classId==='' ? 'disabled' : ''; ?>>Preview report sheets</button><button type="submit" formaction="viewmidterm.php" name="download" value="1" <?php echo $classId==='' ? 'disabled' : ''; ?>>Download PDF</button></div></form>
<?php endif; ?></section></main><div class="midterm-admin-footer midterm-no-print"><?php include __DIR__ . "/foot.php"; ?></div><script src="../assets/js/midterm-report.js?v=1" defer></script></body></html>
