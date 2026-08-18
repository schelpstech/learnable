<?php

class CbtNotificationService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function queueDuePortalReminders($minutesBeforeOpen, $minutesBeforeClose)
    {
        $minutesBeforeOpen = max(5, min(1440, (int) $minutesBeforeOpen));
        $minutesBeforeClose = max(5, min(1440, (int) $minutesBeforeClose));
        $assessments = $this->all(
            'SELECT a.* FROM cbt_assessments a
             WHERE a.status IN (\'scheduled\', \'active\', \'approved\', \'published\')
               AND ((a.start_at > NOW() AND a.start_at <= DATE_ADD(NOW(), INTERVAL ? MINUTE))
                 OR (a.close_at > NOW() AND a.close_at <= DATE_ADD(NOW(), INTERVAL ? MINUTE)))',
            array($minutesBeforeOpen, $minutesBeforeClose)
        );
        $queued = 0;
        foreach ($assessments as $assessment) {
            $openReminder = strtotime($assessment['start_at']) > time()
                && strtotime($assessment['start_at']) <= time() + ($minutesBeforeOpen * 60);
            $eventType = $openReminder ? 'opening_reminder' : 'closing_reminder';
            $learners = $this->all(
                'SELECT DISTINCT u.uname
                 FROM lhpuser u
                 WHERE u.status = 1 AND EXISTS (
                     SELECT 1 FROM cbt_assessment_assignments aa
                     WHERE aa.assessment_id = ? AND aa.status = \'eligible\'
                       AND ((aa.assignment_type = \'class\' AND aa.class_id = u.classid)
                         OR (aa.assignment_type = \'student\' AND aa.learner_id = u.uname))
                 )',
                array($assessment['id'])
            );
            $insert = $this->pdo->prepare(
                'INSERT IGNORE INTO cbt_notification_targets
                 (assessment_id, learner_id, event_type, channel, status, scheduled_at, sent_at)
                 VALUES (?, ?, ?, \'portal\', \'sent\', NOW(), NOW())'
            );
            foreach ($learners as $learner) {
                $insert->execute(array($assessment['id'], $learner['uname'], $eventType));
                $queued += $insert->rowCount();
            }
        }
        return $queued;
    }

    private function all($sql, array $params)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
