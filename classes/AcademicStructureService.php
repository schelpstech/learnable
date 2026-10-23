<?php

final class AcademicStructureService extends SchoolService
{
    public function classes()
    {
        $term = $this->activeTerm();
        $classes = $this->rows(
            'SELECT c.classid,c.classname,COUNT(DISTINCT u.id) AS population
             FROM lhpclass c
             LEFT JOIN lhpuser u ON u.classid=c.classid AND u.status=1
             GROUP BY c.classid,c.classname ORDER BY c.classname'
        );
        $teachers = [];
        foreach ($this->rows('SELECT ca.classid,ca.tutorid,st.staffname FROM lhpclassalloc ca LEFT JOIN lhpstaff st ON st.sname=ca.tutorid WHERE ca.term=? ORDER BY ca.classlocid DESC', [$term]) as $row) {
            if (!isset($teachers[$row['classid']])) $teachers[$row['classid']] = $row['staffname'] ?: $row['tutorid'];
        }
        foreach ($classes as &$class) $class['class_teacher'] = $teachers[$class['classid']] ?? '';
        unset($class);
        return $classes;
    }

    public function subjects()
    {
        return $this->rows(
            'SELECT s.sbjid,s.sbjname,s.classid,c.classname,
                    COUNT(DISTINCT a.aid) AS allocation_count
             FROM lhpsubject s INNER JOIN lhpclass c ON c.classid=s.classid
             LEFT JOIN lhpalloc a ON a.sbjid=s.sbjid
             GROUP BY s.sbjid,s.sbjname,s.classid,c.classname
             ORDER BY c.classname,s.sbjname'
        );
    }

    public function staff()
    {
        return $this->rows('SELECT sname,staffname FROM lhpstaff WHERE status=1 ORDER BY staffname');
    }

    public function classTeacher($classId)
    {
        $classId = self::integer($classId, 'Class', 1);
        $class = $this->one('SELECT classid,classname FROM lhpclass WHERE classid=?', [$classId]);
        if (!$class) throw new InvalidArgumentException('Class not found.');
        $term = $this->activeTerm();
        $assignments = $this->rows('SELECT ca.*,s.staffname FROM lhpclassalloc ca LEFT JOIN lhpstaff s ON s.sname=ca.tutorid WHERE ca.classid=? AND ca.term=? ORDER BY ca.classlocid DESC', [$classId,$term]);
        return ['class'=>$class, 'term'=>$term, 'assignments'=>$assignments];
    }

    public function classTeachers()
    {
        return $this->rows("SELECT sname,staffname FROM lhpstaff WHERE status=1 AND role='t' ORDER BY staffname,sname");
    }

    public function saveClassTeacher(array $data, $actor)
    {
        $classId = self::integer($data['class_id'] ?? 0, 'Class', 1);
        $teacherId = self::text($data['teacher_id'] ?? '', 64);
        $term = $this->activeTerm();
        if (($data['term'] ?? '') !== $term) throw new InvalidArgumentException('The active term changed. Reopen Edit class teacher and try again.');
        return $this->locked('class-teacher:'.$term.':'.$classId, function () use ($classId,$teacherId,$term,$actor) {
            return $this->transaction(function () use ($classId,$teacherId,$term,$actor) {
                $class = $this->one('SELECT classid FROM lhpclass WHERE classid=? FOR UPDATE', [$classId]);
                $teacher = $this->one("SELECT sname FROM lhpstaff WHERE sname=? AND status=1 AND role='t'", [$teacherId]);
                if (!$class || !$teacher) throw new InvalidArgumentException('Choose a valid class and an active teaching staff member.');
                $before = $this->rows('SELECT * FROM lhpclassalloc WHERE classid=? AND term=? ORDER BY classlocid DESC FOR UPDATE', [$classId,$term]);
                if ($before) {
                    $id = (int)$before[0]['classlocid'];
                    // Old versions inserted duplicates. Keep one record for this class and term.
                    $this->execute('DELETE FROM lhpclassalloc WHERE classid=? AND term=? AND classlocid<>?', [$classId,$term,$id]);
                    $this->execute('UPDATE lhpclassalloc SET tutorid=? WHERE classlocid=?', [$teacherId,$id]);
                } else {
                    $this->execute('INSERT INTO lhpclassalloc (classid,tutorid,term) VALUES (?,?,?)', [$classId,$teacherId,$term]);
                    $id = (int)$this->db->lastInsertId();
                }
                $after = $this->one('SELECT * FROM lhpclassalloc WHERE classlocid=?', [$id]);
                // Verify the stored username too: old production columns can truncate long IDs.
                if (!$after || $after['tutorid'] !== $teacherId) throw new RuntimeException('The selected teacher could not be saved. Please contact the administrator.');
                $this->audit('class_teacher',$classId,$actor,$before ? 'replace' : 'assign',$before,$after);
                return $id;
            });
        });
    }

    public function allocations()
    {
        return $this->rows(
            'SELECT a.aid,a.term,a.classid,a.sbjid,a.staffid,c.classname,s.sbjname,st.staffname
             FROM lhpalloc a
             INNER JOIN lhpclass c ON c.classid=a.classid
             INNER JOIN lhpsubject s ON s.sbjid=a.sbjid
             LEFT JOIN lhpstaff st ON st.sname=a.staffid
             WHERE a.term=? ORDER BY c.classname,s.sbjname',
            array($this->activeTerm())
        );
    }

    public function saveClass(array $data, $actor)
    {
        $id = self::integer($data['id'] ?? 0, 'Class');
        $name = self::text($data['name'] ?? '', 16);
        return $this->locked('class:' . ($id ?: strtolower($name)), function () use ($id, $name, $actor) {
            $before = $id ? $this->one('SELECT * FROM lhpclass WHERE classid=? FOR UPDATE', array($id)) : null;
            if ($id && !$before) throw new InvalidArgumentException('Class not found.');
            $duplicate = $this->one('SELECT classid FROM lhpclass WHERE LOWER(classname)=LOWER(?) AND classid<>? LIMIT 1', array($name, $id));
            if ($duplicate) throw new InvalidArgumentException('A class with that name already exists.');
            if ($id) {
                $this->execute('UPDATE lhpclass SET classname=? WHERE classid=?', array($name, $id));
                $this->execute('UPDATE lhpsubject SET classname=? WHERE classid=?', array($name, (string)$id));
                $this->execute('UPDATE lhpalloc SET classname=? WHERE classid=?', array($name, $id));
                $this->execute('UPDATE classact SET classname=? WHERE classid=?', array($name, (string)$id));
                $this->execute('UPDATE schedule SET classname=? WHERE scclass=?', array($name, (string)$id));
            } else {
                $this->execute('INSERT INTO lhpclass (classname) VALUES (?)', array($name));
                $id = (int)$this->db->lastInsertId();
            }
            $after = $this->one('SELECT * FROM lhpclass WHERE classid=?', array($id));
            $this->audit('class', $id, $actor, $before ? 'rename' : 'create', $before, $after);
            return $id;
        });
    }

    public function saveSubject(array $data, $actor)
    {
        $id = self::integer($data['id'] ?? 0, 'Subject');
        $classId = self::integer($data['class_id'] ?? 0, 'Class', 1);
        $name = self::text($data['name'] ?? '', 64);
        return $this->locked('subject:' . ($id ?: $classId . ':' . strtolower($name)), function () use ($id, $classId, $name, $actor) {
            $class = $this->one('SELECT classid,classname FROM lhpclass WHERE classid=?', array($classId));
            if (!$class) throw new InvalidArgumentException('Choose a valid class.');
            $before = $id ? $this->one('SELECT * FROM lhpsubject WHERE sbjid=? FOR UPDATE', array($id)) : null;
            if ($id && !$before) throw new InvalidArgumentException('Subject not found.');
            if ($before && (int)$before['classid'] !== $classId) throw new InvalidArgumentException('A subject cannot be moved to another class after creation.');
            $duplicate = $this->one('SELECT sbjid FROM lhpsubject WHERE classid=? AND LOWER(sbjname)=LOWER(?) AND sbjid<>? LIMIT 1', array((string)$classId, $name, $id));
            if ($duplicate) throw new InvalidArgumentException('That subject already exists in this class.');
            if ($id) {
                $this->execute('UPDATE lhpsubject SET sbjname=?,classname=? WHERE sbjid=?', array($name, $class['classname'], $id));
                $this->execute('UPDATE lhpalloc SET subject=?,classname=? WHERE sbjid=?', array($name, $class['classname'], $id));
            } else {
                $this->execute('INSERT INTO lhpsubject (sbjname,classid,classname) VALUES (?,?,?)', array($name, (string)$classId, $class['classname']));
                $id = (int)$this->db->lastInsertId();
            }
            $after = $this->one('SELECT * FROM lhpsubject WHERE sbjid=?', array($id));
            $this->audit('subject', $id, $actor, $before ? 'rename' : 'create', $before, $after);
            return $id;
        });
    }

    public function saveAllocation(array $data, $actor)
    {
        $id = self::integer($data['id'] ?? 0, 'Allocation');
        $classId = self::integer($data['class_id'] ?? 0, 'Class', 1);
        $subjectId = self::integer($data['subject_id'] ?? 0, 'Subject', 1);
        $teacherId = self::text($data['teacher_id'] ?? '', 64);
        $term = $this->activeTerm();
        return $this->locked('allocation:' . ($id ?: $term . ':' . $classId . ':' . $subjectId), function () use ($id, $classId, $subjectId, $teacherId, $term, $actor) {
            $class = $this->one('SELECT classid,classname FROM lhpclass WHERE classid=?', array($classId));
            $subject = $this->one('SELECT sbjid,sbjname,classid FROM lhpsubject WHERE sbjid=?', array($subjectId));
            $teacher = $this->one('SELECT sname FROM lhpstaff WHERE sname=? AND status=1', array($teacherId));
            if (!$class || !$subject || !$teacher || (int)$subject['classid'] !== $classId) throw new InvalidArgumentException('Choose a valid class, subject and active teacher.');
            $before = $id ? $this->one('SELECT * FROM lhpalloc WHERE aid=? AND term=? FOR UPDATE', array($id, $term)) : null;
            if ($id && !$before) throw new InvalidArgumentException('This active-term allocation was not found.');
            if ($before && ((int)$before['classid'] !== $classId || (int)$before['sbjid'] !== $subjectId)) {
                throw new InvalidArgumentException('The class and subject of an existing allocation cannot be changed. Create a separate allocation instead.');
            }
            $duplicate = $this->one('SELECT aid FROM lhpalloc WHERE term=? AND classid=? AND sbjid=? AND aid<>? LIMIT 1', array($term, $classId, $subjectId, $id));
            if ($duplicate) throw new InvalidArgumentException('This subject is already allocated for the active term. Edit its teacher instead.');
            if ($id) {
                $this->execute('UPDATE lhpalloc SET classname=?,subject=?,staffid=?,classid=?,sbjid=? WHERE aid=?', array($class['classname'], $subject['sbjname'], $teacherId, $classId, $subjectId, $id));
            } else {
                $this->execute('INSERT INTO lhpalloc (term,classname,subject,staffid,supro,classid,sbjid) VALUES (?,?,?,?,?,?,?)', array($term, $class['classname'], $subject['sbjname'], $teacherId, '', $classId, $subjectId));
                $id = (int)$this->db->lastInsertId();
            }
            $after = $this->one('SELECT * FROM lhpalloc WHERE aid=?', array($id));
            $this->audit('allocation', $id, $actor, $before ? 'edit' : 'create', $before, $after);
            return $id;
        });
    }

    public function deleteAllocation($id, $actor, $confirmed)
    {
        $id = self::integer($id, 'Allocation', 1);
        if (!$confirmed) throw new InvalidArgumentException('Confirm removing this subject allocation.');
        return $this->locked('allocation:' . $id, function () use ($id, $actor) {
            $before = $this->one('SELECT * FROM lhpalloc WHERE aid=? AND term=? FOR UPDATE', array($id, $this->activeTerm()));
            if (!$before) throw new InvalidArgumentException('This active-term allocation was not found.');
            $this->audit('allocation', $id, $actor, 'delete', $before, null);
            $this->execute('DELETE FROM lhpalloc WHERE aid=?', array($id));
        });
    }
}
