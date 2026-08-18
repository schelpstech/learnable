<?php

/**
 * Builds a safe, portal-neutral report view model from the legacy result data.
 * The service is deliberately read-only so the report redesign cannot mutate
 * historical scores or require database changes.
 */
final class ReportService
{
    private $pdo;
    private $projectRoot;
    private $baseUrl;
    private $rowCache = array();
    private $termColumnsCache = array();
    private $studentCache = array();
    private $resultClassCache = array();
    private $subjectCache = array();
    private $affectiveCache = array();
    private $historyCache = array();
    private $cumulativeCache = array();

    public function __construct(PDO $pdo, $projectRoot = null, $baseUrl = null)
    {
        $this->pdo = $pdo;
        $this->projectRoot = $projectRoot ?: dirname(__DIR__);
        if ($baseUrl === null && function_exists('app_env')) {
            $baseUrl = app_env('APP_URL', '/learnable');
        }
        $this->baseUrl = rtrim((string) ($baseUrl ?: '/learnable'), '/');
    }

    public function build($learnerId, $term, $cumulative = false)
    {
        $learnerId = trim((string) $learnerId);
        $term = trim((string) $term);
        if ($learnerId === '' || $term === '') {
            throw new InvalidArgumentException('Learner and term are required.');
        }

        if (!array_key_exists($learnerId, $this->studentCache)) {
            $this->studentCache[$learnerId] = $this->one(
                'SELECT u.*, c.classname FROM lhpuser u LEFT JOIN lhpclass c ON c.classid = u.classid WHERE u.uname = ? LIMIT 1',
                array($learnerId)
            );
        }
        $student = $this->studentCache[$learnerId];
        if (!$student) {
            throw new RuntimeException('Learner record not found.');
        }

        $resultClassKey = $learnerId . '|' . $term;
        if (!array_key_exists($resultClassKey, $this->resultClassCache)) {
            $this->resultClassCache[$resultClassKey] = $this->one(
                'SELECT classid FROM lhpresultrecord WHERE lid = ? AND term = ? ORDER BY rectime DESC LIMIT 1',
                array($learnerId, $term)
            );
        }
        $resultClass = $this->resultClassCache[$resultClassKey];
        $classId = $resultClass ? (string) $resultClass['classid'] : (string) $student['classid'];
        $class = $this->cachedOne('class:' . $classId, 'SELECT classname FROM lhpclass WHERE classid = ? LIMIT 1', array($classId));

        $subjectKey = $learnerId . '|' . $term;
        if (!array_key_exists($subjectKey, $this->subjectCache)) {
            $this->subjectCache[$subjectKey] = $this->all(
                'SELECT r.subjid, COALESCE(s.sbjname, CONCAT("Subject ", r.subjid)) AS subject_name,
                        r.score, r.examscore, r.totalscore,
                        stats.lowest_score, stats.average_score, stats.highest_score
                 FROM lhpresultrecord r
                 LEFT JOIN lhpsubject s ON s.sbjid = r.subjid
                 LEFT JOIN (
                     SELECT classid, term, subjid, MIN(totalscore) AS lowest_score,
                            AVG(totalscore) AS average_score, MAX(totalscore) AS highest_score
                     FROM lhpresultrecord
                     WHERE classid = ? AND term = ?
                     GROUP BY classid, term, subjid
                 ) stats ON stats.classid = r.classid AND stats.term = r.term AND stats.subjid = r.subjid
                 WHERE r.lid = ? AND r.term = ?
                 ORDER BY subject_name',
                array($classId, $term, $learnerId, $term)
            );
        }
        $subjects = $this->subjectCache[$subjectKey];

        foreach ($subjects as &$subject) {
            $subject['remark'] = $this->gradeRemark($subject['totalscore']);
        }
        unset($subject);

        $config = $this->cachedOne('config:' . $term, 'SELECT * FROM lhpresultconfig WHERE term = ? LIMIT 1', array($term));
        $school = $this->cachedOne('school', 'SELECT * FROM lhpschool ORDER BY schid LIMIT 1');
        if (!array_key_exists($subjectKey, $this->affectiveCache)) {
            $this->affectiveCache[$subjectKey] = $this->one('SELECT * FROM lhpaffective WHERE uname = ? AND term = ? LIMIT 1', array($learnerId, $term));
        }
        $affective = $this->affectiveCache[$subjectKey];
        $teacher = $this->cachedOne(
            'teacher:' . $classId . ':' . $term,
            'SELECT s.staffname FROM lhpclassalloc a LEFT JOIN lhpstaff s ON s.sname = a.tutorid
             WHERE a.classid = ? AND a.term = ? LIMIT 1',
            array($classId, $term)
        );

        $summary = $this->summarize($subjects);
        $history = $this->history($learnerId, $term);
        $termColumns = $this->termColumns($term);

        $previousAverage = null;
        foreach ($history as $index => $historyRow) {
            if ($historyRow['term'] === $term && $index > 0) {
                $previousAverage = (float) $history[$index - 1]['average'];
                break;
            }
        }
        $change = $previousAverage === null ? null : $summary['average'] - $previousAverage;

        return array(
            'learner' => array(
                'id' => $learnerId,
                'name' => (string) $student['fname'],
                'gender' => (string) $student['gender'],
                'date_of_birth' => (string) $student['dob'],
                'class_id' => $classId,
                'class_name' => $class ? (string) $class['classname'] : (string) ($student['classname'] ?? ''),
                'passport_url' => $this->passportUrl($student['picture'] ?? ''),
            ),
            'school' => array(
                'name' => (string) ($school['schname'] ?? 'LearnAble School'),
                'address' => (string) ($school['address'] ?? ''),
                'phone' => (string) ($school['phone'] ?? ''),
                'email' => (string) ($school['email'] ?? ''),
                'motto' => (string) ($school['motto'] ?? ''),
                'logo_url' => $this->schoolLogoUrl($school['logo'] ?? ''),
            ),
            'term' => $term,
            'session' => $this->sessionFromTerm($term),
            'subjects' => $subjects,
            'summary' => $summary,
            'history' => $history,
            'change' => $change,
            'teacher_name' => (string) ($teacher['staffname'] ?? ''),
            'config' => array(
                'ca_score' => (int) ($config['ca_score'] ?? 40),
                'exam_score' => (int) ($config['exam_score'] ?? 60),
                'school_open' => (int) ($config['sch_open'] ?? 0),
                'resumption' => (string) ($config['resumption'] ?? ''),
            ),
            'affective' => $affective ?: array(),
            'cumulative' => (bool) $cumulative,
            'cumulative_terms' => $termColumns,
            'cumulative_subjects' => $cumulative ? $this->cumulativeSubjects($learnerId, $termColumns) : array(),
            'generated_at' => date('j M Y'),
            'file_name' => $this->fileName($student['fname'], $term),
        );
    }

    /**
     * Build every learner report for one class using the same view model as
     * the individual report. Shared school, class, term and teacher lookups
     * are cached so printing a class does not repeat those database queries.
     */
    public function buildClass($classId, $term, $cumulative = false)
    {
        $classId = trim((string) $classId);
        $term = trim((string) $term);
        if ($classId === '' || $term === '') {
            throw new InvalidArgumentException('Class and term are required.');
        }

        $learners = $this->all(
            'SELECT DISTINCT r.lid, COALESCE(NULLIF(TRIM(u.fname), ""), r.lid) AS learner_name
             FROM lhpresultrecord r
             INNER JOIN lhpuser u ON u.uname = r.lid
             WHERE r.classid = ? AND r.term = ?
             ORDER BY learner_name, r.lid',
            array($classId, $term)
        );

        $this->preloadClassData($learners, $classId, $term, $cumulative);
        $reports = array();
        foreach ($learners as $learner) {
            $reports[] = $this->build($learner['lid'], $term, $cumulative);
        }
        return $reports;
    }

    private function preloadClassData(array $learners, $classId, $term, $cumulative)
    {
        $learnerIds = array_values(array_map(function ($learner) {
            return (string) $learner['lid'];
        }, $learners));
        if (!$learnerIds) {
            return;
        }

        $learnerPlaceholders = implode(',', array_fill(0, count($learnerIds), '?'));
        $students = $this->all(
            'SELECT u.*, c.classname FROM lhpuser u
             LEFT JOIN lhpclass c ON c.classid = u.classid
             WHERE u.uname IN (' . $learnerPlaceholders . ')',
            $learnerIds
        );
        foreach ($students as $student) {
            $this->studentCache[(string) $student['uname']] = $student;
        }

        foreach ($learnerIds as $learnerId) {
            $key = $learnerId . '|' . $term;
            $this->resultClassCache[$key] = array('classid' => $classId);
            $this->subjectCache[$key] = array();
            $this->affectiveCache[$key] = null;
            $this->historyCache[$learnerId] = array();
        }

        $subjectRows = $this->all(
            'SELECT r.lid, r.subjid, COALESCE(s.sbjname, CONCAT("Subject ", r.subjid)) AS subject_name,
                    r.score, r.examscore, r.totalscore,
                    stats.lowest_score, stats.average_score, stats.highest_score
             FROM lhpresultrecord r
             LEFT JOIN lhpsubject s ON s.sbjid = r.subjid
             LEFT JOIN (
                 SELECT classid, term, subjid, MIN(totalscore) AS lowest_score,
                        AVG(totalscore) AS average_score, MAX(totalscore) AS highest_score
                 FROM lhpresultrecord
                 WHERE classid = ? AND term = ?
                 GROUP BY classid, term, subjid
             ) stats ON stats.classid = r.classid AND stats.term = r.term AND stats.subjid = r.subjid
             WHERE r.classid = ? AND r.term = ?
             ORDER BY r.lid, subject_name',
            array($classId, $term, $classId, $term)
        );
        foreach ($subjectRows as $subjectRow) {
            $learnerId = (string) $subjectRow['lid'];
            unset($subjectRow['lid']);
            $this->subjectCache[$learnerId . '|' . $term][] = $subjectRow;
        }

        $affectiveRows = $this->all(
            'SELECT * FROM lhpaffective WHERE term = ? AND uname IN (' . $learnerPlaceholders . ')',
            array_merge(array($term), $learnerIds)
        );
        foreach ($affectiveRows as $affectiveRow) {
            $this->affectiveCache[(string) $affectiveRow['uname'] . '|' . $term] = $affectiveRow;
        }

        $historyRows = $this->all(
            'SELECT r.lid, r.term, ROUND(AVG(r.totalscore), 2) AS average, SUM(r.totalscore) AS total,
                    COUNT(r.totalscore) AS subjects, MAX(r.rectime) AS result_date, MAX(t.tid) AS term_order
             FROM lhpresultrecord r
             LEFT JOIN lpterm t ON t.term = r.term
             WHERE r.lid IN (' . $learnerPlaceholders . ')
             GROUP BY r.lid, r.term
             ORDER BY r.lid, CASE WHEN MAX(t.tid) IS NULL THEN 1 ELSE 0 END, MAX(t.tid), MAX(r.rectime)',
            $learnerIds
        );
        foreach ($historyRows as $historyRow) {
            $learnerId = (string) $historyRow['lid'];
            unset($historyRow['lid']);
            $this->historyCache[$learnerId][] = $historyRow;
        }

        if (!$cumulative) {
            return;
        }

        $terms = $this->termColumns($term);
        $termPlaceholders = implode(',', array_fill(0, count($terms), '?'));
        $cumulativeByLearner = array();
        foreach ($learnerIds as $learnerId) {
            $cumulativeByLearner[$learnerId] = array();
            $this->cumulativeCache[$learnerId . '|' . implode('|', $terms)] = array();
        }
        $cumulativeRows = $this->all(
            'SELECT r.lid, r.term, r.subjid, COALESCE(s.sbjname, CONCAT("Subject ", r.subjid)) AS subject_name,
                    r.totalscore
             FROM lhpresultrecord r
             LEFT JOIN lhpsubject s ON s.sbjid = r.subjid
             WHERE r.lid IN (' . $learnerPlaceholders . ') AND r.term IN (' . $termPlaceholders . ')
             ORDER BY r.lid, subject_name',
            array_merge($learnerIds, $terms)
        );
        foreach ($cumulativeRows as $row) {
            $learnerId = (string) $row['lid'];
            $subjectId = (string) $row['subjid'];
            if (!isset($cumulativeByLearner[$learnerId][$subjectId])) {
                $cumulativeByLearner[$learnerId][$subjectId] = array(
                    'subject_name' => $row['subject_name'],
                    'scores' => array(),
                );
            }
            $cumulativeByLearner[$learnerId][$subjectId]['scores'][$row['term']] = (float) $row['totalscore'];
        }
        foreach ($cumulativeByLearner as $learnerId => $subjects) {
            foreach ($subjects as &$subject) {
                $available = array_values($subject['scores']);
                $subject['average'] = $available ? array_sum($available) / count($available) : 0;
                $subject['remark'] = $this->gradeRemark($subject['average']);
            }
            unset($subject);
            $this->cumulativeCache[$learnerId . '|' . implode('|', $terms)] = array_values($subjects);
        }
    }

    private function summarize(array $subjects)
    {
        $count = count($subjects);
        $ca = 0.0;
        $exam = 0.0;
        $total = 0.0;
        foreach ($subjects as $subject) {
            $ca += (float) $subject['score'];
            $exam += (float) $subject['examscore'];
            $total += (float) $subject['totalscore'];
        }
        $average = $count ? $total / $count : 0.0;

        return array(
            'subject_count' => $count,
            'ca_average' => $count ? $ca / $count : 0.0,
            'exam_average' => $count ? $exam / $count : 0.0,
            'total' => $total,
            'average' => $average,
            'grade' => $this->gradeLetter($average),
            'remark' => $this->performanceRemark($average),
        );
    }

    private function history($learnerId, $selectedTerm)
    {
        if (!array_key_exists($learnerId, $this->historyCache)) {
            $this->historyCache[$learnerId] = $this->all(
                'SELECT r.term, ROUND(AVG(r.totalscore), 2) AS average, SUM(r.totalscore) AS total,
                        COUNT(r.totalscore) AS subjects, MAX(r.rectime) AS result_date, MAX(t.tid) AS term_order
                 FROM lhpresultrecord r
                 LEFT JOIN lpterm t ON t.term = r.term
                 WHERE r.lid = ?
                 GROUP BY r.term
                 ORDER BY CASE WHEN MAX(t.tid) IS NULL THEN 1 ELSE 0 END, MAX(t.tid), MAX(r.rectime)',
                array($learnerId)
            );
        }
        $rows = $this->historyCache[$learnerId];

        return $this->historyWindow($rows, $selectedTerm);
    }

    private function historyWindow(array $rows, $selectedTerm)
    {

        if (count($rows) <= 4) {
            return $rows;
        }

        $selectedIndex = null;
        foreach ($rows as $index => $row) {
            if ($row['term'] === $selectedTerm) {
                $selectedIndex = $index;
                break;
            }
        }
        if ($selectedIndex === null) {
            return array_slice($rows, -4);
        }
        $start = max(0, min($selectedIndex - 3, count($rows) - 4));
        return array_slice($rows, $start, 4);
    }

    private function termColumns($selectedTerm)
    {
        if (isset($this->termColumnsCache[$selectedTerm])) {
            return $this->termColumnsCache[$selectedTerm];
        }
        $current = $this->one('SELECT tid FROM lpterm WHERE term = ? LIMIT 1', array($selectedTerm));
        if (!$current) {
            $this->termColumnsCache[$selectedTerm] = array($selectedTerm);
            return $this->termColumnsCache[$selectedTerm];
        }
        $statement = $this->pdo->prepare('SELECT term FROM lpterm WHERE tid <= ? ORDER BY tid DESC LIMIT 3');
        $statement->execute(array($current['tid']));
        $this->termColumnsCache[$selectedTerm] = array_reverse(array_column($statement->fetchAll(), 'term'));
        return $this->termColumnsCache[$selectedTerm];
    }

    private function cumulativeSubjects($learnerId, array $terms)
    {
        if (!$terms) {
            return array();
        }
        $cacheKey = $learnerId . '|' . implode('|', $terms);
        if (array_key_exists($cacheKey, $this->cumulativeCache)) {
            return $this->cumulativeCache[$cacheKey];
        }
        $placeholders = implode(',', array_fill(0, count($terms), '?'));
        $parameters = array_merge(array($learnerId), $terms);
        $rows = $this->all(
            'SELECT r.term, r.subjid, COALESCE(s.sbjname, CONCAT("Subject ", r.subjid)) AS subject_name,
                    r.score, r.examscore, r.totalscore
             FROM lhpresultrecord r LEFT JOIN lhpsubject s ON s.sbjid = r.subjid
             WHERE r.lid = ? AND r.term IN (' . $placeholders . ')
             ORDER BY subject_name',
            $parameters
        );
        $subjects = array();
        foreach ($rows as $row) {
            $key = (string) $row['subjid'];
            if (!isset($subjects[$key])) {
                $subjects[$key] = array('subject_name' => $row['subject_name'], 'scores' => array());
            }
            $subjects[$key]['scores'][$row['term']] = (float) $row['totalscore'];
        }
        foreach ($subjects as &$subject) {
            $available = array_values($subject['scores']);
            $subject['average'] = $available ? array_sum($available) / count($available) : 0;
            $subject['remark'] = $this->gradeRemark($subject['average']);
        }
        unset($subject);
        $this->cumulativeCache[$cacheKey] = array_values($subjects);
        return $this->cumulativeCache[$cacheKey];
    }

    private function passportUrl($picture)
    {
        $fileName = basename(trim((string) $picture));
        $directory = $this->projectRoot . DIRECTORY_SEPARATOR . 'learn' . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'passport';
        if ($fileName === '' || !is_file($directory . DIRECTORY_SEPARATOR . $fileName)) {
            $fileName = 'nopix.jpg';
        }
        return $this->baseUrl . '/learn/asset/img/passport/' . rawurlencode($fileName);
    }

    private function schoolLogoUrl($logo)
    {
        $fileName = basename(trim((string) $logo));
        $directory = $this->projectRoot . DIRECTORY_SEPARATOR . 'learn' . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'school';
        if ($fileName === '' || !is_file($directory . DIRECTORY_SEPARATOR . $fileName)) {
            $fileName = is_file($directory . DIRECTORY_SEPARATOR . 'schlogo.jpg') ? 'schlogo.jpg' : 'schlogo.jpeg';
        }
        return $this->baseUrl . '/learn/asset/img/school/' . rawurlencode($fileName);
    }

    private function sessionFromTerm($term)
    {
        if (preg_match('/\b(20\d{2}\s*\/\s*20\d{2})\b/', $term, $matches)) {
            return preg_replace('/\s+/', '', $matches[1]);
        }
        $active = $this->cachedOne('active-session', 'SELECT session FROM lhpsession WHERE status = 1 LIMIT 1');
        return (string) ($active['session'] ?? '');
    }

    private function gradeLetter($score)
    {
        $score = (float) $score;
        if ($score >= 75) return 'A';
        if ($score >= 65) return 'B';
        if ($score >= 50) return 'C';
        if ($score >= 45) return 'D';
        if ($score >= 40) return 'E';
        return 'F';
    }

    private function gradeRemark($score)
    {
        $grade = $this->gradeLetter($score);
        $remarks = array('A' => 'Excellent', 'B' => 'Very good', 'C' => 'Good', 'D' => 'Fair', 'E' => 'Pass', 'F' => 'Needs support');
        return $remarks[$grade];
    }

    private function performanceRemark($score)
    {
        if ($score >= 75) return 'Excellent progress. Keep sustaining this strong performance.';
        if ($score >= 65) return 'Very good progress, with room to reach an even higher level.';
        if ($score >= 50) return 'Good foundation. More consistent study will improve the outcome.';
        if ($score >= 40) return 'Fair progress. Focused support and regular practice are recommended.';
        return 'More guided support and consistent practice are needed next term.';
    }

    private function fileName($studentName, $term)
    {
        $safe = preg_replace('/[^A-Za-z0-9]+/', '-', trim($studentName . '-' . $term));
        return trim($safe, '-') . '-report.pdf';
    }

    private function one($sql, array $parameters = array())
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        $row = $statement->fetch();
        return $row ?: null;
    }

    private function cachedOne($key, $sql, array $parameters = array())
    {
        if (!array_key_exists($key, $this->rowCache)) {
            $this->rowCache[$key] = $this->one($sql, $parameters);
        }
        return $this->rowCache[$key];
    }

    private function all($sql, array $parameters = array())
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll();
    }
}
