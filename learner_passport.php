<?php
require_once __DIR__ . '/conf.php';
require_once __DIR__ . '/classes/autoload.php';
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['ok'=>false, 'message'=>'Submit a passport using the upload form.']);
    exit;
}
try {
    $db = database_pdo();
    $access = new StaffAccess($db, $_SESSION);
    if (!$access->academic() && $access->role() !== 'teacher') throw new RuntimeException('You cannot upload learner passports with this account.');
    if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        throw new InvalidArgumentException('The upload exceeds the server limit. Choose a smaller JPG or PNG photograph.');
    }
    CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', $access->academic() ? 'admin' : 'portal');
    $service = new LearnerProfileService($db);
    $picture = $service->uploadPassport($_POST['learner_ref'] ?? '', $access, $_FILES['passport'] ?? []);
    echo json_encode(['ok'=>true, 'message'=>'Passport uploaded successfully.', 'picture'=>$picture, 'path'=>LearnerProfileService::picturePath($picture)]);
} catch (InvalidArgumentException $exception) {
    http_response_code(422);
    echo json_encode(['ok'=>false, 'message'=>$exception->getMessage()]);
} catch (RuntimeException $exception) {
    // Database and filesystem failures should not disclose internal connection details.
    http_response_code($exception instanceof PDOException ? 500 : 403);
    echo json_encode(['ok'=>false, 'message'=>$exception instanceof PDOException ? 'Unable to save the passport. Please try again.' : $exception->getMessage()]);
}
