<?php
include './query.php';

if (isset($_POST['classid']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'fetchsubject') {
    //All classes where subject have been allocated
    $tblName = 'lhpalloc';
    $conditions = array(
        'where' => array(
            'lhpalloc.staffid' => $_SESSION['active'],
            'lhpalloc.term' => $active_term['term'],
            'lhpalloc.classid' => $_POST['classid'],
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpalloc.sbjid = lhpsubject.sbjid ',
        )
    );
    $subject_allocated = $model->getRows($tblName, $conditions);
?>
    <option value="">Select Subject</option>
    <?php
    if (!empty($subject_allocated)) {
        foreach ($subject_allocated as $data) {
    ?>

            <option value="<?php echo $data['sbjid'] ?>"><?php echo $data['sbjname'] ?></option>
<?php
        }
    } else {
        echo '<option value="">No Subject Allocated in Selected Class</option>';
    }
}
?>


<?php
if (isset($_POST['subject']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'fetchscheme') {
    $tblName = 'lhpscheme';
    $conditions = array(
        'where' => array(
            'lhpscheme.subject' => $_POST['subject'],
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
            'lhpscheme.subject' => $_POST['subject'],
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
    include_once '../view/include/pages/viewscheme.php';
}
?>
<?php
if (isset($_POST['subject']) && isset($_POST['classid']) && isset($_POST['topic']) && isset($_POST['week']) && isset($_SESSION['active']) && isset($active_term)) {
    $tblName = 'lhpscheme';
    $schemedata = array(
        'term' => $active_term['term'],
        'classname' => $_POST['classid'],
        'subject' => $_POST['subject'],
        'week' => $_POST['week'],
        'topic' => $_POST['topic'],
        'staffid' => $_SESSION['active'],
        'status' => 1,
    );

    if ($_POST['action'] == 'add_topic_to_scheme') {
        $action = $model->insert_data($tblName, $schemedata);
        if ($action) {
            echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Success! <b>Topic added to scheme successfully</b>!</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Error! Unable to add topic to scheme of work</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    } elseif ($_POST['action'] == 'modify_topic_in_scheme') {
        $conditons = array(
            'schmid' => $_POST['topicid'],
            'staffid' => $_SESSION['active'],
        );
        $action = $model->upDate($tblName, $schemedata, $conditons);
        if ($action) {
            echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Success! <b>Scheme of work modified successfully</b>!</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Error! Unable to modify scheme of work</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    } elseif ($_POST['action'] == 'remove_topic_from_scheme') {
        $schemedata = array(
            'status' => 0,
        );
        $conditons = array(
            'schmid' => $_POST['topicid'],
            'staffid' => $_SESSION['active'],
        );
        $action = $model->upDate($tblName, $schemedata, $conditons);
        if ($action) {
            echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Success! <b>Topic removed from Scheme of work  successfully</b>!</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Error! Unable to remove topic from scheme of work</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    }
} ?>
<?php
if (isset($_POST['subject']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'fetchtopic') {
    //All classes where subject have been allocated
    $tblName = 'lhpscheme';
    $conditions = array(
        'where' => array(
            'subject' => $_POST['subject'],
            'term' => $active_term['term'],
            'status' => 1,
        ),
        'order_by' => 'week ASC',
    );
    $topic_created = $model->getRows($tblName, $conditions);
?>
    <option value="">Select Topic</option>
    <?php
    if (!empty($topic_created)) {
        foreach ($topic_created as $data) {
    ?>

            <option value="<?php echo $data['schmid'] ?>"><?php echo $data['week'] . " - " . $data['topic'] ?></option>
<?php
        }
    } else {
        echo '<option value="">No Topic has been added to scheme of work for the selected Subject</option>';
    }
}
?>
<?php

if (isset($_POST['subject']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'fetchnote') {
    $tblName = 'lhpnote';
    $conditions = array(
        'where' => array(
            'lhpnote.sbjid' => $_POST['subject'],
            'lhpnote.term' => $active_term['term'],
            'lhpnote.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpnote.sbjid = lhpsubject.sbjid',
            'lhpscheme' => ' on lhpnote.topicid = lhpscheme.schmid ',
        ),
        'order_by' => 'lhpnote.rectime ASC',
    );
    $list_note = $model->getRows($tblName, $conditions);
    include_once '../view/include/pages/selectnote.php';
}

?>

<?php
if (isset($_POST['context']) && $_POST['context'] == 'enote' && isset($_POST['subject']) && isset($_POST['classid']) && isset($_POST['topic']) && isset($_POST['note_type']) && isset($_POST['content']) && isset($_SESSION['active']) && isset($active_term)) {
    $tblName = 'lhpnote';
    $notedata = array(
        'term' => $active_term['term'],
        'type' => $_POST['note_type'],
        'sbjid' => $_POST['subject'],
        'content' => $_POST['content'],
        'topicid' => $_POST['topic'],
        'staffid' => $_SESSION['active'],
        'status' => 1,
    );

    if ($_POST['action'] == 'add_enote') {
        $action = $model->insert_data($tblName, $notedata);
        if ($action) {
            echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Success! <b>e-Note has been added  successfully</b>!</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Error! Unable to add e-Note to the portal</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    } elseif ($_POST['action'] == 'modify_enote') {
        $conditons = array(
            'noteid' => $_POST['noteid'],
            'staffid' => $_SESSION['active'],
        );
        $action = $model->upDate($tblName, $notedata, $conditons);
        if ($action) {
            echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Success! <b>e-Note has been modified successfully</b>!</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Error! Unable to modify e-Note</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    } elseif ($_POST['action'] == 'remove_enote') {
        $notedata = array(
            'status' => 0,
        );
        $conditons = array(
            'noteid' => $_POST['noteid'],
            'staffid' => $_SESSION['active'],
        );
        $action = $model->upDate($tblName, $notedata, $conditons);
        if ($action) {
            echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Success! <b>e-Note removed from Scheme of work  successfully</b>!</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Error! Unable to remove e-Note from scheme of work</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    }
}
?>


<?php
//TASK AND ASSEMENT 
if (isset($_POST['subject']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'fetchtask') {
    $tblName = 'lhpquestion';
    $conditions = array(
        'where' => array(
            'lhpquestion.sbjid' => $_POST['subject'],
            'lhpquestion.term' => $active_term['term'],
            'lhpquestion.status' => 1,
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpquestion.sbjid = lhpsubject.sbjid',
            'lhpscheme' => ' on lhpquestion.topicid = lhpscheme.schmid ',
        ),
        'order_by' => 'lhpquestion.rectime ASC',
    );
    $list_task = $model->getRows($tblName, $conditions);
    include_once '../view/include/pages/selectask.php';
}

?>

<?php
if (isset($_POST['context']) && $_POST['context'] == 'task' && isset($_POST['subject']) && isset($_POST['classid']) && isset($_POST['topic']) && isset($_POST['note_type']) && isset($_POST['content']) && isset($_SESSION['active']) && isset($active_term)) {
    $tblName = 'lhpquestion';
    $notedata = array(
        'term' => $active_term['term'],
        'type' => $_POST['note_type'],
        'sbjid' => $_POST['subject'],
        'content' => $_POST['content'],
        'topicid' => $_POST['topic'],
        'grade' => $_POST['grade'],
        'deadline' => $_POST['due_date'],
        'staffid' => $_SESSION['active'],
        'status' => 1,
    );

    if ($_POST['action'] == 'add_task') {
        $action = $model->insert_data($tblName, $notedata);
        if ($action) {
            echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Success! <b>e-Assessment has been added  successfully</b>!</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Error! Unable to add Assessment to the portal</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    } elseif ($_POST['action'] == 'modify_task') {
        $conditons = array(
            'questid ' => $_POST['questid'],
            'staffid' => $_SESSION['active'],
        );
        $action = $model->upDate($tblName, $notedata, $conditons);
        if ($action) {
            echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Success! <b>Assessment has been modified successfully</b>!</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Error! Unable to modify Assessment</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    } elseif ($_POST['action'] == 'remove_task') {
        $notedata = array(
            'status' => 0,
        );
        $conditons = array(
            'questid' => $_POST['questid'],
            'staffid' => $_SESSION['active'],
        );
        $action = $model->upDate($tblName, $notedata, $conditons);
        if ($action) {
            echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Success! <b>Assessment removed from Scheme of work  successfully</b>!</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                          <div class="alert-text">Error! Unable to remove Assessment from scheme of work</div>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    }
}
?>

<?php
//CLASS MANAGER - DASHBOARD
if (isset($_POST['allocated_class']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'load_dashboard') {

    $tblName = 'lhpuser';
    $conditions = array(
        'select' => '
        (SELECT COUNT(uname) from lhpuser where classid = "'. $_POST["allocated_class"] . '" 
                    and status = 1) as population,
        (SELECT COUNT(lhpalloc.aid)  from lhpalloc WHERE lhpalloc.classid = "'. $_POST["allocated_class"] . '" 
                    and lhpalloc.term = "' . $active_term["term"] . '") as subject,
        (SELECT COUNT(DISTINCT lhpuser.uname) from lhpuser 
         	WHERE lhpuser.classid =  "'.$_POST["allocated_class"].'" and
				(SELECT sum(lhpassignedfee.amount) FROM lhpassignedfee 
                 	WHERE 	lhpassignedfee.stdid = lhpuser.uname and 
                 			lhpassignedfee.term = "' . $active_term["term"] . '"
                 			GROUP BY lhpassignedfee.stdid)  <= 
				(SELECT sum(lhptransaction.amount) FROM lhptransaction
                 	WHERE 	lhptransaction.stdid = lhpuser.uname  and 
                 			lhptransaction.term = "' . $active_term["term"] . '" 
                 			GROUP BY lhptransaction.stdid)) as paid,
                            
        (SELECT COUNT(DISTINCT lhpuser.uname) from lhpuser 
         	WHERE lhpuser.classid =  "'. $_POST["allocated_class"] . '" and
				(SELECT sum(lhpassignedfee.amount) FROM lhpassignedfee 
                 	WHERE 	lhpassignedfee.stdid = lhpuser.uname and 
                 			lhpassignedfee.term = "' . $active_term["term"] . '"
                 			GROUP BY lhpassignedfee.stdid)  > 
				(SELECT sum(lhptransaction.amount) FROM lhptransaction
                 	WHERE 	lhptransaction.stdid = lhpuser.uname  and 
                 			lhptransaction.term = "' . $active_term["term"] . '" 
                 			GROUP BY lhptransaction.stdid)) as debtor
                        ',
        'return_type' => 'single',
        'joinl' => array(
            'lhptransaction' => ' on lhpuser.uname = lhptransaction.stdid ',
            'lhpassignedfee' => ' on lhpuser.uname = lhpassignedfee.stdid ',
            'lhpalloc' => ' on lhpuser.classid = lhpalloc.classid ',
        )
    );
    $class_allocated = $model->getRows($tblName, $conditions);
    include_once '../view/include/classmanager/widget.php';
}
//CLASS MANAGER - show LEARNERS
if (isset($_POST['allocated_class']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'show_learners') {
    $tblName = 'lhpuser';
    $conditions = array(
        'where' => array(
            'lhpuser.classid' => $_POST['allocated_class'],
        ),
        'order_by' => 'lhpuser.status DESC, lhpuser.fname ASC ',
    );
    $learner_list = $model->getRows($tblName, $conditions);

    $conditions = array(
        'select' => '
                        (SELECT COUNT(uname) from lhpuser where status = 1 and gender = "Male" and classid = "'. $_POST["allocated_class"] . '") as male,
                        (SELECT COUNT(uname) from lhpuser where status = 1 and gender = "Female" and classid = "'. $_POST["allocated_class"] . '") as female,
                        (SELECT COUNT(uname) from lhpuser where status = 1 and classid = "'. $_POST["allocated_class"] . '") as total
            ',
        'return_type' => 'single',
    );
    $learner_statistics = $model->getRows($tblName, $conditions);

    $tblName = 'lhpclass';
    $conditions = array(
        'where' => array(
            'classid' => $_POST['allocated_class'],
        ),
        'return_type' => 'single',
    );
    $classname = $model->getRows($tblName, $conditions);


    include_once '../view/include/classmanager/learner_list.php';
}

//CLASS MANAGER - show subject list
if (isset($_POST['allocated_class']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'show_subjects') {
        
        $tblName = 'lhpalloc';
        $conditions = array(
            'select' => '   lhpalloc.staffid, lhpstaff.sname, lhpstaff.staffname, 
                            lhpalloc.sbjid as sbjref, lhpsubject.sbjid, lhpsubject.sbjname, 
                            lhpnote.sbjid, lhpalloc.classid, lhpalloc.term, 
                            lhpclass.classid, lhpclass.classname, 

                            (SELECT count(lhpnote.sbjid) FROM lhpnote WHERE  lhpnote.sbjid = lhpalloc.sbjid and lhpnote.status = 1 and lhpnote.term ="' . $active_term["term"] . '") as note ,
                            (SELECT count(lhpquestion.sbjid) FROM lhpquestion WHERE  lhpquestion.sbjid = lhpalloc.sbjid and lhpquestion.status = 1 and lhpquestion.term ="' . $active_term['term'] . '") as task,
                            (SELECT count(lhpscheme.subject) FROM lhpscheme WHERE lhpalloc.sbjid = lhpscheme.subject and lhpscheme.status = 1 and lhpscheme.term ="' . $active_term['term'] . '") as topic',
            'where' => array(
                'lhpalloc.classid' => $_POST['allocated_class'],
                'lhpalloc.term' => $active_term['term'],
            ),
            'joinl' => array(
                'lhpclass' => ' on lhpalloc.classid = lhpclass.classid',
                'lhpstaff' => ' on lhpalloc.staffid = lhpstaff.sname',
                'lhpsubject' => ' on lhpalloc.sbjid = lhpsubject.sbjid',
                'lhpnote' => ' on lhpalloc.sbjid = lhpnote.sbjid',
                'lhpquestion' => ' on lhpalloc.sbjid = lhpquestion.sbjid',
                'lhpscheme' => ' on lhpalloc.sbjid = lhpscheme.subject',
            ),
            'group_by' => 'lhpalloc.sbjid',
        );
        $subject_list = $model->getRows($tblName, $conditions);

        
    $tblName = 'lhpclass';
    $conditions = array(
        'where' => array(
            'classid' => $_POST['allocated_class'],
        ),
        'return_type' => 'single',
    );
    $classname = $model->getRows($tblName, $conditions);

        include_once '../view/include/classmanager/subject_list.php';
}

//CLASS MANAGER - show learner who have paid fully
if (isset($_POST['allocated_class']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'show_fully_paid') {
    $tblName = 'lhpuser';
    $conditions = array(
        'select' => ' DISTINCT lhpuser.uname, lhpuser.fname,

                    (SELECT sum(lhpassignedfee.amount) FROM lhpassignedfee 
                        WHERE lhpassignedfee.stdid = lhpuser.uname and 
                        lhpassignedfee.term = "'. $active_term["term"] .'"
                        GROUP BY lhpassignedfee.stdid) as bill,
                        
                    (SELECT sum(lhptransaction.amount) FROM lhptransaction
                        WHERE lhptransaction.stdid = lhpuser.uname  and 
                        lhptransaction.term = "' . $active_term["term"] . '" 
                        GROUP BY lhptransaction.stdid) as paid ',
        'where' => array(
                    'lhpuser.classid' => $_POST['allocated_class'],
        ),
    );
    $paid_list = $model->getRows($tblName, $conditions);

    $tblName = 'lhpclass';
    $conditions = array(
        'where' => array(
            'classid' => $_POST['allocated_class'],
        ),
        'return_type' => 'single',
    );
    $classname = $model->getRows($tblName, $conditions);

    include_once '../view/include/classmanager/paid_list.php';

}
?>