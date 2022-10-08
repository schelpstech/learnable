<?php
    include 'header.php';
    include 'nav.php';
    include 'navigator.php';
?>
<div class="row">
    <?php
    if ($_SESSION['pageid'] == 'note') {
        include 'pages/selectnote.php';
    } elseif ($_SESSION['pageid'] == 'task') {
        include 'pages/selectask.php';
    } elseif ($_SESSION['pageid'] == 'scheme') {
        include 'pages/viewscheme.php';
    } elseif ($_SESSION['pageid'] == 'work') {
        include 'pages/selectwork.php';
    } elseif ($_SESSION['pageid'] == 'result') {
        include 'pages/result.php';
    } elseif ($_SESSION['pageid'] == 'subject') {
        include 'pages/subject.php';
    }
    ?>
</div>
</section>
<?php
    include 'footer.php';
?>