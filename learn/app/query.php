<?php

if (file_exists('../../controller/start.inc.php')) {
    include '../../controller/start.inc.php';
} else {
    include '../controller/start.inc.php';
};

//School Details
$tblName = 'lhpschool';
$conditions = array(
    'return_type' => 'single',
);
$sch_details = $model->getRows($tblName, $conditions);

//Active Term
$tblName = 'lpterm';
$conditions = array(
    'return_type' => 'single',
    'where' => array(
        'status' => 1,
    )
);
$active_term = $model->getRows($tblName, $conditions);

//Active Session
$tblName = 'lhpsession';
$conditions = array(
    'return_type' => 'single',
    'where' => array(
        'status' => 1,
    )
);
$active_session = $model->getRows($tblName, $conditions);


//List of Notes
if (isset($_SESSION['subjectid']) && isset($_SESSION['pageid']) && $_SESSION['pageid'] == 'note') {
    $tblName = 'lhpnote';
    $conditions = array(
        'where' => array(
            'lhpnote.sbjid' => $_SESSION['subjectid'],
            'lhpnote.term' => $active_term['term'],
            'lhpnote.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpnote.sbjid = lhpsubject.sbjid',
            'lhpscheme' => ' on lhpnote.topicid = lhpscheme.schmid ',
        ),
        'order_by' => 'lhpscheme.week ASC',
    );
    $list_note = $model->getRows($tblName, $conditions);
}
//List of Assessments
if (isset($_SESSION['subjectid']) && isset($_SESSION['pageid']) &&  $_SESSION['pageid'] == 'task') {
    $tblName = 'lhpquestion';
    $conditions = array(
        'where' => array(
            'lhpquestion.sbjid' => $_SESSION['subjectid'],
            'lhpquestion.term' => $active_term['term'],
            'lhpquestion.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpquestion.sbjid = lhpsubject.sbjid',
            'lhpscheme' => ' on lhpquestion.topicid = lhpscheme.schmid ',
        ),
        'order_by' => 'lhpscheme.week ASC',
    );
    $list_task = $model->getRows($tblName, $conditions);
}

//List of Topics- Scheme of work
if (isset($_SESSION['ref']) && isset($_SESSION['pageid']) && $_SESSION['pageid'] == 'scheme') {
    $tblName = 'lhpscheme';
    $conditions = array(
        'where' => array(
            'lhpscheme.subject' => ($_SESSION['ref']),
            'lhpscheme.term' => $active_term['term'],
            'lhpscheme.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpscheme.subject = lhpsubject.sbjid',
            'lhpstaff' => ' on lhpscheme.staffid = lhpstaff.sname ',
        ),
        'return_type' => 'single',
    );
    $alist_scheme = $model->getRows($tblName, $conditions);

    $conditions = array(
        'where' => array(
            'lhpscheme.subject' => $_SESSION['ref'],
            'lhpscheme.term' => $active_term['term'],
            'lhpscheme.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpscheme.subject = lhpsubject.sbjid',
            'lhpstaff' => ' on lhpscheme.staffid = lhpstaff.sname ',
        ),
        'order_by' => 'lhpscheme.week ASC',
    );
    $list_scheme = $model->getRows($tblName, $conditions);
}

if (isset($_SESSION['pageid']) && $_SESSION['pageid'] == 'resources' && isset($_SESSION['item_ref']) && $_SESSION['item'] == 'modify_topic') {

    $tblName = 'lhpscheme';
    $conditions = array(
        'where' => array(
            'lhpscheme.schmid' => $_SESSION['item_ref'],
            'lhpscheme.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpscheme.subject = lhpsubject.sbjid',
            'lhpstaff' => ' on lhpscheme.staffid = lhpstaff.sname ',
            'lhpclass' => ' on lhpscheme.classname = lhpclass.classid ',
        ),
        'return_type' => 'single',
    );
    $mod_scheme = $model->getRows($tblName, $conditions);
}

if (isset($_SESSION['pageid']) && $_SESSION['pageid'] == 'resources' && isset($_SESSION['item_ref']) && $_SESSION['item'] == 'modify_note') {

    $tblName = 'lhpnote';
    $conditions = array(
        'return_type' => 'single',
        'where' => array(
            'lhpnote.noteid' => $_SESSION['item_ref'],
            'lhpnote.term' => $active_term['term'],
            'lhpnote.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpnote.sbjid = lhpsubject.sbjid',
            'lhpscheme' => ' on lhpnote.topicid = lhpscheme.schmid ',
            'lhpclass' => ' on lhpscheme.classname = lhpclass.classid ',
        ),
    );
    $modify_note = $model->getRows($tblName, $conditions);
}
if (isset($_SESSION['pageid']) && $_SESSION['pageid'] == 'resources' && isset($_SESSION['item_ref']) && $_SESSION['item'] == 'modify_task') {

    $tblName = 'lhpquestion';
    $conditions = array(
        'return_type' => 'single',
        'where' => array(
            'lhpquestion.questid' => $_SESSION['item_ref'],
            'lhpquestion.term' => $active_term['term'],
            'lhpquestion.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpquestion.sbjid = lhpsubject.sbjid',
            'lhpscheme' => ' on lhpquestion.topicid = lhpscheme.schmid ',
            'lhpclass' => ' on lhpscheme.classname = lhpclass.classid ',
        ),
    );
    $modify_task = $model->getRows($tblName, $conditions);
}

//Note Details
if (isset($_SESSION['ref']) && isset($_SESSION['pageid']) && $_SESSION['pageid'] == 'note') {
    $tblName = 'lhpnote';
    $conditions = array(
        'return_type' => 'single',
        'where' => array(
            'lhpnote.noteid' => $_SESSION['ref'],
            'lhpnote.term' => $active_term['term'],
            'lhpnote.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpnote.sbjid = lhpsubject.sbjid',
            'lhpscheme' => ' on lhpnote.topicid = lhpscheme.schmid ',
            'lhpstaff' => ' on lhpnote.staffid = lhpstaff.sname',
        ),
    );
    $view_note = $model->getRows($tblName, $conditions);
}


//Assignment Details
if (isset($_SESSION['ref']) && isset($_SESSION['pageid']) && $_SESSION['pageid'] == 'task') {
    $tblName = 'lhpquestion';
    $conditions = array(
        'return_type' => 'single',
        'where' => array(
            'lhpquestion.questid' => $_SESSION['ref'],
            'lhpquestion.term' => $active_term['term'],
            'lhpquestion.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpquestion.sbjid = lhpsubject.sbjid',
            'lhpscheme' => ' on lhpquestion.topicid = lhpscheme.schmid ',
            'lhpstaff' => ' on lhpquestion.staffid = lhpstaff.sname',
        ),
    );
    $view_task = $model->getRows($tblName, $conditions);
}


//User Details - Learner
if (isset($_SESSION['active']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === "Learner") {

    //Learners Profile

    $tblName = 'lhpuser';
    $conditions = array(
        'return_type' => 'single',
        'where' => array(
            'uname' => $_SESSION['active'],
        )
    );
    $learner_profile = $model->getRows($tblName, $conditions);

    //Age Calculator
    if (!empty($learner_profile['dob'])) {
        $tz  = new DateTimeZone('Africa/Lagos');
        $age = DateTime::createFromFormat('Y-m-d', $learner_profile['dob'], $tz)
            ->diff(new DateTime('now', $tz))
            ->y;
    }

    //Class Finder

    $tblName = 'lhpclass';
    $conditions = array(
        'return_type' => 'single',
        'where' => array(
            'classid' => $learner_profile['classid'],
        )
    );
    $learner_class = $model->getRows($tblName, $conditions);

    //Class Teacher Finder
    $tblName = 'lhpclassalloc';
    $conditions = array(
        'join' => 'lhpstaff on lhpclassalloc.tutorid = lhpstaff.sname',
        'return_type' => 'single',
        'where' => array(
            'classid' => $learner_profile['classid'],
            'term' => $active_term['term'],
        )
    );
    $class_teacher = $model->getRows($tblName, $conditions);


    //Subjects Counter
    $tblName = 'lhpalloc';
    $conditions = array(
        'return_type' => 'count',
        'where' => array(
            'classid' => $learner_profile['classid'],
            'term' => $active_term['term'],
        )
    );
    $subject_count = $model->getRows($tblName, $conditions);

    //Notes Counter
    $tblName = 'lhpalloc';
    $conditions = array(
        'join' => 'lhpnote on lhpalloc.sbjid = lhpnote.sbjid',
        'return_type' => 'count',
        'where' => array(
            'lhpalloc.classid' => $learner_profile['classid'],
            'lhpalloc.term' => $active_term['term'],
            'lhpnote.status' => 1,
        )
    );
    $note_count = $model->getRows($tblName, $conditions);

    //Assessment Counter
    $tblName = 'lhpalloc';
    $conditions = array(
        'join' => 'lhpquestion on lhpalloc.sbjid = lhpquestion.sbjid',
        'return_type' => 'count',
        'where' => array(
            'lhpalloc.classid' => $learner_profile['classid'],
            'lhpalloc.term' => $active_term['term'],
            'lhpquestion.status' => 1,
        )
    );
    $work_count = $model->getRows($tblName, $conditions);

    //Outlined Topics Counter
    $tblName = 'lhpalloc';
    $conditions = array(
        'join' => 'lhpscheme on lhpalloc.sbjid = lhpscheme.subject',
        'return_type' => 'count',
        'where' => array(
            'lhpalloc.classid' => $learner_profile['classid'],
            'lhpalloc.term' => $active_term['term'],
            'lhpscheme.status' => 1,
        )
    );
    $topic_count = $model->getRows($tblName, $conditions);

    //Assignment Finder
    $tblName = 'lhpquestion';
    $conditions = array(

        'join' => 'lhpscheme on lhpquestion.topicid = lhpscheme.schmid',
        'leftjoin' => 'lhpsubject on lhpquestion.sbjid = lhpsubject.sbjid',
        'order_by' => 'lhpquestion.rectime DESC',
        'limit' => '5',
        'where' => array(
            'lhpscheme.classname' => $learner_profile['classid'],
            'lhpquestion.term' => $active_term['term'],
            'lhpquestion.status' => 1,
        )
    );
    $task_list = $model->getRows($tblName, $conditions);

    //Recent Activity
    $tblName = 'lhpnotice';
    $conditions = array(
        'order_by' => 'rectime DESC',
        'limit' => '5',
        'where' => array(
            'refid' => $learner_profile['classid'],
            'term' => $active_term['term'],
        )
    );
    $recent = $model->getRows($tblName, $conditions);

    //Subject List
    $tblName = 'lhpalloc';
    $conditions = array(
        'select' => 'lhpalloc.staffid, lhpstaff.sname, lhpstaff.staffname, lhpalloc.sbjid as sbjref, lhpsubject.sbjid, lhpsubject.sbjname, 
                        lhpnote.sbjid, lhpalloc.classid, lhpalloc.term, lhpfeedback.fid, lhpclass.classid, lhpclass.classname, 
                        (SELECT count(lhpnote.sbjid) FROM lhpnote WHERE  lhpnote.sbjid = lhpalloc.sbjid and lhpnote.status = 1 and lhpnote.term ="' . $active_term["term"] . '") as note ,
                        (SELECT count(lhpquestion.sbjid) FROM lhpquestion WHERE  lhpquestion.sbjid = lhpalloc.sbjid and lhpquestion.status = 1 and lhpquestion.term ="' . $active_term['term'] . '") as task,
                        (SELECT count(lhpfeedback.sbjid) FROM lhpfeedback WHERE lhpfeedback.sbjid = lhpalloc.sbjid and lhpfeedback.stdid = "' . $_SESSION['active'] . '" and lhpfeedback.term ="' . $active_term['term'] . '") as feedback,
                        (SELECT count(lhpscheme.subject) FROM lhpscheme WHERE lhpalloc.sbjid = lhpscheme.subject and lhpscheme.status = 1 and lhpscheme.term ="' . $active_term['term'] . '") as topic',
        'where' => array(
            'lhpalloc.classid' => $learner_profile['classid'],
            'lhpalloc.term' => $active_term['term'],
        ),
        'joinl' => array(
            'lhpclass' => ' on lhpalloc.classid = lhpclass.classid',
            'lhpstaff' => ' on lhpalloc.staffid = lhpstaff.sname',
            'lhpsubject' => ' on lhpalloc.sbjid = lhpsubject.sbjid',
            'lhpnote' => ' on lhpalloc.sbjid = lhpnote.sbjid',
            'lhpquestion' => ' on lhpalloc.sbjid = lhpquestion.sbjid',
            'lhpfeedback' => ' on lhpalloc.sbjid = lhpfeedback.sbjid',
            'lhpscheme' => ' on lhpalloc.sbjid = lhpscheme.subject',
        ),
        'group_by' => 'lhpalloc.sbjid',
    );
    $subject_list = $model->getRows($tblName, $conditions);

    //List of Submitted Assignments
    if (isset($_SESSION['subjectid'])) {
        $tblName = 'lhpfeedback';
        $conditions = array(
            'where' => array(
                'lhpfeedback.sbjid' => $_SESSION['subjectid'],
                'lhpfeedback.term' => $active_term['term'],
                'lhpfeedback.stdid' => $learner_profile['uname'],
            ),
            'joinl' => array(
                'lhpsubject' => ' on lhpfeedback.sbjid = lhpsubject.sbjid',
                'lhpscheme' => ' on lhpfeedback.tid = lhpscheme.schmid ',
            ),
            'order_by' => 'lhpscheme.week ASC',
        );
        $list_work = $model->getRows($tblName, $conditions);
    }

    //Individual School Bill

    $tblName = 'lhpassignedfee';
    $conditions = array(
        'where' => array(
            'lhpassignedfee.stdid' => $_SESSION['active'],
            'lhpassignedfee.term' => $active_term['term'],
            'lhpassignedfee.status' => 1,
        ),
        'joinl' => array(
            'lhpfeelist' => ' on lhpassignedfee.feeid = lhpfeelist.feeid',
        ),
    );
    $view_bill = $model->getRows($tblName, $conditions);

    //billed school fees
    $tblName = 'lhpassignedfee';
    $conditions = array(
        'select' => 'SUM(amount) as schfee',
        'return_type' => 'single',
        'where' => array(
            'stdid' => $_SESSION['active'],
            'classid' => $learner_profile['classid'],
            'term' => $active_term['term'],
            'status' => 1,
        )
    );
    $bill_sum = $model->getRows($tblName, $conditions);

    //discount allowed on  school fees
    $tblName = 'lhpassignedfee';
    $conditions = array(
        'select' => 'SUM(discount) as discount',
        'return_type' => 'single',
        'where' => array(
            'stdid' => $learner_profile['uname'],
            'classid' => $learner_profile['classid'],
            'term' => $active_term['term'],
            'status' => 1,
        )
    );
    $bill_discount = $model->getRows($tblName, $conditions);

    //Transaction History
    $tblName = 'lhptransaction';
    $conditions = array(
        'order_by' => 'paydate DESC',
        'where' => array(
            'stdid' => $learner_profile['uname'],
        )
    );
    $history = $model->getRows($tblName, $conditions);


    //Total amount paid this term
    $tblName = 'lhptransaction';
    $conditions = array(
        'select' => 'SUM(amount) as paid',
        'return_type' => 'single',
        'where' => array(
            'stdid' => $learner_profile['uname'],
            'classid' => $learner_profile['classid'],
            'term' => $active_term['term'],
            'status' => 1,
        )
    );
    $bill_paid = $model->getRows($tblName, $conditions);

    //Count Payments this term
   $tblName = 'lhptransaction';
   $conditions = [
       'return_type' => 'count',
       'where' => [
           'stdid' => $learner_profile['uname'],
           'classid' => $learner_profile['classid'],
           'term' => $active_term['term'],
           'status' => 1,
       ]
   ];
   $bill_transaction = $model->getRows($tblName, $conditions);

           //Available Results 

           $tblName = 'lhpresultrecord';
           $conditions = array(
               'select' => 'Distinct lhpresultrecord.term, lhpresultconfig.status',
               'where' => array(
                   'lhpresultrecord.lid' => $learner_profile['uname'],
               ),
               'joinl' => array(
                   'lhpresultconfig' => ' on lhpresultrecord.term = lhpresultconfig.term',
               ),
               'order_by' => 'lhpresultrecord.term',
           );
           $available_result = $model->getRows($tblName, $conditions);
           
   //Results
    $tblName = 'lhpresultconfig';
    $conditions = array(
        'return_type' => 'single',
        'where' => array(
            'term' => $active_term['term'],
        )
    );
    $view_result = $model->getRows($tblName);
    $search_result = $model->getRows($tblName, $conditions);

    if (isset($_SESSION['ref']) && isset($_SESSION['pageid']) &&  $_SESSION['pageid'] == 'midterm_result') {
        //Midterm Result
        $tblName = 'lhpresultrecord';
        $conditions = array(
            'where' => array(
                'lhpresultrecord.lid' => $learner_profile['uname'],
                'lhpresultrecord.term' => $_SESSION['ref'],
            ),
            'joinl' => array(
                'lhpsubject' => ' on lhpresultrecord.subjid = lhpsubject.sbjid',
            ),
            'order_by' => 'lhpsubject.sbjname',
        );
        $show_report = $model->getRows($tblName, $conditions);
    }

    if (isset($_SESSION['ref']) && isset($_SESSION['pageid']) &&  $_SESSION['pageid'] == 'result') {



        //1st and 2nd term result 
        $tblName = 'lhpresultrecord';
        $conditions = array(
            'where' => array(
                'lhpresultrecord.lid' => $learner_profile['uname'],
                'lhpresultrecord.term' => $_SESSION['ref'],
            ),
            'joinl' => array(
                'lhpsubject' => ' on lhpresultrecord.subjid = lhpsubject.sbjid',
            ),
            'order_by' => 'lhpsubject.sbjname',
        );
        $show_result = $model->getRows($tblName, $conditions);

        //1st and 2nd term Affective Domain 
        $tblName = 'lhpaffective';
        $conditions = array(
            'return_type' => 'single',
            'where' => array(
                'lhpaffective.uname' => $learner_profile['uname'],
                'lhpaffective.term' => $_SESSION['ref'],
            ),
            'joinl' => array(
                'lhpclass' => ' on lhpaffective.classid = lhpclass.classid',
                'lhpresultconfig' => ' on lhpaffective.term = lhpresultconfig.term',
            ),
        );
        $show_affective = $model->getRows($tblName, $conditions);

        //Aggregate Score 1st and 2nd term
        $tblName = 'lhpresultrecord';
        $conditions = array(
            'return_type' => 'single',
            'select' => ' 
                    (SELECT SUM(totalscore) FROM lhpresultrecord where lid = "' . $learner_profile["uname"] . '" and term ="' . $_SESSION["ref"] . '") as sumscore, 
                    (SELECT COUNT(totalscore) FROM lhpresultrecord where lid = "' . $learner_profile["uname"] . '" and term ="' . $_SESSION["ref"] . '") as countscore,
                    (SELECT AVG(totalscore) FROM lhpresultrecord where lid = "' . $learner_profile["uname"] . '" and term ="' . $_SESSION["ref"] . '") as avgscore
                ',
        );
        $aggregate = $model->getRows($tblName, $conditions);
    }
} elseif (isset($_SESSION['active']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === "Instructor") {

    //User Details - Staff
    $tblName = 'lhpstaff';
    $conditions = array(
        'return_type' => 'single',
        'where' => array(
            'sname' => $_SESSION['active'],
        ),
    );
    $staff_details = $model->getRows($tblName, $conditions);

    $tblName = 'lhpalloc';
    $conditions = array(
        'return_type' => 'count',
        'where' => array(
            'staffid' => $_SESSION['active'],
            'term' => $active_term['term'],
        )
    );
    $subject_allocated = $model->getRows($tblName, $conditions);

    $tblName = 'lhpnote';
    $conditions = array(
        'return_type' => 'count',
        'where' => array(
            'staffid' => $_SESSION['active'],
            'term' => $active_term['term'],
        )
    );
    $notes_created = $model->getRows($tblName, $conditions);

    //statistics
    $tblName = 'lhpstaff';
    $conditions = array(
        'select' => '

                    (SELECT count(lhpscheme.schmid) FROM lhpscheme WHERE  lhpscheme.status = 1 and lhpscheme.term ="' . $active_term["term"] . '"and lhpscheme.staffid ="' . $_SESSION["active"] . '") as topic ,
                    (SELECT count(lhpalloc.aid) FROM lhpalloc WHERE lhpalloc.term ="' . $active_term["term"] . '"and lhpalloc.staffid ="' . $_SESSION["active"] . '") as subject ,
                    (SELECT count(lhpnote.sbjid) FROM lhpnote WHERE  lhpnote.status = 1 and lhpnote.term ="' . $active_term["term"] . '"and lhpnote.staffid ="' . $_SESSION["active"] . '") as note ,
                    (SELECT count(lhpquestion.questid) FROM lhpquestion WHERE  lhpquestion.status = 1 and lhpquestion.term ="' . $active_term["term"] . '"and lhpquestion.staffid ="' . $_SESSION["active"] . '") as task ,
                    (SELECT count(lhpclassalloc.classlocid) FROM lhpclassalloc WHERE  lhpclassalloc.term ="' . $active_term["term"] . '"and lhpclassalloc.tutorid ="' . $_SESSION["active"] . '") as class
                    ',
        'where' => array(
            'lhpstaff.sname' => $_SESSION['active'],
        ),
        'return_type' => 'single',
    );
    $statistics = $model->getRows($tblName, $conditions);

    //Allocated Subjects
    $tblName = 'lhpalloc';
    $conditions = array(
        'select' => '
            lhpclass.classid, lhpclass.classname, 
            lhpstaff.sname, lhpstaff.staffname, 
            lhpsubject.sbjid, lhpsubject.sbjname, 
            lhpalloc.aid, lhpalloc.term, lhpalloc.staffid, lhpalloc.sbjid,
            lhpnote.sbjid, lhpnote.status, lhpnote.term, lhpnote.staffid, 
            lhpquestion.questid, lhpquestion.status, lhpquestion.term, lhpquestion.staffid,
            lhpscheme.schmid, lhpscheme.status, lhpscheme.term, lhpscheme.staffid, lhpscheme.subject,

                    (SELECT count(lhpscheme.schmid) FROM lhpscheme WHERE lhpalloc.sbjid = lhpscheme.subject and lhpscheme.status = 1 and lhpscheme.term ="' . $active_term["term"] . '"and lhpscheme.staffid ="' . $_SESSION["active"] . '") as topic ,
                    (SELECT count(lhpnote.sbjid) FROM lhpnote WHERE lhpalloc.sbjid = lhpnote.sbjid and lhpnote.status = 1 and lhpnote.term ="' . $active_term["term"] . '"and lhpnote.staffid ="' . $_SESSION["active"] . '") as note ,
                    (SELECT count(lhpquestion.questid) FROM lhpquestion WHERE  lhpalloc.sbjid = lhpquestion.sbjid and lhpquestion.status = 1 and lhpquestion.term ="' . $active_term["term"] . '"and lhpquestion.staffid ="' . $_SESSION["active"] . '") as task, 
                    (SELECT count(lhpfeedback.fid) FROM lhpfeedback WHERE  lhpalloc.sbjid = lhpfeedback.sbjid and lhpfeedback.term ="' . $active_term["term"] . '") as feedback 
                    ',
        'where' => array(
            'lhpalloc.staffid' => $_SESSION['active'],
            'lhpalloc.term' => $active_term["term"],
        ),
        'joinl' => array(
            'lhpstaff' => ' on lhpalloc.staffid = lhpstaff.sname ',
            'lhpnote' => ' on lhpalloc.sbjid = lhpnote.sbjid ',
            'lhpquestion' => ' on lhpalloc.sbjid = lhpquestion.sbjid ',
            'lhpscheme' => ' on lhpalloc.sbjid = lhpscheme.subject ',
            'lhpsubject' => ' on lhpalloc.sbjid = lhpsubject.sbjid ',
            'lhpclass' => ' on lhpalloc.classid = lhpclass.classid ',
        ),
        'group_by' => 'lhpalloc.sbjid',

    );
    $report = $model->getRows($tblName, $conditions);

    //All classes where subject have been allocated
    $tblName = 'lhpalloc';
    $conditions = array(
        'select' => ' DISTINCT lhpalloc.classid, lhpclass.classid, lhpclass.classname',
        'where' => array(
            'lhpalloc.staffid' => $_SESSION['active'],
            'lhpalloc.term' => $active_term['term'],
        ),
        'joinl' => array(
            'lhpclass' => ' on lhpalloc.classid = lhpclass.classid ',
        )
    );
    $class_subject_allocated = $model->getRows($tblName, $conditions);


    $tblName = 'lhpclassalloc';
    $conditions = array(
        'where' => array(
            'tutorid' => $_SESSION['active'],
            'term' => $active_term['term'],
        ),
        'joinl' => array(
            'lhpclass' => ' on lhpclassalloc.classid = lhpclass.classid ',
        )
    );
    $class_allocated = $model->getRows($tblName, $conditions);

    $tblName = 'lhpalloc';
    $conditions = array(
        'where' => array(
            'staffid' => $_SESSION['active'],
            'term' => $active_term['term'],
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpalloc.sbjid = lhpsubject.sbjid ',
        )
    );
    $all_subject_allocated = $model->getRows($tblName, $conditions);

    //Learners Profile

    if (isset($_SESSION['pageid']) && $_SESSION['pageid'] == 'manage_learner' && isset($_SESSION['instance'])) {
        $tblName = 'lhpuser';
        $conditions = array(
            'return_type' => 'single',
            'where' => array(
                'uname' => $_SESSION['instance'],
            ),
            'joinl' => array(
                'lhpclass' => ' on lhpuser.classid = lhpclass.classid ',
            )
        );
        $learner_profile = $model->getRows($tblName, $conditions);
    }
}


//User Details - Admin
$tblName = '123admin';
$conditions = array(
    'return_type' => 'single',
);
$admin_details = $model->getRows($tblName, $conditions);
