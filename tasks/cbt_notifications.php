<?php

require dirname(__DIR__) . '/classes/autoload.php';
require dirname(__DIR__) . '/config/database.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$pdo = database_pdo();
$expired = (new CbtAttemptService($pdo))->finalizeExpiredAttempts(null, 5000);
echo "Expired CBT scripts finalized: {$expired}\n";
$service = new CbtNotificationService($pdo);
$openingMinutes = (int) app_env('CBT_OPENING_REMINDER_MINUTES', 60);
$closingMinutes = (int) app_env('CBT_CLOSING_REMINDER_MINUTES', 60);
$queued = $service->queueDuePortalReminders($openingMinutes, $closingMinutes);
echo "CBT portal reminders created: {$queued}\n";
