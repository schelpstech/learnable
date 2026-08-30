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
                    tr.id AS transfer_id, tr.converted_score, tr.transferred_at
             FROM cbt_attempts atp
             INNER JOIN lhpuser u ON u.uname = atp.learner_id
             LEFT JOIN cbt_score_transfers tr
               ON tr.assessment_id = atp.assessment_id
              AND tr.learner_id = atp.learner_id
              AND tr.component = ?
             WHERE atp.assessment_id = ? AND atp.published_at IS NOT NULL
               AND NOT EXISTS (
                   SELECT 1 FROM cbt_attempts newer
                   WHERE newer.assessment_id = atp.assessment_id
                     AND newer.learner_id = atp.learner_id
                     AND newer.published_at IS NOT NULL
                     AND newer.attempt_no > atp.attempt_no
               )
             ORDER BY u.fname',
            array($mapping['component'], $assessmentId)
        );
        foreach ($attempts as &$attempt) {
            $attempt['target_score'] = $this->convert(
                (float) $attempt['raw_score'], (float) $assessment['total_marks'], (float) $mapping['target_max']
            );
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
        $transferred = 0;
        $skipped = 0;
        foreach ($preview['attempts'] as $attempt) {
            if (!empty($attempt['transfer_id'])) {
                $skipped++;
                continue;
            }
            $this->transferOne($preview['assessment'], $preview['mapping'], $attempt, $actorId);
            $transferred++;
        }
        return array('transferred' => $transferred, 'skipped' => $skipped);
    }

    public function amendTransfer($transferId, $actorId, $reason)
    {
        $reason = CbtSecurity::cleanText($reason, 2000, false);
        $this->pdo->beginTransaction();
        try {
            $transfer = $this->one(
                'SELECT tr.*, atp.total_score, a.total_marks, a.term, a.class_id, a.subject_id,
                        atc.scheme_id, sc.week
                 FROM cbt_score_transfers tr
                 INNER JOIN cbt_attempts atp ON atp.id = tr.attempt_id
                 INNER JOIN cbt_assessments a ON a.id = tr.assessment_id
                 LEFT JOIN cbt_assessment_topics atc ON atc.assessment_id = a.id AND atc.is_primary = 1
                 LEFT JOIN lhpscheme sc ON sc.schmid = atc.scheme_id
                 WHERE tr.id = ? LIMIT 1 FOR UPDATE',
                array($transferId)
            );
            if (!$transfer) {
                throw new RuntimeException('Score transfer not found.');
            }
            $newScore = $this->convert((float) $transfer['total_score'], (float) $transfer['total_marks'], (float) $transfer['target_max']);
            $before = array('raw_score' => $transfer['raw_score'], 'converted_score' => $transfer['converted_score']);
            $this->writeLegacyScore($transfer, $transfer['component'], $newScore);
            $statement = $this->pdo->prepare(
                'UPDATE cbt_score_transfers
                 SET raw_score = ?, converted_score = ?, status = \'amended\', amendment_reason = ?
                 WHERE id = ?'
            );
            $statement->execute(array($transfer['total_score'], $newScore, $reason, $transferId));
            $this->cbt->audit('admin', $actorId, 'score_transfer.amended', 'score_transfer', $transferId, $before, array('raw_score' => $transfer['total_score'], 'converted_score' => $newScore), $reason);
            $this->pdo->commit();
            return $newScore;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    private function transferOne(array $assessment, array $mapping, array $attempt, $actorId)
    {
        $this->pdo->beginTransaction();
        try {
            $existing = $this->one(
                'SELECT id FROM cbt_score_transfers
                 WHERE assessment_id = ? AND learner_id = ? AND component = ? LIMIT 1 FOR UPDATE',
                array($assessment['id'], $attempt['learner_id'], $mapping['component'])
            );
            if ($existing) {
                $this->pdo->commit();
                return;
            }
            $targetScore = $this->convert(
                (float) $attempt['raw_score'], (float) $assessment['total_marks'], (float) $mapping['target_max']
            );
            $legacyData = array(
                'term' => $assessment['term'],
                'class_id' => $assessment['class_id'],
                'subject_id' => $assessment['subject_id'],
                'learner_id' => $attempt['learner_id'],
                'week' => $mapping['week'],
            );
            $targetRecordId = $this->writeLegacyScore($legacyData, $mapping['component'], $targetScore);
            $formula = sprintf('round((raw_score / %.2f) * %.2f)', (float) $assessment['total_marks'], (float) $mapping['target_max']);
            $insert = $this->pdo->prepare(
                'INSERT INTO cbt_score_transfers
                 (assessment_id, attempt_id, learner_id, component, target_max, raw_score,
                  raw_max, converted_score, conversion_formula, target_record_id,
                  status, authorized_by, transferred_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, \'transferred\', ?, NOW())'
            );
            $insert->execute(array(
                $assessment['id'], $attempt['attempt_id'], $attempt['learner_id'], $mapping['component'],
                $mapping['target_max'], $attempt['raw_score'], $assessment['total_marks'],
                $targetScore, $formula, $targetRecordId, $actorId
            ));
            $transferId = (int) $this->pdo->lastInsertId();
            $this->cbt->audit('admin', $actorId, 'score_transfer.created', 'score_transfer', $transferId, null, array(
                'learner_id' => $attempt['learner_id'],
                'component' => $mapping['component'],
                'converted_score' => $targetScore,
            ));
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    private function writeLegacyScore(array $data, $component, $score)
    {
        $guard = new ScorebookService($this->pdo);
        return $guard->locked('scores:'.$data['term'].':'.$data['subject_id'], function () use ($data,$component,$score) {
            return $this->writeLockedLegacyScore($data,$component,$score);
        });
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

    private function mapping(array $assessment)
    {
        $component = $assessment['result_treatment'];
        if (!in_array($component, array('weekly', 'ca', 'exam'), true)) {
            throw new RuntimeException('This assessment is not configured for academic score transfer.');
        }
        $config = $this->one('SELECT ca_score, exam_score FROM lhpresultconfig WHERE term = ? LIMIT 1', array($assessment['term']));
        if (!$config) {
            throw new RuntimeException('Configure result score limits for this term before transferring CBT scores.');
        }
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
            'week' => isset($assessment['week']) && $assessment['week'] !== '' ? $assessment['week'] : 'WEEK 1',
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
