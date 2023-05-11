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
            <option value="<?php echo $data['sbjid'] ?>"><?php echo $data['sbjname']; ?></option>
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
        (SELECT COUNT(uname) from lhpuser where classid = "' . $_POST["allocated_class"] . '" 
                    and status = 1) as population,
        (SELECT COUNT(lhpalloc.aid)  from lhpalloc WHERE lhpalloc.classid = "' . $_POST["allocated_class"] . '" 
                    and lhpalloc.term = "' . $active_term["term"] . '") as subject,
        (SELECT COUNT(lhpaffective.uname)  from lhpaffective WHERE lhpaffective.classid = "' . $_POST["allocated_class"] . '" 
                    and lhpaffective.term = "' . $active_term["term"] . '") as affective,
        (SELECT COUNT(DISTINCT lhpuser.uname) from lhpuser 
         	WHERE lhpuser.classid =  "' . $_POST["allocated_class"] . '" and
				(SELECT sum(lhpassignedfee.amount) FROM lhpassignedfee 
                 	WHERE 	lhpassignedfee.stdid = lhpuser.uname and 
                 			lhpassignedfee.term = "' . $active_term["term"] . '"
                 			GROUP BY lhpassignedfee.stdid)  <= 
				(SELECT sum(lhptransaction.amount) FROM lhptransaction
                 	WHERE 	lhptransaction.stdid = lhpuser.uname  and 
                 			lhptransaction.term = "' . $active_term["term"] . '" 
                 			GROUP BY lhptransaction.stdid)) as paid,
                            
        (SELECT COUNT(DISTINCT lhpuser.uname) from lhpuser 
         	WHERE lhpuser.classid =  "' . $_POST["allocated_class"] . '" and
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
                        (SELECT COUNT(uname) from lhpuser where status = 1 and gender = "Male" and classid = "' . $_POST["allocated_class"] . '") as male,
                        (SELECT COUNT(uname) from lhpuser where status = 1 and gender = "Female" and classid = "' . $_POST["allocated_class"] . '") as female,
                        (SELECT COUNT(uname) from lhpuser where status = 1 and classid = "' . $_POST["allocated_class"] . '") as total
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
if (isset($_POST['allocated_class']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'show_broadsheet') {

    $tblName = 'lhpresultrecord';
    $conditions = array(
        'select' => 'DISTINCT lhpsubject.sbjname, lhpsubject.sbjid',
        'where' => array(
            'lhpresultrecord.classid' => $_POST['allocated_class'],
            'lhpresultrecord.term' => $active_term["term"],
        ),
        'joinl' => array(
            'lhpsubject' => ' on lhpresultrecord.subjid = lhpsubject.sbjid',
        ),
        'order_by' => 'lhpsubject.sbjid',
    );
    $subject_header = $model->getRows($tblName, $conditions);

    $tblName = 'lhpclass';
    $conditions = array(
        'where' => array(
            'classid' => $_POST['allocated_class'],
        ),
        'return_type' => 'single',
    );
    $classname = $model->getRows($tblName, $conditions);

    $tblName = 'lhpresultrecord';
    $conditions = array(
        'select' => 'DISTINCT lhpresultrecord.lid,lhpresultrecord.term, lhpresultrecord.classid, lhpuser.uname, lhpuser.fname, lhpuser.picture',
        'where' => array(
            'lhpresultrecord.classid' => $_POST['allocated_class'],
            'lhpresultrecord.term' => $active_term["term"],
        ),
        'joinl' => array(
            'lhpuser' => ' on lhpresultrecord.lid = lhpuser.uname',
        ),
        'order_by' => 'lhpuser.uname',
    );
    $class_list = $model->getRows($tblName, $conditions);

    include_once '../view/include/classmanager/broadsheet.php';
}
?>
<?php
//CLASS MANAGER - Modify Data
if (isset($_POST['fullname']) && isset($_POST['gender']) && isset($_POST['date_of_birth']) && isset($_POST['phone']) && isset($_SESSION['active']) && isset($active_term) && $_POST['action'] == 'modify_learner') {
    $tblName = 'lhpuser';
    $profile_data = array(
        'fname' => $_POST['fullname'],
        'gender' => $_POST['gender'],
        'dob' => $_POST['date_of_birth'],
        'numb' => $_POST['phone'],
        'email' => $_POST['email'],
    );
    $conditions = array(
        'uname' => $_SESSION['instance'],
    );

    if ($_POST['upload'] == 'yes') {
        $dir = '../asset/img/passport/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $fileTmpPath = $_POST['imagebase64data'];
        $random = $utility->generateRandomString(7);
        // form the filename 
        $filename_path = $_SESSION['instance'] . $random . ".jpg";
        // remove special characters from file name
        $filename_path = $utility->RemoveSpecialChar($filename_path);
        // generate image from posted base64 data
        $decoded = base64_decode($fileTmpPath);
        // move image to folder
        if (file_put_contents($dir . $filename_path, $decoded)) {
            $profile_data += array(
                'picture' => $filename_path
            );
        }
    }
    $action = $model->upDate($tblName, $profile_data, $conditions);
    if ($action) {
        echo
            '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                      <div class="alert-text">Success! <b>Learner profile has been modified successfully</b>!</div>
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    } else {
        echo
            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                      <div class="alert-text">Error! Unable to modify learner profile</div>
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}
//Scoresheet Dashboard - Weekly, CA, Exam, Cumulative
if (isset($_POST['allocated_subject']) && isset($_SESSION['active']) && isset($active_term)) {
    $tblName = 'lhpresultconfig';
    $conditions = array(
        'where' => array(
            'term' => $active_term['term'],
        ),
        'return_type' => 'single',

    );
    $result_config = $model->getRows($tblName, $conditions);
    $tblName = 'lhpsubject';
    $conditions = array(
        'where' => array(
            'lhpsubject.sbjid' => $_POST['allocated_subject'],
        ),
        'joinl' => array(
            'lhpclass' => ' on lhpsubject.classid = lhpclass.classid',
        ),
        'return_type' => 'single',

    );
    $class_details = $model->getRows($tblName, $conditions);

    //Number of students offering subject
    $tblName = 'lhpuser';
    $conditions = array(
        'where' => array(
            'classid' => $class_details['classid'],
            'status' => 1,
        ),
        'return_type' => 'count',
    );
    $class_population = $model->getRows($tblName, $conditions);



    if ($_POST['action'] == 'load_scoresheet_dashboard') {
        $tblName = 'lhpresultrecord';
        $conditions = array(
            'select' => '

                (SELECT COUNT(DISTINCT lhpresultrecord.lid) from lhpresultrecord
                    where lhpresultrecord.score != 0
                    and lhpresultrecord.subjid =  "' . $_POST['allocated_subject'] . '"
                    and lhpresultrecord.term = "' . $active_term["term"] . '") as ca_score,
                (SELECT COUNT(DISTINCT lhpresultrecord.lid) from lhpresultrecord 
                    WHERE lhpresultrecord.examscore != 0
                    and lhpresultrecord.subjid =  "' . $_POST['allocated_subject'] . '"
                    and lhpresultrecord.term = "' . $active_term["term"] . '") as exam_score,
                (SELECT COUNT(DISTINCT lhpresultrecord.lid) from lhpresultrecord 
                    WHERE lhpresultrecord.score != 0
                    and lhpresultrecord.examscore != 0
                    and lhpresultrecord.totalscore != 0
                    and lhpresultrecord.subjid =  "' . $_POST['allocated_subject'] . '"
                    and lhpresultrecord.term = "' . $active_term["term"] . '") as total_score,
                (SELECT COUNT(DISTINCT lhpweekrecord.lid) from lhpweekrecord 
                    WHERE lhpweekrecord.subjid =  "' . $_POST['allocated_subject'] . '"
                    and lhpweekrecord.term = "' . $active_term["term"] . '") as week_score
                ',
            'where' => array(
                'lhpresultrecord.subjid' => $_POST['allocated_subject'],
                'lhpresultrecord.term' => $active_term['term'],
            ),
            'return_type' => 'single',
        );
        $scores_recorded = $model->getRows($tblName, $conditions);
        include_once '../view/include/scoresheet/scores_widget.php';
    }
    //Load all students in the class for ca
    elseif ($_POST['action'] == 'ca_score_manager') {
        $scoresheet_type = 'CA_SCORE';

        $tblName = 'lhpuser';
        $conditions = array(
            'select' => 'lhpuser.fname, lhpuser.picture, lhpuser.classid, lhpuser.uname, (SELECT lhpresultrecord.score from lhpresultrecord where  lhpresultrecord.lid = lhpuser.uname 
            and lhpresultrecord.subjid =  "' . $_POST['allocated_subject'] . '"
            and lhpresultrecord.term = "' . $active_term["term"] . '") as score',
            'where' => array(
                'lhpuser.classid' => $class_details['classid'],
            ),
        );
        $ca_scores_recorder = $model->getRows($tblName, $conditions);
        include_once '../view/include/scoresheet/recorder.php';

        //Record CA SCORES
    } elseif ($_POST['action'] == 'record_ca_scores_for_all') {

        $tblName = 'lhpsubject';
        $conditions = array(
            'where' => array(
                'lhpsubject.sbjid' => $_POST['allocated_subject'],
            ),
            'joinl' => array(
                'lhpclass' => ' on lhpsubject.classid = lhpclass.classid',
            ),
            'return_type' => 'single',

        );
        $class_details = $model->getRows($tblName, $conditions);
        $response = '';
        $tblName = 'lhpresultrecord';
        foreach (array_combine($_POST['all_users'], $_POST['all_scores']) as $user => $scores) {
            if ($scores >= 1 && $result_config['ca_score'] >= $scores) {
                $conditions = array(
                    'where' => array(
                        'lhpresultrecord.lid' => $user,
                        'lhpresultrecord.subjid' => $_POST['allocated_subject'],
                        'lhpresultrecord.term' => $active_term["term"],
                    ),
                    'return_type' => 'single',
                );
                $check_if_exist = $model->getRows($tblName, $conditions);

                if (!empty($check_if_exist)) {
                    $score_data = array(
                        'score' => $scores,
                        'totalscore' => $scores + $check_if_exist['examscore'],
                    );
                    $conditions = array(
                        'lhpresultrecord.lid' => $user,
                        'lhpresultrecord.subjid' => $_POST['allocated_subject'],
                        'lhpresultrecord.term' => $active_term["term"],
                    );
                    $action = $model->upDate($tblName, $score_data, $conditions);
                    if ($action) {

                        $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Success! <b>Successfully updated CA score for learner with ID : ' . $user . '</b>!</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    } else {
                        $response .=
                            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Error! Unable to update CA score for learner with ID : ' . $user . '</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    }
                } else {

                    $score_data = array(
                        'score' => $scores,
                        'examscore' => 0,
                        'totalscore' => $scores,
                        'lid' => $user,
                        'classid' => $class_details['classid'],
                        'subjid' => $_POST['allocated_subject'],
                        'term' => $active_term["term"],
                    );
                    $action = $model->insert_data($tblName, $score_data);
                    if ($action) {

                        $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Success! <b>Successfully recorded CA score for learner with ID : ' . $user . '</b>!</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    } else {
                        $response .=
                            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Error! Unable to record CA score for learner with ID : ' . $user . '</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    }
                }
            } else {
                $response .=
                    '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Error! Unable to record 0 or scores greater than ' . $result_config['ca_score'] . ' as CA score for learner with ID : ' . $user . '</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
            }
        }
        echo $response;
    } elseif ($_POST['action'] == 'exam_score_manager') {
        $scoresheet_type = 'EXAM_SCORE';

        $tblName = 'lhpsubject';
        $conditions = array(
            'where' => array(
                'lhpsubject.sbjid' => $_POST['allocated_subject'],
            ),
            'joinl' => array(
                'lhpclass' => ' on lhpsubject.classid = lhpclass.classid',
            ),
            'return_type' => 'single',

        );
        $class_details = $model->getRows($tblName, $conditions);

        $tblName = 'lhpuser';
        $conditions = array(
            'select' => 'lhpuser.fname, lhpuser.picture, lhpuser.classid, lhpuser.uname, (SELECT lhpresultrecord.examscore from lhpresultrecord where  lhpresultrecord.lid = lhpuser.uname 
            and lhpresultrecord.subjid =  "' . $_POST['allocated_subject'] . '"
            and lhpresultrecord.term = "' . $active_term["term"] . '") as score',
            'where' => array(
                'lhpuser.classid' => $class_details['classid'],
            ),
        );
        $exam_scores_recorder = $model->getRows($tblName, $conditions);
        include_once '../view/include/scoresheet/recorder.php';
    } elseif ($_POST['action'] == 'record_exam_scores_for_all') {

        $tblName = 'lhpsubject';
        $conditions = array(
            'where' => array(
                'lhpsubject.sbjid' => $_POST['allocated_subject'],
            ),
            'joinl' => array(
                'lhpclass' => ' on lhpsubject.classid = lhpclass.classid',
            ),
            'return_type' => 'single',

        );
        $class_details = $model->getRows($tblName, $conditions);
        $response = '';
        $tblName = 'lhpresultrecord';
        foreach (array_combine($_POST['all_users'], $_POST['all_scores']) as $user => $scores) {
            if ($scores >= 1 && $result_config['exam_score'] >= $scores) {
                $conditions = array(
                    'where' => array(
                        'lhpresultrecord.lid' => $user,
                        'lhpresultrecord.subjid' => $_POST['allocated_subject'],
                        'lhpresultrecord.term' => $active_term["term"],
                    ),
                    'return_type' => 'single',
                );
                $check_if_exist = $model->getRows($tblName, $conditions);

                if (!empty($check_if_exist)) {
                    $score_data = array(
                        'examscore' => $scores,
                        'totalscore' => $scores + $check_if_exist['score'],
                    );
                    $conditions = array(
                        'lhpresultrecord.lid' => $user,
                        'lhpresultrecord.subjid' => $_POST['allocated_subject'],
                        'lhpresultrecord.term' => $active_term["term"],
                    );
                    $action = $model->upDate($tblName, $score_data, $conditions);
                    if ($action) {

                        $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Success! <b>Successfully updated Exam score for learner with ID : ' . $user . '</b>!</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    } else {
                        $response .=
                            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Error! Unable to update Exam score for learner with ID : ' . $user . '</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    }
                } else {

                    $score_data = array(
                        'score' => 0,
                        'examscore' => $scores,
                        'totalscore' => $scores,
                        'lid' => $user,
                        'classid' => $class_details['classid'],
                        'subjid' => $_POST['allocated_subject'],
                        'term' => $active_term["term"],
                    );
                    $action = $model->insert_data($tblName, $score_data);
                    if ($action) {

                        $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Success! <b>Successfully recorded Exam score for learner with ID : ' . $user . '</b>!</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    } else {
                        $response .=
                            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Error! Unable to record Exam score for learner with ID : ' . $user . '</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    }
                }
            } else {
                $response .=
                    '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Error! Unable to record 0 or scores greater than ' . $result_config['exam_score'] . ' as Exam score for learner with ID : ' . $user . '</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
            }
        }
        echo $response;
    } elseif ($_POST['action'] == 'total_score_manager') {

        $tblName = 'lhpsubject';
        $conditions = array(
            'where' => array(
                'lhpsubject.sbjid' => $_POST['allocated_subject'],
            ),
            'joinl' => array(
                'lhpclass' => ' on lhpsubject.classid = lhpclass.classid',
            ),
            'return_type' => 'single',

        );
        $class_details = $model->getRows($tblName, $conditions);

        $tblName = 'lhpuser';
        $conditions = array(
            'select' => 'lhpuser.fname, lhpuser.picture, lhpuser.classid, lhpuser.uname,
             (SELECT lhpresultrecord.score from lhpresultrecord where  lhpresultrecord.lid = lhpuser.uname 
            and lhpresultrecord.subjid =  "' . $_POST['allocated_subject'] . '"
            and lhpresultrecord.term = "' . $active_term["term"] . '") as score,
             (SELECT lhpresultrecord.examscore from lhpresultrecord where  lhpresultrecord.lid = lhpuser.uname 
            and lhpresultrecord.subjid =  "' . $_POST['allocated_subject'] . '"
            and lhpresultrecord.term = "' . $active_term["term"] . '") as examscore,
             (SELECT lhpresultrecord.totalscore from lhpresultrecord where  lhpresultrecord.lid = lhpuser.uname 
            and lhpresultrecord.subjid =  "' . $_POST['allocated_subject'] . '"
            and lhpresultrecord.term = "' . $active_term["term"] . '") as totalscore',

            'where' => array(
                'lhpuser.classid' => $class_details['classid'],
            ),
        );
        $cumulative_score = $model->getRows($tblName, $conditions);
        include_once '../view/include/scoresheet/cumulative.php';
    } elseif ($_POST['action'] == 'weekly_score_manager') {
        $scoresheet_type = 'WEEKLY';
        $week = 'WEEK ' . $_POST['week_num'];

        $tblName = 'lhpsubject';
        $conditions = array(
            'where' => array(
                'lhpsubject.sbjid' => $_POST['allocated_subject'],
            ),
            'joinl' => array(
                'lhpclass' => ' on lhpsubject.classid = lhpclass.classid',
            ),
            'return_type' => 'single',

        );
        $class_details = $model->getRows($tblName, $conditions);

        $tblName = 'lhpuser';
        $conditions = array(
            'select' => 'lhpuser.fname, lhpuser.picture, lhpuser.classid, lhpuser.uname, (SELECT lhpweekrecord.score
             from lhpweekrecord
              where  lhpweekrecord.lid = lhpuser.uname 
            and lhpweekrecord.subjid =  "' . $_POST['allocated_subject'] . '"
            and lhpweekrecord.week =  "' . $week . '"
            and lhpweekrecord.term = "' . $active_term["term"] . '") as score',
            'where' => array(
                'lhpuser.classid' => $class_details['classid'],
            ),
        );
        $week_scores_recorder = $model->getRows($tblName, $conditions);
        include_once '../view/include/scoresheet/recorder.php';
    } elseif ($_POST['action'] == 'record_weekly_scores_for_all') {

        $tblName = 'lhpsubject';
        $conditions = array(
            'where' => array(
                'lhpsubject.sbjid' => $_POST['allocated_subject'],
            ),
            'joinl' => array(
                'lhpclass' => ' on lhpsubject.classid = lhpclass.classid',
            ),
            'return_type' => 'single',

        );
        $class_details = $model->getRows($tblName, $conditions);
        $response = '';
        $tblName = 'lhpweekrecord';
        foreach (array_combine($_POST['all_users'], $_POST['all_scores']) as $user => $scores) {
            if ($scores >= 1 && 10 >= $scores) {
                $conditions = array(
                    'where' => array(
                        'lhpweekrecord.lid' => $user,
                        'lhpweekrecord.subjid' => $_POST['allocated_subject'],
                        'lhpweekrecord.term' => $active_term["term"],
                        'lhpweekrecord.week' => $_POST['week_num'],
                    ),
                    'return_type' => 'single',
                );
                $check_if_exist = $model->getRows($tblName, $conditions);

                if (!empty($check_if_exist)) {
                    $score_data = array(
                        'score' => $scores
                    );
                    $conditions = array(
                        'lhpweekrecord.lid' => $user,
                        'lhpweekrecord.subjid' => $_POST['allocated_subject'],
                        'lhpweekrecord.term' => $active_term["term"],
                        'lhpweekrecord.week' => $_POST['week_num'],
                    );
                    $action = $model->upDate($tblName, $score_data, $conditions);
                    if ($action) {

                        $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Success! <b>Successfully updated ' . $_POST['week_num'] . '  score for learner with ID : ' . $user . '</b>!</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    } else {
                        $response .=
                            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Error! Unable to update ' . $_POST['week_num'] . '  score for learner with ID : ' . $user . '</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    }
                } else {

                    $score_data = array(
                        'score' => $scores,
                        'lid' => $user,
                        'classid' => $class_details['classid'],
                        'subjid' => $_POST['allocated_subject'],
                        'week' => $_POST['week_num'],
                        'term' => $active_term["term"],
                    );
                    $action = $model->insert_data($tblName, $score_data);
                    if ($action) {

                        $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Success! <b>Successfully recorded ' . $_POST['week_num'] . '  score for learner with ID : ' . $user . '</b>!</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    } else {
                        $response .=
                            '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Error! Unable to record ' . $_POST['week_num'] . '  score for learner with ID : ' . $user . '</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
                    }
                }
            } else {
                $response .=
                    '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                                  <div class="alert-text">Error! Unable to record 0 or scores greater than ' . $result_config['exam_score'] . ' as Exam score for learner with ID : ' . $user . '</div>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div> <br>';
            }
        }
        echo $response;
    }
}

if ($_POST['action'] == 'affective_manager' && isset($active_term)) {
    $scoresheet_type = 'AFFECTIVE';
    $tblName = 'lhpresultconfig';
    $conditions = array(
        'where' => array(
            'term' => $active_term['term'],
        ),
        'return_type' => 'single',

    );
    $result_config = $model->getRows($tblName, $conditions);

    $tblName = 'lhpclass';
    $conditions = array(
        'where' => array(
            'classid' => $_POST['affective_class'],
        ),
        'return_type' => 'single',

    );
    $class_details = $model->getRows($tblName, $conditions);


    $tblName = 'lhpuser';
    $conditions = array(
        'select' => 'lhpuser.fname, lhpuser.picture, lhpuser.classid, lhpuser.uname, 
        
        (SELECT lhpaffective.total_present from lhpaffective where lhpuser.uname = lhpaffective.uname
        and lhpaffective.classid = "' . $class_details["classid"] . '"
        and lhpaffective.term = "' . $active_term["term"] . '") as present,

        (SELECT lhpaffective.comment from lhpaffective where  lhpaffective.uname = lhpuser.uname 
        and lhpaffective.classid = "' . $class_details["classid"] . '"
        and lhpaffective.term = "' . $active_term["term"] . '") as comment,

        (SELECT lhpaffective.rating1 from lhpaffective where  lhpaffective.uname = lhpuser.uname 
        and lhpaffective.classid = "' . $class_details["classid"] . '"
        and lhpaffective.term = "' . $active_term["term"] . '") as rating1,

        (SELECT lhpaffective.rating2 from lhpaffective where  lhpaffective.uname = lhpuser.uname 
        and lhpaffective.classid = "' . $class_details["classid"] . '"
        and lhpaffective.term = "' . $active_term["term"] . '") as rating2,

        (SELECT lhpaffective.rating3 from lhpaffective where  lhpaffective.uname = lhpuser.uname 
        and lhpaffective.classid = "' . $class_details["classid"] . '"
        and lhpaffective.term = "' . $active_term["term"] . '") as rating3,

        (SELECT lhpaffective.rating4 from lhpaffective where  lhpaffective.uname = lhpuser.uname 
        and lhpaffective.classid = "' . $class_details["classid"] . '"
        and lhpaffective.term = "' . $active_term["term"] . '") as rating4,
        
        (SELECT lhpaffective.rating5 from lhpaffective where  lhpaffective.uname = lhpuser.uname 
        and lhpaffective.classid = "' . $class_details["classid"] . '"
        and lhpaffective.term = "' . $active_term["term"] . '") as rating5',
        'where' => array(
            'lhpuser.classid' => $_POST['affective_class'],
        ),
    );
    $affective_recorder = $model->getRows($tblName, $conditions);
    include_once '../view/include/classmanager/recorder.php';
} elseif ($_POST['action'] == 'record_attendance_for_all') {

    $tblName = 'lhpresultconfig';
    $conditions = array(
        'where' => array(
            'term' => $active_term['term'],
        ),
        'return_type' => 'single',

    );
    $result_config = $model->getRows($tblName, $conditions);

    $response = '';
    $tblName = 'lhpaffective';
    foreach ($_POST['all_users'] as $idx => $user) {
        $days = $_POST['all_present'][$idx];
        $comment = $_POST['all_comment'][$idx];

        if ($days >= 1 && $days <= $result_config['sch_open'] && strlen($comment) > 5) {

            $conditions = array(
                'where' => array(
                    'lhpaffective.uname' => $user,
                    'lhpaffective.classid' => $_POST['affective_class'],
                    'lhpaffective.term' => $active_term["term"],
                ),
                'return_type' => 'single',
            );
            $check_if_exist = $model->getRows($tblName, $conditions);

            if (!empty($check_if_exist)) {
                $update_data = array(
                    'lhpaffective.total_present' => $days,
                    'lhpaffective.comment' => addslashes($comment)
                );
                $conditions = array(
                    'lhpaffective.uname' => $user,
                    'lhpaffective.classid' => $_POST['affective_class'],
                    'lhpaffective.term' => $active_term["term"],
                );
                $action = $model->upDate($tblName, $update_data, $conditions);
                if ($action) {

                    $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Success! <b>Successfully updated Attendance and Comment records for learner with ID : ' . $user . '</b>!</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
                } else {
                    $response .=
                        '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Error! Unable to update Attendance and Comment records for learner with ID : ' . $user . '</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
                }
            } else {

                $new_data = array(
                    'uname' => $user,
                    'classid' => $_POST['affective_class'],
                    'term' => $active_term["term"],
                    'total_present' => $days,
                    'comment' => addslashes($comment),
                );
                $action = $model->insert_data($tblName, $new_data);
                if ($action) {

                    $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Success! <b>Successfully recorded Attendance and Comment data for learner with ID : ' . $user . '</b>!</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
                } else {
                    $response .=
                        '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Error! Unable to record Attendance and Comment data for learner with ID : ' . $user . '</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
                }
            }
        } else {
            $response .=
                '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Error! Unable to record present days greater than school open days : ' . $result_config['sch_open'] . ' or Empty Comment for learner with ID : ' . $user . '</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
        }
    }

    echo $response;
} elseif ($_POST['action'] == 'record_ratings_for_all') {

    $tblName = 'lhpresultconfig';
    $conditions = array(
        'where' => array(
            'term' => $active_term['term'],
        ),
        'return_type' => 'single',

    );
    $result_config = $model->getRows($tblName, $conditions);

    $response = '';
    $tblName = 'lhpaffective';
    foreach ($_POST['all_users'] as $idx => $user) {
        $rate_a = $_POST['all_rating1'][$idx];
        $rate_b = $_POST['all_rating2'][$idx];
        $rate_c = $_POST['all_rating3'][$idx];
        $rate_d = $_POST['all_rating4'][$idx];
        $rate_e = $_POST['all_rating5'][$idx];

        if (
            $rate_a >= 1 && $rate_a <= 5
            && $rate_b >= 1 && $rate_b <= 5
            && $rate_c >= 1 && $rate_c <= 5
            && $rate_d >= 1 && $rate_d <= 5
            && $rate_e >= 1 && $rate_e <= 5
        ) {

            $conditions = array(
                'where' => array(
                    'lhpaffective.uname' => $user,
                    'lhpaffective.classid' => $_POST['affective_class'],
                    'lhpaffective.term' => $active_term["term"]
                ),
                'return_type' => 'single',
            );
            $check_if_exist = $model->getRows($tblName, $conditions);

            if (!empty($check_if_exist)) {
                $update_data = array(
                    'lhpaffective.rating1' => $rate_a,
                    'lhpaffective.rating2' => $rate_b,
                    'lhpaffective.rating3' => $rate_c,
                    'lhpaffective.rating4' => $rate_d,
                    'lhpaffective.rating5' => $rate_e
                );
                $conditions = array(
                    'lhpaffective.uname' => $user,
                    'lhpaffective.classid' => $_POST['affective_class'],
                    'lhpaffective.term' => $active_term["term"]
                );
                $action = $model->upDate($tblName, $update_data, $conditions);
                if ($action) {

                    $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Success! <b>Successfully updated Affective domain ratings records for learner with ID : ' . $user . '</b>!</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
                } else {
                    $response .=
                        '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Error! Unable to update Affective domain ratings  records for learner with ID : ' . $user . '</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
                }
            } else {

                $new_data = array(
                    'uname' => $user,
                    'classid' => $_POST['affective_class'],
                    'term' => $active_term["term"],
                    'rating1' => $rate_a,
                    'rating2' => $rate_b,
                    'rating3' => $rate_c,
                    'rating4' => $rate_d,
                    'rating5' => $rate_e,
                );
                $action = $model->insert_data($tblName, $new_data);
                if ($action) {

                    $response .= '<div class="alert text-white bg-success d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Success! <b>Successfully recorded Affective domain ratings for learner with ID : ' . $user . '</b>!</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
                } else {
                    $response .=
                        '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Error! Unable to record Affective domain ratings  for learner with ID : ' . $user . '</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
                }
            }
        } else {
            $response .=
                '<div class="alert text-white bg-danger d-flex align-items-center justify-content-between" role="alert">
                              <div class="alert-text">Error! Unable to record Affective domain ratings greater than 5 or less than 1 for learner with ID : ' . $user . '</div>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div> <br>';
        }
    }

    echo $response;
} elseif ($_POST['action'] == 'compute_class_broasdsheet') {

}


?>