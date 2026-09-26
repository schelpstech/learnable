<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../classes/SchoolProfile.php';
$db = database_pdo();
$base = rtrim((string) app_env('APP_URL', 'http://localhost/learnable'), '/');
if (!in_array(parse_url($base, PHP_URL_HOST), ['localhost','127.0.0.1','::1'], true)) { throw new RuntimeException('Run this test against a local development site only.'); }
$original = SchoolProfile::read($db);
if (!$original) { throw new RuntimeException('A saved school profile is required.'); }
$password = (string) app_env('E2E_DEMO_PASSWORD', '');
if (strlen($password) < 12) { throw new RuntimeException('Configure the existing demo admin before running this HTTP test.'); }
$cookie = tempnam(sys_get_temp_dir(), 'school-profile-');
$created = [];
function check($ok, $message) { if (!$ok) { throw new RuntimeException($message); } echo "PASS: $message\n"; }
function request_profile($path, $data = null, $auth = true) {
    global $base, $cookie;
    $curl = curl_init($base . $path);
    $options = [CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>20];
    if ($auth) { $options[CURLOPT_COOKIEFILE] = $cookie; $options[CURLOPT_COOKIEJAR] = $cookie; }
    if ($data !== null) { $options[CURLOPT_POST] = true; $options[CURLOPT_POSTFIELDS] = $data; }
    curl_setopt_array($curl,$options);
    $body = curl_exec($curl); $code = curl_getinfo($curl,CURLINFO_RESPONSE_CODE); $redirect = curl_getinfo($curl,CURLINFO_REDIRECT_URL);
    if ($body === false) { throw new RuntimeException(curl_error($curl)); } curl_close($curl);
    check($code < 500, "$path responds without server errors");
    return [$code,$body,$redirect];
}
try {
    [$status,,$redirect] = request_profile('/admin/schprofile.php', ['schname'=>'Unauthorized'], false);
    check($status === 302 && str_ends_with($redirect,'/admin.php'), 'Anonymous profile writes require login');
    request_profile('/entadmin.php', ['but_admn'=>'1','aaname'=>'codex_demo_admin','aapwd'=>$password]);
    [$status,$html] = request_profile('/admin/index.php?route=profile');
    check($status === 200 && str_contains($html,'School website'), 'Authenticated profile route renders website fields');
    preg_match('/name="profile_csrf" value="([a-f0-9]+)"/', $html, $matches);
    check(isset($matches[1]), 'Profile form includes a CSRF token');
    $input = ['profile_csrf'=>$matches[1], 'schname'=>$original['schname'], 'schowner'=>$original['proprietor'], 'schmotto'=>$original['motto'], 'schaddress'=>$original['address'], 'schphone'=>$original['phone'], 'schemail'=>$original['email'], 'schweb'=>$original['website'], 'schyear'=>$original['founded'], 'about_school'=>'Profile QA: learning & discovery <script>alert(1)</script>', 'admissions_message'=>'Profile QA: contact the school office.', 'school_sections[0]'=>'Nursery','school_sections[1]'=>'Primary'];
    request_profile('/admin/schprofile.php', array_replace($input,['profile_csrf'=>'invalid']));
    check(SchoolProfile::read($db) === $original, 'Invalid CSRF leaves the saved profile unchanged');
    request_profile('/admin/schprofile.php', array_replace($input,['schyear'=>(string)((int)date('Y')+1)]));
    check(SchoolProfile::read($db) === $original, 'Future founding year is rejected without changing data');
    request_profile('/admin/schprofile.php', array_replace($input,['school_photo'=>new CURLFile(__FILE__,'image/jpeg','invalid.jpg')]));
    check(SchoolProfile::read($db) === $original, 'Disguised non-image upload is rejected without changing data');
    request_profile('/admin/schprofile.php', array_replace($input,['whatsapp_number'=>'08012345678']));
    check(SchoolProfile::read($db) === $original, 'WhatsApp requires an international number');
    $input['whatsapp_number'] = '+234 (801) 234-5678';
    $input['school_photo'] = new CURLFile(__DIR__.'/../images/learnable-landing-classroom-v3.jpg','image/jpeg','classroom.jpg');
    request_profile('/admin/schprofile.php',$input);
    $saved = SchoolProfile::read($db);
    if ($saved['school_photo'] !== ($original['school_photo'] ?? '')) { $created[] = $saved['school_photo']; }
    check($saved['about_school'] === $input['about_school'] && $saved['school_sections'] === 'Nursery,Primary', 'Website text and section selections persist');
    check($saved['whatsapp_number'] === '+2348012345678' && $saved['phone'] === $original['phone'], 'Separate WhatsApp number persists without changing the school phone');
    check($saved['logo'] === $original['logo'], 'Saving without a logo upload preserves the school logo');
    check(SchoolProfile::imageUrl($saved['school_photo']) !== '', 'School photograph uploads successfully');
    [$status,$html] = request_profile('/index.php');
    check(str_contains($html,'id="about"') && str_contains($html,'id="our-school"') && str_contains($html,'id="admissions-title"'), 'Filled optional sections appear on the public page');
    check(str_contains($html,'&lt;script&gt;alert(1)&lt;/script&gt;') && !str_contains($html,'<script>alert(1)</script>'), 'Saved content is escaped on the public page');
    check(str_contains($html,'href="https://wa.me/2348012345678"'), 'Website WhatsApp button uses normalized international digits');
    check(!str_contains($html,$original['proprietor']), 'Proprietor name stays off the public page');
    [$status,$html] = request_profile('/admin/profile.php');
    check(str_contains($html,'school-photo-') && str_contains($html,'School profile saved.'), 'Profile shows uploaded photograph and save confirmation');
    unset($input['school_photo'], $input['school_sections[0]'], $input['school_sections[1]']);
    $input['whatsapp_number']='';
    $input['about_school']=''; $input['admissions_message']=''; $input['remove_photo']='1';
    request_profile('/admin/schprofile.php',$input);
    [$status,$html] = request_profile('/index.php');
    check(!str_contains($html,'https://wa.me/'), 'Blank WhatsApp number hides the button');
    check(!str_contains($html,'id="about"') && !str_contains($html,'id="our-school"') && !str_contains($html,'id="admissions-title"'), 'Empty optional sections are hidden');
    check(str_contains($html,'images/learnable-landing-classroom-v3.jpg'), 'Removed photograph uses the default image');
    foreach (['student'=>'student.php','staff'=>'staff.php'] as $key=>$target) {
        [$status,,$redirect] = request_profile('/index.php',[$key=>'1']);
        check($status===302 && str_ends_with($redirect,'/'.$target),'Existing '.$key.' redirect remains available');
    }
    foreach (['student.php','staff.php','admin.php'] as $target) { [$status] = request_profile('/'.$target, null, false); check($status===200, $target.' entrance is available'); }
} finally {
    $restore = $original; unset($restore['schid']); SchoolProfile::save($db,$restore);
    foreach ($created as $name) { if (preg_match('/^school-photo-[a-f0-9]{24}\.jpg$/D',$name)) { @unlink(__DIR__.'/../learn/asset/img/school/'.$name); } }
    @unlink($cookie);
    check(SchoolProfile::read($db) === $original, 'Original school information restored after testing');
}
echo "School website HTTP checks passed.\n";
