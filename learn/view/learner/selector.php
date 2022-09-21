<?php
include '../include/header.php';
include '../include/nav.php';
include '../include/navigator.php';
?>
<div class="row">
    <?php
        if($_SESSION['pageid'] == 'note'){
            include '../include/pages/selectnote.php'; 
        }elseif($_SESSION['pageid'] == 'task'){
            include '../include/pages/selectask.php'; 
        }elseif($_SESSION['pageid'] == 'scheme'){
            include '../include/pages/viewscheme.php'; 
        }elseif($_SESSION['pageid'] == 'work'){
            include '../include/pages/selectwork.php'; 
        }elseif($_SESSION['pageid'] == 'result'){
            include '../include/pages/result.php'; 
        }
    ?>
</div>
</section>
<?php
include '../include/footer.php';
?>