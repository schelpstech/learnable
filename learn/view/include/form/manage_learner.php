<?php
$escapeLearner = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$passportLearner = $learner_profile;
$passportRoot = '../../../';
$passportScope = 'portal';
?>
<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <p><a class="btn_1" href="../../app/router.php?pageid=class_learners&amp;class_id=<?= rawurlencode($learner_profile['classid']) ?>">Back to student list</a></p>
                <h2><?= $escapeLearner($learner_profile['fname']) ?>'s profile</h2>
                <p><?= $escapeLearner($learner_profile['classname']) ?> · <?= $escapeLearner($learner_profile['uname']) ?></p>
                <?php require dirname(__DIR__, 4) . '/views/shared/learner-passport.php'; ?>
                <div class="white_card mb_30">
                    <div class="white_card_header"><h3>Learner details</h3></div>
                    <div class="white_card_body">
                        <form id="learner-details-form" onsubmit="event.preventDefault(); modify_learner();">
                            <input type="hidden" name="action" value="modify_learner">
                            <input type="hidden" name="learner_ref" value="<?= $escapeLearner($routeInstance) ?>">
                            <input type="hidden" name="csrf_token" value="<?= $escapeLearner(CbtSecurity::csrfToken('portal')) ?>">
                            <div class="mb-3">
                                <label for="fullname">Full name</label>
                                <input id="fullname" name="fullname" class="form-control" required maxlength="64" value="<?= $escapeLearner($learner_profile['fname']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-select">
                                    <option value="">Select gender</option>
                                    <?php foreach (['Male', 'Female'] as $gender): ?>
                                        <option value="<?= $gender ?>" <?= $learner_profile['gender'] === $gender ? 'selected' : '' ?>><?= $gender ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="date_of_birth">Date of birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= $escapeLearner($learner_profile['dob'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="phone">Phone number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" maxlength="11" value="<?= $escapeLearner($learner_profile['numb'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email">Email address</label>
                                <input type="email" id="email" name="email" class="form-control" maxlength="64" value="<?= $escapeLearner($learner_profile['email'] ?? '') ?>">
                            </div>
                            <button type="submit" class="btn_1">Save learner details</button>
                            <p id="learner-details-status" role="status" aria-live="polite" class="mt-3"></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
