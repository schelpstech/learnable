<?php
    if (!isset($portalRoute)) {
        header('Location: ../../app/router.php?pageid=subject');
        exit;
    }
    include 'header.php';
    include 'nav.php';
    include 'navigator.php';
?>
<div class="row">
    <?php
    $pageid = $portalRoute->page();
    if ($pageid == 'note') {
        include 'pages/selectnote.php';
    } elseif ($pageid == 'task') {
        include 'pages/selectask.php';
    } elseif ($pageid == 'scheme') {
        include 'pages/viewscheme.php';
    } elseif ($pageid == 'work') {
        include 'pages/selectwork.php';
    } elseif ($pageid == 'result') {
        include 'pages/result.php';
    } elseif ($pageid == 'subject') {
        include 'pages/subject.php';
    }
    ?>
</div>
</section>
<?php
    include 'footer.php';
?>
