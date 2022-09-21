<?php
    include '../include/header.php';
    include '../include/nav.php';
    include '../include/navigator.php';
?>
<?php
    if ($_SESSION['pageid'] == 'note') {
        include '../include/pages/viewnote.php';
    } elseif ($_SESSION['pageid'] == 'task') {
        include '../include/pages/viewtask.php';
    } elseif ($_SESSION['pageid'] == 'work') {
        include '../include/pages/viewwork.php';
    }elseif ($_SESSION['pageid'] == 'scheme') {
        include '../include/pages/viewscheme.php';
    }elseif ($_SESSION['pageid'] == 'result') {
        include '../include/pages/viewresult.php';
    }
?>
</section>
<?php
    include '../include/footer.php';
?>