<?php if (!isset($workflowCsrf)) { http_response_code(404); exit; } ?>
</main><script src="../assets/js/school-workflows.js?v=2"></script><?php foreach(($workflowScripts ?? array()) as $workflowScript): ?><script src="<?php echo wh($workflowScript); ?>"></script><?php endforeach; ?></body></html>
