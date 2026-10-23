<?php
require __DIR__ . '/workflow_bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Use Edit class teacher to change the assignment.');
}
$classId = filter_var($_POST['class_id'] ?? $_POST['curclass'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
try {
    CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', 'admin');
    (new AcademicStructureService($workflowDb))->saveClassTeacher([
        'class_id'=>$classId ?: 0,
        'teacher_id'=>$_POST['teacher_id'] ?? $_POST['supervisor'] ?? '',
        'term'=>$_POST['term'] ?? ''
    ], $staffAccess->username());
    workflow_redirect('classes', 'Class teacher updated successfully.');
} catch (Throwable $error) {
    $_SESSION['class_teacher_error'] = workflow_error($error);
    header('Location: ' . ($classId ? 'allocateclass.php?classid=' . (int)$classId : 'index.php?route=classes'));
    exit;
}
