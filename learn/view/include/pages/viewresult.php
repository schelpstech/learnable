<?php
if (empty($learner_profile['uname']) || empty($routeRef)) {
    echo '<div class="main_content_iner"><div class="alert alert-warning">A learner and result term are required.</div></div>';
    return;
}

try {
    $reportService = new ReportService(database_pdo(), dirname(__DIR__, 4));
    $isCumulative = stripos($routeRef, '3rd') === 0;
    $report = $reportService->build($learner_profile['uname'], $routeRef, $isCumulative);
} catch (RuntimeException $exception) {
    echo '<div class="main_content_iner"><div class="alert alert-warning">' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</div></div>';
    return;
}
?>
<link rel="stylesheet" href="../../../assets/css/report-sheet.css?v=3">
<main class="report-workspace">
    <div class="report-workspace-inner">
        <?php require dirname(__DIR__, 4) . '/views/shared/report-sheet.php'; ?>
    </div>
</main>
<script src="../../../assets/js/html2pdf-loader.js?v=5"></script>
<script src="../../../assets/js/report-sheet.js?v=3"></script>
