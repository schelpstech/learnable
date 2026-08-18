<?php

require __DIR__ . '/../config/database.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$password = (string) app_env('E2E_DEMO_PASSWORD', '');
if (strlen($password) < 12) {
    throw new RuntimeException('Set E2E_DEMO_PASSWORD to at least 12 characters in the local .env file.');
}

$pdo = database_pdo();
$pdo->beginTransaction();

try {
    $activeTerm = $pdo->query('SELECT term FROM lpterm WHERE status = 1 LIMIT 1')->fetchColumn();
    if (!$activeTerm) {
        throw new RuntimeException('An active term is required before demo accounts can be created.');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $adminStatement = $pdo->prepare(
        'INSERT INTO `123admin` (dname, dpwd) VALUES (:username, :password) '
        . 'ON DUPLICATE KEY UPDATE dpwd = VALUES(dpwd)'
    );
    $adminStatement->execute(array(
        ':username' => 'codex_demo_admin',
        ':password' => $passwordHash,
    ));

    $sourceInstructorStatement = $pdo->prepare(
        "SELECT s.sname, s.sfone, s.semail
         FROM lhpstaff s
         LEFT JOIN lhpalloc a ON a.staffid = s.sname AND a.term = :term
         WHERE s.status = 1 AND s.role = 't' AND s.sname <> 'codex_demo_teacher'
         GROUP BY s.sname, s.sfone, s.semail
         ORDER BY COUNT(a.aid) DESC, s.staffid ASC
         LIMIT 1"
    );
    $sourceInstructorStatement->execute(array(':term' => $activeTerm));
    $sourceInstructor = $sourceInstructorStatement->fetch();
    if (!$sourceInstructor) {
        throw new RuntimeException('No active instructor is available as a safe demo-data template.');
    }

    $instructorStatement = $pdo->prepare(
        "INSERT INTO lhpstaff (sname, staffname, spwd, sfone, semail, status, role)
         VALUES (:username, :name, :password, :phone, :email, 1, 't')
         ON DUPLICATE KEY UPDATE staffname = VALUES(staffname), spwd = VALUES(spwd),
             sfone = VALUES(sfone), semail = VALUES(semail), status = 1, role = 't'"
    );
    $instructorStatement->execute(array(
        ':username' => 'codex_demo_teacher',
        ':name' => 'Codex Demo Instructor',
        ':password' => $passwordHash,
        ':phone' => $sourceInstructor['sfone'],
        ':email' => 'codex.demo.instructor@example.test',
    ));

    $allocationStatement = $pdo->prepare(
        "INSERT INTO lhpalloc (term, classname, subject, staffid, supro, classid, sbjid)
         SELECT a.term, a.classname, a.subject, 'codex_demo_teacher', a.supro, a.classid, a.sbjid
         FROM lhpalloc a
         WHERE a.staffid = :source AND a.term = :term
           AND NOT EXISTS (
               SELECT 1 FROM lhpalloc existing
               WHERE existing.staffid = 'codex_demo_teacher'
                 AND existing.term = a.term
                 AND existing.classid = a.classid
                 AND existing.sbjid = a.sbjid
           )"
    );
    $allocationStatement->execute(array(
        ':source' => $sourceInstructor['sname'],
        ':term' => $activeTerm,
    ));

    // A separate demo-only class lets the instructor timetable workflow be
    // exercised without changing a real class teacher's ownership.
    $pdo->exec("INSERT INTO lhpclass (classname)
        SELECT 'Codex Demo' FROM DUAL
        WHERE NOT EXISTS (SELECT 1 FROM lhpclass WHERE classname = 'Codex Demo')");
    $demoClassId = $pdo->query("SELECT classid FROM lhpclass WHERE classname = 'Codex Demo' ORDER BY classid LIMIT 1")->fetchColumn();
    $classTeacherStatement = $pdo->prepare(
        "INSERT INTO lhpclassalloc (classid, tutorid, term)
         SELECT :classid, 'codex_demo_teacher', :term FROM DUAL
         WHERE NOT EXISTS (
             SELECT 1 FROM lhpclassalloc
             WHERE classid = :classid_check AND tutorid = 'codex_demo_teacher' AND term = :term_check
         )"
    );
    $classTeacherStatement->execute(array(
        ':classid' => $demoClassId,
        ':term' => $activeTerm,
        ':classid_check' => $demoClassId,
        ':term_check' => $activeTerm,
    ));

    $sourceLearnerStatement = $pdo->prepare(
        "SELECT u.uname, u.gender, u.dob, u.classid, u.picture, u.numb
         FROM lhpuser u
         WHERE u.status = 1 AND u.uname <> 'codex_demo_std'
           AND EXISTS (
               SELECT 1 FROM lhpalloc a
               WHERE a.classid = u.classid AND a.term = :term AND a.staffid = :source_instructor
           )
           AND EXISTS (SELECT 1 FROM lhpresultrecord r WHERE r.lid = u.uname AND r.term = :result_term)
         ORDER BY u.id ASC
         LIMIT 1"
    );
    $sourceLearnerStatement->execute(array(
        ':term' => $activeTerm,
        ':source_instructor' => $sourceInstructor['sname'],
        ':result_term' => $activeTerm,
    ));
    $sourceLearner = $sourceLearnerStatement->fetch();
    if (!$sourceLearner) {
        throw new RuntimeException('No active learner is available as a safe demo-data template.');
    }

    $learnerStatement = $pdo->prepare(
        "INSERT INTO lhpuser (uname, gender, dob, upwd, email, classid, fname, status, picture, numb)
         VALUES (:username, :gender, :dob, :password, :email, :classid, :name, 1, :picture, :phone)
         ON DUPLICATE KEY UPDATE gender = VALUES(gender), dob = VALUES(dob), upwd = VALUES(upwd),
             email = VALUES(email), classid = VALUES(classid), fname = VALUES(fname), status = 1,
             picture = VALUES(picture), numb = VALUES(numb)"
    );
    $learnerStatement->execute(array(
        ':username' => 'codex_demo_std',
        ':gender' => $sourceLearner['gender'],
        ':dob' => $sourceLearner['dob'],
        ':password' => $passwordHash,
        ':email' => 'codex.demo.student@example.test',
        ':classid' => $sourceLearner['classid'],
        ':name' => 'Codex Demo Learner',
        ':picture' => 'codex-demo-passport-missing.jpg',
        ':phone' => $sourceLearner['numb'],
    ));

    $resultCopyStatement = $pdo->prepare(
        "INSERT INTO lhpresultrecord (term, classid, subjid, lid, score, examscore, totalscore)
         SELECT source.term, source.classid, source.subjid, 'codex_demo_std', source.score, source.examscore, source.totalscore
         FROM lhpresultrecord source
         INNER JOIN lpterm term_order ON term_order.term = source.term
         WHERE source.lid = :source
           AND term_order.tid <= (SELECT tid FROM lpterm WHERE term = :active_term LIMIT 1)
           AND term_order.tid > (SELECT tid FROM lpterm WHERE term = :active_term_floor LIMIT 1) - 3
           AND NOT EXISTS (
               SELECT 1 FROM lhpresultrecord existing
               WHERE existing.lid = 'codex_demo_std' AND existing.term = source.term AND existing.subjid = source.subjid
           )"
    );
    $resultCopyStatement->execute(array(
        ':source' => $sourceLearner['uname'],
        ':active_term' => $activeTerm,
        ':active_term_floor' => $activeTerm,
    ));

    $affectiveCopyStatement = $pdo->prepare(
        "INSERT INTO lhpaffective (term, classid, uname, total_present, rating1, rating2, rating3, rating4, rating5, comment)
         SELECT source.term, source.classid, 'codex_demo_std', source.total_present,
                source.rating1, source.rating2, source.rating3, source.rating4, source.rating5, source.comment
         FROM lhpaffective source
         INNER JOIN lpterm term_order ON term_order.term = source.term
         WHERE source.uname = :source
           AND term_order.tid <= (SELECT tid FROM lpterm WHERE term = :active_term LIMIT 1)
           AND term_order.tid > (SELECT tid FROM lpterm WHERE term = :active_term_floor LIMIT 1) - 3
           AND NOT EXISTS (
               SELECT 1 FROM lhpaffective existing
               WHERE existing.uname = 'codex_demo_std' AND existing.term = source.term
           )"
    );
    $affectiveCopyStatement->execute(array(
        ':source' => $sourceLearner['uname'],
        ':active_term' => $activeTerm,
        ':active_term_floor' => $activeTerm,
    ));

    $pdo->commit();

    $allocationCountStatement = $pdo->prepare(
        "SELECT COUNT(*) FROM lhpalloc WHERE staffid = 'codex_demo_teacher' AND term = :term"
    );
    $allocationCountStatement->execute(array(':term' => $activeTerm));

    echo "Demo accounts ready:\n";
    echo "- codex_demo_admin (Administrator)\n";
    echo "- codex_demo_teacher (Instructor and class teacher, " . (int) $allocationCountStatement->fetchColumn() . " active-term allocations)\n";
    echo "- codex_demo_std (Learner with multi-term demo results, class " . $sourceLearner['classid'] . ")\n";
    echo "Password source: E2E_DEMO_PASSWORD in the ignored local .env file.\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    throw $exception;
}
