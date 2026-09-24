<?php

final class SchemeService extends TeachingService
{
    public function classes($actor)
    {
        return $this->rows(
            'SELECT DISTINCT c.classid, c.classname
             FROM lhpalloc a INNER JOIN lhpclass c ON c.classid=a.classid
             WHERE a.staffid=? AND a.term=? ORDER BY c.classname',
            array($actor, $this->activeTerm())
        );
    }

    public function subjects($actor, $classId)
    {
        $classId = self::integer($classId, 'Class', 1);
        return $this->rows(
            'SELECT DISTINCT s.sbjid, s.sbjname
             FROM lhpalloc a INNER JOIN lhpsubject s ON s.sbjid=a.sbjid
             WHERE a.staffid=? AND a.term=? AND a.classid=?
             ORDER BY s.sbjname',
            array($actor, $this->activeTerm(), $classId)
        );
    }

    public function topics($actor, $classId, $subjectId)
    {
        $classId = self::integer($classId, 'Class', 1);
        $subjectId = self::integer($subjectId, 'Subject', 1);
        $allocation = $this->allocation($actor, $classId, $subjectId);
        return $this->rows(
            'SELECT schmid,week,topic,staffid,rectime
             FROM lhpscheme
             WHERE term=? AND classname=? AND subject=? AND status=1
             ORDER BY CAST(REPLACE(UPPER(week),\'WEEK \',\'\') AS UNSIGNED),schmid',
            array($this->activeTerm(), (string)$classId, (string)$subjectId)
        );
    }

    public function get($id, $actor)
    {
        $id = self::integer($id, 'Topic', 1);
        $topic = $this->one(
            'SELECT t.schmid,t.classname AS class_id,t.subject AS subject_id,t.week,t.topic,t.staffid,c.classname,s.sbjname
             FROM lhpscheme t
             INNER JOIN lhpclass c ON c.classid=t.classname
             INNER JOIN lhpsubject s ON s.sbjid=t.subject
             WHERE t.schmid=? AND t.term=? AND t.status=1 LIMIT 1',
            array($id, $this->activeTerm())
        );
        if (!$topic) throw new InvalidArgumentException('This current-term topic was not found.');
        $this->allocation($actor, $topic['class_id'], $topic['subject_id']);
        if ((string)$topic['staffid'] !== (string)$actor) throw new RuntimeException('Only the teacher who created this topic can edit it.');
        $topic['week_number'] = (int)preg_replace('/\D+/', '', (string)$topic['week']);
        return $topic;
    }

    public function save(array $data, $actor)
    {
        $id = self::integer($data['id'] ?? 0, 'Topic');
        $classId = self::integer($data['class_id'] ?? 0, 'Class', 1);
        $subjectId = self::integer($data['subject_id'] ?? 0, 'Subject', 1);
        $weekNumber = self::integer($data['week'] ?? 0, 'Week', 1, 20);
        $topic = self::text($data['topic'] ?? '', 254);
        $term = $this->activeTerm();
        $this->allocation($actor, $classId, $subjectId);

        return $this->locked('scheme:' . ($id ?: $actor . ':' . $classId . ':' . $subjectId), function () use ($id, $classId, $subjectId, $weekNumber, $topic, $term, $actor) {
            $before = null;
            if ($id) {
                $before = $this->one('SELECT * FROM lhpscheme WHERE schmid=? AND status=1 FOR UPDATE', array($id));
                if (!$before || (string)$before['staffid'] !== (string)$actor || (string)$before['term'] !== (string)$term) {
                    throw new RuntimeException('Only the teacher who created this current-term topic can edit it.');
                }
                if ((int)$before['classname'] !== $classId || (int)$before['subject'] !== $subjectId) {
                    throw new InvalidArgumentException('The class and subject of an existing topic cannot be changed. Add a new topic in the other subject instead.');
                }
            }
            $duplicate = $this->one(
                'SELECT schmid FROM lhpscheme WHERE term=? AND classname=? AND subject=? AND week=? AND LOWER(topic)=LOWER(?) AND status=1 AND schmid<>? LIMIT 1',
                array($term, (string)$classId, (string)$subjectId, 'Week ' . $weekNumber, $topic, $id)
            );
            if ($duplicate) throw new InvalidArgumentException('That topic is already in this week of the scheme.');

            if ($id) {
                $this->execute(
                    'UPDATE lhpscheme SET classname=?,subject=?,week=?,topic=? WHERE schmid=?',
                    array((string)$classId, (string)$subjectId, 'Week ' . $weekNumber, $topic, $id)
                );
            } else {
                $this->execute(
                    'INSERT INTO lhpscheme (term,classname,subject,week,topic,staffid,status) VALUES (?,?,?,?,?,?,1)',
                    array($term, (string)$classId, (string)$subjectId, 'Week ' . $weekNumber, $topic, $actor)
                );
                $id = (int)$this->db->lastInsertId();
            }
            $after = $this->one('SELECT * FROM lhpscheme WHERE schmid=?', array($id));
            $this->audit('scheme', $id, $actor, $before ? 'edit' : 'create', $before, $after);
            return $after;
        });
    }

    public function archive($id, $actor, $confirmed)
    {
        $id = self::integer($id, 'Topic', 1);
        if (!$confirmed) throw new InvalidArgumentException('Confirm removing this topic from the active scheme.');
        return $this->locked('scheme:' . $id, function () use ($id, $actor) {
            $topic = $this->one('SELECT * FROM lhpscheme WHERE schmid=? AND status=1 FOR UPDATE', array($id));
            if (!$topic || (string)$topic['staffid'] !== (string)$actor || (string)$topic['term'] !== (string)$this->activeTerm()) {
                throw new RuntimeException('Only the teacher who created this current-term topic can remove it.');
            }
            $this->allocation($actor, $topic['classname'], $topic['subject']);
            $this->execute('UPDATE lhpscheme SET status=0 WHERE schmid=?', array($id));
            $this->audit('scheme', $id, $actor, 'archive', $topic, null);
        });
    }
}
