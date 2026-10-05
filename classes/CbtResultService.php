<?php

class CbtResultService
{
    private $pdo;
    private $cbt;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->cbt = new CbtService($pdo);
    }

    public function previewAssessmentTransfer($assessmentId, $actorId, $isAdmin)
    {
        $assessment = $this->cbt->assessment($assessmentId);
        $this->cbt->assertAssessmentManager($assessment, $actorId, $isAdmin);
        $mapping = $this->mapping($assessment);
        $attempts = $this->all(
            'SELECT atp.id AS attempt_id, atp.learner_id, u.fname, atp.total_score AS raw_score,
                    atp.percentage, atp.published_at, atp.attempt_no,
                    tr.id AS transfer_id, tr.converted_score, tr.transferred_at,
                    tr.raw_score AS transferred_raw_score, tr.raw_max AS transferred_raw_max,
                    tr.target_max AS transferred_target_max, tr.attempt_id AS transferred_attempt_id
             FROM cbt_attempts atp
             INNER JOIN lhpuser u ON u.uname = atp.learner_id
             LEFT JOIN cbt_score_transfers tr
               ON tr.assessment_id = atp.assessment_id
              AND tr.learner_id = atp.learner_id
              AND tr.component = ?
             WHERE atp.assessment_id = ? AND atp.status = \'published\' AND atp.published_at IS NOT NULL
               AND NOT EXISTS (
                   SELECT 1 FROM cbt_attempts newer
                   WHERE newer.assessment_id = atp.assessment_id
                     AND newer.learner_id = atp.learner_id
                     AND newer.status = \'published\' AND newer.published_at IS NOT NULL
                     AND newer.attempt_no > atp.attempt_no
               )
             ORDER BY u.fname',
            array($mapping['component'], $assessmentId)
        );
        foreach ($attempts as &$attempt) {
            $attempt['target_score'] = $this->convert(
                (float) $attempt['raw_score'], (float) $assessment['total_marks'], (float) $mapping['target_max']
            );
            $attempt['needs_amendment'] = !empty($attempt['transfer_id']) && (
                (float) $attempt['converted_score'] !== (float) $attempt['target_score']
                || (float) $attempt['transferred_raw_score'] !== (float) $attempt['raw_score']
                || (float) $attempt['transferred_raw_max'] !== (float) $assessment['total_marks']
                || (float) $attempt['transferred_target_max'] !== (float) $mapping['target_max']
                || (int) $attempt['transferred_attempt_id'] !== (int) $attempt['attempt_id']);
        }
        unset($attempt);
        return array('assessment' => $assessment, 'mapping' => $mapping, 'attempts' => $attempts);
    }

    public function transferAssessment($assessmentId, $actorId, $isAdmin)
    {
        $preview = $this->previewAssessmentTransfer($assessmentId, $actorId, $isAdmin);
        if (!in_array($preview['assessment']['status'], array('approved', 'published'), true)) {
            throw new RuntimeException('Only approved assessment scores can be transferred.');
        }
        if (!$preview['attempts']) {
            throw new RuntimeException('There are no published learner scores to transfer.');
        }
        $mapping = $this->mapping($preview['assessment'], true);
        foreach ($preview['attempts'] as $attempt) {
            if ($attempt['needs_amendment']) throw new RuntimeException('A transferred score has changed. Academic administration must amend the transfer with a reason after fresh result approval.');
        }
        $transferred = 0;
        $skipped = 0;
        foreach ($preview['attempts'] as $attempt) {
            if (!empty($attempt['transfer_id'])) {
                $skipped++;
                continue;
            }
            if ($this->transferOne($preview['assessment'], $mapping, $attempt, $actorId)) $transferred++;
            else $skipped++;
        }
        return array('transferred' => $transferred, 'skipped' => $skipped);
    }

    public function amendTransfer($transferId, $actorId, $reason)
    {
        $access = new StaffAccess($this->pdo, $_SESSION);
        if (!$access->academic() || $access->username() !== $actorId) throw new RuntimeException('Academic administration access is required to amend transferred scores.');
        $reason = CbtSecurity::cleanText($reason, 2000, false);
        $transfer = $this->one('SELECT * FROM cbt_score_transfers WHERE id = ?', array($transferId));
        if (!$transfer) throw new RuntimeException('Score transfer not found.');
        $paper = $this->cbt->assessment($transfer['assessment_id']);
        return (new ScorebookService($this->pdo))->locked('scores:'.$paper['term'].':'.$paper['subject_id'], function () use ($transferId, $transfer, $actorId, $reason) {
            $this->pdo->beginTransaction();
            try {
                $paper = $this->one('SELECT * FROM cbt_assessments WHERE id = ? FOR UPDATE', array($transfer['assessment_id']));
                if ($paper['status'] !== 'published' || !$paper['approved_at']) throw new RuntimeException('Approve and republish the corrected results before amending a transfer.');
                $mapping = $this->mapping($paper, true);
                $row = $this->one('SELECT * FROM cbt_score_transfers WHERE id = ? FOR UPDATE', array($transferId));
                if ($mapping['component'] !== $row['component']) throw new RuntimeException('The score component does not match the recorded transfer.');
                $attempt = $this->one("SELECT * FROM cbt_attempts WHERE assessment_id = ? AND learner_id = ? AND status = 'published' AND published_at IS NOT NULL ORDER BY attempt_no DESC LIMIT 1 FOR UPDATE", array($paper['id'], $row['learner_id']));
                if (!$attempt) throw new RuntimeException('There is no published corrected result for this learner.');
                $score = $this->convert($attempt['total_score'], $paper['total_marks'], $mapping['target_max']);
                $data = array_merge($paper, array('learner_id' => $row['learner_id'], 'week' => $mapping['week']));
                $this->assertLegacyUnchanged($data, $row);
                $this->pdo->prepare("UPDATE cbt_score_transfers SET attempt_id = ?, target_max = ?, raw_score = ?, raw_max = ?, converted_score = ?, status = 'amended', amendment_reason = ?, authorized_by = ?, transferred_at = NOW() WHERE id = ?")
                    ->execute(array($attempt['id'], $mapping['target_max'], $attempt['total_score'], $paper['total_marks'], $score, $reason, $actorId, $transferId));
                $this->cbt->audit('admin', $actorId, 'score_transfer.amended', 'score_transfer', $transferId, $row, array('raw_score' => $attempt['total_score'], 'converted_score' => $score), $reason);
                // Keep the legacy write last: deployed score tables may be MyISAM.
                $this->writeLockedLegacyScore($data, $row['component'], $score);
                $this->pdo->commit();
                return $score;
            } catch (Throwable $exception) {
                if ($this->pdo->inTransaction()) $this->pdo->rollBack();
                throw $exception;
            }
        });
    }

    private function transferOne(array $assessment, array $mapping, array $attempt, $actorId)
    {
        return (new ScorebookService($this->pdo))->locked('scores:'.$assessment['term'].':'.$assessment['subject_id'], function () use ($assessment, $mapping, $attempt, $actorId) {
            $this->pdo->beginTransaction();
            try {
                $paper = $this->one('SELECT * FROM cbt_assessments WHERE id = ? FOR UPDATE', array($assessment['id']));
                if (!in_array($paper['status'], array('approved', 'published'), true) || !$paper['approved_at']) throw new RuntimeException('Approve corrected results before transferring scores.');
                $mapping = $this->mapping($paper, true);
                $current = $this->one("SELECT * FROM cbt_attempts WHERE assessment_id = ? AND learner_id = ? AND status = 'published' AND published_at IS NOT NULL ORDER BY attempt_no DESC LIMIT 1 FOR UPDATE", array($paper['id'], $attempt['learner_id']));
                if (!$current) throw new RuntimeException('The learner result is no longer published. Refresh the transfer preview.');
                $score = $this->convert($current['total_score'], $paper['total_marks'], $mapping['target_max']);
                $existing = $this->one('SELECT * FROM cbt_score_transfers WHERE assessment_id = ? AND learner_id = ? AND component = ? FOR UPDATE', array($paper['id'], $current['learner_id'], $mapping['component']));
                if ($existing) {
                    if ((float) $existing['converted_score'] !== (float) $score || (float) $existing['raw_score'] !== (float) $current['total_score'] || (int) $existing['attempt_id'] !== (int) $current['id']) throw new RuntimeException('Academic administration must amend this previously transferred score with a reason.');
                    $this->pdo->commit();
                    return false;
                }
                $data = array_merge($paper, array('learner_id' => $current['learner_id'], 'week' => $mapping['week']));
                $record = $this->legacyRecord($data, $mapping['component']);
                $formula = sprintf('round((raw_score / %.2f) * %.2f)', $paper['total_marks'], $mapping['target_max']);
                $this->pdo->prepare("INSERT INTO cbt_score_transfers (assessment_id, attempt_id, learner_id, component, target_max, raw_score, raw_max, converted_score, conversion_formula, target_record_id, status, authorized_by, transferred_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'transferred', ?, NOW())")
                    ->execute(array($paper['id'], $current['id'], $current['learner_id'], $mapping['component'], $mapping['target_max'], $current['total_score'], $paper['total_marks'], $score, $formula, $record ? $record['id'] : null, $actorId));
                $transferId = (int) $this->pdo->lastInsertId();
                $this->cbt->audit('admin', $actorId, 'score_transfer.created', 'score_transfer', $transferId, null, array('learner_id' => $current['learner_id'], 'component' => $mapping['component'], 'converted_score' => $score));
                $recordId = $this->writeLockedLegacyScore($data, $mapping['component'], $score);
                $this->pdo->prepare('UPDATE cbt_score_transfers SET target_record_id = ? WHERE id = ?')->execute(array($recordId, $transferId));
                $this->pdo->commit();
                return true;
            } catch (Throwable $exception) {
                if ($this->pdo->inTransaction()) $this->pdo->rollBack();
                throw $exception;
            }
        });
    }

    private function legacyRecord(array $data, $component)
    {
        $weekly = $component === 'weekly';
        $params = array($data['term'], $data['class_id'], $data['subject_id'], $data['learner_id']);
        if ($weekly) $params[] = $data['week'];
        $rows = $this->all('SELECT * FROM ' . ($weekly ? 'lhpweekrecord' : 'lhpresultrecord') . ' WHERE term = ? AND classid = ? AND subjid = ? AND lid = ?' . ($weekly ? ' AND week = ?' : '') . ' FOR UPDATE', $params);
        if (count($rows) > 1) throw new RuntimeException('Duplicate school score records must be reconciled before transferring.');
        return $rows ? $rows[0] : null;
    }

    private function assertLegacyUnchanged(array $data, array $transfer)
    {
        $record = $this->legacyRecord($data, $transfer['component']);
        $column = $transfer['component'] === 'exam' ? 'examscore' : 'score';
        if (!$record || (int) $record['id'] !== (int) $transfer['target_record_id'] || (float) $record[$column] !== (float) $transfer['converted_score']) {
            throw new RuntimeException('The school score has changed since this transfer. Reconcile it in the scorebook before amending.');
        }
    }

    private function writeLockedLegacyScore(array $data, $component, $score)
    {
        $score = (int) round($score);
        if ($component === 'weekly') {
            $existing = $this->one(
                'SELECT id FROM lhpweekrecord
                 WHERE term = ? AND week = ? AND classid = ? AND subjid = ? AND lid = ?
                 ORDER BY id LIMIT 1 FOR UPDATE',
                array($data['term'], $data['week'], $data['class_id'], $data['subject_id'], $data['learner_id'])
            );
            if ($existing) {
                $this->pdo->prepare('UPDATE lhpweekrecord SET score = ? WHERE id = ?')->execute(array($score, $existing['id']));
                return (int) $existing['id'];
            }
            $statement = $this->pdo->prepare(
                'INSERT INTO lhpweekrecord (term, week, classid, subjid, lid, score) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $statement->execute(array($data['term'], $data['week'], $data['class_id'], $data['subject_id'], $data['learner_id'], $score));
            return (int) $this->pdo->lastInsertId();
        }

        $existing = $this->one(
            'SELECT id, score, examscore FROM lhpresultrecord
             WHERE term = ? AND classid = ? AND subjid = ? AND lid = ?
             ORDER BY id LIMIT 1 FOR UPDATE',
            array($data['term'], $data['class_id'], $data['subject_id'], $data['learner_id'])
        );
        if (!$existing) {
            $insert = $this->pdo->prepare(
                'INSERT INTO lhpresultrecord (term, classid, subjid, lid, score, examscore, totalscore)
                 VALUES (?, ?, ?, ?, 0, 0, 0)'
            );
            $insert->execute(array($data['term'], $data['class_id'], $data['subject_id'], $data['learner_id']));
            $existing = array('id' => (int) $this->pdo->lastInsertId(), 'score' => 0, 'examscore' => 0);
        }
        $ca = $component === 'ca' ? $score : (int) $existing['score'];
        $exam = $component === 'exam' ? $score : (int) $existing['examscore'];
        $statement = $this->pdo->prepare(
            'UPDATE lhpresultrecord SET score = ?, examscore = ?, totalscore = ? WHERE id = ?'
        );
        $statement->execute(array($ca, $exam, $ca + $exam, $existing['id']));
        return (int) $existing['id'];
    }

    private function mapping(array $assessment, $forWrite = false)
    {
        $component = $assessment['result_treatment'];
        if (!in_array($component, array('weekly', 'ca', 'exam'), true)) {
            throw new RuntimeException('This assessment is not configured for academic score transfer.');
        }
        $config = $this->one('SELECT * FROM lhpresultconfig WHERE term = ? LIMIT 1', array($assessment['term']));
        if (!$config) {
            throw new RuntimeException('Configure result score limits for this term before transferring CBT scores.');
        }
        if ($forWrite && (int) $config[$component === 'weekly' ? 'midterm' : 'status'] === 1) throw new RuntimeException('School results are locked. Unlock the relevant result period before transferring or amending CBT scores.');
        if ($forWrite && $assessment['term'] !== $this->cbt->activeContext()['term']) throw new RuntimeException('Only active-term scores may be transferred.');
        if ($component === 'weekly') {
            $targetMax = 10;
        } elseif ($component === 'ca') {
            $targetMax = (int) $config['ca_score'];
        } else {
            $targetMax = (int) $config['exam_score'];
        }
        return array(
            'component' => $component,
            'target_max' => $targetMax,
            'week' => !empty($assessment['week']) ? $assessment['week'] : ($this->one('SELECT sc.week FROM cbt_assessment_topics t INNER JOIN lhpscheme sc ON sc.schmid = t.scheme_id WHERE t.assessment_id = ? AND t.is_primary = 1', array($assessment['id']))['week'] ?? 'WEEK 1'),
            'formula' => sprintf('Raw score ÷ %.2f × %d, rounded to the nearest whole mark', (float) $assessment['total_marks'], $targetMax),
        );
    }

    private function convert($rawScore, $rawMax, $targetMax)
    {
        if ($rawMax <= 0) {
            throw new RuntimeException('The assessment total marks must be greater than zero.');
        }
        return min($targetMax, max(0, round(($rawScore / $rawMax) * $targetMax)));
    }

    private function all($sql, array $params = array())
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function one($sql, array $params = array())
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
