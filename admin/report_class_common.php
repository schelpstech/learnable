<?php

include __DIR__ . '/conf.php';
require_once dirname(__DIR__) . '/classes/autoload.php';
require_once dirname(__DIR__) . '/config/database.php';

$term = isset($_GET['term']) && is_string($_GET['term']) ? trim($_GET['term']) : '';
$classId = isset($_GET['class_id']) && is_string($_GET['class_id']) ? trim($_GET['class_id']) : '';
if ($term === '' || strlen($term) > 64 || $classId === '' || strlen($classId) > 64) {
    http_response_code(400);
    exit('A valid class and term are required.');
}

try {
    $reportService = new ReportService(database_pdo(), dirname(__DIR__));
    $reports = $reportService->buildClass($classId, $term, !empty($cumulativeReport));
} catch (RuntimeException $exception) {
    http_response_code(404);
    exit(htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
}

if (!$reports) {
    http_response_code(404);
    exit('No learner results were found for this class and term.');
}

$className = $reports[0]['learner']['class_name'];
$adminRoute = !empty($cumulativeReport) ? 'reports-class-cumulative' : 'reports-class';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($className . ' · ' . $term . ' Class Reports', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/admin-modern.css?v=1">
    <link rel="stylesheet" href="../assets/css/report-sheet.css?v=3">
</head>
<body class="bulk-report-body">
    <?php include __DIR__ . '/nav.html'; ?>
    <main class="report-workspace">
        <div class="report-workspace-inner">
            <div class="report-toolbar bulk-report-toolbar report-no-print" aria-label="Class report actions">
                <div>
                    <span class="report-toolbar-kicker">Class report register</span>
                    <strong><?php echo htmlspecialchars($className . ' · ' . $term, ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span class="bulk-report-count"><?php echo count($reports); ?> learner<?php echo count($reports) === 1 ? '' : 's'; ?></span>
                </div>
                <div class="report-toolbar-actions">
                    <a href="index.php?route=reports" class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to reports</a>
                    <button type="button" class="is-primary" data-report-print><i class="fa fa-print" aria-hidden="true"></i> Print all / Save PDF</button>
                </div>
            </div>
            <div class="bulk-report-stack">
                <?php foreach ($reports as $reportIndex => $report): ?>
                    <?php
                    $reportShowToolbar = false;
                    $reportSheetId = 'report-sheet-' . ($reportIndex + 1);
                    require dirname(__DIR__) . '/views/shared/report-sheet.php';
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <script src="../assets/js/report-sheet.js?v=3"></script>
</body>
</html>

