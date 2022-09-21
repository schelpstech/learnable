<?php
    require_once('../controller/start.inc.php');

 //redirect to note selection page - Learner  
    if (isset($_GET['pageid']) &&isset($_GET['subjectid'])) {
        $pageid = $_GET['pageid'];
        $subjectid = $_GET['subjectid'];
        $_SESSION['subjectid'] = $subjectid;
        $_SESSION['pageid'] = $pageid;
      $model->redirect('../view/learner/selector.php');
    }

 //redirect to view selected note page - Learner    
    if (isset($_GET['pageid']) && isset($_GET['ref'])) {
      $pageid = $_GET['pageid'];
      $ref = $_GET['ref'];
        $_SESSION['ref'] = $ref;
        $_SESSION['pageid'] = $pageid;
      $model->redirect('../view/learner/viewer.php');
    }

     //redirect to select result - Learner    
     if (isset($_GET['pageid']) && isset($_GET['instance'])) {
        $pageid = $_GET['pageid'];
        $instance = $_GET['instance'];
        $_SESSION['pageid'] = $pageid;
        $_SESSION['instance'] = $instance;
      $model->redirect('../view/learner/selector.php');
    }


         //redirect to view selected  result - Learner    
     if (isset($_GET['pageid']) && isset($_GET['ref'])) {
        $pageid = $_GET['pageid'];
        $ref = $_GET['ref'];
        $_SESSION['pageid'] = $pageid;
        $_SESSION['ref'] = $ref;
      $model->redirect('../view/learner/viewer.php');
    }


     //redirect to view school bill for active term - Learner    
     if (isset($_GET['pageid']) && $_GET['pageid'] =='payment') {
      $pageid = $_GET['pageid'];
      $instance = $_GET['instance'];
        $_SESSION['instance'] = $instance;
        $_SESSION['pageid'] = $pageid;
      $model->redirect('../view/learner/payment.php');
    }

     //redirect to view transactions - Learner    
     if (isset($_GET['pageid']) && $_GET['pageid'] =='payment') {
      $pageid = $_GET['pageid'];
      $instance = $_GET['instance'];
        $_SESSION['instance'] = $instance;
        $_SESSION['pageid'] = $pageid;
      $model->redirect('../view/learner/payment.php');
    }