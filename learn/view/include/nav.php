<nav class="sidebar">
    <div class="logo d-flex justify-content-between">
        <a class="large_logo" href="index-2.html"><img src="../../asset/img/school/<?php echo $sch_details['logo'] ?>" alt=""></a>
        <a class="small_logo" href="index-2.html"><img src="../../asset/img/school/<?php echo $sch_details['logo'] ?>" alt=""></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <ul id="sidebar_menu">
        <li>
            <a href="index.php" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
                </div>
                <div class="nav_title">
                    <span>My Profile</span>
                </div>
            </a>
        </li>
        <li>
            <a href="index-2.html" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
                </div>
                <div class="nav_title">
                    <span>Notice Board</span>
                </div>
            </a>
        </li>
        <h4 class="menu-text"><span>My Class</span> <i class="fas fa-ellipsis-h"></i> </h4>
        <li>
            <a href="./subject.php" aria-expanded="false">
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

        <li>
            <a href="index-2.html" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="../../asset/img/menu-icon/dashboard.svg" alt="">
                </div>
                <div class="nav_title">
                    <span>School Calendar</span>
                </div>
            </a>
        </li>

    </ul>
</nav>


<section class="main_content dashboard_part large_header_bg">

    <div class="container-fluid g-0">
        <div class="row">
            <div class="col-lg-12 p-0 ">
                <div class="header_iner d-flex justify-content-between align-items-center">
                    <div class="sidebar_icon d-lg-none">
                        <i class="ti-menu"></i>
                    </div>
                    <label class="form-label switch_toggle d-none d-lg-block" for="checkbox">
                        <input type="checkbox" id="checkbox">
                        <div class="slider round open_miniSide"></div>
                    </label>
                    <div class="header_right d-flex justify-content-between align-items-center">
                        <?php
                        if (isset($_SESSION['msg'])) {
                            printf('<b>%s</b>', $_SESSION['msg']);
                            unset($_SESSION['msg']);
                        }
                        ?>
                        <div class="profile_info">
                            <img src="../../asset/img/client_img.png" alt="#">
                            <div class="profile_info_iner">
                                <div class="profile_author_name">
                                    <p><?php echo $learner_profile['fname'] ?></p>
                                    <p><?php echo $learner_profile['uname'] ?></p>
                                </div>
                                <div class="profile_info_details">
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logout">
                                        Log Out
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="modal fade" id="logout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"> Are you sure you want to log out?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="../../app/useracces.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <button type="submit" value="logout" name="logout" class="btn btn-danger">Yes! Log out</button>
                    </div>
                </form>
            </div>
        </div>
    </div>