<?php
if (isset($_POST['staff'])) { header('Location: ./staff.php'); exit; }
if (isset($_POST['student'])) { header('Location: ./student.php'); exit; }
require_once __DIR__ . '/classes/SchoolProfile.php';
$school = [];
try {
    require_once __DIR__ . '/config/database.php';
    $school = SchoolProfile::read(database_pdo());
} catch (Throwable $exception) {
    // Keep the public entrance and portal links available during database outages.
}
function landing_escape($value) { return SchoolProfile::escape($value); }
$name = trim($school['schname'] ?? '') ?: 'LearnAble School';
$motto = trim($school['motto'] ?? '');
$about = trim($school['about_school'] ?? '');
$admissions = trim($school['admissions_message'] ?? '');
$address = trim($school['address'] ?? '');
$email = filter_var($school['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '';
$phone = trim($school['phone'] ?? '');
$phoneLink = preg_replace('/[^0-9+]/', '', $phone);
$whatsappNumber = SchoolProfile::whatsappNumber($school['whatsapp_number'] ?? '');
$logo = SchoolProfile::imageUrl($school['logo'] ?? '');
$photo = SchoolProfile::imageUrl($school['school_photo'] ?? '');
$hasSchoolPhoto = $photo !== '';
$photo = $photo ?: 'images/learnable-landing-classroom-v3.jpg';
$sections = SchoolProfile::sections($school['school_sections'] ?? '');
$founded = (int) ($school['founded'] ?? 0);
$hasFounded = $founded >= 1901 && $founded <= (int) date('Y');
$description = $about ?: $name . ($motto !== '' ? ' — ' . $motto : '') . '. School information, contact details and portal access.';
$sectionLabels = ['Creche' => 'Crèche', 'Nursery' => 'Nursery', 'Primary' => 'Primary', 'Secondary' => 'Secondary'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173e36">
    <meta name="description" content="<?php echo landing_escape($description); ?>">
    <meta property="og:title" content="<?php echo landing_escape($name); ?>">
    <meta property="og:description" content="<?php echo landing_escape($description); ?>">
    <meta property="og:type" content="website">
    <title><?php echo landing_escape($name); ?> | Welcome</title>
    <?php if ($logo): ?><link rel="icon" href="<?php echo landing_escape($logo); ?>"><?php endif; ?>
    <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/landing-modern.css?v=5">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<div class="contact-strip"><div class="page-width"><span><?php echo $hasFounded ? 'Growing together since ' . $founded : 'Welcome to our school community'; ?></span><div><?php if ($phoneLink): ?><a href="tel:<?php echo landing_escape($phoneLink); ?>"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo landing_escape($phone); ?></a><?php endif; ?><?php if ($email): ?><a class="strip-email" href="mailto:<?php echo landing_escape($email); ?>"><?php echo landing_escape($email); ?></a><?php endif; ?></div></div></div>
<header class="school-header page-width">
    <a class="school-brand" href="index.php" aria-label="<?php echo landing_escape($name); ?> home">
        <?php if ($logo): ?><img src="<?php echo landing_escape($logo); ?>" alt="" width="60" height="60"><?php else: ?><span class="brand-mark"><i class="fa fa-graduation-cap" aria-hidden="true"></i></span><?php endif; ?>
        <span><strong><?php echo landing_escape($name); ?></strong><?php if ($motto): ?><small><?php echo landing_escape($motto); ?></small><?php endif; ?></span>
    </a>
    <nav aria-label="Main navigation">
        <?php if ($about): ?><a href="#about">About us</a><?php endif; ?>
        <?php if ($sections): ?><a href="#our-school">Our school</a><?php endif; ?>
        <a href="#contact">Contact</a>
        <a class="nav-portal" href="#portals">School portal <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
    </nav>
</header>
<main id="main">
    <section class="hero page-width" aria-labelledby="welcome-title">
        <div class="hero-copy"><span class="eyebrow"><span></span> A WARM WELCOME TO</span><h1 id="welcome-title"><?php echo landing_escape($name); ?></h1><?php if ($motto): ?><p class="hero-motto"><?php echo landing_escape($motto); ?></p><?php endif; ?><p class="hero-description">Get to know our school, connect with us, and access everything you need for your school day.</p><div class="hero-actions"><a class="button button-green" href="#contact">Contact the school <i class="fa fa-arrow-right" aria-hidden="true"></i></a><a class="text-link" href="#portals">Access your portal <i class="fa fa-angle-right" aria-hidden="true"></i></a></div><div class="hero-footnote"><i class="fa fa-graduation-cap" aria-hidden="true"></i><span><?php echo $hasFounded ? 'Part of your learning journey since ' . $founded : 'A place for learning. A school community.'; ?></span></div></div>
        <figure class="hero-image"><img src="<?php echo landing_escape($photo); ?>" alt="<?php echo $hasSchoolPhoto ? landing_escape($name . ' — school photograph') : 'A teacher guiding pupils during a classroom lesson'; ?>" fetchpriority="high" width="900" height="1000"><figcaption><span>OUR SCHOOL. OUR COMMUNITY.</span><strong>Learning brings us together.</strong></figcaption></figure>
    </section>
    <?php if ($about): ?>
    <section id="about" class="about-section page-width" aria-labelledby="about-title"><div><span class="eyebrow">ROOTED IN OUR COMMUNITY</span><h2 id="about-title">Get to know<br>our school.</h2><?php if ($hasFounded): ?><span class="founded-label">Established <?php echo $founded; ?></span><?php endif; ?></div><div class="about-copy"><p><?php echo nl2br(landing_escape($about)); ?></p><a class="text-link" href="#contact">We’d love to hear from you <i class="fa fa-arrow-right" aria-hidden="true"></i></a></div></section>
    <?php endif; ?>
    <?php if ($sections): ?>
    <section id="our-school" class="sections-band" aria-labelledby="sections-title"><div class="page-width"><div class="section-heading"><div><span class="eyebrow">OUR SCHOOL</span><h2 id="sections-title">Room to learn and grow.</h2></div><p>Explore the sections available at <?php echo landing_escape($name); ?>.</p></div><div class="school-sections"><?php foreach ($sections as $i => $section): ?><article class="section-card"><span class="section-number"><?php echo sprintf('%02d', $i + 1); ?></span><i class="fa fa-<?php echo ['Creche'=>'heart-o','Nursery'=>'sun-o','Primary'=>'book','Secondary'=>'graduation-cap'][$section]; ?>" aria-hidden="true"></i><h3><?php echo $sectionLabels[$section]; ?></h3><a href="#contact">Enquire about this section <span aria-hidden="true">↗</span></a></article><?php endforeach; ?></div></div></section>
    <?php endif; ?>
    <?php if ($admissions): ?>
    <section class="admissions page-width" aria-labelledby="admissions-title"><div class="admissions-box"><div><span class="eyebrow">JOIN OUR SCHOOL COMMUNITY</span><h2 id="admissions-title">Your next chapter starts here.</h2><p><?php echo nl2br(landing_escape($admissions)); ?></p></div><a class="button button-green" href="#contact">Admissions enquiries <i class="fa fa-arrow-right" aria-hidden="true"></i></a></div></section>
    <?php endif; ?>
    <section id="portals" class="portal-section page-width" aria-labelledby="portal-title"><div class="section-heading"><div><span class="eyebrow">ALREADY PART OF OUR SCHOOL?</span><h2 id="portal-title">Your school day, connected.</h2></div><p>Sign in with the account issued by the school.</p></div><div class="portal-cards"><a class="portal-card" href="student.php"><span class="portal-icon"><i class="fa fa-graduation-cap" aria-hidden="true"></i></span><div><h3>Learner portal</h3><p>Lessons, results, fees and your school calendar.</p></div><span class="portal-arrow" aria-hidden="true">↗</span></a><a class="portal-card" href="staff.php"><span class="portal-icon"><i class="fa fa-briefcase" aria-hidden="true"></i></span><div><h3>Staff portal</h3><p>Teaching, classes and school records.</p></div><span class="portal-arrow" aria-hidden="true">↗</span></a></div></section>
    <section id="contact" class="contact-section" aria-labelledby="contact-title"><div class="page-width contact-grid"><div><span class="eyebrow">LET’S TALK</span><h2 id="contact-title">A conversation is<br>a good place to start.</h2><p>For school enquiries or help accessing your account, get in touch with us.</p></div><div class="contact-details"><?php if ($address): ?><div><i class="fa fa-map-marker" aria-hidden="true"></i><div><h3>Visit our school</h3><p><?php echo landing_escape($address); ?></p><a href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo rawurlencode($name . ', ' . $address); ?>" target="_blank" rel="noopener">Get directions <span aria-hidden="true">↗</span></a></div></div><?php endif; ?><?php if ($phoneLink): ?><div><i class="fa fa-phone" aria-hidden="true"></i><div><h3>Call us</h3><a href="tel:<?php echo landing_escape($phoneLink); ?>"><?php echo landing_escape($phone); ?></a></div></div><?php endif; ?><?php if ($email): ?><div><i class="fa fa-envelope-o" aria-hidden="true"></i><div><h3>Email us</h3><a href="mailto:<?php echo landing_escape($email); ?>"><?php echo landing_escape($email); ?></a></div></div><?php endif; ?><?php if (!$address && !$phoneLink && !$email): ?><p>Please contact the school office for enquiries.</p><?php endif; ?></div></div></section>
</main>
<?php if ($whatsappNumber !== ''): ?>
<a class="whatsapp-button" href="https://wa.me/<?php echo landing_escape($whatsappNumber); ?>" target="_blank" rel="noopener noreferrer" aria-label="Chat with <?php echo landing_escape($name); ?> on WhatsApp (opens in a new tab)"><i class="fa fa-whatsapp" aria-hidden="true"></i><span>Chat on WhatsApp</span></a>
<?php endif; ?>
<footer class="school-footer page-width"><span>© <?php echo date('Y'); ?> <?php echo landing_escape($name); ?></span><div><a href="admin.php"><i class="fa fa-lock" aria-hidden="true"></i> Administration</a><span>Powered by LearnAble</span></div></footer>
</body>
</html>
