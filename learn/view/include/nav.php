<nav class="sidebar" id="portal-navigation" aria-label="Portal navigation">
    <div class="logo d-flex justify-content-between">
        <a class="large_logo" href="../../app/router.php?pageid=index"><img src="../../asset/img/school/<?php echo htmlspecialchars($sch_details['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="<?php echo htmlspecialchars($sch_details['schname'], ENT_QUOTES, 'UTF-8') ?>"></a>
        <a class="small_logo" href="../../app/router.php?pageid=index"><img src="../../asset/img/school/<?php echo htmlspecialchars($sch_details['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="<?php echo htmlspecialchars($sch_details['schname'], ENT_QUOTES, 'UTF-8') ?>"></a>
        <button type="button" class="sidebar_close_icon d-lg-none border-0 bg-transparent" aria-label="Close navigation">
            <i class="ti-close" aria-hidden="true"></i>
        </button>
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
                    <button type="button" class="sidebar_icon d-lg-none border-0 bg-transparent" aria-label="Open navigation" aria-expanded="false" aria-controls="portal-navigation">
                        <i class="ti-menu" aria-hidden="true"></i>
                    </button>
                    <label class="form-label switch_toggle d-none d-lg-block" for="checkbox">
                        <input type="checkbox" id="checkbox" aria-label="Compact navigation">
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
                            <?php
                            $profilePicture = basename(trim((string)($learner_profile['picture'] ?? '')));
                            if ($profilePicture === '' || !is_file(__DIR__.'/../../asset/img/passport/'.$profilePicture)) {
                                $profilePicture = 'nopix.jpg';
                            }
                            ?>
                            <img src="../../asset/img/passport/<?php echo rawurlencode($profilePicture); ?>" alt="Profile photo">
                            <div class="profile_info_iner">
                                <div class="profile_author_name">
                                    <p><?php echo htmlspecialchars((string)($learner_profile['fname'] ?? $staff_details['staffname'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p><?php echo htmlspecialchars((string)($learner_profile['uname'] ?? $staff_details['sname'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
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
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['portal_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
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
        <div class="modal fade" id="resources" tabindex="-1" role="dialog" aria-labelledby="resourceActionsTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resourceActionsTitle">Learning resource actions</h5>
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
