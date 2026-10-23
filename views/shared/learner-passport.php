<?php
// Caller supplies the learner record, application-root prefix, and CSRF scope.
$passportEscape = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$passportLimit = LearnerProfileService::uploadLimit();
?>
<link rel="stylesheet" href="<?= $passportEscape($passportRoot) ?>assets/css/learner-passport.css">
<section class="learner-passport-card" aria-labelledby="passport-title">
    <div>
        <h3 id="passport-title">Learner's passport</h3>
        <p>Upload or replace the photograph used on the learner's profile and reports.</p>
    </div>
    <form action="<?= $passportEscape($passportRoot) ?>learner_passport.php" method="post" enctype="multipart/form-data" class="learner-passport-form" data-root="<?= $passportEscape($passportRoot) ?>" data-max-bytes="<?= $passportLimit ?>">
        <img class="learner-passport-image" src="<?= $passportEscape($passportRoot . LearnerProfileService::picturePath($passportLearner['picture'] ?? '')) ?>" alt="Learner passport" width="120" height="145" data-learner-passport="<?= $passportEscape($passportLearner['uname']) ?>">
        <div class="learner-passport-controls">
            <input type="hidden" name="csrf_token" value="<?= $passportEscape(CbtSecurity::csrfToken($passportScope)) ?>">
            <input type="hidden" name="learner_ref" value="<?= $passportEscape($passportLearner['uname']) ?>">
            <label for="learner-passport-file">Choose passport photograph</label>
            <input id="learner-passport-file" type="file" name="passport" accept="image/jpeg,image/png" required aria-describedby="passport-file-help">
            <small id="passport-file-help">JPG or PNG, up to <?= $passportEscape(round($passportLimit / 1048576, 1)) ?> MB. Preview the photograph, then upload.</small>
            <button type="submit" class="learner-passport-button">Upload passport</button>
            <p class="learner-passport-status" role="status" aria-live="polite"></p>
        </div>
    </form>
</section>
<script src="<?= $passportEscape($passportRoot) ?>assets/js/learner-passport.js" defer></script>
