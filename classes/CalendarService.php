<?php

/**
 * Uses the existing `schedule` table as a shared academic calendar and class
 * timetable. Existing columns are mapped without schema changes.
 */
final class CalendarService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function activeTerm()
    {
        $row = $this->one('SELECT term FROM lpterm WHERE status = 1 LIMIT 1');
        return (string) ($row['term'] ?? '');
    }

    public function allClasses()
    {
        return $this->all('SELECT classid, classname FROM lhpclass ORDER BY classname');
    }

    public function className($classId)
    {
        $row = $this->one('SELECT classname FROM lhpclass WHERE classid = ? LIMIT 1', array($classId));
        return (string) ($row['classname'] ?? 'My class');
    }

    public function assignedClasses($staffId, $term)
    {
        return $this->all(
            'SELECT DISTINCT c.classid, c.classname
             FROM lhpclassalloc a INNER JOIN lhpclass c ON c.classid = a.classid
             WHERE a.tutorid = ? AND a.term = ? ORDER BY c.classname',
            array($staffId, $term)
        );
    }

    public function createAcademicEvent(array $input, $adminId)
    {
        $title = $this->text($input, 'title', 300, true);
        $description = $this->text($input, 'description', 3000, false);
        $term = $this->text($input, 'term', 64, true);
        $classId = $this->text($input, 'class_id', 64, true);
        $startDate = $this->date($input, 'start_date');
        $endDate = $this->date($input, 'end_date', $startDate);
        $startTime = $this->time($input, 'start_time', '00:00');
        $endTime = $this->time($input, 'end_time', $startTime);
        if ($endDate < $startDate || ($endDate === $startDate && $endTime < $startTime)) {
            throw new InvalidArgumentException('The event end must be after its start.');
        }

        $className = 'All learners';
        if ($classId !== 'ALL') {
            $class = $this->one('SELECT classname FROM lhpclass WHERE classid = ? LIMIT 1', array($classId));
            if (!$class) throw new InvalidArgumentException('The selected class is unavailable.');
            $className = $class['classname'];
        }

        return $this->insertSchedule($startDate, $title, $startTime, $description, $endTime, $term, $classId, $endDate, 'academic', $className, $adminId);
    }

    public function createClassSession(array $input, $staffId, $activeTerm)
    {
        $title = $this->text($input, 'title', 300, true);
        $description = $this->text($input, 'description', 3000, false);
        $classId = $this->text($input, 'class_id', 64, true);
        $startDate = $this->date($input, 'start_date');
        $isWeekly = !empty($input['repeat_weekly']);
        $endDate = $isWeekly ? $this->date($input, 'end_date', date('Y-m-d', strtotime($startDate . ' +12 weeks'))) : $startDate;
        $startTime = $this->time($input, 'start_time', null);
        $endTime = $this->time($input, 'end_time', null);
        if ($endDate < $startDate || $endTime <= $startTime) {
            throw new InvalidArgumentException('The class end must be after its start.');
        }

        $class = $this->one(
            'SELECT c.classname FROM lhpclassalloc a INNER JOIN lhpclass c ON c.classid = a.classid
             WHERE a.tutorid = ? AND a.term = ? AND a.classid = ? LIMIT 1',
            array($staffId, $activeTerm, $classId)
        );
        if (!$class) {
            throw new RuntimeException('You can only schedule a class assigned to you for the active term.');
        }

        return $this->insertSchedule($startDate, $title, $startTime, $description, $endTime, $activeTerm, $classId, $endDate, $isWeekly ? 'weekly' : 'class', $class['classname'], $staffId);
    }

    public function deleteEvent($eventId, $actorId, $isAdmin)
    {
        $eventId = filter_var($eventId, FILTER_VALIDATE_INT);
        if (!$eventId) throw new InvalidArgumentException('Invalid calendar entry.');
        if ($isAdmin) {
            $statement = $this->pdo->prepare('DELETE FROM schedule WHERE scidd = ?');
            $statement->execute(array($eventId));
        } else {
            $statement = $this->pdo->prepare('DELETE FROM schedule WHERE scidd = ? AND scstaff = ? AND scfone IN ("weekly", "class")');
            $statement->execute(array($eventId, $actorId));
        }
        return $statement->rowCount() > 0;
    }

    public function month($month, $classId, $term)
    {
        if (!preg_match('/^\d{4}-\d{2}$/', (string) $month)) {
            $month = date('Y-m');
        }
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));
        return array(
            'month' => $month,
            'label' => date('F Y', strtotime($start)),
            'start' => $start,
            'end' => $end,
            'previous' => date('Y-m', strtotime($start . ' -1 month')),
            'next' => date('Y-m', strtotime($start . ' +1 month')),
            'events' => $this->eventsBetween($start, $end, $classId, $term),
        );
    }

    public function weeklyTimetable($classId, $term)
    {
        if ($classId === null || $classId === '') {
            return $this->all(
                'SELECT scidd AS id, schact AS title, schdetails AS description, start_time, stop_time,
                        schdate, stp_date, classname, scstaff
                 FROM schedule WHERE scterm = ? AND scfone = "weekly"
                 ORDER BY WEEKDAY(schdate), start_time, classname, schact',
                array($term)
            );
        }
        return $this->all(
            'SELECT scidd AS id, schact AS title, schdetails AS description, start_time, stop_time,
                    schdate, stp_date, classname, scstaff
             FROM schedule
             WHERE scclass = ? AND scterm = ? AND scfone = "weekly"
             ORDER BY WEEKDAY(schdate), start_time, schact',
            array($classId, $term)
        );
    }

    private function eventsBetween($start, $end, $classId, $term)
    {
        $parameters = array($end, $start, $term);
        $classClause = '';
        if ($classId !== null && $classId !== '') {
            $classClause = ' AND (scclass = "ALL" OR scclass = ?)';
            $parameters[] = $classId;
        }
        $rows = $this->all(
            'SELECT scidd AS id, schdate, schact AS title, start_time, schdetails AS description,
                    stop_time, scterm, scclass, stp_date, scfone AS event_type, classname, scstaff
             FROM schedule
             WHERE schdate <= ? AND stp_date >= ? AND scterm = ?' . $classClause . '
             ORDER BY schdate, start_time, schact',
            $parameters
        );

        $events = array();
        foreach ($rows as $row) {
            if ($row['event_type'] === 'weekly') {
                $cursor = max(strtotime($row['schdate']), strtotime($start));
                $targetWeekday = (int) date('N', strtotime($row['schdate']));
                while ((int) date('N', $cursor) !== $targetWeekday) $cursor = strtotime('+1 day', $cursor);
                $last = min(strtotime($row['stp_date']), strtotime($end));
                while ($cursor <= $last) {
                    $events[] = $this->normaliseEvent($row, date('Y-m-d', $cursor));
                    $cursor = strtotime('+7 days', $cursor);
                }
            } elseif ($row['event_type'] === 'academic' && $row['stp_date'] > $row['schdate']) {
                $cursor = max(strtotime($row['schdate']), strtotime($start));
                $last = min(strtotime($row['stp_date']), strtotime($end));
                while ($cursor <= $last) {
                    $events[] = $this->normaliseEvent($row, date('Y-m-d', $cursor));
                    $cursor = strtotime('+1 day', $cursor);
                }
            } else {
                $events[] = $this->normaliseEvent($row, $row['schdate']);
            }
        }
        return $events;
    }

    private function normaliseEvent(array $row, $date)
    {
        return array(
            'id' => (int) $row['id'],
            'date' => $date,
            'title' => (string) $row['title'],
            'description' => (string) $row['description'],
            'start_time' => substr((string) $row['start_time'], 0, 5),
            'end_time' => substr((string) $row['stop_time'], 0, 5),
            'type' => (string) $row['event_type'],
            'class_name' => (string) $row['classname'],
            'owner' => (string) $row['scstaff'],
        );
    }

    private function insertSchedule($date, $title, $startTime, $description, $endTime, $term, $classId, $endDate, $type, $className, $staffId)
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO schedule (schdate, schact, start_time, schdetails, stop_time, scterm, scclass, stp_date, scfone, classname, scstaff)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute(array($date, $title, $startTime, $description, $endTime, $term, $classId, $endDate, $type, $className, $staffId));
        return (int) $this->pdo->lastInsertId();
    }

    private function text(array $input, $key, $maximum, $required)
    {
        $value = isset($input[$key]) && is_string($input[$key]) ? trim($input[$key]) : '';
        if ($required && $value === '') throw new InvalidArgumentException(ucwords(str_replace('_', ' ', $key)) . ' is required.');
        if (strlen($value) > $maximum) throw new InvalidArgumentException(ucwords(str_replace('_', ' ', $key)) . ' is too long.');
        return $value;
    }

    private function date(array $input, $key, $default = null)
    {
        $value = $this->text($input, $key, 10, false);
        if ($value === '' && $default !== null) return $default;
        $date = DateTime::createFromFormat('Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) throw new InvalidArgumentException(ucwords(str_replace('_', ' ', $key)) . ' is invalid.');
        return $value;
    }

    private function time(array $input, $key, $default)
    {
        $value = $this->text($input, $key, 5, false);
        if ($value === '' && $default !== null) return $default;
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value)) throw new InvalidArgumentException(ucwords(str_replace('_', ' ', $key)) . ' is invalid.');
        return $value;
    }

    private function one($sql, array $parameters = array())
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        $row = $statement->fetch();
        return $row ?: null;
    }

    private function all($sql, array $parameters = array())
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll();
    }
}
