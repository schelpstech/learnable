<ul id="sidebar_menu">
    <li>
        <a href="../../app/router.php?pageid=index" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
            </div>
            <div class="nav_title">
                <span>My Profile</span>
            </div>
        </a>
    </li>
    <li>
        <a href="../../app/router.php?pageid=overview" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
            </div>
            <div class="nav_title">
                <span>Overview</span>
            </div>
        </a>
    </li>
    <h4 class="menu-text"><span>My Class</span> <i class="fas fa-ellipsis-h"></i> </h4>
    <li>
        <a href="../../app/router.php?pageid=subject" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
            </div>
            <div class="nav_title">
                <span>My E-Notes</span>
            </div>
        </a>
    </li>
    <li>
        <a href="../../app/router.php?pageid=payment&instance=bill" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
            </div>
            <div class="nav_title">
                <span>My School Bills</span>
            </div>
        </a>
    </li>

    <li>
        <a href="../../app/router.php?pageid=payment&instance=transaction" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
            </div>
            <div class="nav_title">
                <span>My Transaction History</span>
            </div>
        </a>
    </li>

    <li>
        <a href="../../app/router.php?pageid=payment&instance=payment" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
            </div>
            <div class="nav_title">
                <span>Make Payment</span>
            </div>
        </a>
    </li>
    <li>
        <a href="../../app/router.php?pageid=result&instance=select" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
            </div>
            <div class="nav_title">
                <span>My Results</span>
            </div>
        </a>
    </li>

    <?php
    if (!empty($search_result)) {
        if ($search_result['midterm'] == 1) {
    ?>
        <li>
        <a href="../../app/router.php?pageid=midterm_result&ref=<?php echo $active_term['term'] ?>" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
            </div>
            <div class="nav_title">
                <span>Mid-Term Report</span>
            </div>
        </a>
    </li>
<?php
    }
    else{
        echo '';
    }
    }else{
        echo '';
    }
?>


    <li>
        <a href="calendar.php" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
            </div>
            <div class="nav_title">
                <span>School Calendar</span>
            </div>
        </a>
    </li>

</ul>