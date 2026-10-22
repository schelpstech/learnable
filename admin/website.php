<?php
require __DIR__ . '/conf.php';
require_once dirname(__DIR__) . '/classes/SchoolWebsite.php';
$websiteDb = database_pdo();
if (empty($_SESSION['website_csrf'])) $_SESSION['website_csrf'] = bin2hex(random_bytes(32));
$websiteLabels = ['gallery' => 'Gallery', 'event' => 'Events', 'announcement' => 'Announcements'];
$websiteToday = (new DateTimeImmutable('today', new DateTimeZone('Africa/Lagos')))->format('Y-m-d');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newImage = null;
    $kind = is_string($_POST['kind'] ?? null) && isset($websiteLabels[$_POST['kind']]) ? $_POST['kind'] : 'gallery';
    $id = filter_var($_POST['id'] ?? '0', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    try {
        if (!is_string($_POST['website_csrf'] ?? null) || !hash_equals($_SESSION['website_csrf'], $_POST['website_csrf'])) throw new InvalidArgumentException('The form expired or the upload exceeded the server limit. Refresh and try again.');
        if ($id === false) throw new InvalidArgumentException('Invalid website item.');
        $action = $_POST['website_action'] ?? '';
        if ($action === 'delete' && $id > 0 && ($_POST['confirm_delete'] ?? '') === '1') {
            SchoolWebsite::delete($websiteDb, $id);
            $message = 'Website item removed.';
        } elseif ($action === 'save') {
            $data = SchoolWebsite::validate($_POST);
            if ($id) SchoolWebsite::find($websiteDb, $id);
            if ($data['kind'] === 'gallery') $newImage = SchoolProfile::upload($_FILES['gallery_photo'] ?? [], 'school-gallery');
            SchoolWebsite::save($websiteDb, $_POST, $newImage, $staffAccess->username(), $id);
            $message = $data['published'] ? 'Saved and published on the school website.' : 'Draft saved. It is hidden from website visitors.';
            if ($data['published'] && (($data['event_date'] && $data['event_date'] < $websiteToday) || ($data['expires_on'] && $data['expires_on'] < $websiteToday))) $message = 'Saved for your records. This past or expired item is hidden from website visitors.';
        } else throw new InvalidArgumentException('Choose a valid action and confirm removal when deleting.');
        $_SESSION['website_notice'] = ['success', $message];
        unset($_SESSION['website_input']);
    } catch (Throwable $exception) {
        if ($newImage !== null) SchoolWebsite::removeImage($newImage);
        $_SESSION['website_notice'] = ['danger', $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Unable to save website content. Please try again or contact your administrator.'];
        if (!($exception instanceof InvalidArgumentException)) error_log('School website save: ' . $exception->getMessage());
        if (($_POST['website_action'] ?? '') === 'save') $_SESSION['website_input'] = array_intersect_key($_POST, array_flip(['id','kind','title','body','event_date','venue','expires_on','published']));
    }
    header('Location: index.php?route=website#' . $kind);
    exit;
}
$notice = $_SESSION['website_notice'] ?? null;
$old = $_SESSION['website_input'] ?? [];
unset($_SESSION['website_notice'], $_SESSION['website_input']);
$rows = []; $edit = null; $ready = true;
try {
    $rows = SchoolWebsite::all($websiteDb);
    if (isset($_GET['edit'])) {
        $editId = filter_var($_GET['edit'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$editId) throw new InvalidArgumentException('Invalid website item.');
        $edit = SchoolWebsite::find($websiteDb, $editId);
    }
} catch (InvalidArgumentException $exception) { $notice = ['danger', $exception->getMessage()]; }
catch (Throwable $exception) { $ready = false; $notice = ['danger', 'Website content needs its database update. Ask your administrator to run database/migrate.php before adding items.']; }
$uploadMb = round(min(6 * 1024 * 1024, SchoolProfile::uploadLimit()) / 1048576, 1);
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>School website | LearnAble</title><link rel="stylesheet" href="css/bootstrap.min.css"><link rel="stylesheet" href="css/font-awesome.min.css"><link rel="stylesheet" href="../assets/css/school-profile.css?v=2"></head>
<body>
<?php include __DIR__ . '/nav.html'; ?>
<main class="profile-page website-editor">
<header class="profile-heading"><div><span class="profile-eyebrow">YOUR SCHOOL, ONLINE</span><h1>School website</h1><p>Share school life, upcoming events and news with visitors.</p></div><a class="profile-preview" href="../index.php" target="_blank" rel="noopener">View school website <i class="fa fa-external-link" aria-hidden="true"></i></a></header>
<nav class="website-tabs" aria-label="Website sections"><?php foreach ($websiteLabels as $key => $label): ?><a href="#<?php echo $key; ?>"><?php echo $label; ?></a><?php endforeach; ?><a href="index.php?route=profile">School profile</a></nav>
<p class="website-public-note"><i class="fa fa-globe" aria-hidden="true"></i> Published items are visible to everyone on the public school website. Portal notices and private academic calendars stay separate.</p>
<?php if ($notice): ?><div class="alert alert-<?php echo $notice[0] === 'success' ? 'success' : 'danger'; ?>" role="status"><?php echo SchoolProfile::escape($notice[1]); ?></div><?php endif; ?>
<?php if ($ready): foreach ($websiteLabels as $kind => $label):
    $item = ['id'=>0,'title'=>'','body'=>'','event_date'=>'','venue'=>'','expires_on'=>'','published'=>'1','image_filename'=>''];
    if ($edit && $edit['kind'] === $kind) $item = $edit;
    if (($old['kind'] ?? '') === $kind) {
        foreach ($old as $key => $value) if (is_string($value)) $item[$key] = $value;
    }
    $isEdit = (int) $item['id'] > 0;
    $list = array_filter($rows, static function ($row) use ($kind) { return $row['kind'] === $kind; });
?>
<section id="<?php echo $kind; ?>" class="profile-card">
<div class="profile-card-heading"><span class="profile-step"><i class="fa fa-<?php echo ['gallery'=>'picture-o','event'=>'calendar-o','announcement'=>'bullhorn'][$kind]; ?>" aria-hidden="true"></i></span><div><h2><?php echo $label; ?></h2><p><?php echo ['gallery'=>'The latest 30 published photos slide across every 10 seconds.','event'=>'Show the next six upcoming events. Past events stay here for your records.','announcement'=>'Show the latest six announcements. Expired items are automatically hidden.'][$kind]; ?></p></div></div>
<form method="post" action="website.php" enctype="multipart/form-data" class="website-content-form">
<input type="hidden" name="website_csrf" value="<?php echo SchoolProfile::escape($_SESSION['website_csrf']); ?>"><input type="hidden" name="website_action" value="save"><input type="hidden" name="kind" value="<?php echo $kind; ?>"><input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
<h3><?php echo $isEdit ? 'Edit item' : ['gallery'=>'Add a photograph','event'=>'Add an event','announcement'=>'Add an announcement'][$kind]; ?></h3>
<div class="profile-grid">
<?php if ($kind === 'gallery'): ?>
<div class="profile-field"><label for="gallery-photo">Photograph</label><?php if ($isEdit && !empty($item['image_filename'])): ?><img class="website-edit-photo" src="../<?php echo SchoolProfile::escape(SchoolProfile::imageUrl($item['image_filename'])); ?>" alt="Current gallery photo"><?php endif; ?><input id="gallery-photo" name="gallery_photo" type="file" accept="image/jpeg,image/png" <?php echo $isEdit ? '' : 'required'; ?>><small>JPG or PNG, up to <?php echo $uploadMb; ?> MB. <?php echo $isEdit ? 'Leave blank to keep the photograph.' : 'Only upload photographs the school is happy to share publicly.'; ?></small></div>
<?php endif; ?>
<div class="profile-field"><label for="<?php echo $kind; ?>-title"><?php echo $kind === 'gallery' ? 'Caption (optional)' : 'Title'; ?></label><input id="<?php echo $kind; ?>-title" name="title" maxlength="160" value="<?php echo SchoolProfile::escape($item['title']); ?>" <?php echo $kind === 'gallery' ? '' : 'required'; ?>></div>
<?php if ($kind === 'event'): ?><div class="profile-field"><label for="event-date">Event date</label><input id="event-date" name="event_date" type="date" value="<?php echo SchoolProfile::escape($item['event_date']); ?>" required></div><div class="profile-field"><label for="event-venue">Venue (optional)</label><input id="event-venue" name="venue" maxlength="160" value="<?php echo SchoolProfile::escape($item['venue']); ?>"></div><?php endif; ?>
<?php if ($kind !== 'gallery'): ?><div class="profile-field profile-wide"><label for="<?php echo $kind; ?>-body"><?php echo $kind === 'event' ? 'Short details (optional)' : 'Message'; ?></label><textarea id="<?php echo $kind; ?>-body" name="body" maxlength="1200" rows="3" <?php echo $kind === 'announcement' ? 'required' : ''; ?>><?php echo SchoolProfile::escape($item['body']); ?></textarea></div><?php endif; ?>
<?php if ($kind === 'announcement'): ?><div class="profile-field"><label for="announcement-expiry">Show until (optional)</label><input id="announcement-expiry" name="expires_on" type="date" value="<?php echo SchoolProfile::escape($item['expires_on']); ?>"><small>Visible through this date. Leave blank to keep it on the website.</small></div><?php endif; ?>
</div><div class="profile-save website-form-footer"><label><input type="checkbox" name="published" value="1" <?php echo (int) $item['published'] ? 'checked' : ''; ?>> Publish on the school website</label><div><?php if ($isEdit): ?><a class="website-cancel" href="index.php?route=website#<?php echo $kind; ?>">Cancel edit</a><?php endif; ?><button type="submit"><?php echo $isEdit ? 'Save changes' : 'Save item'; ?></button></div></div>
</form>
<div class="website-item-list">
<?php if (!$list): ?><p class="website-empty">No <?php echo strtolower($label); ?> yet. Add the first item above.</p><?php endif; ?>
<?php foreach ($list as $row): ?><article class="website-item">
<?php if ($kind === 'gallery' && SchoolProfile::imageUrl($row['image_filename']) !== ''): ?><img loading="lazy" src="../<?php echo SchoolProfile::escape(SchoolProfile::imageUrl($row['image_filename'])); ?>" alt="<?php echo SchoolProfile::escape($row['title']); ?>"><?php endif; ?>
<div class="website-item-copy"><span class="website-badge"><?php echo !(int) $row['published'] ? 'Draft' : ((($row['expires_on'] && $row['expires_on'] < $websiteToday) || ($row['event_date'] && $row['event_date'] < $websiteToday)) ? 'Past / expired' : 'Published'); ?></span><h3><?php echo SchoolProfile::escape($row['title']); ?></h3><?php if ($kind === 'event'): ?><p><?php echo date('j M Y', strtotime($row['event_date'])); ?><?php echo $row['venue'] ? ' · ' . SchoolProfile::escape($row['venue']) : ''; ?></p><?php endif; ?><?php if ($row['body']): ?><p><?php echo nl2br(SchoolProfile::escape($row['body'])); ?></p><?php endif; ?></div>
<div class="website-item-actions"><a href="index.php?route=website&amp;edit=<?php echo (int) $row['id']; ?>#<?php echo $kind; ?>">Edit</a><details><summary>Remove</summary><form method="post" action="website.php"><input type="hidden" name="website_csrf" value="<?php echo SchoolProfile::escape($_SESSION['website_csrf']); ?>"><input type="hidden" name="website_action" value="delete"><input type="hidden" name="kind" value="<?php echo $kind; ?>"><input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>"><label><input type="checkbox" name="confirm_delete" value="1" required> Remove this item from the website</label><button class="website-delete" type="submit">Confirm removal</button></form></details></div>
</article><?php endforeach; ?>
</div></section>
<?php endforeach; endif; ?>
</main>
</body></html>
