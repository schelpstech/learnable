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
         WHERE s.status = 1 AND s.role = 't' AND s.sname <> 'codex_demo_instructor'
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
        ':username' => 'codex_demo_instructor',
        ':name' => 'Codex Demo Instructor',
        ':password' => $passwordHash,
        ':phone' => $sourceInstructor['sfone'],
        ':email' => 'codex.demo.instructor@example.test',
    ));

    $allocationStatement = $pdo->prepare(
        "INSERT INTO lhpalloc (term, classname, subject, staffid, supro, classid, sbjid)
         SELECT a.term, a.classname, a.subject, 'codex_demo_instructor', a.supro, a.classid, a.sbjid
         FROM lhpalloc a
         WHERE a.staffid = :source AND a.term = :term
           AND NOT EXISTS (
               SELECT 1 FROM lhpalloc existing
               WHERE existing.staffid = 'codex_demo_instructor'
                 AND existing.term = a.term
                 AND existing.classid = a.classid
                 AND existing.sbjid = a.sbjid
           )"
    );
    $allocationStatement->execute(array(
        ':source' => $sourceInstructor['sname'],
        ':term' => $activeTerm,
    ));

    $sourceLearnerStatement = $pdo->prepare(
        "SELECT u.gender, u.dob, u.classid, u.picture, u.numb
         FROM lhpuser u
         WHERE u.status = 1 AND u.uname <> 'codex_demo_student'
           AND EXISTS (SELECT 1 FROM lhpalloc a WHERE a.classid = u.classid AND a.term = :term)
         ORDER BY u.id ASC
         LIMIT 1"
    );
    $sourceLearnerStatement->execute(array(':term' => $activeTerm));
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
        ':username' => 'codex_demo_student',
        ':gender' => $sourceLearner['gender'],
        ':dob' => $sourceLearner['dob'],
        ':password' => $passwordHash,
        ':email' => 'codex.demo.student@example.test',
        ':classid' => $sourceLearner['classid'],
        ':name' => 'Codex Demo Learner',
        ':picture' => $sourceLearner['picture'] !== '' ? $sourceLearner['picture'] : 'nopix.jpg',
        ':phone' => $sourceLearner['numb'],
    ));

    $pdo->commit();

    $allocationCountStatement = $pdo->prepare(
        "SELECT COUNT(*) FROM lhpalloc WHERE staffid = 'codex_demo_instructor' AND term = :term"
    );
    $allocationCountStatement->execute(array(':term' => $activeTerm));

    echo "Demo accounts ready:\n";
    echo "- codex_demo_admin (Administrator)\n";
    echo "- codex_demo_instructor (Instructor, " . (int) $allocationCountStatement->fetchColumn() . " active-term allocations)\n";
    echo "- codex_demo_student (Learner, class " . $sourceLearner['classid'] . ")\n";
    echo "Password source: E2E_DEMO_PASSWORD in the ignored local .env file.\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    throw $exception;
}
