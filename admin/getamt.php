<?php
require __DIR__ . '/conf.php';
require_once dirname(__DIR__) . '/classes/autoload.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    $service = new PaymentLedgerService(database_pdo());
    if (isset($_GET['class_id'])) {
        $data = ['learners' => $service->learners($_GET['term'] ?? null, $_GET['class_id'])];
    } else {
        $data = ['balance' => $service->learnerBalance($_GET['term'] ?? null, $_GET['stdid'] ?? '')];
    }
    echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
} catch (InvalidArgumentException $error) {
    http_response_code(422);
    echo json_encode(['message' => $error->getMessage()]);
} catch (Throwable $error) {
    http_response_code(500);
    error_log($error->getMessage());
    echo json_encode(['message' => 'Unable to load the ledger.']);
}
