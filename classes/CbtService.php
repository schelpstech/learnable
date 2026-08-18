<?php

class CbtService
{
    private $pdo;

    private static $assessmentTypes = array(
        'weekly_test', 'continuous_assessment', 'mid_term', 'terminal_exam',
        'mock_exam', 'practice_test', 'homework', 'diagnostic'
    );
    private static $resultTreatments = array('weekly', 'ca', 'exam', 'practice', 'temporary', 'excluded');
    private static $questionTypes = array(
        'single_choice', 'multiple_choice', 'true_false', 'fill_blank',
        'short_answer', 'essay', 'matching', 'ordering'
    );
    private static $objectiveTypes = array(
        'single_choice', 'multiple_choice', 'true_false', 'fill_blank',
        'short_answer', 'matching', 'ordering'
    );

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function pdo()
    {
        return $this->pdo;
    }

    public static function assessmentTypes()
    {
        return self::$assessmentTypes;
    }

    public static function resultTreatments()
    {
        return self::$resultTreatments;
    }

    public static function questionTypes()
    {
        return self::$questionTypes;
    }

    public static function isObjectiveType($type)
    {
        return in_array($type, self::$objectiveTypes, true);
    }

    public function activeContext()
    {
        $term = $this->one('SELECT term FROM lpterm WHERE status = 1 ORDER BY tid DESC LIMIT 1');
        $session = $this->one('SELECT session FROM lhpsession WHERE status = 1 ORDER BY sessionid DESC LIMIT 1');
        if (!$term || !$session) {
            throw new RuntimeException('An active academic session and term are required.');
        }
        return array('term' => $term['term'], 'session' => $session['session']);
    }

    public function teacherAllocations($teacherId)
    {
        $context = $this->activeContext();
        return $this->all(
            'SELECT DISTINCT a.classid AS class_id, c.classname, a.sbjid AS subject_id, s.sbjname, a.term
             FROM lhpalloc a
             INNER JOIN lhpclass c ON c.classid = a.classid
             INNER JOIN lhpsubject s ON s.sbjid = a.sbjid
             WHERE a.staffid = ? AND a.term = ?
             ORDER BY c.classname, s.sbjname',
            array($teacherId, $context['term'])
        );
    }

    public function allClassesAndSubjects()
    {
        $context = $this->activeContext();
        return $this->all(
            'SELECT DISTINCT a.classid AS class_id, c.classname, a.sbjid AS subject_id, s.sbjname, a.staffid, st.staffname
             FROM lhpalloc a
             INNER JOIN lhpclass c ON c.classid = a.classid
             INNER JOIN lhpsubject s ON s.sbjid = a.sbjid
             LEFT JOIN lhpstaff st ON st.sname = a.staffid
             WHERE a.term = ? ORDER BY c.classname, s.sbjname',
            array($context['term'])
        );
    }

    public function topics($teacherId, $classId, $subjectId, $isAdmin)
    {
        $context = $this->activeContext();
        if (!$isAdmin) {
            $this->assertTeacherAllocation($teacherId, $classId, $subjectId, $context['term']);
        }
        return $this->all(
            'SELECT schmid AS id, week, topic, staffid, status
             FROM lhpscheme
             WHERE term = ? AND classname = ? AND subject = ? AND status = 1
             ORDER BY CAST(REPLACE(UPPER(week), \'WEEK \' , \'\') AS UNSIGNED), schmid',
            array($context['term'], (string) $classId, (string) $subjectId)
        );
    }

    public function createAssessment(array $input, $actorId, $isAdmin)
    {
        $context = $this->activeContext();
        $classId = CbtSecurity::positiveInt(isset($input['class_id']) ? $input['class_id'] : null, 'Class', 1, PHP_INT_MAX);
        $subjectId = CbtSecurity::positiveInt(isset($input['subject_id']) ? $input['subject_id'] : null, 'Subject', 1, PHP_INT_MAX);
        $teacherId = $isAdmin
            ? CbtSecurity::cleanText(isset($input['teacher_id']) ? $input['teacher_id'] : '', 64, false)
            : $actorId;
        $schemeId = CbtSecurity::positiveInt(isset($input['scheme_id']) ? $input['scheme_id'] : null, 'Scheme topic', 1, PHP_INT_MAX);

        if (!$isAdmin) {
            $this->assertTeacherAllocation($teacherId, $classId, $subjectId, $context['term']);
        }
        $topic = $this->assertSchemeTopic($schemeId, $classId, $subjectId, $context['term']);
        $this->assertTopicIsCovered($topic, $isAdmin);

        $type = isset($input['assessment_type']) ? (string) $input['assessment_type'] : '';
        $treatment = isset($input['result_treatment']) ? (string) $input['result_treatment'] : '';
        if (!in_array($type, self::$assessmentTypes, true)) {
            throw new InvalidArgumentException('Select a valid assessment type.');
        }
        if (!in_array($treatment, self::$resultTreatments, true)) {
            throw new InvalidArgumentException('Select how the score should be treated.');
        }

        $startAt = $this->dateTime(isset($input['start_at']) ? $input['start_at'] : '', 'Opening date');
        $closeAt = $this->dateTime(isset($input['close_at']) ? $input['close_at'] : '', 'Closing date');
        if ($closeAt <= $startAt) {
            throw new InvalidArgumentException('The closing date must be after the opening date.');
        }

        $values = array(
            $context['session'], $context['term'], $classId, $subjectId, $teacherId,
            CbtSecurity::cleanText(isset($input['title']) ? $input['title'] : '', 190, false),
            CbtSecurity::cleanText(isset($input['instructions']) ? $input['instructions'] : '', 5000, true),
            $type, $treatment,
            CbtSecurity::decimal(isset($input['total_marks']) ? $input['total_marks'] : 100, 'Total marks', 1, 10000),
            CbtSecurity::decimal(isset($input['pass_mark']) ? $input['pass_mark'] : 50, 'Pass mark', 0, 10000),
            $startAt->format('Y-m-d H:i:s'), $closeAt->format('Y-m-d H:i:s'),
            CbtSecurity::positiveInt(isset($input['duration_minutes']) ? $input['duration_minutes'] : 30, 'Time allowed', 1, 720),
            CbtSecurity::positiveInt(isset($input['max_attempts']) ? $input['max_attempts'] : 1, 'Number of attempts', 1, 5),
            isset($input['navigation_mode']) && $input['navigation_mode'] === 'linear' ? 'linear' : 'free',
            $this->flag($input, 'allow_backtrack'), $this->flag($input, 'randomize_questions'),
            $this->flag($input, 'shuffle_options'), $this->flag($input, 'auto_submit', true),
            $this->flag($input, 'show_score'), $this->flag($input, 'allow_review'),
            $this->flag($input, 'show_correct_answers'), $this->flag($input, 'require_approval', true),
            $this->flag($input, 'late_entry'), $this->flag($input, 'late_submission'),
            $this->flag($input, 'fullscreen_mode'), $this->flag($input, 'monitor_tab_switch', true),
            $this->flag($input, 'restrict_clipboard'), $actorId
        );

        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO cbt_assessments
                 (session_name, term, class_id, subject_id, teacher_id, title, instructions,
                  assessment_type, result_treatment, total_marks, pass_mark, start_at, close_at,
                  duration_minutes, max_attempts, navigation_mode, allow_backtrack,
                  randomize_questions, shuffle_options, auto_submit, show_score, allow_review,
                  show_correct_answers, require_approval, late_entry, late_submission,
                  fullscreen_mode, monitor_tab_switch, restrict_clipboard, status, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, \'draft\', ?)'
            );
            $statement->execute($values);
            $assessmentId = (int) $this->pdo->lastInsertId();

            $topicStatement = $this->pdo->prepare(
                'INSERT INTO cbt_assessment_topics (assessment_id, scheme_id, is_primary) VALUES (?, ?, 1)'
            );
            $topicStatement->execute(array($assessmentId, $schemeId));

            $assignment = $this->pdo->prepare(
                'INSERT INTO cbt_assessment_assignments
                 (assessment_id, assignment_type, class_id, learner_id, status)
                 VALUES (?, \'class\', ?, NULL, \'eligible\')'
            );
            $assignment->execute(array($assessmentId, $classId));
            $this->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'assessment.created', 'assessment', $assessmentId, null, array('title' => $input['title']));
            $this->pdo->commit();
            return $assessmentId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function createQuestion(array $input, $actorId, $isAdmin)
    {
        $context = $this->activeContext();
        $classId = CbtSecurity::positiveInt(isset($input['class_id']) ? $input['class_id'] : null, 'Class', 1, PHP_INT_MAX);
        $subjectId = CbtSecurity::positiveInt(isset($input['subject_id']) ? $input['subject_id'] : null, 'Subject', 1, PHP_INT_MAX);
        $schemeId = CbtSecurity::positiveInt(isset($input['scheme_id']) ? $input['scheme_id'] : null, 'Scheme topic', 1, PHP_INT_MAX);
        if (!$isAdmin) {
            $this->assertTeacherAllocation($actorId, $classId, $subjectId, $context['term']);
        }
        $topic = $this->assertSchemeTopic($schemeId, $classId, $subjectId, $context['term']);
        $this->assertTopicIsCovered($topic, $isAdmin);

        $type = isset($input['question_type']) ? (string) $input['question_type'] : '';
        if (!in_array($type, self::$questionTypes, true)) {
            throw new InvalidArgumentException('Select a supported question type.');
        }
        $difficulty = isset($input['difficulty']) ? (string) $input['difficulty'] : 'medium';
        if (!in_array($difficulty, array('easy', 'medium', 'hard'), true)) {
            throw new InvalidArgumentException('Select a valid difficulty level.');
        }

        $options = $this->normalizeOptions($input, $type);
        $acceptedAnswers = $this->normalizeAcceptedAnswers($input, $type, $options);
        $visibility = isset($input['visibility']) && $input['visibility'] === 'school' ? 'school' : 'private';
        $status = $isAdmin || (isset($input['status']) && $input['status'] === 'active') ? 'active' : 'draft';

        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO cbt_questions
                 (owner_teacher_id, session_name, term, class_id, subject_id, scheme_id,
                  learning_objective, question_type, difficulty, prompt_html, media_type, media_url,
                  marks, negative_marks, allow_partial, accepted_answers, model_answer,
                  marking_guide, explanation, visibility, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $statement->execute(array(
                $actorId, $context['session'], $context['term'], $classId, $subjectId, $schemeId,
                CbtSecurity::cleanText(isset($input['learning_objective']) ? $input['learning_objective'] : '', 255, true),
                $type, $difficulty, CbtSecurity::safeHtml(isset($input['prompt_html']) ? $input['prompt_html'] : '', 20000),
                $this->nullableChoice(isset($input['media_type']) ? $input['media_type'] : null, array('image', 'audio', 'video')),
                $this->safeMediaUrl(isset($input['media_url']) ? $input['media_url'] : ''),
                CbtSecurity::decimal(isset($input['marks']) ? $input['marks'] : 1, 'Question marks', 0.25, 1000),
                CbtSecurity::decimal(isset($input['negative_marks']) ? $input['negative_marks'] : 0, 'Negative marks', 0, 1000),
                $this->flag($input, 'allow_partial'),
                $acceptedAnswers === null ? null : json_encode($acceptedAnswers, JSON_UNESCAPED_UNICODE),
                CbtSecurity::cleanText(isset($input['model_answer']) ? $input['model_answer'] : '', 20000, true),
                CbtSecurity::cleanText(isset($input['marking_guide']) ? $input['marking_guide'] : '', 20000, true),
                CbtSecurity::cleanText(isset($input['explanation']) ? $input['explanation'] : '', 20000, true),
                $visibility, $status
            ));
            $questionId = (int) $this->pdo->lastInsertId();
            if ($options) {
                $optionStatement = $this->pdo->prepare(
                    'INSERT INTO cbt_question_options
                     (question_id, option_key, option_text, is_correct, match_key, sort_order)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                foreach ($options as $index => $option) {
                    $optionStatement->execute(array(
                        $questionId, $option['key'], $option['text'], $option['correct'] ? 1 : 0,
                        $option['match_key'], $index + 1
                    ));
                }
            }
            $this->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'question.created', 'question', $questionId, null, array('type' => $type));
            $this->pdo->commit();
            return $questionId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function addQuestionToAssessment($assessmentId, $questionId, $actorId, $isAdmin)
    {
        $assessment = $this->assessment($assessmentId);
        $this->assertAssessmentManager($assessment, $actorId, $isAdmin);
        if (!in_array($assessment['status'], array('draft', 'pending_approval'), true)) {
            throw new RuntimeException('Questions can only be changed while an assessment is a draft.');
        }
        $question = $this->question($questionId, $actorId, $isAdmin);
        if ((int) $question['class_id'] !== (int) $assessment['class_id']
            || (int) $question['subject_id'] !== (int) $assessment['subject_id']) {
            throw new InvalidArgumentException('The question does not belong to this class and subject.');
        }

        $this->pdo->beginTransaction();
        try {
            $order = (int) $this->scalar('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM cbt_assessment_questions WHERE assessment_id = ?', array($assessmentId));
            $statement = $this->pdo->prepare(
                'INSERT IGNORE INTO cbt_assessment_questions (assessment_id, question_id, sort_order) VALUES (?, ?, ?)'
            );
            $statement->execute(array($assessmentId, $questionId, $order));
            if ($statement->rowCount() < 1) {
                throw new RuntimeException('That question is already part of the assessment.');
            }
            $this->recalculateAssessment($assessmentId);
            $this->pdo->prepare('UPDATE cbt_questions SET use_count = use_count + 1, last_used_at = NOW() WHERE id = ?')->execute(array($questionId));
            $this->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'assessment.question_added', 'assessment', $assessmentId, null, array('question_id' => $questionId));
            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function removeQuestionFromAssessment($assessmentId, $questionId, $actorId, $isAdmin)
    {
        $assessment = $this->assessment($assessmentId);
        $this->assertAssessmentManager($assessment, $actorId, $isAdmin);
        if ($assessment['status'] !== 'draft') {
            throw new RuntimeException('Questions can only be removed from a draft.');
        }
        $statement = $this->pdo->prepare('DELETE FROM cbt_assessment_questions WHERE assessment_id = ? AND question_id = ?');
        $statement->execute(array($assessmentId, $questionId));
        $this->recalculateAssessment($assessmentId);
        $this->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'assessment.question_removed', 'assessment', $assessmentId, null, array('question_id' => $questionId));
    }

    public function duplicateAssessment($assessmentId, $actorId, $isAdmin)
    {
        $assessment = $this->assessment($assessmentId);
        $this->assertAssessmentManager($assessment, $actorId, $isAdmin);
        $start = new DateTimeImmutable($assessment['start_at']);
        $close = new DateTimeImmutable($assessment['close_at']);
        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO cbt_assessments
                 (session_name, term, class_id, subject_id, teacher_id, title, instructions,
                  assessment_type, result_treatment, total_marks, pass_mark, question_count,
                  start_at, close_at, duration_minutes, max_attempts, navigation_mode,
                  allow_backtrack, randomize_questions, shuffle_options, auto_submit,
                  show_score, allow_review, show_correct_answers, require_approval,
                  late_entry, late_submission, fullscreen_mode, monitor_tab_switch,
                  restrict_clipboard, status, created_by)
                 SELECT session_name, term, class_id, subject_id, teacher_id,
                        LEFT(CONCAT(title, \' · Copy\'), 190), instructions,
                        assessment_type, result_treatment, total_marks, pass_mark, question_count,
                        ?, ?, duration_minutes, max_attempts, navigation_mode,
                        allow_backtrack, randomize_questions, shuffle_options, auto_submit,
                        show_score, allow_review, show_correct_answers, require_approval,
                        late_entry, late_submission, fullscreen_mode, monitor_tab_switch,
                        restrict_clipboard, \'draft\', ?
                 FROM cbt_assessments WHERE id = ?'
            );
            $statement->execute(array(
                $start->modify('+7 days')->format('Y-m-d H:i:s'),
                $close->modify('+7 days')->format('Y-m-d H:i:s'),
                $actorId, $assessmentId
            ));
            $copyId = (int) $this->pdo->lastInsertId();
            $this->pdo->prepare(
                'INSERT INTO cbt_assessment_topics (assessment_id, scheme_id, is_primary)
                 SELECT ?, scheme_id, is_primary FROM cbt_assessment_topics WHERE assessment_id = ?'
            )->execute(array($copyId, $assessmentId));
            $this->pdo->prepare(
                'INSERT INTO cbt_assessment_questions
                 (assessment_id, question_id, marks_override, negative_marks_override, sort_order)
                 SELECT ?, question_id, marks_override, negative_marks_override, sort_order
                 FROM cbt_assessment_questions WHERE assessment_id = ?'
            )->execute(array($copyId, $assessmentId));
            $this->pdo->prepare(
                'INSERT INTO cbt_assessment_assignments
                 (assessment_id, assignment_type, class_id, learner_id, extra_time_minutes, allow_late, status)
                 SELECT ?, assignment_type, class_id, learner_id, extra_time_minutes, allow_late, status
                 FROM cbt_assessment_assignments WHERE assessment_id = ?'
            )->execute(array($copyId, $assessmentId));
            $this->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'assessment.duplicated', 'assessment', $copyId, array('source_id' => $assessmentId), array('status' => 'draft'));
            $this->pdo->commit();
            return $copyId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function duplicateQuestion($questionId, $actorId, $isAdmin)
    {
        $question = $this->question($questionId, $actorId, $isAdmin);
        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO cbt_questions
                 (owner_teacher_id, session_name, term, class_id, subject_id, scheme_id,
                  learning_objective, question_type, difficulty, prompt_html, media_type,
                  media_url, marks, negative_marks, allow_partial, accepted_answers,
                  model_answer, marking_guide, explanation, visibility, status,
                  version_no, parent_question_id)
                 SELECT ?, session_name, term, class_id, subject_id, scheme_id,
                        learning_objective, question_type, difficulty, prompt_html, media_type,
                        media_url, marks, negative_marks, allow_partial, accepted_answers,
                        model_answer, marking_guide, explanation, \'private\', \'draft\', 1, id
                 FROM cbt_questions WHERE id = ?'
            );
            $statement->execute(array($actorId, $questionId));
            $copyId = (int) $this->pdo->lastInsertId();
            $this->pdo->prepare(
                'INSERT INTO cbt_question_options
                 (question_id, option_key, option_text, is_correct, match_key, sort_order)
                 SELECT ?, option_key, option_text, is_correct, match_key, sort_order
                 FROM cbt_question_options WHERE question_id = ?'
            )->execute(array($copyId, $questionId));
            $this->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'question.duplicated', 'question', $copyId, array('source_id' => $questionId), array('status' => 'draft'));
            $this->pdo->commit();
            return $copyId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function importQuestionsCsv($filePath, $actorId)
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) throw new RuntimeException('The CSV file could not be read.');
        $expected = array('class_id', 'subject_id', 'scheme_id', 'question_type', 'difficulty', 'prompt', 'marks', 'options', 'correct_answers', 'accepted_answers', 'match_keys', 'learning_objective', 'explanation', 'visibility');
        $header = fgetcsv($handle);
        if (!$header) { fclose($handle); throw new InvalidArgumentException('The CSV file is empty.'); }
        $header = array_map(function ($value) { return strtolower(trim((string) $value)); }, $header);
        foreach (array('class_id', 'subject_id', 'scheme_id', 'question_type', 'prompt', 'marks') as $required) {
            if (!in_array($required, $header, true)) { fclose($handle); throw new InvalidArgumentException('The CSV template is missing the ' . $required . ' column.'); }
        }
        $created = 0;
        $errors = array();
        $line = 1;
        while (($row = fgetcsv($handle)) !== false && $line < 251) {
            $line++;
            if (!array_filter($row, function ($value) { return trim((string) $value) !== ''; })) continue;
            $row = array_pad($row, count($header), '');
            $data = array_combine($header, array_slice($row, 0, count($header)));
            try {
                $options = $data['options'] !== '' ? array_map('trim', explode('|', $data['options'])) : array();
                $correct = $data['correct_answers'] !== '' ? array_map('trim', explode('|', $data['correct_answers'])) : array();
                $matches = isset($data['match_keys']) && $data['match_keys'] !== '' ? array_map('trim', explode('|', $data['match_keys'])) : array();
                $input = array(
                    'class_id' => $data['class_id'], 'subject_id' => $data['subject_id'], 'scheme_id' => $data['scheme_id'],
                    'question_type' => $data['question_type'], 'difficulty' => $data['difficulty'] ?: 'medium',
                    'prompt_html' => $data['prompt'], 'marks' => $data['marks'], 'negative_marks' => 0,
                    'option_text' => $options, 'match_key' => $matches,
                    'correct_option' => isset($correct[0]) ? $correct[0] : '', 'correct_options' => $correct,
                    'true_false_answer' => isset($correct[0]) ? $correct[0] : 'true',
                    'accepted_answer' => isset($data['accepted_answers']) ? str_replace('|', "\n", $data['accepted_answers']) : '',
                    'learning_objective' => isset($data['learning_objective']) ? $data['learning_objective'] : '',
                    'explanation' => isset($data['explanation']) ? $data['explanation'] : '',
                    'visibility' => isset($data['visibility']) ? $data['visibility'] : 'private',
                    'status' => 'active', 'model_answer' => '', 'marking_guide' => '',
                    'media_type' => '', 'media_url' => '', 'allow_partial' => 0,
                );
                $this->createQuestion($input, $actorId, false);
                $created++;
            } catch (Throwable $exception) {
                $errors[] = 'Row ' . $line . ': ' . $exception->getMessage();
            }
        }
        fclose($handle);
        return array('created' => $created, 'errors' => $errors);
    }

    public function submitForApproval($assessmentId, $actorId)
    {
        $assessment = $this->assessment($assessmentId);
        $this->assertAssessmentManager($assessment, $actorId, false);
        $this->assertAssessmentReady($assessmentId);
        $status = (int) $assessment['require_approval'] === 1 ? 'pending_approval' : 'scheduled';
        $this->setAssessmentStatus($assessmentId, $status, $actorId, false, 'Assessment submitted by its teacher.');
        if ($status === 'scheduled') {
            $this->queuePortalNotices($assessmentId, 'published');
        }
        return $status;
    }

    public function setAssessmentStatus($assessmentId, $newStatus, $actorId, $isAdmin, $reason)
    {
        $allowed = array('draft', 'pending_approval', 'scheduled', 'active', 'completed', 'marking', 'awaiting_approval', 'approved', 'published', 'paused', 'archived', 'cancelled');
        if (!in_array($newStatus, $allowed, true)) {
            throw new InvalidArgumentException('Unknown assessment status.');
        }
        $assessment = $this->assessment($assessmentId);
        $this->assertAssessmentManager($assessment, $actorId, $isAdmin);
        $teacherMayPublishApproved = $newStatus === 'published' && in_array($assessment['status'], array('approved', 'published'), true);
        if (!$isAdmin && !$teacherMayPublishApproved && !in_array($newStatus, array('draft', 'pending_approval', 'scheduled', 'paused', 'cancelled'), true)) {
            throw new RuntimeException('An administrator must approve or publish this assessment.');
        }
        if (in_array($newStatus, array('scheduled', 'approved', 'published'), true)) {
            $this->assertAssessmentReady($assessmentId);
        }

        $sql = 'UPDATE cbt_assessments SET status = ?, approved_by = approved_by, approved_at = approved_at, published_at = published_at, archived_at = archived_at WHERE id = ?';
        if ($newStatus === 'approved') {
            $sql = 'UPDATE cbt_assessments SET status = ?, approved_by = ?, approved_at = NOW() WHERE id = ?';
            $params = array($newStatus, $actorId, $assessmentId);
        } elseif (in_array($newStatus, array('scheduled', 'published'), true)) {
            $sql = 'UPDATE cbt_assessments SET status = ?, published_at = COALESCE(published_at, NOW()) WHERE id = ?';
            $params = array($newStatus, $assessmentId);
        } elseif ($newStatus === 'archived') {
            $sql = 'UPDATE cbt_assessments SET status = ?, archived_at = NOW() WHERE id = ?';
            $params = array($newStatus, $assessmentId);
        } else {
            $params = array($newStatus, $assessmentId);
        }
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $this->audit($isAdmin ? 'admin' : 'instructor', $actorId, 'assessment.status_changed', 'assessment', $assessmentId, array('status' => $assessment['status']), array('status' => $newStatus), $reason);

        if (in_array($newStatus, array('scheduled', 'published', 'paused', 'cancelled'), true)) {
            $this->queuePortalNotices($assessmentId, $newStatus);
        }
    }

    public function teacherAssessments($teacherId)
    {
        $context = $this->activeContext();
        return $this->assessmentList('a.teacher_id = ? AND a.term = ?', array($teacherId, $context['term']));
    }

    public function adminAssessments()
    {
        return $this->assessmentList('1 = 1', array());
    }

    public function learnerAssessments($learnerId)
    {
        $learner = $this->one('SELECT uname, classid FROM lhpuser WHERE uname = ? AND status = 1 LIMIT 1', array($learnerId));
        if (!$learner) {
            throw new RuntimeException('Learner account not found.');
        }
        $context = $this->activeContext();
        return $this->all(
            'SELECT a.*, c.classname, s.sbjname, st.staffname, sc.week, sc.topic,
                    (SELECT COUNT(*) FROM cbt_assessment_questions aq WHERE aq.assessment_id = a.id) AS actual_question_count,
                    (SELECT COUNT(*) FROM cbt_attempts atp WHERE atp.assessment_id = a.id AND atp.learner_id = ?) AS attempts_used,
                    (SELECT atp.status FROM cbt_attempts atp WHERE atp.assessment_id = a.id AND atp.learner_id = ? ORDER BY atp.attempt_no DESC LIMIT 1) AS attempt_status,
                    (SELECT atp.id FROM cbt_attempts atp WHERE atp.assessment_id = a.id AND atp.learner_id = ? ORDER BY atp.attempt_no DESC LIMIT 1) AS attempt_id
             FROM cbt_assessments a
             INNER JOIN lhpclass c ON c.classid = a.class_id
             INNER JOIN lhpsubject s ON s.sbjid = a.subject_id
             LEFT JOIN lhpstaff st ON st.sname = a.teacher_id
             LEFT JOIN cbt_assessment_topics atc ON atc.assessment_id = a.id AND atc.is_primary = 1
             LEFT JOIN lhpscheme sc ON sc.schmid = atc.scheme_id
             WHERE a.term = ? AND a.status IN (\'scheduled\', \'active\', \'completed\', \'marking\', \'awaiting_approval\', \'approved\', \'published\')
               AND EXISTS (
                   SELECT 1 FROM cbt_assessment_assignments aa
                   WHERE aa.assessment_id = a.id AND aa.status = \'eligible\'
                     AND ((aa.assignment_type = \'class\' AND aa.class_id = ?)
                       OR (aa.assignment_type = \'student\' AND aa.learner_id = ?))
               )
             ORDER BY a.start_at DESC',
            array($learnerId, $learnerId, $learnerId, $context['term'], (int) $learner['classid'], $learnerId)
        );
    }

    public function questionBank($actorId, $isAdmin, array $filters)
    {
        $where = $isAdmin ? '(q.status <> \'archived\')' : '(q.owner_teacher_id = ? OR q.visibility = \'school\') AND q.status <> \'archived\'';
        $params = $isAdmin ? array() : array($actorId);
        foreach (array('class_id', 'subject_id', 'scheme_id') as $field) {
            if (!empty($filters[$field]) && ctype_digit((string) $filters[$field])) {
                $where .= ' AND q.' . $field . ' = ?';
                $params[] = (int) $filters[$field];
            }
        }
        if (!empty($filters['question_type']) && in_array($filters['question_type'], self::$questionTypes, true)) {
            $where .= ' AND q.question_type = ?';
            $params[] = $filters['question_type'];
        }
        return $this->all(
            'SELECT q.*, c.classname, s.sbjname, sc.week, sc.topic, st.staffname,
                    (SELECT COUNT(*) FROM cbt_question_options o WHERE o.question_id = q.id) AS option_count
             FROM cbt_questions q
             INNER JOIN lhpclass c ON c.classid = q.class_id
             INNER JOIN lhpsubject s ON s.sbjid = q.subject_id
             INNER JOIN lhpscheme sc ON sc.schmid = q.scheme_id
             LEFT JOIN lhpstaff st ON st.sname = q.owner_teacher_id
             WHERE ' . $where . '
             ORDER BY q.updated_at DESC LIMIT 250',
            $params
        );
    }

    public function assessment($id)
    {
        $id = CbtSecurity::positiveInt($id, 'Assessment', 1, PHP_INT_MAX);
        $row = $this->one(
            'SELECT a.*, c.classname, s.sbjname, st.staffname,
                    sc.schmid AS scheme_id, sc.week, sc.topic,
                    (SELECT COUNT(*) FROM cbt_attempts atp WHERE atp.assessment_id = a.id) AS attempt_count,
                    (SELECT COUNT(*) FROM cbt_attempts atp WHERE atp.assessment_id = a.id AND atp.status IN (\'submitted\', \'auto_submitted\', \'marking\', \'marked\', \'published\')) AS submitted_count
             FROM cbt_assessments a
             INNER JOIN lhpclass c ON c.classid = a.class_id
             INNER JOIN lhpsubject s ON s.sbjid = a.subject_id
             LEFT JOIN lhpstaff st ON st.sname = a.teacher_id
             LEFT JOIN cbt_assessment_topics atc ON atc.assessment_id = a.id AND atc.is_primary = 1
             LEFT JOIN lhpscheme sc ON sc.schmid = atc.scheme_id
             WHERE a.id = ? LIMIT 1',
            array($id)
        );
        if (!$row) {
            throw new RuntimeException('Assessment not found.');
        }
        $row['effective_status'] = $this->effectiveStatus($row);
        return $row;
    }

    public function assessmentQuestions($assessmentId, $includeAnswers)
    {
        $columns = $includeAnswers
            ? 'q.*, aq.sort_order, aq.marks_override, aq.negative_marks_override'
            : 'q.id, q.question_type, q.difficulty, q.prompt_html, q.media_type, q.media_url, q.marks, q.learning_objective, aq.sort_order';
        $questions = $this->all(
            'SELECT ' . $columns . '
             FROM cbt_assessment_questions aq
             INNER JOIN cbt_questions q ON q.id = aq.question_id
             WHERE aq.assessment_id = ? ORDER BY aq.sort_order, aq.id',
            array($assessmentId)
        );
        if ($includeAnswers) {
            foreach ($questions as &$question) {
                $question['options'] = $this->all(
                    'SELECT id, option_key, option_text, is_correct, match_key, sort_order
                     FROM cbt_question_options WHERE question_id = ? ORDER BY sort_order, id',
                    array($question['id'])
                );
            }
            unset($question);
        }
        return $questions;
    }

    public function question($questionId, $actorId, $isAdmin)
    {
        $row = $this->one('SELECT * FROM cbt_questions WHERE id = ? LIMIT 1', array($questionId));
        if (!$row) {
            throw new RuntimeException('Question not found.');
        }
        if (!$isAdmin && $row['owner_teacher_id'] !== $actorId && $row['visibility'] !== 'school') {
            throw new RuntimeException('You do not have permission to use this question.');
        }
        return $row;
    }

    public function analytics($assessmentId, $actorId, $isAdmin)
    {
        $assessment = $this->assessment($assessmentId);
        $this->assertAssessmentManager($assessment, $actorId, $isAdmin);
        $summary = $this->one(
            'SELECT COUNT(*) AS attempts,
                    SUM(status IN (\'submitted\',\'auto_submitted\',\'marking\',\'marked\',\'published\')) AS submitted,
                    ROUND(AVG(CASE WHEN submitted_at IS NOT NULL THEN total_score END), 2) AS average_score,
                    MAX(total_score) AS highest_score, MIN(CASE WHEN submitted_at IS NOT NULL THEN total_score END) AS lowest_score,
                    SUM(CASE WHEN submitted_at IS NOT NULL AND total_score >= ? THEN 1 ELSE 0 END) AS passed,
                    ROUND(AVG(CASE WHEN submitted_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, started_at, submitted_at) END), 0) AS average_seconds
             FROM cbt_attempts WHERE assessment_id = ?',
            array($assessment['pass_mark'], $assessmentId)
        );
        $eligible = (int) $this->scalar(
            'SELECT COUNT(DISTINCT u.uname)
             FROM lhpuser u
             WHERE u.status = 1 AND EXISTS (
                 SELECT 1 FROM cbt_assessment_assignments aa
                 WHERE aa.assessment_id = ? AND aa.status = \'eligible\'
                   AND ((aa.assignment_type = \'class\' AND aa.class_id = u.classid)
                     OR (aa.assignment_type = \'student\' AND aa.learner_id = u.uname))
             )',
            array($assessmentId)
        );
        $summary['eligible'] = $eligible;
        $summary['absent'] = max(0, $eligible - (int) $summary['attempts']);
        $summary['pass_rate'] = (int) $summary['submitted'] > 0
            ? round(((int) $summary['passed'] / (int) $summary['submitted']) * 100, 1) : 0;

        $questions = $this->all(
            'SELECT aq.source_question_id, MIN(aq.prompt_snapshot) AS prompt_snapshot,
                    MIN(aq.question_type) AS question_type, MAX(aq.marks_available) AS marks_available,
                    COUNT(aa.id) AS responses,
                    ROUND(AVG(aa.final_marks), 2) AS average_marks,
                    ROUND(100 * AVG(CASE WHEN aa.final_marks >= aq.marks_available THEN 1 ELSE 0 END), 1) AS full_mark_rate
             FROM cbt_attempt_questions aq
             INNER JOIN cbt_attempts atp ON atp.id = aq.attempt_id
             LEFT JOIN cbt_attempt_answers aa ON aa.attempt_question_id = aq.id
             WHERE atp.assessment_id = ? AND atp.submitted_at IS NOT NULL
             GROUP BY aq.source_question_id
             ORDER BY full_mark_rate ASC, aq.source_question_id',
            array($assessmentId)
        );
        $distribution = $this->all(
            'SELECT FLOOR(percentage / 10) * 10 AS band, COUNT(*) AS total
             FROM cbt_attempts WHERE assessment_id = ? AND submitted_at IS NOT NULL
             GROUP BY band ORDER BY band',
            array($assessmentId)
        );
        return array('assessment' => $assessment, 'summary' => $summary, 'questions' => $questions, 'distribution' => $distribution);
    }

    public function audit($actorType, $actorId, $action, $entityType, $entityId, $before, $after, $reason = null)
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO cbt_audit_log
             (actor_type, actor_id, action, entity_type, entity_id, before_json, after_json, reason, ip_hash)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute(array(
            $actorType, $actorId, $action, $entityType, (string) $entityId,
            $before === null ? null : json_encode($before, JSON_UNESCAPED_UNICODE),
            $after === null ? null : json_encode($after, JSON_UNESCAPED_UNICODE),
            $reason, CbtSecurity::requestIpHash()
        ));
    }

    public function effectiveStatus(array $assessment)
    {
        if (in_array($assessment['status'], array('cancelled', 'archived', 'paused', 'draft', 'pending_approval'), true)) {
            return $assessment['status'];
        }
        $now = new DateTimeImmutable('now');
        if ($now < new DateTimeImmutable($assessment['start_at'])) {
            return 'scheduled';
        }
        if ($now > new DateTimeImmutable($assessment['close_at'])) {
            return 'completed';
        }
        return 'active';
    }

    public function assertAssessmentManager(array $assessment, $actorId, $isAdmin)
    {
        if (!$isAdmin && $assessment['teacher_id'] !== $actorId) {
            throw new RuntimeException('You may manage only assessments assigned to you.');
        }
    }

    private function assessmentList($where, array $params)
    {
        return $this->all(
            'SELECT a.*, c.classname, s.sbjname, st.staffname, sc.week, sc.topic,
                    (SELECT COUNT(*) FROM cbt_attempts atp WHERE atp.assessment_id = a.id) AS attempt_count,
                    (SELECT COUNT(*) FROM cbt_attempts atp WHERE atp.assessment_id = a.id AND atp.submitted_at IS NOT NULL) AS submitted_count,
                    (SELECT COUNT(*) FROM cbt_attempts atp WHERE atp.assessment_id = a.id AND atp.status = \'marking\') AS marking_count
             FROM cbt_assessments a
             INNER JOIN lhpclass c ON c.classid = a.class_id
             INNER JOIN lhpsubject s ON s.sbjid = a.subject_id
             LEFT JOIN lhpstaff st ON st.sname = a.teacher_id
             LEFT JOIN cbt_assessment_topics atc ON atc.assessment_id = a.id AND atc.is_primary = 1
             LEFT JOIN lhpscheme sc ON sc.schmid = atc.scheme_id
             WHERE ' . $where . ' ORDER BY a.updated_at DESC',
            $params
        );
    }

    private function assertAssessmentReady($assessmentId)
    {
        $count = (int) $this->scalar('SELECT COUNT(*) FROM cbt_assessment_questions WHERE assessment_id = ?', array($assessmentId));
        if ($count < 1) {
            throw new RuntimeException('Add at least one question before scheduling this assessment.');
        }
        $this->recalculateAssessment($assessmentId);
    }

    private function recalculateAssessment($assessmentId)
    {
        $statement = $this->pdo->prepare(
            'UPDATE cbt_assessments a
             SET a.question_count = (SELECT COUNT(*) FROM cbt_assessment_questions aq WHERE aq.assessment_id = a.id),
                 a.total_marks = COALESCE((
                     SELECT SUM(COALESCE(aq.marks_override, q.marks))
                     FROM cbt_assessment_questions aq
                     INNER JOIN cbt_questions q ON q.id = aq.question_id
                     WHERE aq.assessment_id = a.id
                 ), a.total_marks)
             WHERE a.id = ?'
        );
        $statement->execute(array($assessmentId));
    }

    private function assertTeacherAllocation($teacherId, $classId, $subjectId, $term)
    {
        $exists = $this->scalar(
            'SELECT 1 FROM lhpalloc WHERE staffid = ? AND classid = ? AND sbjid = ? AND term = ? LIMIT 1',
            array($teacherId, $classId, $subjectId, $term)
        );
        if (!$exists) {
            throw new RuntimeException('This class and subject are not assigned to you for the active term.');
        }
    }

    private function assertSchemeTopic($schemeId, $classId, $subjectId, $term)
    {
        $topic = $this->one(
            'SELECT * FROM lhpscheme
             WHERE schmid = ? AND classname = ? AND subject = ? AND term = ? AND status = 1 LIMIT 1',
            array($schemeId, (string) $classId, (string) $subjectId, $term)
        );
        if (!$topic) {
            throw new RuntimeException('Select an approved scheme-of-work topic for this class and subject.');
        }
        return $topic;
    }

    private function assertTopicIsCovered(array $topic, $isAdmin)
    {
        if ($isAdmin) {
            return;
        }
        if (!preg_match('/(\d+)/', (string) $topic['week'], $match)) {
            return;
        }
        $config = $this->one('SELECT resumption FROM lhpresultconfig WHERE term = ? LIMIT 1', array($topic['term']));
        if (!$config || empty($config['resumption'])) {
            return;
        }
        $resumption = new DateTimeImmutable($config['resumption']);
        $today = new DateTimeImmutable('today');
        $currentWeek = $today < $resumption ? 0 : ((int) floor(($today->getTimestamp() - $resumption->getTimestamp()) / 604800) + 1);
        if ((int) $match[1] > $currentWeek) {
            throw new RuntimeException('That topic is scheduled for a future week. Ask an administrator to approve it first.');
        }
    }

    private function queuePortalNotices($assessmentId, $eventType)
    {
        $assessment = $this->assessment($assessmentId);
        $students = $this->all(
            'SELECT DISTINCT u.uname
             FROM lhpuser u
             WHERE u.status = 1 AND EXISTS (
                 SELECT 1 FROM cbt_assessment_assignments aa
                 WHERE aa.assessment_id = ? AND aa.status = \'eligible\'
                   AND ((aa.assignment_type = \'class\' AND aa.class_id = u.classid)
                     OR (aa.assignment_type = \'student\' AND aa.learner_id = u.uname))
             )',
            array($assessmentId)
        );
        $notification = $this->pdo->prepare(
            'INSERT INTO cbt_notification_targets
             (assessment_id, learner_id, event_type, channel, status, scheduled_at)
             VALUES (?, ?, ?, \'portal\', \'sent\', NOW())
             ON DUPLICATE KEY UPDATE status = VALUES(status), scheduled_at = VALUES(scheduled_at), sent_at = NOW()'
        );
        foreach ($students as $student) {
            $notification->execute(array($assessmentId, $student['uname'], $eventType));
        }

        $message = sprintf(
            '%s: %s (%s) opens %s. %d minutes. View in My Assessments.',
            ucfirst(str_replace('_', ' ', $eventType)), $assessment['title'], $assessment['sbjname'],
            date('j M Y, g:i a', strtotime($assessment['start_at'])), (int) $assessment['duration_minutes']
        );
        $message = mb_substr($message, 0, 254);
        $subject = mb_substr('CBT · ' . $assessment['title'], 0, 64);
        $hasClassAssignment = $this->scalar(
            'SELECT 1 FROM cbt_assessment_assignments
             WHERE assessment_id = ? AND assignment_type = \'class\' AND status = \'eligible\' LIMIT 1',
            array($assessmentId)
        );
        if (!$hasClassAssignment) {
            return;
        }
        $notice = $this->pdo->prepare(
            'INSERT INTO lhpnotice (term, refid, subject, message)
             SELECT ?, ?, ?, ? FROM DUAL
             WHERE NOT EXISTS (
                 SELECT 1 FROM lhpnotice WHERE term = ? AND refid = ? AND subject = ? AND message = ?
             )'
        );
        $notice->execute(array(
            $assessment['term'], $assessment['class_id'], $subject, $message,
            $assessment['term'], $assessment['class_id'], $subject, $message
        ));
    }

    private function normalizeOptions(array $input, $type)
    {
        if ($type === 'true_false') {
            $correct = isset($input['true_false_answer']) && strtolower((string) $input['true_false_answer']) === 'false' ? 'F' : 'T';
            return array(
                array('key' => 'T', 'text' => 'True', 'correct' => $correct === 'T', 'match_key' => null),
                array('key' => 'F', 'text' => 'False', 'correct' => $correct === 'F', 'match_key' => null),
            );
        }
        if (!in_array($type, array('single_choice', 'multiple_choice', 'matching', 'ordering'), true)) {
            return array();
        }
        $texts = isset($input['option_text']) && is_array($input['option_text']) ? $input['option_text'] : array();
        $correctValues = isset($input['correct_options']) && is_array($input['correct_options'])
            ? array_map('strval', $input['correct_options'])
            : array((string) (isset($input['correct_option']) ? $input['correct_option'] : ''));
        $matches = isset($input['match_key']) && is_array($input['match_key']) ? $input['match_key'] : array();
        $options = array();
        foreach ($texts as $index => $text) {
            $text = CbtSecurity::cleanText($text, 5000, true);
            if ($text === '') {
                continue;
            }
            $key = chr(65 + count($options));
            $options[] = array(
                'key' => $key,
                'text' => $text,
                'correct' => in_array((string) $index, $correctValues, true) || in_array($key, $correctValues, true),
                'match_key' => isset($matches[$index]) ? CbtSecurity::cleanText($matches[$index], 64, true) : null,
            );
        }
        if (count($options) < 2) {
            throw new InvalidArgumentException('Add at least two answer options.');
        }
        if (in_array($type, array('single_choice', 'multiple_choice'), true)) {
            $correctCount = count(array_filter($options, function ($option) { return $option['correct']; }));
            if ($correctCount < 1 || ($type === 'single_choice' && $correctCount !== 1)) {
                throw new InvalidArgumentException('Select the correct answer option.');
            }
        }
        return $options;
    }

    private function normalizeAcceptedAnswers(array $input, $type, array $options)
    {
        if (in_array($type, array('single_choice', 'multiple_choice', 'true_false'), true)) {
            return array_values(array_map(function ($option) { return $option['key']; }, array_filter($options, function ($option) { return $option['correct']; })));
        }
        if (in_array($type, array('fill_blank', 'short_answer'), true)) {
            $answers = isset($input['accepted_answer']) && is_array($input['accepted_answer'])
                ? $input['accepted_answer'] : preg_split('/\r\n|\r|\n/', (string) (isset($input['accepted_answer']) ? $input['accepted_answer'] : ''));
            $answers = array_values(array_filter(array_map(function ($answer) {
                return CbtSecurity::cleanText($answer, 500, true);
            }, $answers), function ($answer) { return $answer !== ''; }));
            if (!$answers) {
                throw new InvalidArgumentException('Add at least one accepted answer.');
            }
            return $answers;
        }
        if ($type === 'matching') {
            $answers = array_values(array_map(function ($option) {
                return $option['match_key'];
            }, $options));
            if (in_array(null, $answers, true) || in_array('', $answers, true)) {
                throw new InvalidArgumentException('Add the matching answer for every option.');
            }
            return $answers;
        }
        if ($type === 'ordering') {
            $ordered = $options;
            usort($ordered, function ($left, $right) {
                $leftOrder = $left['match_key'] !== null && $left['match_key'] !== '' ? $left['match_key'] : $left['key'];
                $rightOrder = $right['match_key'] !== null && $right['match_key'] !== '' ? $right['match_key'] : $right['key'];
                return strnatcasecmp((string) $leftOrder, (string) $rightOrder);
            });
            return array_values(array_map(function ($option) { return $option['key']; }, $ordered));
        }
        return null;
    }

    private function flag(array $input, $key, $default = false)
    {
        if (!array_key_exists($key, $input)) {
            return $default ? 1 : 0;
        }
        return in_array($input[$key], array(1, '1', true, 'true', 'on', 'yes'), true) ? 1 : 0;
    }

    private function safeMediaUrl($value)
    {
        $value = CbtSecurity::cleanText($value, 500, true);
        if ($value === '') {
            return null;
        }
        if (!preg_match('#^(https?://|[A-Za-z0-9_./-]+$)#i', $value)) {
            throw new InvalidArgumentException('The media URL is invalid.');
        }
        return $value;
    }

    private function nullableChoice($value, array $allowed)
    {
        $value = is_string($value) ? trim($value) : '';
        return in_array($value, $allowed, true) ? $value : null;
    }

    private function dateTime($value, $label)
    {
        try {
            $date = new DateTimeImmutable((string) $value);
        } catch (Throwable $exception) {
            throw new InvalidArgumentException($label . ' is invalid.');
        }
        return $date;
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
