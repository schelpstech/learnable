<?php

if (isset($_POST['staff'])) {
    header('Location: ./staff.php');
    exit;
}

if (isset($_POST['student'])) {
    header('Location: ./student.php');
    exit;
}

$school = array(
    'name' => 'LearnAble School',
    'address' => '',
    'motto' => 'Learning, character and service',
    'logo_url' => 'learn/asset/img/school/schlogo.png',
);

try {
    require_once __DIR__ . '/config/database.php';
    $schoolRow = database_pdo()->query('SELECT * FROM lhpschool ORDER BY schid LIMIT 1')->fetch();
    if ($schoolRow) {
        $school['name'] = trim((string) ($schoolRow['schname'] ?? '')) ?: $school['name'];
        $school['address'] = trim((string) ($schoolRow['address'] ?? ''));
        $school['motto'] = trim((string) ($schoolRow['motto'] ?? '')) ?: $school['motto'];
        $logoName = basename(trim((string) ($schoolRow['logo'] ?? '')));
        $logoDirectory = __DIR__ . '/learn/asset/img/school/';
        if ($logoName !== '' && is_file($logoDirectory . $logoName)) {
            $school['logo_url'] = 'learn/asset/img/school/' . rawurlencode($logoName);
        }
    }
} catch (Throwable $exception) {
    // The portal entrance remains available while the database is restarting.
}

function landing_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#26384a">
    <meta name="description" content="<?php echo landing_escape($school['name']); ?> learning and administration portal">
    <title><?php echo landing_escape($school['name']); ?> | School Portal</title>
    <link rel="shortcut icon" type="image/x-icon" href="admin/img/favicon.ico">
    <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/landing-modern.css?v=3">
</head>
<body>
    <div class="school-page">
        <header class="school-header">
            <a class="school-brand" href="index.php" aria-label="<?php echo landing_escape($school['name']); ?> home">
                <img src="<?php echo landing_escape($school['logo_url']); ?>" alt="<?php echo landing_escape($school['name']); ?> crest">
                <span>
                    <strong><?php echo landing_escape($school['name']); ?></strong>
                    <small>Academic portal</small>
                </span>
            </a>
            <nav aria-label="Administration">
                <a class="admin-entry" href="admin.php"><i class="fa fa-lock" aria-hidden="true"></i> Administration</a>
            </nav>
        </header>

        <main class="school-main">
            <section class="portal-intro" aria-labelledby="landing-title">
                <span class="academic-label">School information system</span>
                <h1 id="landing-title">Welcome to your school portal</h1>
                <p class="school-motto"><?php echo landing_escape($school['motto']); ?></p>
                <p class="intro-copy">Use the portal to follow lessons, review academic progress, manage class activities and keep up with the school calendar.</p>

                <section class="portal-access" aria-labelledby="access-title">
                    <div class="portal-access-heading">
                        <div>
                            <span>Secure account access</span>
                            <h2 id="access-title">Select your portal</h2>
                        </div>
                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                    </div>
                    <form method="post" action="index.php" class="portal-options">
                        <button type="submit" name="student" value="1">
                            <span class="portal-option-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
                            <span><strong>Learner portal</strong><small>Results, lessons, fees and calendar</small></span>
                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </button>
                        <button type="submit" name="staff" value="1">
                            <span class="portal-option-icon"><i class="fa fa-briefcase" aria-hidden="true"></i></span>
                            <span><strong>Staff portal</strong><small>Teaching, classes and school records</small></span>
                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </button>
                    </form>
                    <p><i class="fa fa-info-circle" aria-hidden="true"></i> Sign in with the account issued by the school.</p>
                </section>
            </section>

            <figure class="school-photograph">
                <img src="images/learnable-landing-classroom-v3.jpg" alt="A teacher guiding pupils during a classroom lesson">
                <figcaption>
                    <span>Learning in practice</span>
                    <strong>A clear connection between home, classroom and school.</strong>
                </figcaption>
            </figure>
        </main>

        <footer class="school-footer">
            <span>&copy; <?php echo date('Y'); ?> <?php echo landing_escape($school['name']); ?></span>
            <?php if ($school['address'] !== ''): ?><span><?php echo landing_escape($school['address']); ?></span><?php endif; ?>
            <span>Powered by LearnAble</span>
        </footer>
    </div>
</body>
</html>
