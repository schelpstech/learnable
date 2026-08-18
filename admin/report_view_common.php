<?php

include __DIR__ . '/conf.php';
require_once dirname(__DIR__) . '/classes/autoload.php';
require_once dirname(__DIR__) . '/config/database.php';

$term = isset($_GET['term']) && is_string($_GET['term']) ? trim($_GET['term']) : '';
$learnerId = isset($_GET['lid']) && is_string($_GET['lid']) ? trim($_GET['lid']) : '';
if ($term === '' || strlen($term) > 64 || $learnerId === '' || strlen($learnerId) > 64) {
    http_response_code(400);
    exit('A valid learner and term are required.');
}

try {
    $reportService = new ReportService(database_pdo(), dirname(__DIR__));
    $report = $reportService->build($learnerId, $term, !empty($cumulativeReport));
} catch (RuntimeException $exception) {
    http_response_code(404);
    exit(htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$adminRoute = 'reports';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($report['learner']['name'] . ' - ' . $report['term'] . ' Report', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/admin-modern.css?v=1">
    <link rel="stylesheet" href="../assets/css/report-sheet.css?v=3">
    <script src="../assets/js/html2pdf-loader.js?v=5"></script>
</head>
<body>
    <?php include __DIR__ . '/nav.html'; ?>
    <main class="report-workspace">
        <div class="report-workspace-inner">
            <?php require dirname(__DIR__) . '/views/shared/report-sheet.php'; ?>
        </div>
    </main>
    <script src="../assets/js/report-sheet.js?v=3"></script>
</body>
</html>
