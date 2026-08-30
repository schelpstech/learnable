<?php
require_once __DIR__ . '/../controller/start.inc.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    $actor=CbtSecurity::requirePortalRole(array('Instructor'));
    if($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405);throw new RuntimeException('Use the scorebook to save scores.'); }
    CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '','portal');
    $changes=json_decode($_POST['changes'] ?? '',true);
    if(!is_array($changes)) throw new InvalidArgumentException('Invalid score data.');
    $service=new ScorebookService($db_conn);
    $count=$service->save($actor,$_POST['class_id'] ?? '',$_POST['subject_id'] ?? '',$_POST['week'] ?? 0,$changes);
    echo json_encode(array('ok'=>true,'message'=>$count.' learner records saved.','rows'=>$service->sheet($actor,$_POST['class_id'],$_POST['subject_id'],$_POST['week'] ?? 0)));
} catch(Throwable $e) {
    if(http_response_code()<400) http_response_code(422);
    if($e instanceof PDOException) error_log($e->getMessage());
    echo json_encode(array('ok'=>false,'message'=>$e instanceof PDOException?'Unable to save. Your entries are still on screen. Please try again.':$e->getMessage()));
}
