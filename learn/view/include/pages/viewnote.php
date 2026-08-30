<?php
$noteService=new NoteService(database_pdo());$h=function($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');};
try{$lesson=$noteService->get($portalRoute->param('ref'),$_SESSION['active'],$_SESSION['user_type']);}catch(Throwable $e){echo '<div class="main_content_iner"><div class="alert alert-danger">This note is not available for your account.</div></div>';return;}
$written=$lesson['type']==='text';$html=$written?NoteService::html($lesson['content']):'';
$wordCount=str_word_count(strip_tags($html));$notice=$_SESSION['note_notice'] ?? '';unset($_SESSION['note_notice']);
?>
<div class="main_content_iner school-workspace"><div class="container-fluid p-0 note-reading">
<header class="workspace-heading"><div><p class="workspace-eyebrow"><?php echo $h($lesson['sbjname'].' · '.$lesson['class_name'].' · '.$lesson['week']); ?></p><h1><?php echo $h($lesson['topic']); ?></h1><p><?php echo $h($lesson['staffname'] ?: $lesson['staffid']); ?><?php if($written): ?> · <?php echo max(1,(int)ceil($wordCount/180)); ?> min read<?php endif; ?></p></div><div class="workspace-actions"><button type="button" data-print class="workspace-button secondary">Print lesson</button><?php if($_SESSION['user_type']==='Instructor' && $lesson['staffid']===$_SESSION['active']): ?><a class="workspace-button" href="../../app/router.php?pageid=resources&amp;item=modify_note&amp;item_ref=<?php echo (int)$lesson['noteid']; ?>">Edit note</a><?php endif; ?></div></header>
<?php if($notice): ?><div class="workspace-notice" role="status"><?php echo $h($notice); ?></div><?php endif; ?>
<article class="workspace-card">
<?php if($written): ?><div class="note-content"><?php echo $html; ?></div>
<?php elseif($lesson['type']==='file'): ?><p class="workspace-muted">Open the original lesson document below.</p><a class="workspace-button" href="../../app/note_file.php?id=<?php echo (int)$lesson['noteid']; ?>" target="_blank" rel="noopener">Open lesson document</a>
<?php else: ?><?php try{$resource=NoteService::safeUrl($lesson['content']);}catch(Throwable $e){$resource=null;} ?>
<?php if($resource): ?><h2>Learning resource</h2><p>This lesson includes a resource on <?php echo $h(parse_url($resource,PHP_URL_HOST)); ?>. It opens in a new tab.</p><a class="workspace-button" href="<?php echo $h($resource); ?>" target="_blank" rel="noopener noreferrer">Open learning resource</a><?php else: ?><div class="workspace-error">This resource link needs to be updated by your teacher.</div><?php endif; ?>
<?php endif; ?></article><a class="workspace-button secondary no-print" href="../../app/router.php?pageid=note&amp;subjectid=<?php echo (int)$lesson['sbjid']; ?>">Back to subject notes</a></div></div>
