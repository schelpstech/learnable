<?php
if (!isset($classLearnerList)) { http_response_code(404); exit('Class not found.'); }
$classname = $classLearnerList['class'];
$learner_list = $classLearnerList['learners'];
$learner_statistics = $classLearnerList['statistics'];
?>
<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <h2>Student list · <?= htmlspecialchars($classname['classname'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><a href="../../app/router.php?pageid=class_manager">Back to class manager</a></p>
        <div class="row"><?php require __DIR__ . '/learner_list.php'; ?></div>
    </div>
</div>
