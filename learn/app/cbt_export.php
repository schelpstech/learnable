<?php

require_once '../controller/start.inc.php';

$assessmentId = isset($_GET['assessment_id']) && ctype_digit((string) $_GET['assessment_id']) ? (int) $_GET['assessment_id'] : 0;
$isAdmin = isset($_SESSION['unamed']) && !empty($_SESSION['unamed']);
$isInstructor = isset($_SESSION['active'], $_SESSION['user_type']) && $_SESSION['user_type'] === 'Instructor';
if (!$assessmentId || (!$isAdmin && !$isInstructor)) {
    http_response_code(403);
    exit('Access denied.');
}
$actorId = $isAdmin ? (string) $_SESSION['unamed'] : (string) $_SESSION['active'];
$service = new CbtService($db_conn);
$assessment = $service->assessment($assessmentId);
$service->assertAssessmentManager($assessment, $actorId, $isAdmin);
$attemptService = new CbtAttemptService($db_conn);
$rows = $attemptService->attemptsForAssessment($assessmentId, $actorId, $isAdmin);

$filename = preg_replace('/[^A-Za-z0-9._-]+/', '-', $assessment['title']) . '-scores.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . trim($filename, '-') . '"');
header('X-Content-Type-Options: nosniff');
$output = fopen('php://output', 'wb');
fputcsv($output, array('Learner ID', 'Learner name', 'Attempt', 'Status', 'Raw score', 'Total marks', 'Percentage', 'Grade', 'Started', 'Submitted', 'Submission reference', 'Integrity flags'));
foreach ($rows as $row) {
    fputcsv($output, array(
        $row['learner_id'], $row['fname'], $row['attempt_no'], $row['status'],
        $row['total_score'], $assessment['total_marks'], $row['percentage'], $row['grade'],
        $row['started_at'], $row['submitted_at'], $row['submission_ref'], $row['warning_count']
    ));
}
fclose($output);
