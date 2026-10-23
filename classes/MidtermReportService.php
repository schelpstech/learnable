<?php

/** Read-only Week 1-6 reports, using the class stored with the weekly scores. */
final class MidtermReportService
{
    private $db;
    private $root;
    private $base;
    private $context = [];
    private const WEEKS = "LOWER(TRIM(w.week)) IN ('week 1','week 2','week 3','week 4','week 5','week 6')";

    public function __construct(PDO $db, $root = null, $base = null)
    {
        $this->db = $db;
        $this->root = $root ?: dirname(__DIR__);
        if ($base === null) {
            $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
            $base = preg_match('~/admin/[^/]+$~', $script) ? dirname(dirname($script)) : parse_url((string) app_env('APP_URL', '/learnable'), PHP_URL_PATH);
        }
        $this->base = rtrim((string) $base, '/.');
    }

    public function terms()
    {
        return $this->rows('SELECT DISTINCT w.term, COALESCE(t.tid,0) AS term_order FROM lhpweekrecord w LEFT JOIN lpterm t ON t.term=w.term WHERE ' . self::WEEKS . ' ORDER BY term_order DESC, w.term DESC');
    }

    public function classes($term)
    {
        return $this->rows('SELECT w.classid, COALESCE(c.classname,w.classid) AS classname, COUNT(DISTINCT w.lid) AS learner_count FROM lhpweekrecord w INNER JOIN lhpuser u ON u.uname=w.lid LEFT JOIN lhpclass c ON c.classid=w.classid WHERE w.term=? AND ' . self::WEEKS . ' GROUP BY w.classid,c.classname ORDER BY classname', [$term]);
    }

    public function learners($classId, $term)
    {
        return $this->rows('SELECT DISTINCT u.uname,u.fname FROM lhpweekrecord w INNER JOIN lhpuser u ON u.uname=w.lid WHERE w.classid=? AND w.term=? AND ' . self::WEEKS . ' ORDER BY u.fname,u.uname', [$classId,$term]);
    }

    public function build($learnerId, $term, $classId = '')
    {
        if ($classId === '') {
            $classes = $this->rows('SELECT DISTINCT w.classid FROM lhpweekrecord w WHERE w.lid=? AND w.term=? AND ' . self::WEEKS, [$learnerId,$term]);
            if (count($classes) !== 1) throw new RuntimeException('Choose the recorded class and term for this learner.');
            $classId = $classes[0]['classid'];
        }
        $reports = $this->load($classId,$term,$learnerId);
        if (!$reports) throw new RuntimeException('No Week 1-6 results were found for this learner, class and term.');
        return $reports[0];
    }

    public function buildClass($classId, $term)
    {
        return $this->load($classId,$term,null);
    }

    private function load($classId, $term, $learnerId)
    {
        $params = [$classId,$term];
        if ($learnerId !== null) $params[] = $learnerId;
        $rows = $this->rows('SELECT w.id,w.lid,w.subjid,w.week,w.score,u.fname,u.gender,u.dob,u.picture,COALESCE(s.sbjname,CONCAT("Subject ",w.subjid)) AS subject_name FROM lhpweekrecord w INNER JOIN lhpuser u ON u.uname=w.lid LEFT JOIN lhpsubject s ON s.sbjid=w.subjid WHERE w.classid=? AND w.term=? AND ' . self::WEEKS . ($learnerId !== null ? ' AND w.lid=?' : '') . ' ORDER BY u.fname,w.lid,subject_name,w.id', $params);
        $reports = [];
        foreach ($rows as $row) {
            $id = $row['lid'];
            $week = (int) substr(trim($row['week']), -1);
            if (!isset($reports[$id])) $reports[$id] = ['learner'=>['id'=>$id,'name'=>$row['fname'],'gender'=>$row['gender'],'dob'=>$row['dob'],'class_id'=>(string)$classId,'passport_url'=>$this->image($row['picture'],'learn/asset/img/passport','assets/images/student-placeholder.svg')], 'subjects'=>[]];
            $subject = $row['subjid'];
            if (!isset($reports[$id]['subjects'][$subject])) $reports[$id]['subjects'][$subject] = ['id'=>$subject,'name'=>$row['subject_name'],'weeks'=>array_fill(1,6,null)];
            if ($reports[$id]['subjects'][$subject]['weeks'][$week] !== null) throw new RuntimeException('Duplicate weekly scores for learner ' . $id . ', ' . $row['subject_name'] . ', Week ' . $week . ' need review before export.');
            if (!is_numeric($row['score']) || $row['score'] < 0 || $row['score'] > 10) throw new RuntimeException('A weekly score is outside 0-10 for learner ' . $id . '. Correct the scorebook before export.');
            $reports[$id]['subjects'][$subject]['weeks'][$week] = (float) $row['score'];
        }
        if (!$reports) return [];
        $key = $classId . '|' . $term;
        if (!isset($this->context[$key])) {
            $school = $this->one('SELECT * FROM lhpschool ORDER BY schid LIMIT 1') ?: [];
            $config = $this->one('SELECT * FROM lhpresultconfig WHERE term=? ORDER BY id DESC LIMIT 1',[$term]) ?: [];
            $class = $this->one('SELECT classname FROM lhpclass WHERE classid=?',[$classId]);
            $teacher = $this->one('SELECT s.staffname FROM lhpclassalloc a LEFT JOIN lhpstaff s ON s.sname=a.tutorid WHERE a.classid=? AND a.term=? LIMIT 1',[$classId,$term]);
            $population = $this->one('SELECT COUNT(DISTINCT w.lid) AS total FROM lhpweekrecord w INNER JOIN lhpuser u ON u.uname=w.lid WHERE w.classid=? AND w.term=? AND ' . self::WEEKS,[$classId,$term]);
            $this->context[$key] = ['school'=>$school,'logo_url'=>$this->image($school['logo'] ?? '','learn/asset/img/school','assets/images/school-placeholder.svg'), 'signature_url'=>$this->image($config['signature'] ?? '','admin/archive',''), 'class_name'=>$class['classname'] ?? (string)$classId, 'teacher'=>$teacher['staffname'] ?? '', 'population'=>(int)$population['total'], 'term'=>$term];
        }
        foreach ($reports as &$report) {
            $report += $this->context[$key];
            $report['total'] = 0;
            $report['missing'] = 0;
            foreach ($report['subjects'] as &$subject) {
                $subject['total'] = array_sum($subject['weeks']) / 2;
                [$subject['grade'],$subject['remark']] = self::grade($subject['total']);
                $report['total'] += $subject['total'];
                $report['missing'] += count(array_filter($subject['weeks'],static function ($score) { return $score === null; }));
            }
            unset($subject);
            $report['subjects'] = array_values($report['subjects']);
            $report['maximum'] = count($report['subjects']) * 30;
            $report['average'] = $report['maximum'] ? $report['total'] / $report['maximum'] * 100 : 0;
            [$report['grade'],$report['remark']] = self::grade($report['average'] * .3);
            $report['file_name'] = self::fileName($report['learner']['name'] . '-' . $term);
        }
        unset($report);
        return array_values($reports);
    }

    public static function grade($score)
    {
        if ($score >= 22.5) return ['A','Excellent'];
        if ($score >= 19.5) return ['B','Very Good'];
        if ($score >= 15) return ['C','Moderate'];
        if ($score >= 13.5) return ['D','Fair'];
        if ($score >= 12) return ['E','Needs Help'];
        return ['F','Needs Help'];
    }

    public static function fileName($label)
    {
        $name = trim(preg_replace('/[^A-Za-z0-9]+/','-',(string)$label),'-');
        return ($name ?: 'School') . '-midterm-report.pdf';
    }

    private function image($value, $directory, $fallback)
    {
        $name = basename(str_replace('\\','/',trim((string)$value)));
        $path = $this->root . '/' . $directory . '/' . $name;
        if ($name !== '' && !in_array(strtolower($name),['nopix.jpg','nopix.png'],true) && preg_match('/\.(jpe?g|png|gif|webp)$/iD',$name) && is_file($path) && @getimagesize($path)) return $this->base . '/' . $directory . '/' . rawurlencode($name);
        return $fallback === '' ? '' : $this->base . '/' . $fallback;
    }

    private function rows($sql, array $params=[])
    {
        $q=$this->db->prepare($sql); $q->execute($params); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    private function one($sql, array $params=[])
    {
        $rows=$this->rows($sql,$params); return $rows[0] ?? null;
    }
}
