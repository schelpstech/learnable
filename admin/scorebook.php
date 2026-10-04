<?php
require __DIR__ . '/workflow_bootstrap.php';
$adminRoute='scores';
$workflowTitle='Score entry';
$workflowIntro='Manage class scores for the active term. Published results stay locked until score entry is reopened in Result settings.';
$service=new ScorebookService($workflowDb);
$actor=$staffAccess->username();
$source=$_SERVER['REQUEST_METHOD']==='POST' ? $_POST : $_GET;
$class=$subject=$week=0; $sheet=[]; $config=null; $term=null;
try {
    $class=CbtSecurity::positiveInt($source['class_id'] ?? 0,'Class',0,PHP_INT_MAX);
    $subject=CbtSecurity::positiveInt($source['subject_id'] ?? 0,'Subject',0,PHP_INT_MAX);
    $week=CbtSecurity::positiveInt($source['week'] ?? 0,'Week',0,13);
    $term=$service->activeTerm();
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        CbtSecurity::requireCsrf($_POST['csrf_token'] ?? null,'admin');
        $changes=$_POST['changes'] ?? [];
        if (!is_array($changes)) throw new InvalidArgumentException('Invalid score sheet.');
        if (count($changes)!==(int)($_POST['row_count'] ?? -1)) throw new RuntimeException('The score sheet was incomplete. No scores were saved. Contact the administrator to increase the form input limit.');
        foreach ($changes as $change) {
            if (!is_array($change) || !array_key_exists('score',$change) || (!$week && !array_key_exists('examscore',$change))) throw new RuntimeException('The score sheet was incomplete. No scores were saved.');
        }
        $count=$service->save($actor,$class,$subject,$week,$changes,true);
        $_SESSION['workflow_notice']=$count.' student score record(s) saved.';
        header('Location: index.php?route=scores&class_id='.$class.'&subject_id='.$subject.'&week='.$week); exit;
    }
    $config=$service->config();
    if ($class && $subject) $sheet=$service->sheet($actor,$class,$subject,$week,true);
} catch (Throwable $e) { $workflowError=workflow_error($e); }
$classes=$workflowDb->query('SELECT classid,classname FROM lhpclass ORDER BY classname')->fetchAll(PDO::FETCH_ASSOC);
$subjects=[];
if ($class && $term) { $q=$workflowDb->prepare('SELECT DISTINCT s.sbjid,s.sbjname FROM lhpsubject s JOIN lhpalloc a ON a.sbjid=s.sbjid WHERE a.classid=? AND a.term=? ORDER BY s.sbjname'); $q->execute([$class,$term]); $subjects=$q->fetchAll(PDO::FETCH_ASSOC); }
$locked=!$config || (int)$config[$week ? 'midterm' : 'status']===1;
require __DIR__ . '/workflow_header.php';
?>
<section class="workspace-card"><form method="get" action="index.php" class="workspace-form"><input type="hidden" name="route" value="scores"><div class="workspace-fields">
<label>Class<select name="class_id" required onchange="this.form.subject_id.value=''; this.form.submit()"><option value="">Select class</option><?php foreach($classes as $row): ?><option value="<?php echo (int)$row['classid']; ?>" <?php echo $class===$row['classid'] || $class===(int)$row['classid'] ? 'selected' : ''; ?>><?php echo wh($row['classname']); ?></option><?php endforeach; ?></select></label>
<label>Subject<select name="subject_id" required><option value="">Select allocated subject</option><?php foreach($subjects as $row): ?><option value="<?php echo (int)$row['sbjid']; ?>" <?php echo $subject===(int)$row['sbjid'] ? 'selected' : ''; ?>><?php echo wh($row['sbjname']); ?></option><?php endforeach; ?></select></label>
<label>Score sheet<select name="week"><option value="0">Term CA & examination</option><?php for($i=1;$i<=13;$i++): ?><option value="<?php echo $i; ?>" <?php echo $week===$i ? 'selected' : ''; ?>>Week <?php echo $i; ?></option><?php endfor; ?></select></label>
</div><button class="workspace-button" type="submit">Open score sheet</button></form></section>
<?php if($class && $subject && !$workflowError): ?>
<section class="workspace-card"><h2><?php echo $week ? 'Week '.$week.' scores' : 'Term scores'; ?></h2>
<?php if($locked): ?><p class="workspace-notice">Score entry is locked. Reopen it in Result settings before making changes.</p><?php endif; ?>
<?php if(!$sheet): ?><p>No active students in this class.</p><?php else: ?>
<form method="post" action="index.php?route=scores"><input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>"><input type="hidden" name="class_id" value="<?php echo $class; ?>"><input type="hidden" name="subject_id" value="<?php echo $subject; ?>"><input type="hidden" name="week" value="<?php echo $week; ?>"><input type="hidden" name="row_count" value="<?php echo count($sheet); ?>">
<div class="workspace-table-wrap"><table class="workspace-table score-table"><thead><tr><th>Student</th><th><?php echo $week ? 'Score / 10' : 'CA / '.(int)$config['ca_score']; ?></th><?php if(!$week): ?><th>Exam / <?php echo (int)$config['exam_score']; ?></th><th>Total</th><?php endif; ?></tr></thead><tbody>
<?php foreach($sheet as $i=>$learner): ?><tr><td><?php echo wh($learner['fname']); ?><br><small><?php echo wh($learner['uname']); ?></small><input type="hidden" name="changes[<?php echo $i; ?>][learner]" value="<?php echo wh($learner['uname']); ?>"><input type="hidden" name="changes[<?php echo $i; ?>][version]" value="<?php echo wh($learner['version']); ?>"></td>
<td><input aria-label="Score for <?php echo wh($learner['fname']); ?>" type="number" min="0" max="<?php echo $week ? 10 : (int)$config['ca_score']; ?>" name="changes[<?php echo $i; ?>][score]" value="<?php echo wh($learner['record']['score'] ?? ''); ?>" <?php echo $locked ? 'disabled' : ''; ?>></td>
<?php if(!$week): ?><td><input aria-label="Exam score for <?php echo wh($learner['fname']); ?>" type="number" min="0" max="<?php echo (int)$config['exam_score']; ?>" name="changes[<?php echo $i; ?>][examscore]" value="<?php echo wh($learner['record']['examscore'] ?? ''); ?>" <?php echo $locked ? 'disabled' : ''; ?>></td><td><?php echo wh($learner['record']['totalscore'] ?? '—'); ?></td><?php endif; ?></tr><?php endforeach; ?>
</tbody></table></div><button class="workspace-button" type="submit" <?php echo $locked ? 'disabled' : ''; ?>>Save scores</button></form>
<?php endif; ?></section><?php endif; ?>
<?php require __DIR__ . '/workflow_footer.php'; ?>
