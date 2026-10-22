<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../classes/SchoolWebsite.php';
$db = database_pdo();
$base = rtrim((string) app_env('APP_URL', 'http://localhost/learnable'), '/');
if (!in_array(parse_url($base, PHP_URL_HOST), ['localhost', '127.0.0.1', '::1'], true)) throw new RuntimeException('Use a local development site only.');
$manifestPath = __DIR__ . '/../tasks/_website_preview.json';
function website_check($ok, $message) { if (!$ok) throw new RuntimeException('FAIL: ' . $message); echo 'PASS: ' . $message . "\n"; }
function website_cleanup($db, $prefix) {
    if (!preg_match('/^Website QA [a-f0-9]{12} $/D', $prefix)) throw new RuntimeException('Invalid owned test prefix.');
    $query = $db->prepare('SELECT id FROM school_website_posts WHERE title LIKE ?'); $query->execute([$prefix . '%']);
    foreach ($query->fetchAll(PDO::FETCH_COLUMN) as $id) SchoolWebsite::delete($db, $id);
}
if (in_array('--cleanup-preview', $argv, true)) {
    $manifest = json_decode(file_get_contents($manifestPath), true);
    website_cleanup($db, $manifest['prefix'] ?? '');
    unlink($manifestPath);
    echo "Owned website preview items removed.\n"; exit;
}
if (is_file($manifestPath)) throw new RuntimeException('Clean up the previous owned preview first.');
$prefix = 'Website QA ' . bin2hex(random_bytes(6)) . ' ';
$password = (string) app_env('E2E_DEMO_PASSWORD', '');
if (strlen($password) < 12) throw new RuntimeException('Configure the existing demo admin password.');
$originalProfile = SchoolProfile::read($db);
$cookie = tempnam(sys_get_temp_dir(), 'website-admin-');
$cookies = [$cookie]; $staff = []; $passed = false;
function website_request($path, $fields = null, $cookie = null) {
    global $base;
    $curl = curl_init($base . $path);
    $options = [CURLOPT_RETURNTRANSFER=>true, CURLOPT_FOLLOWLOCATION=>false, CURLOPT_CONNECTTIMEOUT=>5, CURLOPT_TIMEOUT=>25];
    if ($cookie) { $options[CURLOPT_COOKIEFILE]=$cookie; $options[CURLOPT_COOKIEJAR]=$cookie; }
    if ($fields !== null) { $options[CURLOPT_POST]=true; $options[CURLOPT_POSTFIELDS]=$fields; }
    curl_setopt_array($curl,$options);
    $html = curl_exec($curl); $status = curl_getinfo($curl,CURLINFO_RESPONSE_CODE); $url = curl_getinfo($curl,CURLINFO_REDIRECT_URL); $error = curl_error($curl); curl_close($curl);
    if ($html === false || $error) throw new RuntimeException('HTTP failed: ' . $error);
    website_check($status < 500, $path . ' responds without a server error');
    return [$status,$html,$url];
}
$find = function ($label) use ($db,$prefix) { $q=$db->prepare('SELECT * FROM school_website_posts WHERE title=?'); $q->execute([$prefix.$label]); return $q->fetch(PDO::FETCH_ASSOC); };
try {
    [$status,,$url] = website_request('/admin/website.php',['website_action'=>'save','kind'=>'announcement','title'=>$prefix.'Unauthorized','body'=>'Do not save']);
    website_check($status===302 && str_ends_with($url,'/admin.php'),'anonymous website writes require login');
    website_request('/entadmin.php',['but_admn'=>'1','aaname'=>'codex_demo_admin','aapwd'=>$password],$cookie);
    [$status,$html] = website_request('/admin/index.php?route=website',null,$cookie);
    website_check($status===200 && str_contains($html,'Add a photograph') && str_contains($html,'Add an event') && str_contains($html,'Add an announcement'),'the admin editor exposes all three content sections');
    preg_match('/name="website_csrf" value="([a-f0-9]{64})"/',$html,$token);
    website_check(isset($token[1]),'website forms contain a CSRF token');
    $submit = function ($kind,$label,$extra=[]) use ($prefix,$token,$cookie) {
        website_request('/admin/website.php',array_replace(['website_action'=>'save','website_csrf'=>$token[1],'id'=>'0','kind'=>$kind,'title'=>$prefix.$label,'body'=>'School update & details <script>alert(1)</script>','published'=>'1'],$extra),$cookie);
    };
    $submit('announcement','Invalid CSRF',['website_csrf'=>'bad']);
    website_check(!$find('Invalid CSRF'),'invalid CSRF cannot save content');
    $submit('event','Invalid date',['event_date'=>'2026-02-30']);
    website_check(!$find('Invalid date'),'impossible dates are rejected');
    $submit('gallery','Invalid photo',['gallery_photo'=>new CURLFile(__FILE__,'image/jpeg','fake.jpg')]);
    website_check(!$find('Invalid photo'),'a disguised executable is rejected as a photo');
    $today = new DateTimeImmutable('today',new DateTimeZone('Africa/Lagos'));
    $submit('announcement','Announcement',['expires_on'=>$today->modify('+1 day')->format('Y-m-d')]);
    $submit('announcement','Draft',['published'=>'0']);
    $submit('announcement','Expired',['expires_on'=>$today->modify('-1 day')->format('Y-m-d')]);
    $submit('event','Event',['event_date'=>$today->format('Y-m-d'),'venue'=>'Main hall & courtyard']);
    $submit('event','Past event',['event_date'=>$today->modify('-1 day')->format('Y-m-d')]);
    $photo = __DIR__.'/../images/learnable-landing-classroom-v3.jpg';
    foreach (['Photo one','Photo two'] as $label) $submit('gallery',$label,['gallery_photo'=>new CURLFile($photo,'image/jpeg','classroom.jpg')]);
    $one=$find('Photo one'); $two=$find('Photo two');
    website_check($one && $two && SchoolProfile::imageUrl($one['image_filename']) !== '', 'valid photos upload and persist in the gallery');
    [$status,$html] = website_request('/index.php');
    foreach (['id="gallery"','id="events"','id="announcements"',$prefix.'Announcement',$prefix.'Event',$prefix.'Photo one'] as $expected) website_check(str_contains($html,$expected),'public website renders '.$expected);
    foreach (['Draft','Expired','Past event'] as $label) website_check(!str_contains($html,$prefix.$label),$label.' content remains private or hidden');
    website_check(str_contains($html,'&lt;script&gt;alert(1)&lt;/script&gt;') && !str_contains($html,'<script>alert(1)</script>'),'website messages are safely escaped');
    website_check(str_contains($html,'data-gallery-controls') && str_contains($html,'school-website.js?v=1'),'the gallery includes working slider controls and script');
    $submit('gallery','Photo one',['id'=>(string)$one['id']]);
    website_check($find('Photo one')['image_filename']===$one['image_filename'],'editing a caption without an upload preserves the photo');
    $submit('gallery','Photo one',['id'=>(string)$one['id'],'gallery_photo'=>new CURLFile($photo,'image/jpeg','replacement.jpg')]);
    $replacement=$find('Photo one');
    website_check($replacement['image_filename']!==$one['image_filename'] && !is_file(__DIR__.'/../learn/asset/img/school/'.$one['image_filename']),'replacing a gallery photo removes only its old owned image');
    $submit('gallery','Photo one',['id'=>(string)$one['id'],'gallery_photo'=>new CURLFile(__FILE__,'image/png','bad.png')]);
    website_check($find('Photo one')['image_filename']===$replacement['image_filename'],'a bad replacement cannot remove the saved photo');
    website_request('/admin/website.php',['website_action'=>'delete','id'=>(string)$two['id'],'kind'=>'gallery','website_csrf'=>$token[1]],$cookie);
    website_check((bool)$find('Photo two'),'removing an item requires explicit confirmation');
    website_request('/admin/website.php',['website_action'=>'delete','id'=>(string)$two['id'],'kind'=>'gallery','website_csrf'=>$token[1],'confirm_delete'=>'1'],$cookie);
    website_check(!$find('Photo two') && !is_file(__DIR__.'/../learn/asset/img/school/'.$two['image_filename']),'confirmed removal deletes the gallery record and its owned image');
    $submit('gallery','Photo two',['gallery_photo'=>new CURLFile($photo,'image/jpeg','classroom.jpg')]);
    $announcement=$find('Announcement');
    $submit('announcement','Announcement',['id'=>(string)$announcement['id'],'published'=>'0']);
    [, $html]=website_request('/index.php');
    website_check(!str_contains($html,$prefix.'Announcement'),'unpublishing hides an existing announcement immediately');
    $submit('announcement','Announcement',['id'=>(string)$announcement['id']]);
    foreach (['r','b'] as $role) {
        $name='qa-web-'.bin2hex(random_bytes(5)); $staff[]=$name;
        $secret=bin2hex(random_bytes(16));
        $db->prepare('INSERT INTO lhpstaff (sname,staffname,spwd,semail,sfone,role,status) VALUES (?,?,?,?,?,?,1)')->execute([$name,'QA Website Staff',password_hash($secret,PASSWORD_DEFAULT),'','',$role]);
        $staffCookie=tempnam(sys_get_temp_dir(),'website-staff-'); $cookies[]=$staffCookie;
        website_request('/entstaff.php',['but_submit'=>'1','uname'=>$name,'upass'=>$secret],$staffCookie);
        [$status]=website_request('/admin/index.php?route=website',null,$staffCookie);
        website_check($status===403,$role.' staff cannot open website publishing controls');
        [$status]=website_request('/admin/website.php',['website_action'=>'save','kind'=>'announcement','title'=>$prefix.'Forbidden','body'=>'Do not publish','website_csrf'=>$token[1]],$staffCookie);
        website_check($status===403,$role.' staff cannot write through the direct endpoint');
    }
    website_check(SchoolProfile::read($db)===$originalProfile,'school identity, contacts and profile settings remain unchanged');
    [, $html] = website_request('/admin/index.php?route=website',null,$cookie);
    preg_match_all('/<form[^>]*class="website-content-form"[^>]*>(.*?)<\/form>/s',$html,$forms);
    website_check(count($forms[1])===3,'each website section keeps an independent creation form');
    foreach ($forms[1] as $form) website_check(str_contains($form,'name="id" value="0"'),'successful saves return to a fresh form for the next item');
    $passed=true;
} finally {
    foreach ($staff as $name) $db->prepare('DELETE FROM lhpstaff WHERE sname=?')->execute([$name]);
    foreach ($cookies as $path) @unlink($path);
    if ($passed && in_array('--preview',$argv,true)) {
        file_put_contents($manifestPath,json_encode(['prefix'=>$prefix]));
        echo "Owned preview content retained temporarily. Run this test with --cleanup-preview after visual inspection.\n";
    } else { website_cleanup($db,$prefix); echo "Owned website test records and photos removed.\n"; }
}
echo "School website content HTTP checks passed.\n";
