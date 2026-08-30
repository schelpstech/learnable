<?php
require_once __DIR__ . '/../controller/start.inc.php';
try {
    $actor=CbtSecurity::requirePortalRole(array('Instructor'));
    if($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405);exit('Use the note editor.'); }
    CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '','portal');
    $service=new NoteService($db_conn);
    if(($_POST['action'] ?? '')==='remove') {
        $service->remove($_POST['id'] ?? 0,$_POST['version'] ?? '',$actor,($_POST['confirm'] ?? '')==='yes');
        $_SESSION['note_notice']='Note removed from the library. Its content is retained in the audit history.';
        header('Location: router.php?pageid=resources&item=add_note');exit;
    }
    $id=$service->save($_POST,$actor);$_SESSION['note_notice']='Note saved and available to the class.';
    header('Location: router.php?pageid=note&ref='.$id);exit;
} catch(Throwable $e) {
    if($e instanceof PDOException) error_log($e->getMessage());
    $_SESSION['note_error']=$e instanceof PDOException?'Unable to save this note. Your draft has been kept.':$e->getMessage();
    $_SESSION['note_draft']=array_intersect_key($_POST,array_flip(array('id','topicid','type','content','weblink','version')));
    $id=filter_var($_POST['id'] ?? 0,FILTER_VALIDATE_INT);
    header('Location: router.php?pageid=resources&item='.($id?'modify_note&item_ref='.$id:'add_note'));exit;
}
