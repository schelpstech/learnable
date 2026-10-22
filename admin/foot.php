<?php
require_once dirname(__DIR__) . '/classes/SchoolProfile.php';
require_once dirname(__DIR__) . '/config/database.php';

$adminFooterSchoolName = 'LearnAble School';
try {
    $adminFooterProfile = SchoolProfile::read(database_pdo());
    $adminFooterSchoolName = trim((string) ($adminFooterProfile['schname'] ?? '')) ?: $adminFooterSchoolName;
} catch (Throwable $exception) {
    error_log('Admin footer could not load the school profile.');
}
?>
<div class="footer-copyright-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="footer-copy-right">
                    <p><strong>LearnAble for <?php echo SchoolProfile::escape($adminFooterSchoolName); ?> - Powered by <a href="https://schelps.com.ng">SCHELPS</a></strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
