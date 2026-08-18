<?php

require_once '../controller/start.inc.php';
if (!isset($_SESSION['active'], $_SESSION['user_type']) || $_SESSION['user_type'] !== 'Instructor') {
    http_response_code(403);
    exit('Access denied.');
}
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="learnable-cbt-question-template.csv"');
header('X-Content-Type-Options: nosniff');
$output = fopen('php://output', 'wb');
fputcsv($output, array(
    'class_id', 'subject_id', 'scheme_id', 'question_type', 'difficulty',
    'prompt', 'marks', 'options', 'correct_answers', 'accepted_answers',
    'match_keys', 'learning_objective', 'explanation', 'visibility'
));
fputcsv($output, array(
    '7', '44', '123', 'single_choice', 'medium',
    'Which number is even?', '1', '3|4|5|7', '1', '', '',
    'Identify an even number.', 'Four is divisible by two.', 'private'
));
fclose($output);
