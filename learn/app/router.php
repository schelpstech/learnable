<?php
require_once('../controller/start.inc.php');

//redirect to subject page 
if (isset($_GET['pageid'])) {
  $pageid = $_GET['pageid'];
  if ($pageid == 'index' && $_SESSION['user_type'] === "Learner") {
    $model->redirect('../view/learner/index.php');
  } elseif ($pageid == 'overview' && $_SESSION['user_type'] === "Learner") {
    $model->redirect('../view/learner/notice.php');
  } elseif ($pageid == 'subject' && $_SESSION['user_type'] === "Learner") {
    $_SESSION['pageid'] = $pageid;
    $model->redirect('../view/include/selector.php');
  } elseif ($pageid == 'index' && $_SESSION['user_type'] === "Instructor") {
    $model->redirect('../view/instructor/index.php');
  } elseif ($pageid == 'overview' && $_SESSION['user_type'] === "Instructor") {
    $model->redirect('../view/instructor/notice.php');
  } elseif ($pageid == 'subject' && $_SESSION['user_type'] === "Instructor") {
    $_SESSION['pageid'] = $pageid;
    $model->redirect('../view/include/selector.php');
  }
}

//redirect to note selection page  
if (isset($_GET['pageid']) && isset($_GET['subjectid'])) {
  $pageid = $_GET['pageid'];
  $subjectid = $_GET['subjectid'];
  $_SESSION['subjectid'] = $subjectid;
  $_SESSION['pageid'] = $pageid;
  $model->redirect('../view/include/selector.php');
}

//redirect to view selected  page     
if (isset($_GET['pageid']) && isset($_GET['ref'])) {
  $pageid = $_GET['pageid'];
  $ref = $_GET['ref'];
  $_SESSION['ref'] = $ref;
  $_SESSION['pageid'] = $pageid;
  $model->redirect('../view/include/viewer.php');
}

//redirect to select result - Learner    
if (isset($_GET['pageid']) && isset($_GET['instance'])) {
  $pageid = $_GET['pageid'];
  $instance = $_GET['instance'];
  $_SESSION['pageid'] = $pageid;
  $_SESSION['instance'] = $instance;
  $model->redirect('../view/include/selector.php');
}


//redirect to view selected  result / midterm report - Learner    
if (isset($_GET['pageid']) && isset($_GET['ref'])) {
  $pageid = $_GET['pageid'];
  $ref = $_GET['ref'];
  $_SESSION['pageid'] = $pageid;
  $_SESSION['ref'] = $ref;
  $model->redirect('../view/include/viewer.php');
}


//redirect to view school bill for active term - Learner    
if (isset($_GET['pageid']) && $_GET['pageid'] == 'payment') {
  $pageid = $_GET['pageid'];
  $instance = $_GET['instance'];
  $_SESSION['instance'] = $instance;
  $_SESSION['pageid'] = $pageid;
  $model->redirect('../view/include/viewer.php');
}

//redirect to view transactions - Learner    
if (isset($_GET['pageid']) && $_GET['pageid'] == 'payment') {
  $pageid = $_GET['pageid'];
  $instance = $_GET['instance'];
  $_SESSION['instance'] = $instance;
  $_SESSION['pageid'] = $pageid;
  $model->redirect('../view/include/viewer.php');
}

//redirect to add Resources -   Topic
if (isset($_GET['pageid']) && isset($_GET['item'])) {
  $pageid = $_GET['pageid'];
  $item = $_GET['item'];
  $_SESSION['item'] = $item;
  $_SESSION['pageid'] = $pageid;
  $model->redirect('../view/include/viewer.php');
}

//Modify Note, Scheme and Task
if (isset($_GET['pageid']) && isset($_GET['item'])  && isset($_GET['item_ref'])) {
  $pageid = $_GET['pageid'];
  $item = $_GET['item'];
  $item_ref = $_GET['item_ref'];
  $_SESSION['item'] = $item;
  $_SESSION['pageid'] = $pageid;
  $_SESSION['item_ref'] = $item_ref;
  $model->redirect('../view/include/viewer.php');
}

//Class Manager
//redirect to view Class Manager - Instructor    
if (isset($_GET['pageid']) && $_GET['pageid'] == 'class_manager') {
  $_SESSION['pageid'] = $_GET['pageid'];
  $model->redirect('../view/include/viewer.php');
}
if (isset($_GET['pageid']) && $_GET['pageid'] == 'manage_learner') {
  $_SESSION['pageid'] = $_GET['pageid'];
  $_SESSION['instance'] = $_GET['instance'];
  $model->redirect('../view/include/viewer.php');
}

//Class Manager
//redirect to view Scoresheet - Instructor    
if (isset($_GET['pageid']) && $_GET['pageid'] == 'scoresheet') {
  $_SESSION['pageid'] = $_GET['pageid'];
  $model->redirect('../view/include/viewer.php');
}
