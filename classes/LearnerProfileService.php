<?php

/** Shared learner-photo storage and class-teacher ownership rules. */
final class LearnerProfileService extends SchoolService
{
    public function assignedClass($classId, StaffAccess $access)
    {
        if ($access->role() !== 'teacher') throw new RuntimeException('Only class teachers can open this student list.');
        $classId = self::integer($classId, 'Class', 1);
        $class = $this->one('SELECT c.classid,c.classname FROM lhpclass c INNER JOIN lhpclassalloc ca ON ca.classid=c.classid WHERE c.classid=? AND ca.tutorid=? AND ca.term=? LIMIT 1',
            [$classId,$access->username(),$this->activeTerm()]);
        if (!$class) throw new RuntimeException('This class is not assigned to you in the active term.');
        return $class;
    }

    public function classList($classId, StaffAccess $access)
    {
        $class = $this->assignedClass($classId,$access);
        $learners = $this->rows('SELECT * FROM lhpuser WHERE classid=? ORDER BY status DESC,fname,uname', [$class['classid']]);
        $statistics = ['male'=>0,'female'=>0,'total'=>0];
        foreach ($learners as $learner) {
            if ((int)$learner['status'] !== 1) continue;
            $statistics['total']++;
            if ($learner['gender'] === 'Male') $statistics['male']++;
            if ($learner['gender'] === 'Female') $statistics['female']++;
        }
        return ['class'=>$class,'learners'=>$learners,'statistics'=>$statistics];
    }

    public function learner($reference, StaffAccess $access, $lock = false)
    {
        if (!$access->academic() && $access->role() !== 'teacher') {
            throw new RuntimeException('You cannot manage learner profiles with this account.');
        }
        $reference = self::text($reference, 64);
        $learner = $this->one('SELECT * FROM lhpuser WHERE uname=? LIMIT 1' . ($lock ? ' FOR UPDATE' : ''), [$reference]);
        if (!$learner) throw new InvalidArgumentException('Learner not found.');
        if (!$access->academic()) {
            $allocation = $this->one('SELECT classlocid FROM lhpclassalloc WHERE tutorid=? AND classid=? AND term=? LIMIT 1',
                [$access->username(), $learner['classid'], $this->activeTerm()]);
            if (!$allocation) throw new RuntimeException('Only the assigned class teacher can manage this learner.');
        }
        return $learner;
    }

    public function uploadPassport($reference, StaffAccess $access, array $file)
    {
        // Check access before processing any uploaded content.
        $this->learner($reference, $access);
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException($error === UPLOAD_ERR_NO_FILE ? 'Choose a passport photograph first.' : 'The upload did not complete. Choose a JPG or PNG within the displayed size limit.');
        }
        $tmp = $file['tmp_name'] ?? '';
        if (!is_string($tmp) || !is_uploaded_file($tmp) || filesize($tmp) > self::uploadLimit()) {
            throw new InvalidArgumentException('The passport is too large or is not a valid upload.');
        }
        $size = @getimagesize($tmp);
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        if (!$size || !isset($types[$mime]) || ($size['mime'] ?? '') !== $mime || $size[0] * $size[1] > 40000000) {
            throw new InvalidArgumentException('Choose a valid JPG or PNG photograph, up to 40 megapixels.');
        }
        $directory = dirname(__DIR__) . '/learn/asset/img/passport/';
        if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('The passport folder is unavailable. Please contact the administrator.');
        }
        $name = 'learner-' . bin2hex(random_bytes(12)) . '.' . $types[$mime];
        if (!@move_uploaded_file($tmp, $directory . $name)) throw new RuntimeException('Unable to store the passport. Please try again.');
        try {
            $this->transaction(function () use ($reference, $access, $name) {
                $learner = $this->learner($reference, $access, true);
                $this->execute('UPDATE lhpuser SET picture=? WHERE id=?', [$name, $learner['id']]);
            });
        } catch (Throwable $exception) {
            @unlink($directory . $name);
            throw $exception;
        }
        // Keep old files: legacy records may share a photograph or default image.
        return $name;
    }

    public function updateDetails($reference, StaffAccess $access, array $input)
    {
        $name = self::text($input['fullname'] ?? '', 64);
        $email = self::text($input['email'] ?? '', 64, true);
        $phone = self::text($input['phone'] ?? '', 11, true);
        $gender = self::text($input['gender'] ?? '', 6, true);
        $dob = self::text($input['date_of_birth'] ?? '', 10, true);
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Enter a valid email address.');
        if (!in_array($gender, ['', 'Male', 'Female'], true)) throw new InvalidArgumentException('Choose a valid gender.');
        if ($dob !== '') {
            $date = DateTime::createFromFormat('!Y-m-d', $dob);
            if (!$date || $date->format('Y-m-d') !== $dob || $dob > date('Y-m-d')) throw new InvalidArgumentException('Enter a valid date of birth.');
        }
        $this->transaction(function () use ($reference, $access, $name, $email, $phone, $gender, $dob) {
            $learner = $this->learner($reference, $access, true);
            $this->execute('UPDATE lhpuser SET fname=?,email=?,numb=?,gender=?,dob=? WHERE id=?',
                [$name, $email, $phone, $gender ?: null, $dob ?: null, $learner['id']]);
        });
    }

    public static function uploadLimit()
    {
        $setting = trim((string) ini_get('upload_max_filesize'));
        $configured = (int)$setting * (['g'=>1073741824, 'm'=>1048576, 'k'=>1024][strtolower(substr($setting, -1))] ?? 1);
        return min(5 * 1024 * 1024, $configured > 0 ? $configured : 5 * 1024 * 1024);
    }

    /** Return a path relative to the application root, including a missing-photo fallback. */
    public static function picturePath($picture)
    {
        $name = basename(str_replace('\\', '/', (string)$picture));
        if ($name !== '' && !in_array(strtolower($name), ['nopix.jpg', 'nopix.png'], true)
            && preg_match('/\.(jpe?g|png|gif|webp)$/iD', $name)
            && is_file(dirname(__DIR__) . '/learn/asset/img/passport/' . $name)) {
            return 'learn/asset/img/passport/' . rawurlencode($name);
        }
        return 'assets/images/student-placeholder.svg';
    }
}
