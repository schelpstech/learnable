<?php
    include 'header.php';
    include 'nav.php';
    include 'navigator.php';
?>
<?php
    if ($_SESSION['pageid'] == 'note') {
        include 'pages/viewnote.php';
    } elseif ($_SESSION['pageid'] == 'task') {
        include 'pages/viewtask.php';
    } elseif ($_SESSION['pageid'] == 'work') {
        include 'pages/viewwork.php';
    }elseif ($_SESSION['pageid'] == 'scheme') {
        include 'pages/viewscheme.php';
    }elseif ($_SESSION['pageid'] == 'result') {
        include 'pages/viewresult.php';
    }elseif ($_SESSION['pageid'] == 'class_manager') {
        include 'classmanager/dashboard.php';
    }elseif ($_SESSION['pageid'] == 'resources' && $_SESSION['item'] == 'modify_topic' && isset($_SESSION['item_ref'])) {
        include 'form/modifyscheme.php';
    }elseif ($_SESSION['pageid'] == 'resources' && $_SESSION['item'] == 'add_topic' ) {
        include 'form/addscheme.php';
    }elseif ($_SESSION['pageid'] == 'resources' && $_SESSION['item'] == 'modify_note' && isset($_SESSION['item_ref'])) {
        include 'form/modifynote.php';
    }elseif ($_SESSION['pageid'] == 'resources' && $_SESSION['item'] == 'add_note' ) {
        include 'form/addnote.php';
    }elseif ($_SESSION['pageid'] == 'resources' && $_SESSION['item'] == 'modify_task' && isset($_SESSION['item_ref'])) {
        include 'form/modifytask.php';
    }elseif ($_SESSION['pageid'] == 'resources' && $_SESSION['item'] == 'add_task' ) {
        include 'form/addtask.php';
    }elseif ($_SESSION['pageid'] == 'payment' && $_SESSION['instance'] == 'bill') {
        include 'payment/bill.php';
    } elseif ($_SESSION['pageid'] == 'payment' && $_SESSION['instance'] == 'transaction') {
        include 'payment/transaction.php';
    } elseif ($_SESSION['pageid'] == 'payment' && $_SESSION['instance'] == 'payment') {
        include 'payment/paynow.php';
    }
?>
</section>
<?php
    include 'footer.php';
?>