<?php
require __DIR__ . '/conf.php';
require_once dirname(__DIR__) . '/classes/MidtermReportService.php';
$term=isset($_GET['term']) && is_string($_GET['term']) ? trim($_GET['term']) : '';
$learnerId=isset($_GET['lid']) && is_string($_GET['lid']) ? trim($_GET['lid']) : '';
$classId=isset($_GET['class_id']) && is_string($_GET['class_id']) ? trim($_GET['class_id']) : '';
if ($term==='' || strlen($term)>64 || strlen($learnerId)>64 || strlen($classId)>12 || ($learnerId==='' && $classId==='')) { http_response_code(400); exit('A valid term and either a learner or recorded class are required.'); }
try {
    $service=new MidtermReportService(database_pdo());
    $reports=$learnerId!=='' ? [$service->build($learnerId,$term,$classId)] : $service->buildClass($classId,$term);
    if (!$reports) throw new RuntimeException('No Week 1-6 results were found for this class and term.');
} catch (RuntimeException $exception) { http_response_code(404); exit(htmlspecialchars($exception->getMessage(),ENT_QUOTES | ENT_SUBSTITUTE,'UTF-8')); }
$classId=$reports[0]['learner']['class_id'];
$label=$learnerId!=='' ? $reports[0]['learner']['name'] : $reports[0]['class_name'];
$fileName=MidtermReportService::fileName($label.'-'.$term);
$adminRoute='midterm';
$selectionUrl='index.php?'.http_build_query(['route'=>'midterm','term'=>$term,'class_id'=>$classId]);
function midterm_view_escape($value) { return htmlspecialchars((string)$value,ENT_QUOTES | ENT_SUBSTITUTE,'UTF-8'); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?php echo midterm_view_escape($label.' · '.$term.' · Midterm reports'); ?></title><link rel="stylesheet" href="css/font-awesome.min.css"><link rel="stylesheet" href="../assets/css/midterm-report.css?v=1"><script src="../assets/js/html2pdf-loader.js?v=5"></script></head><body>
<?php include __DIR__ . '/nav.html'; ?>
<main class="midterm-workspace"><header class="midterm-toolbar midterm-no-print"><div><span class="midterm-kicker">A4 landscape · Midterm assessment</span><h1><?php echo midterm_view_escape($label); ?></h1><p><?php echo midterm_view_escape($term); ?> · <?php echo count($reports); ?> learner<?php echo count($reports)===1 ? '' : 's'; ?> · Weeks 1-6</p></div><div class="midterm-actions"><a href="<?php echo midterm_view_escape($selectionUrl); ?>">Choose reports</a><button type="button" data-midterm-print>Print / Save PDF</button><button type="button" class="is-primary" data-midterm-download>Download <?php echo $learnerId!=='' ? 'learner' : 'class'; ?> PDF</button></div></header>
<p class="midterm-status midterm-no-print" role="status" aria-live="polite" data-midterm-status></p>
<div class="midterm-scroll"><div class="midterm-stack" data-midterm-reports data-midterm-file="<?php echo midterm_view_escape($fileName); ?>" <?php echo ($_GET['download'] ?? '')==='1' ? 'data-midterm-auto-download' : ''; ?>>
<?php foreach($reports as $midterm): ?>
<?php if(count($reports)>1): ?><div class="midterm-learner-link midterm-no-print"><strong><?php echo midterm_view_escape($midterm['learner']['name']); ?></strong><a href="viewmidterm.php?<?php echo midterm_view_escape(http_build_query(['term'=>$term,'class_id'=>$classId,'lid'=>$midterm['learner']['id']])); ?>">Open individual report</a></div><?php endif; ?>
<?php require dirname(__DIR__) . '/views/shared/midterm-sheet.php'; ?>
<?php endforeach; ?>
</div></div></main><div class="midterm-admin-footer midterm-no-print"><?php include __DIR__ . "/foot.php"; ?></div><script src="../assets/js/midterm-report.js?v=1" defer></script></body></html>
