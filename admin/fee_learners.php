<?php
require __DIR__.'/conf.php';
require_once dirname(__DIR__).'/classes/autoload.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    $service=new FeeService(database_pdo());
    echo json_encode(array('ok'=>true,'learners'=>$service->learners($_GET['class_id'] ?? 0)),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
} catch(Throwable $e) {
    http_response_code($e instanceof InvalidArgumentException?422:500);
    echo json_encode(array('ok'=>false,'message'=>$e instanceof PDOException?'Unable to load learners.':$e->getMessage()));
}
