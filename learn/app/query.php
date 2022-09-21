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

//User Details - Learner

$tblName = 'lhpuser';
$conditions = array(
    'return_type' => 'single',
);
$learner_details = $model->getRows($tblName, $conditions);

if (isset($_SESSION['active'])) {
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
    if (!empty($learner_profile['classid'])) {
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
    }


    //Subjects Counter
    if (!empty($learner_profile['classid'])) {
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

        //billed school fees
        $tblName = 'lhpassignedfee';
        $conditions = array(
            'select' => 'SUM(amount) as schfee',
            'return_type' => 'single',
            'where' => array(
                'stdid' => $learner_profile['uname'],
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
        $conditions = array(
            'return_type' => 'count',
            'where' => array(
                'stdid' => $learner_profile['uname'],
                'classid' => $learner_profile['classid'],
                'term' => $active_term['term'],
                'status' => 1,
            )
        );
        $bill_transaction = $model->getRows($tblName, $conditions);
    }

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
                        lhpnote.sbjid, lhpalloc.classid, lhpalloc.term, lhpfeedback.fid,
                        (SELECT count(lhpnote.sbjid) FROM lhpnote WHERE  lhpnote.sbjid = lhpalloc.sbjid and lhpnote.status = 1 and lhpnote.term ="' . $active_term["term"] . '") as note ,
                        (SELECT count(lhpquestion.sbjid) FROM lhpquestion WHERE  lhpquestion.sbjid = lhpalloc.sbjid and lhpquestion.status = 1 and lhpquestion.term ="' . $active_term['term'] . '") as task,
                        (SELECT count(lhpfeedback.sbjid) FROM lhpfeedback WHERE lhpfeedback.sbjid = lhpalloc.sbjid and lhpfeedback.stdid = "' . $_SESSION['active'] . '" and lhpfeedback.term ="' . $active_term['term'] . '") as submitted,
                        (SELECT count(lhpscheme.subject) FROM lhpscheme WHERE lhpalloc.sbjid = lhpscheme.subject and lhpscheme.status = 1 and lhpscheme.term ="' . $active_term['term'] . '") as topic',
        'where' => array(
            'lhpalloc.classid' => $learner_profile['classid'],
            'lhpalloc.term' => $active_term['term'],
        ),
        'joinl' => array(
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

    //Scheme of Work
    $tblName = 'lhpalloc';
    $conditions = array(
        'where' => array(
            'lhpalloc.classid' => $learner_profile['classid'],
            'lhpalloc.term' => $active_term['term'],
            'lhpscheme.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpalloc.sbjid = lhpsubject.sbjid',
            'lhpscheme' => ' on lhpalloc.sbjid = lhpscheme.subject',
        ),
        'group_by' => 'lhpalloc.sbjid',
    );
    $outline = $model->getRows($tblName, $conditions);

    //List of Notes
    if (isset($_SESSION['subjectid'])) {
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
    if (isset($_SESSION['subjectid'])) {
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
    } else {
        $tblName = 'lhpquestion';
        $conditions = array(
            'where' => array(
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


    //List of Topics- Scheme of work
    if (isset($_SESSION['subjectid'])) {
        $tblName = 'lhpscheme';
        $conditions = array(
            'where' => array(
                'lhpscheme.subject' => $_SESSION['subjectid'],
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
                'lhpscheme.subject' => $_SESSION['subjectid'],
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


    //Note Details
    if (isset($_SESSION['ref']) && $_SESSION['pageid'] == 'note') {
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
    if (isset($_SESSION['ref']) && $_SESSION['pageid'] == 'task') {
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


    //Results
    $tblName = 'lhpresultconfig';
    $view_result = $model->getRows($tblName);
    
    if (isset($_SESSION['ref']) && $_SESSION['pageid'] == 'result') {
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
        'order_by'=> 'lhpsubject.sbjname',
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
}





//User Details - Staff
$tblName = 'lhpstaff';
$conditions = array(
    'return_type' => 'single',
);
$staff_details = $model->getRows($tblName, $conditions);

//User Details - Learner
$tblName = '123admin';
$conditions = array(
    'return_type' => 'single',
);
$admin_details = $model->getRows($tblName, $conditions);
