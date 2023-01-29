<nav class="sidebar">
    <div class="logo d-flex justify-content-between">
        <a class="large_logo" href="index-2.html"><img src="../../asset/img/school/<?php echo $sch_details['logo'] ?>" alt=""></a>
        <a class="small_logo" href="index-2.html"><img src="../../asset/img/school/<?php echo $sch_details['logo'] ?>" alt=""></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <?php
    if ($_SESSION['user_type'] === "Instructor") {
        include '../include/navinstructor.php';
    } elseif ($_SESSION['user_type'] === "Learner") {
        include '../include/navlearner.php';
    }
    ?>
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
                        <div id="info">

                        </div>
                        <div class="profile_info">
                            <img src="
                            <?php
                            if (isset($learner_profile['picture'])) {
                                echo '../../asset/img/passport/'.$learner_profile['picture'];
                            } else {
                                echo '../../asset/img/passport/nopix.jpg';
                            }
                            ?>" alt="#">
                            <div class="profile_info_iner">
                                <div class="profile_author_name">
                                    <p><?php echo $learner_profile['fname'] ?? $staff_details['staffname'] ?></p>
                                    <p><?php echo $learner_profile['uname'] ?? $staff_details['sname'] ?></p>
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
    <?php
    if ($_SESSION['user_type'] === "Instructor") {
        echo '
        <div class="modal fade" id="resources" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-footer" >
                            <a href="../../app/router.php?pageid=resources&item=add_topic" type="button" class="btn btn-primary" >Add Scheme of work</a>
                        </div>
                        <div class="modal-footer">
                            <a href="../../app/router.php?pageid=resources&item=add_note" type="button" class="btn btn-warning" >Add e-Notes</a> <br>
                        </div>
                        <div class="modal-footer">
                            <a href="../../app/router.php?pageid=resources&item=add_task" type="button" class="btn btn-info">Add e-Assessment</a><br>
                        </div>
                        <div class="modal-footer">
                            <a href="../../app/router.php?pageid=resources&item=add_cbt" type="button" class="btn btn-success">Create CBT Test</a><br>
                        </div>
                </div>
            </div>
        </div>            
        ';
    } ?>
