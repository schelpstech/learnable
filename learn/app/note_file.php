<?php
require_once __DIR__ . '/../controller/start.inc.php';
try {
    $actor=CbtSecurity::requirePortalRole(array('Instructor','Learner'));
    $note=(new NoteService($db_conn))->get($_GET['id'] ?? 0,$actor,$_SESSION['user_type']);
    if($note['type']!=='file') throw new RuntimeException('Not a document.');
    $name=basename(str_replace('\\','/',$note['content']));
    $root=realpath(dirname(__DIR__,2).'/instructor/noteoflesson');
    $file=$root ? realpath($root.DIRECTORY_SEPARATOR.$name) : false;
    if(!$file || !is_file($file) || strpos($file,$root.DIRECTORY_SEPARATOR)!==0) {http_response_code(404);exit('The lesson file is unavailable. Please contact your teacher.');}
    $ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));
    $inline=array('pdf'=>'application/pdf','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png');
    header('X-Content-Type-Options: nosniff');header('Cache-Control: no-store');header("Content-Security-Policy: sandbox");
    header('Content-Type: '.($inline[$ext] ?? 'application/octet-stream'));
    header('Content-Disposition: '.(isset($inline[$ext])?'inline':'attachment').'; filename="lesson.'.$ext.'"');
    readfile($file);
}catch(Throwable $e){http_response_code(403);echo 'This lesson document is not available for your account.';}
