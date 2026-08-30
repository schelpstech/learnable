<?php if (!isset($workflowCsrf)) { http_response_code(404); exit; } ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo wh($workflowTitle); ?> · LearnAble</title>
<link rel="stylesheet" href="css/bootstrap.min.css"><link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/admin-modern.css?v=1"><link rel="stylesheet" href="../assets/css/school-workflows.css?v=2">
</head><body><?php (function($adminRoute) { include __DIR__ . '/nav.html'; })($adminRoute ?? null); ?>
<main class="school-workspace admin-workspace"><header class="workspace-heading"><div><p class="workspace-eyebrow">School administration</p><h1><?php echo wh($workflowTitle); ?></h1><p><?php echo wh($workflowIntro); ?></p></div></header>
<?php if ($workflowNotice): ?><div class="workspace-notice" role="status"><?php echo wh($workflowNotice); ?></div><?php endif; ?>
<?php if ($workflowError): ?><div class="workspace-error" role="alert"><?php echo wh($workflowError); ?></div><?php endif; ?>
