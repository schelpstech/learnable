<?php

$sharedClasses = array(
    'DBController.php',
    'Model.php',
    'User.php',
    'Utility.php',
    'DBBackup.php',
    'PortalRoute.php',
    'ReportService.php',
    'CalendarService.php',
    'CbtSecurity.php',
    'CbtService.php',
    'CbtAttemptService.php',
    'CbtResultService.php',
    'CbtNotificationService.php',
    'SchoolService.php',
    'DiscountService.php',
    'FeeService.php',
    'ExpenseService.php',
    'InventoryService.php',
    'TeachingService.php',
    'ScorebookService.php',
    'NoteService.php',
);

foreach ($sharedClasses as $classFile) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . $classFile;
}
