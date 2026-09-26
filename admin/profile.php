<?php
require __DIR__ . '/conf.php';
require_once __DIR__ . '/../classes/SchoolProfile.php';
$profile = SchoolProfile::read(database_pdo());
if (empty($_SESSION['profile_csrf'])) { $_SESSION['profile_csrf'] = bin2hex(random_bytes(32)); }
$notice = $_SESSION['school_profile_notice'] ?? null;
$old = $_SESSION['school_profile_input'] ?? [];
unset($_SESSION['school_profile_notice'], $_SESSION['school_profile_input']);
function profile_value($name, $column) {
    global $profile, $old;
    $value = $old[$name] ?? $profile[$column] ?? '';
    return SchoolProfile::escape(is_string($value) ? $value : '');
}
$selectedSections = isset($old['profile_csrf']) ? ($old['school_sections'] ?? []) : SchoolProfile::sections($profile['school_sections'] ?? '');
if (!is_array($selectedSections)) { $selectedSections = []; }
$logo = SchoolProfile::imageUrl($profile['logo'] ?? '');
$photo = SchoolProfile::imageUrl($profile['school_photo'] ?? '');
$uploadMb = round(min(6 * 1024 * 1024, SchoolProfile::uploadLimit()) / 1048576, 1);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>School Profile | LearnAble</title>
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="../assets/css/school-profile.css?v=1">
</head>
<body>
<?php include __DIR__ . '/nav.html'; ?>
<main class="profile-page">
    <header class="profile-heading">
        <div><span class="profile-eyebrow">YOUR SCHOOL, ONLINE</span><h1>School profile</h1><p>Your school details and website, managed in one place.</p></div>
        <a class="profile-preview" href="../index.php" target="_blank" rel="noopener">View school website <i class="fa fa-external-link" aria-hidden="true"></i></a>
    </header>
    <?php if ($notice): ?><div class="alert alert-<?php echo $notice[0] === 'success' ? 'success' : 'danger'; ?>" role="status"><?php echo SchoolProfile::escape($notice[1]); ?></div><?php endif; ?>
    <form method="post" action="schprofile.php" enctype="multipart/form-data">
        <input type="hidden" name="profile_csrf" value="<?php echo SchoolProfile::escape($_SESSION['profile_csrf']); ?>">
        <section class="profile-card" aria-labelledby="school-details">
            <div class="profile-card-heading"><span class="profile-step">01</span><div><h2 id="school-details">School details</h2><p>These details identify your school across the portal.</p></div></div>
            <div class="profile-grid">
                <?php foreach ([['schname','schname','School name','text',254], ['schowner','proprietor',"Proprietor’s full name",'text',88], ['schmotto','motto','School motto','text',88], ['schaddress','address','School address','text',626], ['schphone','phone','School phone number','tel',16], ['schemail','email','School email','email',88], ['schweb','website','School website (optional)','url',88]] as [$name,$column,$label,$type,$max]): ?>
                <div class="profile-field"><label for="<?php echo $name; ?>"><?php echo SchoolProfile::escape($label); ?></label><input id="<?php echo $name; ?>" name="<?php echo $name; ?>" type="<?php echo $type; ?>" maxlength="<?php echo $max; ?>" value="<?php echo profile_value($name,$column); ?>" <?php echo $name !== 'schweb' ? 'required' : ''; ?>><?php if ($name === 'schowner'): ?><small>For school records; not displayed on the public website.</small><?php endif; ?></div>
                <?php endforeach; ?>
                <div class="profile-field"><label for="whatsapp_number">WhatsApp number (optional)</label><input id="whatsapp_number" name="whatsapp_number" type="tel" maxlength="32" placeholder="+2348012345678" aria-describedby="whatsapp-help" value="<?php echo profile_value('whatsapp_number','whatsapp_number'); ?>"><small id="whatsapp-help">Enter a WhatsApp-enabled number with country code, without the leading local zero. This is separate from your school phone number. Leave blank to hide the website’s WhatsApp button.</small></div>
                <div class="profile-field"><label for="schyear">Year founded</label><input id="schyear" name="schyear" type="number" min="1901" max="<?php echo date('Y'); ?>" required value="<?php echo profile_value('schyear','founded'); ?>"></div>
                <div class="profile-field profile-wide"><label for="schlogo">School logo</label><div class="profile-image-row"><?php if ($logo): ?><img class="profile-logo" src="../<?php echo SchoolProfile::escape($logo); ?>" alt="Current school logo"><?php endif; ?><div><input id="schlogo" name="schlogo" type="file" accept="image/jpeg,image/png"><small>JPG or PNG, up to <?php echo $uploadMb; ?> MB. Leave empty to keep your current logo.</small></div></div></div>
            </div>
        </section>
        <section class="profile-card" aria-labelledby="website-details">
            <div class="profile-card-heading"><span class="profile-step">02</span><div><h2 id="website-details">School website</h2><p>Just four optional additions. Saved content appears on your public home page.</p></div></div>
            <div class="profile-grid">
                <div class="profile-field profile-wide"><label for="about_school">About the school</label><textarea id="about_school" name="about_school" rows="4" maxlength="800" placeholder="Introduce your school in a few sentences: who you welcome and what matters to you."><?php echo profile_value('about_school','about_school'); ?></textarea><small>One short paragraph, up to 800 characters.</small></div>
                <fieldset class="profile-field profile-wide"><legend>School sections offered</legend><div class="profile-checks"><?php foreach (SchoolProfile::SECTIONS as $section): ?><label><input type="checkbox" name="school_sections[]" value="<?php echo $section; ?>" <?php echo in_array($section,$selectedSections,true) ? 'checked' : ''; ?>> <?php echo $section === 'Creche' ? 'Crèche' : $section; ?></label><?php endforeach; ?></div><small>Select only the sections your school offers.</small></fieldset>
                <div class="profile-field profile-wide"><label for="school_photo">Main school photograph</label><?php if ($photo): ?><img class="profile-photo" src="../<?php echo SchoolProfile::escape($photo); ?>" alt="Current school website photograph"><?php endif; ?><input id="school_photo" name="school_photo" type="file" accept="image/jpeg,image/png"><small>A landscape photograph works best. JPG or PNG, up to <?php echo $uploadMb; ?> MB. Leave empty to keep the current image.</small><?php if ($photo): ?><label class="profile-remove"><input type="checkbox" name="remove_photo" value="1"> Remove this photo and use the default image</label><?php endif; ?></div>
                <div class="profile-field profile-wide"><label for="admissions_message">Admissions message</label><textarea id="admissions_message" name="admissions_message" rows="3" maxlength="400" placeholder="Tell interested parents how to enquire or arrange a visit."><?php echo profile_value('admissions_message','admissions_message'); ?></textarea><small>Up to 400 characters. Leave blank to hide the admissions section.</small></div>
            </div>
        </section>
        <div class="profile-save"><p>Saving updates your school website immediately.</p><button type="submit" name="sch" value="Modify School Profile">Save school profile <i class="fa fa-check" aria-hidden="true"></i></button></div>
    </form>
</main>
</body>
</html>
