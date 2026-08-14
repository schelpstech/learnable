<?php
    if (!isset($portalRoute)) {
        header('Location: ../../app/router.php?pageid=index');
        exit;
    }
    include 'header.php';
    include 'nav.php';
    include 'navigator.php';
?>
<?php
    $pageid = $portalRoute->page();
    $instance = $portalRoute->param('instance');
    $item = $portalRoute->param('item');
    $itemRef = $portalRoute->param('item_ref');
    if ($pageid == 'note') {
        include 'pages/viewnote.php';
    } elseif ($pageid == 'task') {
        include 'pages/viewtask.php';
    } elseif ($pageid == 'work') {
        include 'pages/viewwork.php';
    }elseif ($pageid == 'scheme') {
        include 'pages/viewscheme.php';
    }
    
    elseif ($pageid == 'result') {
        include 'pages/viewresult.php';
    }
    
    elseif ($pageid == 'midterm_result') {
        include 'pages/viewmidtermreport.php';
    }
    
    elseif ($pageid == 'class_manager') {
        include 'classmanager/dashboard.php';
    }elseif ($pageid == 'scoresheet') {
        include 'scoresheet/dashboard.php';
    }elseif ($pageid == 'manage_learner' && $instance !== null) {
        include 'form/manage_learner.php';
    }elseif ($pageid == 'resources' && $item == 'modify_topic' && $itemRef !== null) {
        include 'form/modifyscheme.php';
    }elseif ($pageid == 'resources' && $item == 'add_topic' ) {
        include 'form/addscheme.php';
    }elseif ($pageid == 'resources' && $item == 'modify_note' && $itemRef !== null) {
        include 'form/modifynote.php';
    }elseif ($pageid == 'resources' && $item == 'add_note' ) {
        include 'form/addnote.php';
    }elseif ($pageid == 'resources' && $item == 'modify_task' && $itemRef !== null) {
        include 'form/modifytask.php';
    }elseif ($pageid == 'resources' && $item == 'add_task' ) {
        include 'form/addtask.php';
    }elseif ($pageid == 'payment' && $instance == 'bill') {
        include 'payment/bill.php';
    } elseif ($pageid == 'payment' && $instance == 'transaction') {
        include 'payment/transaction.php';
    } elseif ($pageid == 'payment' && $instance == 'payment') {
        include 'payment/paynow.php';
    } elseif ($pageid == 'resources' && $item == 'add_cbt') {
        include 'form/createcbt.php';
    }
?>
</section>
<?php
    include 'footer.php';
?>
