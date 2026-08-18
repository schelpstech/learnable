<?php

class CbtAttemptService
{
    private $pdo;
    private $cbt;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->cbt = new CbtService($pdo);
    }

    public function startAttempt($assessmentId, $learnerId, $fingerprint)
    {
        $assessmentId = CbtSecurity::positiveInt($assessmentId, 'Assessment', 1, PHP_INT_MAX);
        $learner = $this->one('SELECT uname, fname, classid, status FROM lhpuser WHERE uname = ? LIMIT 1', array($learnerId));
        if (!$learner || (int) $learner['status'] !== 1) {
            throw new RuntimeException('Your learner account is not active.');
        }

        $this->pdo->beginTransaction();
        try {
            $assessment = $this->lockedAssessment($assessmentId);
            $this->assertEligible($assessmentId, $learnerId, (int) $learner['classid']);
            $this->assertAttemptWindow($assessment);

            $active = $this->one(
                'SELECT * FROM cbt_attempts
                 WHERE assessment_id = ? AND learner_id = ? AND status = \'in_progress\'
                 ORDER BY attempt_no DESC LIMIT 1 FOR UPDATE',
                array($assessmentId, $learnerId)
            );
            $plainToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $plainToken);

            if ($active) {
                if (new DateTimeImmutable($active['expires_at']) <= new DateTimeImmutable('now')) {
                    $this->finalizeLocked($active, true);
                } else {
                    $statement = $this->pdo->prepare(
                        'UPDATE cbt_attempts SET token_hash = ?, last_seen_at = NOW(),
                         client_fingerprint_hash = COALESCE(client_fingerprint_hash, ?),
                         ip_hash = COALESCE(ip_hash, ?) WHERE id = ?'
                    );
                    $statement->execute(array(
                        $tokenHash, CbtSecurity::fingerprintHash($fingerprint),
                        CbtSecurity::requestIpHash(), $active['id']
                    ));
                    $this->cbt->audit('learner', $learnerId, 'attempt.resumed', 'attempt', $active['id'], null, null);
                    $this->pdo->commit();
                    $this->setAttemptCookie((int) $active['id'], $plainToken, $active['expires_at']);
                    return array('attempt_id' => (int) $active['id'], 'token' => $plainToken, 'resumed' => true);
                }
            }

            $attemptsUsed = (int) $this->scalar(
                'SELECT COUNT(*) FROM cbt_attempts
                 WHERE assessment_id = ? AND learner_id = ? AND status <> \'cancelled\'',
                array($assessmentId, $learnerId)
            );
            if ($attemptsUsed >= (int) $assessment['max_attempts']) {
                throw new RuntimeException('You have used all permitted attempts for this assessment.');
            }

            $extraTime = (int) $this->scalar(
                'SELECT COALESCE(MAX(extra_time_minutes), 0)
                 FROM cbt_assessment_assignments
                 WHERE assessment_id = ? AND status = \'eligible\'
                   AND ((assignment_type = \'student\' AND learner_id = ?)
                     OR (assignment_type = \'class\' AND class_id = ?))',
                array($assessmentId, $learnerId, (int) $learner['classid'])
            );
            $start = new DateTimeImmutable('now');
            $expires = $start->modify('+' . ((int) $assessment['duration_minutes'] + $extraTime) . ' minutes');
            if (!(int) $assessment['late_submission']) {
                $close = new DateTimeImmutable($assessment['close_at']);
                if ($expires > $close) {
                    $expires = $close;
                }
            }

            $insert = $this->pdo->prepare(
                'INSERT INTO cbt_attempts
                 (assessment_id, learner_id, attempt_no, status, token_hash, started_at, expires_at,
                  last_seen_at, extra_time_minutes, client_fingerprint_hash, ip_hash)
                 VALUES (?, ?, ?, \'in_progress\', ?, ?, ?, ?, ?, ?, ?)'
            );
            $insert->execute(array(
                $assessmentId, $learnerId, $attemptsUsed + 1, $tokenHash,
                $start->format('Y-m-d H:i:s'), $expires->format('Y-m-d H:i:s'),
                $start->format('Y-m-d H:i:s'), $extraTime,
                CbtSecurity::fingerprintHash($fingerprint), CbtSecurity::requestIpHash()
            ));
            $attemptId = (int) $this->pdo->lastInsertId();
            $this->snapshotQuestions($attemptId, $assessment);
            $this->cbt->audit('learner', $learnerId, 'attempt.started', 'attempt', $attemptId, null, array('assessment_id' => $assessmentId));
            $this->pdo->commit();

            $this->setAttemptCookie($attemptId, $plainToken, $expires->format('Y-m-d H:i:s'));
            return array('attempt_id' => $attemptId, 'token' => $plainToken, 'resumed' => false);
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function authenticateAttemptCookie()
    {
        $cookie = isset($_COOKIE['learnable_cbt_attempt']) ? (string) $_COOKIE['learnable_cbt_attempt'] : '';
        if (!preg_match('/^(\d+)\.([a-f0-9]{64})$/', $cookie, $match)) {
            throw new RuntimeException('Your secure examination session is unavailable. Start the assessment again from your portal.');
        }
        return $this->authenticate((int) $match[1], $match[2]);
    }

    public function authenticate($attemptId, $plainToken)
    {
        $attempt = $this->one(
            'SELECT atp.*, a.title, a.term, a.class_id, a.subject_id, a.teacher_id,
                    a.instructions, a.duration_minutes, a.navigation_mode, a.allow_backtrack,
                    a.fullscreen_mode, a.monitor_tab_switch, a.restrict_clipboard, a.auto_submit,
                    a.allow_review, a.show_correct_answers, a.show_score, a.close_at,
                    a.total_marks, a.pass_mark, a.status AS assessment_status,
                    s.sbjname, c.classname, u.fname, u.picture
             FROM cbt_attempts atp
             INNER JOIN cbt_assessments a ON a.id = atp.assessment_id
             INNER JOIN lhpsubject s ON s.sbjid = a.subject_id
             INNER JOIN lhpclass c ON c.classid = a.class_id
             INNER JOIN lhpuser u ON u.uname = atp.learner_id
             WHERE atp.id = ? LIMIT 1',
            array($attemptId)
        );
        if (!$attempt || !is_string($plainToken) || !hash_equals($attempt['token_hash'], hash('sha256', $plainToken))) {
            throw new RuntimeException('The secure examination session could not be verified.');
        }
        return $attempt;
    }

    public function examState($attemptId, $plainToken)
    {
        $attempt = $this->authenticate($attemptId, $plainToken);
        if ($attempt['status'] === 'in_progress' && new DateTimeImmutable($attempt['expires_at']) <= new DateTimeImmutable('now')) {
            $this->submitAttempt($attemptId, $plainToken, true);
            $attempt = $this->authenticate($attemptId, $plainToken);
        }
        $questions = $this->all(
            'SELECT aq.id, aq.question_type, aq.prompt_snapshot, aq.media_type, aq.media_url,
                    aq.options_snapshot, aq.marks_available, aq.display_order,
                    ans.answer_json, ans.is_flagged, ans.saved_at, ans.save_version
             FROM cbt_attempt_questions aq
             LEFT JOIN cbt_attempt_answers ans
               ON ans.attempt_question_id = aq.id AND ans.attempt_id = aq.attempt_id
             WHERE aq.attempt_id = ? ORDER BY aq.display_order',
            array($attemptId)
        );
        foreach ($questions as &$question) {
            $question['options'] = $this->decode($question['options_snapshot'], array());
            $question['answer'] = $this->decode($question['answer_json'], null);
            unset($question['options_snapshot'], $question['answer_json']);
        }
        unset($question);
        return array('attempt' => $attempt, 'questions' => $questions, 'server_time' => date(DATE_ATOM));
    }

    public function saveAnswer($attemptId, $plainToken, $attemptQuestionId, $answer, $flagged, $saveVersion)
    {
        $attemptId = CbtSecurity::positiveInt($attemptId, 'Attempt', 1, PHP_INT_MAX);
        $attemptQuestionId = CbtSecurity::positiveInt($attemptQuestionId, 'Question', 1, PHP_INT_MAX);
        $encoded = json_encode($answer, JSON_UNESCAPED_UNICODE);
        if ($encoded === false || strlen($encoded) > 50000) {
            throw new InvalidArgumentException('The answer could not be saved because it is too large.');
        }

        $this->pdo->beginTransaction();
        try {
            $attempt = $this->lockedAttempt($attemptId, $plainToken);
            if ($attempt['status'] !== 'in_progress') {
                throw new RuntimeException('This attempt has already been submitted.');
            }
            if (new DateTimeImmutable($attempt['expires_at']) <= new DateTimeImmutable('now')) {
                $receipt = $this->finalizeLocked($attempt, true);
                $this->pdo->commit();
                return array('submitted' => true, 'receipt' => $receipt);
            }
            $belongs = $this->scalar(
                'SELECT 1 FROM cbt_attempt_questions WHERE id = ? AND attempt_id = ? LIMIT 1',
                array($attemptQuestionId, $attemptId)
            );
            if (!$belongs) {
                throw new RuntimeException('That question is not part of this attempt.');
            }
            $saveVersion = max(1, (int) $saveVersion);
            $statement = $this->pdo->prepare(
                'INSERT INTO cbt_attempt_answers
                 (attempt_id, attempt_question_id, answer_json, is_flagged, save_version, saved_at)
                 VALUES (?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE
                    answer_json = IF(VALUES(save_version) >= save_version, VALUES(answer_json), answer_json),
                    is_flagged = IF(VALUES(save_version) >= save_version, VALUES(is_flagged), is_flagged),
                    saved_at = IF(VALUES(save_version) >= save_version, NOW(), saved_at),
                    save_version = GREATEST(save_version, VALUES(save_version))'
            );
            $statement->execute(array($attemptId, $attemptQuestionId, $encoded, $flagged ? 1 : 0, $saveVersion));
            $this->pdo->prepare('UPDATE cbt_attempts SET last_seen_at = NOW() WHERE id = ?')->execute(array($attemptId));
            $this->pdo->commit();
            return array('submitted' => false, 'saved_at' => date(DATE_ATOM), 'save_version' => $saveVersion);
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function submitAttempt($attemptId, $plainToken, $automatic)
    {
        $this->pdo->beginTransaction();
        try {
            $attempt = $this->lockedAttempt($attemptId, $plainToken);
            $receipt = $this->finalizeLocked($attempt, $automatic);
            $this->pdo->commit();
            return $receipt;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function recordIntegrityEvent($attemptId, $plainToken, $eventType, array $details)
    {
        $attempt = $this->authenticate($attemptId, $plainToken);
        $allowed = array('tab_hidden', 'tab_visible', 'offline', 'online', 'clipboard', 'fullscreen_exit', 'conflicting_session', 'client_error');
        if (!in_array($eventType, $allowed, true)) {
            throw new InvalidArgumentException('Unknown activity event.');
        }
        $severity = in_array($eventType, array('conflicting_session', 'clipboard'), true) ? 'warning' : 'info';
        $statement = $this->pdo->prepare(
            'INSERT INTO cbt_integrity_events
             (attempt_id, event_type, severity, details_json, client_time)
             VALUES (?, ?, ?, ?, ?)'
        );
        $clientTime = isset($details['client_time']) && is_string($details['client_time'])
            ? date('Y-m-d H:i:s', strtotime($details['client_time'])) : null;
        unset($details['client_time']);
        $statement->execute(array(
            $attempt['id'], $eventType, $severity,
            json_encode($details, JSON_UNESCAPED_UNICODE), $clientTime
        ));
    }

    public function attemptsForAssessment($assessmentId, $actorId, $isAdmin)
    {
        $assessment = $this->cbt->assessment($assessmentId);
        $this->cbt->assertAssessmentManager($assessment, $actorId, $isAdmin);
        return $this->all(
            'SELECT atp.*, u.fname, u.picture,
                    (SELECT COUNT(*) FROM cbt_attempt_answers aa
                     INNER JOIN cbt_attempt_questions aq ON aq.id = aa.attempt_question_id
                     WHERE aa.attempt_id = atp.id AND aq.question_type = \'essay\' AND aa.manual_marks IS NULL) AS awaiting_manual,
                    (SELECT COUNT(*) FROM cbt_integrity_events ie WHERE ie.attempt_id = atp.id AND ie.severity = \'warning\') AS warning_count
             FROM cbt_attempts atp
             INNER JOIN lhpuser u ON u.uname = atp.learner_id
             WHERE atp.assessment_id = ? ORDER BY u.fname, atp.attempt_no',
            array($assessmentId)
        );
    }

    public function scriptForMarking($attemptId, $actorId, $isAdmin)
    {
        $attempt = $this->one(
            'SELECT atp.*, a.title, a.teacher_id, a.total_marks, a.status AS assessment_status,
                    s.sbjname, c.classname, u.fname, u.picture
             FROM cbt_attempts atp
             INNER JOIN cbt_assessments a ON a.id = atp.assessment_id
             INNER JOIN lhpsubject s ON s.sbjid = a.subject_id
             INNER JOIN lhpclass c ON c.classid = a.class_id
             INNER JOIN lhpuser u ON u.uname = atp.learner_id
             WHERE atp.id = ? LIMIT 1',
            array($attemptId)
        );
        if (!$attempt || (!$isAdmin && $attempt['teacher_id'] !== $actorId)) {
            throw new RuntimeException('You do not have permission to view this script.');
        }
        $questions = $this->all(
            'SELECT aq.*, ans.id AS answer_id, ans.answer_json, ans.auto_marks, ans.manual_marks,
                    ans.final_marks, ans.marker_comment, ans.marked_by, ans.marked_at
             FROM cbt_attempt_questions aq
             LEFT JOIN cbt_attempt_answers ans ON ans.attempt_question_id = aq.id
             WHERE aq.attempt_id = ? ORDER BY aq.display_order',
            array($attemptId)
        );
        foreach ($questions as &$question) {
            $question['options'] = $this->decode($question['options_snapshot'], array());
            $question['answer'] = $this->decode($question['answer_json'], null);
            $question['correct_answer'] = $this->decode($question['correct_answer_snapshot'], null);
        }
        unset($question);
        $events = $this->all('SELECT * FROM cbt_marking_events WHERE attempt_id = ? ORDER BY created_at DESC', array($attemptId));
        return array('attempt' => $attempt, 'questions' => $questions, 'events' => $events);
    }

    public function markAnswer($answerId, $marks, $comment, $reason, $actorId, $isAdmin)
    {
        $answer = $this->one(
            'SELECT ans.*, aq.marks_available, aq.question_type, atp.assessment_id,
                    atp.status AS attempt_status, a.teacher_id
             FROM cbt_attempt_answers ans
             INNER JOIN cbt_attempt_questions aq ON aq.id = ans.attempt_question_id
             INNER JOIN cbt_attempts atp ON atp.id = ans.attempt_id
             INNER JOIN cbt_assessments a ON a.id = atp.assessment_id
             WHERE ans.id = ? LIMIT 1',
            array($answerId)
        );
        if (!$answer || (!$isAdmin && $answer['teacher_id'] !== $actorId)) {
            throw new RuntimeException('You do not have permission to mark this answer.');
        }
        if ($answer['attempt_status'] === 'in_progress') {
            throw new RuntimeException('A script cannot be marked before submission.');
        }
        $marks = CbtSecurity::decimal($marks, 'Awarded mark', 0, (float) $answer['marks_available']);
        $comment = CbtSecurity::cleanText($comment, 5000, true);
        $reason = CbtSecurity::cleanText($reason, 5000, true);
        $original = (float) $answer['final_marks'];
        if ($answer['manual_marks'] !== null && abs($original - $marks) > 0.001 && $reason === '') {
            throw new InvalidArgumentException('Give a reason for changing a previously awarded mark.');
        }
        if (CbtService::isObjectiveType($answer['question_type']) && abs($original - $marks) > 0.001 && $reason === '') {
            throw new InvalidArgumentException('Give a reason for overriding an automatically marked answer.');
        }

        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare(
                'UPDATE cbt_attempt_answers
                 SET manual_marks = ?, final_marks = ?, marker_comment = ?, marked_by = ?, marked_at = NOW()
                 WHERE id = ?'
            );
            $statement->execute(array($marks, $marks, $comment, $actorId, $answerId));
            $event = $this->pdo->prepare(
                'INSERT INTO cbt_marking_events
                 (attempt_id, attempt_answer_id, event_type, original_marks, revised_marks, reason, actor_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $event->execute(array(
                $answer['attempt_id'], $answerId,
                $answer['manual_marks'] === null ? 'manual_mark' : 'mark_override',
                $original, $marks, $reason, $actorId
            ));
            $this->recalculateAttempt((int) $answer['attempt_id']);
            $this->cbt->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'answer.marked', 'attempt', $answer['attempt_id'], array('marks' => $original), array('marks' => $marks), $reason);
            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function publishResults($assessmentId, $actorId, $isAdmin)
    {
        $assessment = $this->cbt->assessment($assessmentId);
        $this->cbt->assertAssessmentManager($assessment, $actorId, $isAdmin);
        if (!in_array($assessment['status'], array('approved', 'published'), true)) {
            throw new RuntimeException('The assessment must be approved before results are published.');
        }
        $pending = (int) $this->scalar(
            'SELECT COUNT(*) FROM cbt_attempts WHERE assessment_id = ? AND status = \'marking\'',
            array($assessmentId)
        );
        if ($pending > 0) {
            throw new RuntimeException('Complete manual marking before publishing results.');
        }
        $statement = $this->pdo->prepare(
            'UPDATE cbt_attempts SET status = \'published\', published_at = COALESCE(published_at, NOW())
             WHERE assessment_id = ? AND submitted_at IS NOT NULL AND status IN (\'marked\', \'submitted\', \'auto_submitted\')'
        );
        $statement->execute(array($assessmentId));
        $this->cbt->setAssessmentStatus($assessmentId, 'published', $actorId, $isAdmin, 'Assessment results published.');
        $this->cbt->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'results.published', 'assessment', $assessmentId, null, array('scripts' => $statement->rowCount()));
        return $statement->rowCount();
    }

    public function learnerReview($attemptId, $learnerId)
    {
        $attempt = $this->one(
            'SELECT atp.*, a.title, a.allow_review, a.show_correct_answers, a.show_score,
                    a.close_at, a.total_marks, s.sbjname, c.classname, st.staffname
             FROM cbt_attempts atp
             INNER JOIN cbt_assessments a ON a.id = atp.assessment_id
             INNER JOIN lhpsubject s ON s.sbjid = a.subject_id
             INNER JOIN lhpclass c ON c.classid = a.class_id
             LEFT JOIN lhpstaff st ON st.sname = a.teacher_id
             WHERE atp.id = ? AND atp.learner_id = ? LIMIT 1',
            array($attemptId, $learnerId)
        );
        if (!$attempt || empty($attempt['published_at'])) {
            throw new RuntimeException('This result has not been published.');
        }
        $questions = array();
        if ((int) $attempt['allow_review'] === 1) {
            $showCorrect = (int) $attempt['show_correct_answers'] === 1
                && new DateTimeImmutable('now') > new DateTimeImmutable($attempt['close_at']);
            $questions = $this->all(
                'SELECT aq.display_order, aq.question_type, aq.prompt_snapshot, aq.options_snapshot,
                        aq.marks_available, aq.explanation_snapshot, aq.correct_answer_snapshot,
                        ans.answer_json, ans.final_marks, ans.marker_comment
                 FROM cbt_attempt_questions aq
                 LEFT JOIN cbt_attempt_answers ans ON ans.attempt_question_id = aq.id
                 WHERE aq.attempt_id = ? ORDER BY aq.display_order',
                array($attemptId)
            );
            foreach ($questions as &$question) {
                $question['answer'] = $this->decode($question['answer_json'], null);
                $question['options'] = $this->decode($question['options_snapshot'], array());
                $question['correct_answer'] = $showCorrect ? $this->decode($question['correct_answer_snapshot'], null) : null;
                if (!$showCorrect) {
                    $question['explanation_snapshot'] = null;
                }
            }
            unset($question);
        }
        return array('attempt' => $attempt, 'questions' => $questions);
    }

    public function addExtraTime($attemptId, $minutes, $actorId, $isAdmin, $reason)
    {
        $minutes = CbtSecurity::positiveInt($minutes, 'Extra time', 1, 240);
        $reason = CbtSecurity::cleanText($reason, 1000, false);
        $attempt = $this->one(
            'SELECT atp.*, a.teacher_id FROM cbt_attempts atp
             INNER JOIN cbt_assessments a ON a.id = atp.assessment_id WHERE atp.id = ? LIMIT 1',
            array($attemptId)
        );
        if (!$attempt || (!$isAdmin && $attempt['teacher_id'] !== $actorId)) {
            throw new RuntimeException('You do not have permission to extend this attempt.');
        }
        if ($attempt['status'] !== 'in_progress') {
            throw new RuntimeException('Extra time can be added only to an active attempt.');
        }
        $statement = $this->pdo->prepare(
            'UPDATE cbt_attempts SET expires_at = DATE_ADD(expires_at, INTERVAL ? MINUTE),
             extra_time_minutes = extra_time_minutes + ? WHERE id = ?'
        );
        $statement->execute(array($minutes, $minutes, $attemptId));
        $this->cbt->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'attempt.extra_time', 'attempt', $attemptId, null, array('minutes' => $minutes), $reason);
    }

    public function reopenAttempt($attemptId, $minutes, $actorId, $isAdmin, $reason)
    {
        if (!$isAdmin) {
            throw new RuntimeException('Only an administrator may reopen a submitted attempt.');
        }
        $minutes = CbtSecurity::positiveInt($minutes, 'Reopened time', 1, 240);
        $reason = CbtSecurity::cleanText($reason, 1000, false);
        $before = $this->one(
            'SELECT id, status, submission_ref, submitted_at, expires_at
             FROM cbt_attempts WHERE id = ? LIMIT 1',
            array($attemptId)
        );
        if (!$before || $before['status'] === 'in_progress') {
            throw new RuntimeException('This attempt could not be reopened.');
        }
        $token = bin2hex(random_bytes(32));
        $statement = $this->pdo->prepare(
            'UPDATE cbt_attempts
             SET status = \'in_progress\', token_hash = ?, expires_at = DATE_ADD(NOW(), INTERVAL ? MINUTE),
                 submitted_at = NULL, submission_ref = NULL, reopened_at = NOW(), reopened_by = ?,
                 extra_time_minutes = extra_time_minutes + ?, published_at = NULL
             WHERE id = ? AND status <> \'in_progress\''
        );
        $statement->execute(array(hash('sha256', $token), $minutes, $actorId, $minutes, $attemptId));
        if ($statement->rowCount() < 1) {
            throw new RuntimeException('This attempt could not be reopened.');
        }
        $this->cbt->audit('admin', $actorId, 'attempt.reopened', 'attempt', $attemptId, $before, array('status' => 'in_progress', 'minutes' => $minutes), $reason);
        return $token;
    }

    private function snapshotQuestions($attemptId, array $assessment)
    {
        $questions = $this->all(
            'SELECT q.*, aq.marks_override, aq.negative_marks_override, aq.sort_order
             FROM cbt_assessment_questions aq
             INNER JOIN cbt_questions q ON q.id = aq.question_id
             WHERE aq.assessment_id = ? ORDER BY aq.sort_order, aq.id',
            array($assessment['id'])
        );
        if (!$questions) {
            throw new RuntimeException('This assessment has no questions.');
        }
        if ((int) $assessment['randomize_questions'] === 1) {
            shuffle($questions);
        }
        $insert = $this->pdo->prepare(
            'INSERT INTO cbt_attempt_questions
             (attempt_id, source_question_id, question_version, question_type, scheme_id,
              learning_objective, difficulty, prompt_snapshot, media_type, media_url,
              options_snapshot, correct_answer_snapshot, model_answer_snapshot,
              marking_guide_snapshot, explanation_snapshot, marks_available, negative_marks,
              allow_partial, display_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        foreach ($questions as $index => $question) {
            $options = $this->all(
                'SELECT option_key, option_text, is_correct, match_key, sort_order
                 FROM cbt_question_options WHERE question_id = ? ORDER BY sort_order, id',
                array($question['id'])
            );
            if ((int) $assessment['shuffle_options'] === 1
                && in_array($question['question_type'], array('single_choice', 'multiple_choice', 'matching'), true)) {
                shuffle($options);
            }
            $correct = $this->decode($question['accepted_answers'], null);
            $publicOptions = array_map(function ($option) {
                return array(
                    'option_key' => $option['option_key'],
                    'option_text' => $option['option_text'],
                    'sort_order' => $option['sort_order'],
                );
            }, $options);
            if ($question['question_type'] === 'matching') {
                $targets = array_values(array_unique(array_filter(array_map(function ($option) {
                    return $option['match_key'];
                }, $options), function ($target) { return $target !== null && $target !== ''; })));
                shuffle($targets);
                $publicOptions = array('items' => $publicOptions, 'targets' => $targets);
            }
            $insert->execute(array(
                $attemptId, $question['id'], $question['version_no'], $question['question_type'],
                $question['scheme_id'], $question['learning_objective'], $question['difficulty'],
                $question['prompt_html'], $question['media_type'], $question['media_url'],
                $publicOptions ? json_encode($publicOptions, JSON_UNESCAPED_UNICODE) : null,
                $correct === null ? null : json_encode($correct, JSON_UNESCAPED_UNICODE),
                $question['model_answer'], $question['marking_guide'], $question['explanation'],
                $question['marks_override'] !== null ? $question['marks_override'] : $question['marks'],
                $question['negative_marks_override'] !== null ? $question['negative_marks_override'] : $question['negative_marks'],
                $question['allow_partial'], $index + 1
            ));
        }
    }

    private function finalizeLocked(array $attempt, $automatic)
    {
        if ($attempt['status'] !== 'in_progress') {
            return array(
                'submission_ref' => $attempt['submission_ref'],
                'submitted_at' => $attempt['submitted_at'],
                'status' => $attempt['status'],
                'idempotent' => true
            );
        }
        $questions = $this->all(
            'SELECT aq.*, ans.id AS answer_id, ans.answer_json
             FROM cbt_attempt_questions aq
             LEFT JOIN cbt_attempt_answers ans ON ans.attempt_question_id = aq.id
             WHERE aq.attempt_id = ? ORDER BY aq.display_order FOR UPDATE',
            array($attempt['id'])
        );
        $objectiveScore = 0.0;
        $hasManual = false;
        $update = $this->pdo->prepare(
            'UPDATE cbt_attempt_answers SET auto_marks = ?, final_marks = ?, updated_at = NOW() WHERE id = ?'
        );
        foreach ($questions as $question) {
            if (!CbtService::isObjectiveType($question['question_type'])) {
                if (!empty($question['answer_id'])) {
                    $hasManual = true;
                }
                continue;
            }
            if (empty($question['answer_id'])) {
                continue;
            }
            $awarded = $this->scoreObjective($question, $this->decode($question['answer_json'], null));
            $objectiveScore += $awarded;
            $update->execute(array($awarded, $awarded, $question['answer_id']));
        }
        $totalMarks = (float) $this->scalar('SELECT total_marks FROM cbt_assessments WHERE id = ?', array($attempt['assessment_id']));
        $percentage = $totalMarks > 0 ? round(($objectiveScore / $totalMarks) * 100, 2) : 0;
        $reference = 'CBT-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
        $status = $hasManual ? 'marking' : 'marked';
        $statement = $this->pdo->prepare(
            'UPDATE cbt_attempts
             SET status = ?, submitted_at = NOW(), objective_score = ?, manual_score = 0,
                 total_score = ?, percentage = ?, grade = ?, submission_ref = ?, last_seen_at = NOW()
             WHERE id = ?'
        );
        $statement->execute(array(
            $status, $objectiveScore, $objectiveScore, $percentage,
            $this->grade($percentage), $reference, $attempt['id']
        ));
        $this->cbt->audit('learner', $attempt['learner_id'], $automatic ? 'attempt.auto_submitted' : 'attempt.submitted', 'attempt', $attempt['id'], null, array('reference' => $reference));
        return array(
            'submission_ref' => $reference,
            'submitted_at' => date('Y-m-d H:i:s'),
            'status' => $status,
            'idempotent' => false
        );
    }

    private function scoreObjective(array $question, $answer)
    {
        $expected = $this->decode($question['correct_answer_snapshot'], array());
        $available = (float) $question['marks_available'];
        $negative = (float) $question['negative_marks'];
        $partial = (int) $question['allow_partial'] === 1;
        $type = $question['question_type'];
        $correct = false;
        $ratio = 0.0;

        if (in_array($type, array('single_choice', 'true_false'), true)) {
            $correct = (string) $answer === (string) (isset($expected[0]) ? $expected[0] : '');
        } elseif ($type === 'multiple_choice') {
            $actual = is_array($answer) ? array_values(array_unique(array_map('strval', $answer))) : array();
            $expected = array_values(array_unique(array_map('strval', (array) $expected)));
            sort($actual);
            sort($expected);
            $correct = $actual === $expected;
            if (!$correct && $partial && $expected) {
                $right = count(array_intersect($actual, $expected));
                $wrong = count(array_diff($actual, $expected));
                $ratio = max(0, ($right - $wrong) / count($expected));
            }
        } elseif (in_array($type, array('fill_blank', 'short_answer'), true)) {
            $actual = $this->normalizeTextAnswer($answer);
            foreach ((array) $expected as $accepted) {
                if ($actual === $this->normalizeTextAnswer($accepted)) {
                    $correct = true;
                    break;
                }
            }
        } elseif (in_array($type, array('matching', 'ordering'), true)) {
            $actual = is_array($answer) ? array_values(array_map('strval', $answer)) : array();
            $expected = array_values(array_map('strval', (array) $expected));
            $correct = $actual === $expected;
            if (!$correct && $partial && $expected) {
                $hits = 0;
                foreach ($expected as $index => $value) {
                    if (isset($actual[$index]) && $actual[$index] === $value) {
                        $hits++;
                    }
                }
                $ratio = $hits / count($expected);
            }
        }
        if ($correct) {
            return $available;
        }
        if ($ratio > 0) {
            return round($available * $ratio, 2);
        }
        return $answer === null || $answer === '' || $answer === array() ? 0 : -$negative;
    }

    private function recalculateAttempt($attemptId)
    {
        $scores = $this->one(
            'SELECT COALESCE(SUM(CASE WHEN aq.question_type <> \'essay\' THEN ans.final_marks ELSE 0 END), 0) AS objective_score,
                    COALESCE(SUM(CASE WHEN aq.question_type = \'essay\' THEN ans.final_marks ELSE 0 END), 0) AS manual_score,
                    SUM(CASE WHEN aq.question_type = \'essay\' AND ans.manual_marks IS NULL THEN 1 ELSE 0 END) AS pending
             FROM cbt_attempt_questions aq
             LEFT JOIN cbt_attempt_answers ans ON ans.attempt_question_id = aq.id
             WHERE aq.attempt_id = ?',
            array($attemptId)
        );
        $attempt = $this->one(
            'SELECT atp.assessment_id, a.total_marks FROM cbt_attempts atp
             INNER JOIN cbt_assessments a ON a.id = atp.assessment_id WHERE atp.id = ?',
            array($attemptId)
        );
        $total = (float) $scores['objective_score'] + (float) $scores['manual_score'];
        $percentage = (float) $attempt['total_marks'] > 0 ? round(($total / (float) $attempt['total_marks']) * 100, 2) : 0;
        $status = (int) $scores['pending'] > 0 ? 'marking' : 'marked';
        $statement = $this->pdo->prepare(
            'UPDATE cbt_attempts SET objective_score = ?, manual_score = ?, total_score = ?, percentage = ?, grade = ?, status = ? WHERE id = ?'
        );
        $statement->execute(array(
            $scores['objective_score'], $scores['manual_score'], $total,
            $percentage, $this->grade($percentage), $status, $attemptId
        ));
    }

    private function lockedAssessment($assessmentId)
    {
        $row = $this->one('SELECT * FROM cbt_assessments WHERE id = ? LIMIT 1 FOR UPDATE', array($assessmentId));
        if (!$row) {
            throw new RuntimeException('Assessment not found.');
        }
        return $row;
    }

    private function lockedAttempt($attemptId, $plainToken)
    {
        $attempt = $this->one('SELECT * FROM cbt_attempts WHERE id = ? LIMIT 1 FOR UPDATE', array($attemptId));
        if (!$attempt || !is_string($plainToken) || !hash_equals($attempt['token_hash'], hash('sha256', $plainToken))) {
            throw new RuntimeException('The secure examination session could not be verified.');
        }
        return $attempt;
    }

    private function assertEligible($assessmentId, $learnerId, $classId)
    {
        $eligible = $this->scalar(
            'SELECT 1 FROM cbt_assessment_assignments
             WHERE assessment_id = ? AND status = \'eligible\'
               AND ((assignment_type = \'class\' AND class_id = ?)
                 OR (assignment_type = \'student\' AND learner_id = ?)) LIMIT 1',
            array($assessmentId, $classId, $learnerId)
        );
        if (!$eligible) {
            throw new RuntimeException('This assessment is not assigned to you.');
        }
    }

    private function assertAttemptWindow(array $assessment)
    {
        if (!in_array($assessment['status'], array('scheduled', 'active', 'approved', 'published'), true)) {
            throw new RuntimeException('This assessment is not available.');
        }
        $now = new DateTimeImmutable('now');
        if ($now < new DateTimeImmutable($assessment['start_at'])) {
            throw new RuntimeException('This assessment has not opened yet.');
        }
        if ($now > new DateTimeImmutable($assessment['close_at']) && !(int) $assessment['late_entry']) {
            throw new RuntimeException('This assessment has closed.');
        }
    }

    private function normalizeTextAnswer($answer)
    {
        $answer = trim(mb_strtolower((string) $answer));
        return preg_replace('/\s+/u', ' ', $answer);
    }

    private function grade($percentage)
    {
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        if ($percentage >= 40) return 'E';
        return 'F';
    }

    private function setAttemptCookie($attemptId, $plainToken, $expiresAt)
    {
        if (PHP_SAPI === 'cli') {
            return;
        }
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $appPath = function_exists('app_env') ? parse_url((string) app_env('APP_URL', '/learnable'), PHP_URL_PATH) : '/learnable';
        $appPath = is_string($appPath) && $appPath !== '' ? rtrim($appPath, '/') : '';
        setcookie('learnable_cbt_attempt', $attemptId . '.' . $plainToken, array(
            'expires' => strtotime($expiresAt) + 86400,
            'path' => $appPath . '/learn',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Strict',
        ));
    }

    private function decode($value, $default)
    {
        if ($value === null || $value === '') {
            return $default;
        }
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
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

    private function scalar($sql, array $params = array())
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchColumn();
    }
}
