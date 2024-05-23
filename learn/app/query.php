<?php

// Include start file
$startFile = file_exists('../../controller/start.inc.php') ? '../../controller/start.inc.php' : '../controller/start.inc.php';
include $startFile;

// Initialize variables
$sch_details = $active_term = $active_session = $list_note = $list_task = $alist_scheme = $list_scheme = [];
$view_note = $view_task = $modify_note = $modify_task = [];
$learner_profile = $learner_class = $class_teacher = [];
$subject_count = $note_count = $work_count = $topic_count = 0;
$task_list = $recent = $subject_list = $list_work = $view_bill = [];
$bill_sum = $bill_discount = $history = $bill_paid = $bill_transaction = [];
$available_result = $view_result = $show_report = $show_result = $show_affective = $aggregate = [];
$staff_details = $statistics = $report = $class_subject_allocated = $class_allocated = $all_subject_allocated = [];
$admin_details = [];

// Function to get rows
function getRows($tblName, $conditions)
{
    global $model;
    return $model->getRows($tblName, $conditions);
}

// School Details
$sch_details = getRows('lhpschool', ['return_type' => 'single']);

// Active Term
$active_term = getRows('lpterm', ['return_type' => 'single', 'where' => ['status' => 1]]);

// Active Session
$active_session = getRows('lhpsession', ['return_type' => 'single', 'where' => ['status' => 1]]);

// User Details
if (isset($_SESSION['active'])) {
    if ($_SESSION['user_type'] === "Learner") {
        // Learners Profile
        $learner_profile = getRows('lhpuser', ['return_type' => 'single', 'where' => ['uname' => $_SESSION['active']]]);

        // Age Calculator
        if (!empty($learner_profile['dob'])) {
            $tz = new DateTimeZone('Africa/Lagos');
            $age = DateTime::createFromFormat('Y-m-d', $learner_profile['dob'], $tz)
                ->diff(new DateTime('now', $tz))
                ->y;
        }

        // Class Finder
        $learner_class = getRows('lhpclass', ['return_type' => 'single', 'where' => ['classid' => $learner_profile['classid']]]);

        // Class Teacher Finder
        $class_teacher = getRows('lhpclassalloc', [
            'return_type' => 'single',
            'join' => 'lhpstaff on lhpclassalloc.tutorid = lhpstaff.sname',
            'where' => ['classid' => $learner_profile['classid'], 'term' => $active_term['term']]
        ]);

        // Subjects Counter
        $subject_count = getRows('lhpalloc', [
            'return_type' => 'count',
            'where' => ['classid' => $learner_profile['classid'], 'term' => $active_term['term']]
        ]);

        // Notes Counter
        $note_count = getRows('lhpalloc', [
            'join' => 'lhpnote on lhpalloc.sbjid = lhpnote.sbjid',
            'return_type' => 'count',
            'where' => ['lhpalloc.classid' => $learner_profile['classid'], 'lhpalloc.term' => $active_term['term'], 'lhpnote.status' => 1]
        ]);

        // Assessment Counter
        $work_count = getRows('lhpalloc', [
            'join' => 'lhpquestion on lhpalloc.sbjid = lhpquestion.sbjid',
            'return_type' => 'count',
            'where' => ['lhpalloc.classid' => $learner_profile['classid'], 'lhpalloc.term' => $active_term['term'], 'lhpquestion.status' => 1]
        ]);

        // Outlined Topics Counter
        $topic_count = getRows('lhpalloc', [
            'join' => 'lhpscheme on lhpalloc.sbjid = lhpscheme.subject',
            'return_type' => 'count',
            'where' => ['lhpalloc.classid' => $learner_profile['classid'], 'lhpalloc.term' => $active_term['term'], 'lhpscheme.status' => 1]
        ]);

        // Assignment Finder
        $task_list = getRows('lhpquestion', [
            'join' => 'lhpscheme on lhpquestion.topicid = lhpscheme.schmid',
            'leftjoin' => 'lhpsubject on lhpquestion.sbjid = lhpsubject.sbjid',
            'order_by' => 'lhpquestion.rectime DESC',
            'limit' => '5',
            'where' => ['lhpscheme.classname' => $learner_profile['classid'], 'lhpquestion.term' => $active_term['term'], 'lhpquestion.status' => 1]
        ]);

        // Recent Activity
        $recent = getRows('lhpnotice', [
            'order_by' => 'rectime DESC',
            'limit' => '5'
        ]);

        // Submitted Assignment
        $submitted = getRows('lhpquestion', [
            'return_type' => 'count',
            'where' => ['stdid' => $learner_profile['uname'], 'status' => 1]
        ]);

        // School Bill
        $bill_sum = getRows('lhpbill', [
            'return_type' => 'sum',
            'where' => ['uname' => $learner_profile['uname'], 'status' => 1]
        ]);

        $bill_discount = getRows('lhpdiscount', ['return_type' => 'single']);
        $history = getRows('lhpbill', ['where' => ['uname' => $learner_profile['uname']], 'return_type' => 'results']);
        $bill_paid = getRows('lhpbill', [
            'return_type' => 'sum',
            'where' => ['uname' => $learner_profile['uname'], 'status' => 1]
        ]);
        $bill_transaction = getRows('lhptransaction', [
            'return_type' => 'results',
            'where' => ['uname' => $learner_profile['uname'], 'status' => 1]
        ]);

        // School Results
        $available_result = getRows('lhpalloc', [
            'join' => 'lhpresult on lhpalloc.sbjid = lhpresult.subject',
            'return_type' => 'count',
            'where' => ['lhpalloc.classid' => $learner_profile['classid'], 'lhpalloc.term' => $active_term['term'], 'lhpresult.status' => 1]
        ]);

        $view_result = getRows('lhpalloc', [
            'join' => 'lhpresult on lhpalloc.sbjid = lhpresult.subject',
            'return_type' => 'results',
            'where' => ['lhpalloc.classid' => $learner_profile['classid'], 'lhpalloc.term' => $active_term['term'], 'lhpresult.status' => 1]
        ]);
    }

    if ($_SESSION['user_type'] === "Staff") {
        // Staff Details
        $staff_details = getRows('lhpstaff', ['return_type' => 'single', 'where' => ['sname' => $_SESSION['active']]]);

        // Statistics
        $statistics = getRows('lhpalloc', [
            'join' => 'lhpnote on lhpalloc.sbjid = lhpnote.sbjid',
            'return_type' => 'count',
            'where' => ['lhpalloc.tutorid' => $staff_details['sname'], 'lhpalloc.term' => $active_term['term']]
        ]);
    }

    if ($_SESSION['user_type'] === "Admin") {
        // Admin Details
        $admin_details = getRows('lhpadmin', ['return_type' => 'single', 'where' => ['username' => $_SESSION['active']]]);
    }
}


