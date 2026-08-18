<?php

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../classes/autoload.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$pdo = database_pdo();
$sample = $pdo->query(
    'SELECT r.lid, r.term, r.classid FROM lhpresultrecord r
     INNER JOIN lhpresultconfig c ON c.term = r.term
     GROUP BY r.lid, r.term, r.classid ORDER BY MAX(r.rectime) DESC LIMIT 1'
)->fetch();
if (!$sample) throw new RuntimeException('A result sample is required for the report smoke test.');

$reportService = new ReportService($pdo, dirname(__DIR__));
$report = $reportService->build($sample['lid'], $sample['term'], stripos($sample['term'], '3rd') === 0);
if (!$report['subjects']) throw new RuntimeException('Report subjects were not loaded.');
if ($report['learner']['passport_url'] === '') throw new RuntimeException('Passport fallback was not resolved.');

ob_start();
require __DIR__ . '/../views/shared/report-sheet.php';
$reportHtml = ob_get_clean();
if (strpos($reportHtml, 'id="report-sheet"') === false || strpos($reportHtml, 'Progress across terms') === false) {
    throw new RuntimeException('The shared report sheet did not render its analytical layout.');
}
if (stripos($reportHtml, 'class position') !== false) {
    throw new RuntimeException('Class position must not be shown on learner reports.');
}
$classReports = $reportService->buildClass($sample['classid'], $sample['term'], stripos($sample['term'], '3rd') === 0);
if (!$classReports || $classReports[0]['learner']['class_id'] != $sample['classid']) {
    throw new RuntimeException('Class-wide reports were not built from the shared report service.');
}
$reportCss = file_get_contents(__DIR__ . '/../assets/css/report-sheet.css');
if (!preg_match('/@page\s*\{\s*size:\s*A4 portrait;\s*margin:\s*0;\s*\}/', $reportCss) || !preg_match('/height:\s*297mm/', $reportCss)) {
    throw new RuntimeException('The report is not constrained to one A4 page.');
}
if (strpos($reportCss, '.report-results td { height: 6mm;') === false) {
    throw new RuntimeException('The report result cells are not using the readable academic sizing.');
}

$calendarService = new CalendarService($pdo);
$term = $calendarService->activeTerm();
$class = $pdo->query('SELECT classid, classname FROM lhpclass ORDER BY classid LIMIT 1')->fetch();
if (!$term || !$class) throw new RuntimeException('Active term and class data are required.');

$pdo->beginTransaction();
try {
    $eventId = $calendarService->createAcademicEvent(array(
        'title' => 'Smoke test academic event',
        'description' => 'Rollback-only test event',
        'term' => $term,
        'class_id' => (string) $class['classid'],
        'start_date' => date('Y-m-d'),
        'end_date' => date('Y-m-d'),
        'start_time' => '09:00',
        'end_time' => '10:00',
    ), 'codex_smoke_admin');
    $month = $calendarService->month(date('Y-m'), (string) $class['classid'], $term);
    $found = false;
    foreach ($month['events'] as $event) {
        if ($event['id'] === $eventId) $found = true;
    }
    if (!$found) throw new RuntimeException('Created academic event was not visible in the class calendar.');

    $ownershipBlocked = false;
    try {
        $calendarService->createClassSession(array(
            'title' => 'Unauthorised class',
            'class_id' => (string) $class['classid'],
            'start_date' => date('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ), 'not_an_assigned_teacher', $term);
    } catch (RuntimeException $exception) {
        $ownershipBlocked = true;
    }
    if (!$ownershipBlocked) throw new RuntimeException('Class-teacher ownership was not enforced.');
} finally {
    if ($pdo->inTransaction()) $pdo->rollBack();
}

restore_error_handler();
echo "Report and calendar smoke tests passed.\n";
echo '- report learner: ' . $sample['lid'] . "\n";
echo '- report term: ' . $sample['term'] . "\n";
echo '- report subjects: ' . count($report['subjects']) . "\n";
echo '- class reports: ' . count($classReports) . "\n";
echo '- calendar ownership: enforced' . "\n";
