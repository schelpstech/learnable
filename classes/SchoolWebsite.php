<?php
require_once __DIR__ . '/SchoolProfile.php';

/** Public website content is separate from private portal notices and calendars. */
final class SchoolWebsite
{
    public const KINDS = ['gallery', 'event', 'announcement'];

    public static function validate(array $input)
    {
        $kind = $input['kind'] ?? '';
        if (!is_string($kind) || !in_array($kind, self::KINDS, true)) throw new InvalidArgumentException('Choose a valid website section.');
        $data = ['kind' => $kind];
        foreach (['title' => 160, 'body' => 1200, 'venue' => 160] as $field => $max) {
            $value = $input[$field] ?? '';
            if (!is_string($value)) throw new InvalidArgumentException('Enter valid text in the website fields.');
            $value = trim($value);
            $length = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
            if ($length > $max) throw new InvalidArgumentException(ucfirst($field) . ' is too long.');
            $data[$field] = $value;
        }
        if ($data['title'] === '') {
            if ($kind !== 'gallery') throw new InvalidArgumentException('Enter a title.');
            $data['title'] = 'School moment';
        }
        if ($kind === 'announcement' && $data['body'] === '') throw new InvalidArgumentException('Enter the announcement message.');
        $data['event_date'] = $kind === 'event' ? self::date($input['event_date'] ?? '', true) : null;
        $data['expires_on'] = $kind === 'announcement' ? self::date($input['expires_on'] ?? '', false) : null;
        if ($kind !== 'event') $data['venue'] = '';
        if ($kind === 'gallery') $data['body'] = '';
        $published = $input['published'] ?? '0';
        if (!is_string($published) || !in_array($published, ['0', '1'], true)) throw new InvalidArgumentException('Choose draft or published.');
        $data['published'] = (int) $published;
        return $data;
    }

    private static function date($value, $required)
    {
        if (!is_string($value)) throw new InvalidArgumentException('Enter a valid date.');
        if ($value === '' && !$required) return null;
        if (!preg_match('/^[1-9][0-9]{3}-[0-9]{2}-[0-9]{2}$/D', $value)) throw new InvalidArgumentException('Enter a valid date.');
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) throw new InvalidArgumentException('Enter a valid date.');
        return $value;
    }

    public static function all(PDO $db)
    {
        return $db->query('SELECT * FROM school_website_posts ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(PDO $db, $id)
    {
        $query = $db->prepare('SELECT * FROM school_website_posts WHERE id = ?');
        $query->execute([$id]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (!$row) throw new InvalidArgumentException('This website item no longer exists.');
        return $row;
    }

    public static function publicContent(PDO $db)
    {
        $today = (new DateTimeImmutable('today', new DateTimeZone('Africa/Lagos')))->format('Y-m-d');
        $content = [];
        foreach (self::KINDS as $kind) {
            $where = $kind === 'event' ? ' AND event_date >= ?' : ($kind === 'announcement' ? ' AND (expires_on IS NULL OR expires_on >= ?)' : '');
            $query = $db->prepare('SELECT id, title, body, image_filename, event_date, venue, created_at FROM school_website_posts WHERE kind = ? AND published = 1' . $where
                . ($kind === 'event' ? ' ORDER BY event_date, id DESC LIMIT 6' : ' ORDER BY id DESC LIMIT ' . ($kind === 'gallery' ? '30' : '6')));
            $query->execute($where === '' ? [$kind] : [$kind, $today]);
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            $content[$kind] = $kind === 'gallery' ? array_values(array_filter($rows, static function ($row) { return SchoolProfile::imageUrl($row['image_filename']) !== ''; })) : $rows;
        }
        return $content;
    }

    public static function save(PDO $db, array $input, $image, $actor, $id = 0)
    {
        $data = self::validate($input);
        if ($image !== null && (!is_string($image) || !preg_match('/^school-gallery-[a-f0-9]{24}\.(jpg|png)$/D', $image))) throw new InvalidArgumentException('Invalid gallery image.');
        $db->beginTransaction();
        try {
            $previousImage = '';
            if ($id) {
                $query = $db->prepare('SELECT * FROM school_website_posts WHERE id = ? FOR UPDATE');
                $query->execute([$id]); $old = $query->fetch(PDO::FETCH_ASSOC);
                if (!$old || $old['kind'] !== $data['kind']) throw new InvalidArgumentException('This website item no longer exists in that section.');
                $previousImage = $old['image_filename'];
            }
            $data['image_filename'] = $data['kind'] === 'gallery' ? ($image ?? $previousImage) : '';
            if ($data['kind'] === 'gallery' && SchoolProfile::imageUrl($data['image_filename']) === '') throw new InvalidArgumentException('Choose a gallery photograph.');
            if ($id) {
                $columns = implode(', ', array_map(static function ($key) { return '`' . $key . '` = ?'; }, array_keys($data)));
                $db->prepare('UPDATE school_website_posts SET ' . $columns . ' WHERE id = ?')->execute(array_merge(array_values($data), [$id]));
            } else {
                $data['created_by'] = $actor;
                $db->prepare('INSERT INTO school_website_posts (`' . implode('`,`', array_keys($data)) . '`) VALUES (' . implode(',', array_fill(0, count($data), '?')) . ')')->execute(array_values($data));
                $id = (int) $db->lastInsertId();
            }
            $db->commit();
            if ($image !== null && $previousImage !== $image) self::removeImage($previousImage);
            return $id;
        } catch (Throwable $exception) {
            if ($db->inTransaction()) $db->rollBack();
            throw $exception;
        }
    }

    public static function delete(PDO $db, $id)
    {
        $db->beginTransaction();
        try {
            $query = $db->prepare('SELECT * FROM school_website_posts WHERE id = ? FOR UPDATE');
            $query->execute([$id]); $row = $query->fetch(PDO::FETCH_ASSOC);
            if (!$row) throw new InvalidArgumentException('This website item no longer exists.');
            $db->prepare('DELETE FROM school_website_posts WHERE id = ?')->execute([$id]);
            $db->commit();
            self::removeImage($row['image_filename']);
        } catch (Throwable $exception) {
            if ($db->inTransaction()) $db->rollBack();
            throw $exception;
        }
    }

    public static function removeImage($name)
    {
        if (is_string($name) && preg_match('/^school-gallery-[a-f0-9]{24}\.(jpg|png)$/D', $name)) {
            $path = dirname(__DIR__) . '/learn/asset/img/school/' . $name;
            if (is_file($path)) @unlink($path);
        }
    }
}
