<?php

/** School identity shared by the public website and administrator profile. */
final class SchoolProfile
{
    public const SECTIONS = ['Creche', 'Nursery', 'Primary', 'Secondary'];

    public static function escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function read(PDO $db)
    {
        return $db->query('SELECT * FROM lhpschool ORDER BY schid LIMIT 1')->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public static function imageUrl($filename)
    {
        $filename = basename((string) $filename);
        if ($filename === '' || !preg_match('/\.(jpe?g|png)$/iD', $filename)) { return ''; }
        return is_file(dirname(__DIR__) . '/learn/asset/img/school/' . $filename)
            ? 'learn/asset/img/school/' . rawurlencode($filename) : '';
    }

    public static function sections($value)
    {
        return array_values(array_intersect(self::SECTIONS, explode(',', (string) $value)));
    }

    public static function whatsappNumber($value)
    {
        if (!is_string($value) || !preg_match('/^\+?[0-9 ()-]+$/D', trim($value))) { return ''; }
        $digits = preg_replace('/[^0-9]/', '', $value);
        return preg_match('/^[1-9][0-9]{7,14}$/D', $digits) ? $digits : '';
    }

    public static function validate(array $input)
    {
        $fields = [
            'schname' => ['schname', 254, true], 'schaddress' => ['address', 626, true],
            'schphone' => ['phone', 16, true], 'schemail' => ['email', 88, true],
            'schweb' => ['website', 88, false], 'schowner' => ['proprietor', 88, true],
            'schmotto' => ['motto', 88, true], 'about_school' => ['about_school', 800, false],
            'admissions_message' => ['admissions_message', 400, false],
            'whatsapp_number' => ['whatsapp_number', 32, false],
        ];
        $data = [];
        foreach ($fields as $key => [$column, $max, $required]) {
            if (isset($input[$key]) && !is_string($input[$key])) { throw new InvalidArgumentException('Please enter valid text in the profile fields.'); }
            $value = trim($input[$key] ?? '');
            if ($required && $value === '') { throw new InvalidArgumentException('Please complete all required school details.'); }
            $length = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
            if ($length > $max) { throw new InvalidArgumentException('One of the profile fields exceeds its displayed character limit.'); }
            $data[$column] = $value;
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { throw new InvalidArgumentException('Please enter a valid school email address.'); }
        if (!preg_match('/^\+?[0-9 ()-]{7,16}$/D', $data['phone'])) { throw new InvalidArgumentException('Please enter a valid phone number (up to 16 characters).'); }
        if ($data['website'] !== '' && (!filter_var($data['website'], FILTER_VALIDATE_URL)
            || !in_array(strtolower((string) parse_url($data['website'], PHP_URL_SCHEME)), ['http', 'https'], true))) {
            throw new InvalidArgumentException('The website must be a valid http or https address.');
        }
        if ($data['whatsapp_number'] !== '') {
            $number = self::whatsappNumber($data['whatsapp_number']);
            if ($number === '') { throw new InvalidArgumentException('Enter a WhatsApp number with its country code, for example +2348012345678 (omit the leading local zero).'); }
            $data['whatsapp_number'] = '+' . $number;
        }
        $year = $input['schyear'] ?? '';
        if (!is_string($year) || !preg_match('/^\d{4}$/D', $year) || (int) $year < 1901 || (int) $year > (int) date('Y')) {
            throw new InvalidArgumentException('Enter a founding year between 1901 and ' . date('Y') . '.');
        }
        $data['founded'] = $year;
        $sections = $input['school_sections'] ?? [];
        if (!is_array($sections)) { throw new InvalidArgumentException('Please select valid school sections.'); }
        foreach ($sections as $section) {
            if (!is_string($section) || !in_array($section, self::SECTIONS, true)) { throw new InvalidArgumentException('Please select valid school sections.'); }
        }
        $data['school_sections'] = implode(',', array_values(array_intersect(self::SECTIONS, $sections)));
        return $data;
    }

    public static function upload(array $file, $prefix)
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_NO_FILE) { return null; }
        if ($error !== UPLOAD_ERR_OK) { throw new InvalidArgumentException('The image could not be uploaded. Choose a JPG or PNG within the size limit.'); }
        $tmp = $file['tmp_name'] ?? '';
        if (!is_string($tmp) || !is_uploaded_file($tmp) || filesize($tmp) > min(6 * 1024 * 1024, self::uploadLimit())) {
            throw new InvalidArgumentException('The image is too large or is not a valid upload.');
        }
        $size = @getimagesize($tmp);
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        if (!$size || !isset($types[$mime]) || ($size['mime'] ?? '') !== $mime || $size[0] * $size[1] > 40000000) {
            throw new InvalidArgumentException('Please upload a valid JPG or PNG image, up to 40 megapixels.');
        }
        $name = $prefix . '-' . bin2hex(random_bytes(12)) . '.' . $types[$mime];
        if (!move_uploaded_file($tmp, dirname(__DIR__) . '/learn/asset/img/school/' . $name)) { throw new RuntimeException('Unable to store the image.'); }
        return $name;
    }

    public static function uploadLimit()
    {
        $value = trim((string) ini_get('upload_max_filesize'));
        return (int) $value * (['g' => 1073741824, 'm' => 1048576, 'k' => 1024][strtolower(substr($value, -1))] ?? 1);
    }

    public static function save(PDO $db, array $data)
    {
        $current = self::read($db);
        if ($current) {
            $assignments = implode(', ', array_map(static function ($key) { return '`' . $key . '` = ?'; }, array_keys($data)));
            $query = $db->prepare('UPDATE lhpschool SET ' . $assignments . ' WHERE schid = ?');
            $query->execute(array_merge(array_values($data), [$current['schid']]));
        } else {
            $data += ['logo' => '', 'school_photo' => ''];
            $query = $db->prepare('INSERT INTO lhpschool (`' . implode('`, `', array_keys($data)) . '`) VALUES (' . implode(',', array_fill(0, count($data), '?')) . ')');
            $query->execute(array_values($data));
        }
    }
}
