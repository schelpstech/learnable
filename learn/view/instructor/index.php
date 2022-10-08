<?php
include '../include/header.php';
include '../include/nav.php';
include '../include/navigator.php';
?>
<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">


        <div class="row ">
            <div class="col-md-6 col-lg-6 col-xl-4 box-col-6">
                <div class="card custom-card">
                    <div class="card-header"><img class="img-fluid" src="../../asset/img/app/profile_bg.jpg" alt="" data-original-title="" title=""></div>
                    <div class="card-profile"><img class="rounded-circle" src="
                            <?php
                            if (isset($learner_profile['passport'])) {
                                $dir = '../../storage/staff/' . $learner_profile['passport'];
                                if (file_exists($dir)) {
                                    echo $dir;
                                } else {
                                    echo '../../asset/img/staff/instructor.jpg';
                                }
                            } else {
                                echo '../../asset/img/staff/instructor.jpg';
                            }
                            ?>" alt="" data-original-title="" title=""></div>

                    <div class="text-center profile-details">
                        <h4><?php echo $staff_details['staffname'] ?? ' - '; ?></h4>
                    </div>
                    <div class="card-footer row">
                        <div class="col-6 col-sm-6">
                            <h6>Email address</h6>
                            <strong><small><?php echo $staff_details['semail'] ?? ' - '; ?></small></strong>
                        </div>
                        <div class="col-6 col-sm-6">
                            <h6>Current Class</h6>
                            <p><?php echo $staff_details['sfone'] ?? ' - '; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 ">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Edit Profile</h3>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">

                        <form action="../../app/update.php" method="POST" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <label for="tel" class="form-label col-sm-4 col-form-label">Phone number</label>
                                <div class="col-sm-8">
                                    <input type="tel" class="form-label form-control" id="tel" minlength="11" maxlength="11" required="yes" name="phone" value="<?php echo $learner_profile['numb'] ?? ''; ?>" placeholder="<?php echo $learner_profile['numb'] ?? 'Enter Phonenumber '; ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputPassword3" class="form-label col-sm-4 col-form-label">Password</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="inputPassword3" minlength="8" maxlength="8" required="yes" name="password" value="<?php echo $learner_profile['upwd'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class=" row">
                                <div class="col-sm-10">
                                    <button type="submit" name="update" value="update_profile" class="btn btn-primary">Update Information</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 ">
                <div class="white_card card_height_100 mb_30 social_media_card">
                    <div class="white_card_header">
                        <div class="main-title">
                            <h3 class="m-0"> Dashboard</h3>
                        </div>
                    </div>
                    <div class="media_thumb ml_25">
                        <img src="../../asset/img/media.svg" alt="">
                    </div>
                    <div class="media_card_body">
                        <div class="media_card_list">
                            <div class="single_media_card">
                                <span>Allocated Subjects</span>
                                <h3><?php echo $statistics['subject'] ?? 0; ?></h3>
                            </div>
                            <div class="single_media_card">
                                <span>Notes Uploaded</span>
                                <h3><?php echo $statistics['note'] ?? 0; ?></h3>
                            </div>
                            <div class="single_media_card">
                                <span>Assessments Created</span>
                                <h3><?php echo $statistics['task'] ?? 0; ?></h3>
                            </div>
                            <div class="single_media_card">
                                <span>Class Allocated</span>
                                <h3><?php echo $statistics['class'] ?? 0; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="white_card card_height_100 mb_30 ">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Task List</h3>
                                <span>Recent Assignments</span>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body QA_section">
                        <div class="todo_wrapper">

                            <?php
                            if (!empty($task_list)) {
                                foreach ($task_list as $task) {
                            ?>
                                    <div class="single_todo d-flex justify-content-between align-items-center">
                                        <a href="../../app/router.php?pageid=task&ref=<?php echo $task['questid'] ?>">
                                            <div class="lodo_left d-flex align-items-center">
                                                <div class="bar_line mr_10"></div>

                                                <div class="todo_head">
                                                    <h5 class="f_s_18 f_w_900 mb-0"><?php echo ucwords($task['topic']) ?></h5>
                                                    <br>
                                                    <h6><?php echo ucwords($task['sbjname']) ?></h6>
                                                    <p class="f_s_12 f_w_400 mb-0 text_color_8">Assigned :: <?php echo $task['rectime']; ?></p>
                                                </div>
                                            </div>
                                            <div class="lodo_right"> <a class="mark_complete">Deadline :: <?php echo $task['deadline'] ?? date("d-m-Y"); ?></a> </div>
                                        </a>
                                    </div>
                            <?php
                                }
                            } else {
                                echo '
                            <div class="single_todo d-flex justify-content-between align-items-center">
                            <div class="lodo_left d-flex align-items-center">
                                <div class="bar_line mr_10"></div>
                                
                                <div class="todo_head">
                                    <h5 class="f_s_18 f_w_900 mb-0">No Assignment yet </h5>
                                    <p class="f_s_12 f_w_400 mb-0 text_color_8"></p>
                                </div>
                            </div>
                            <div class="lodo_right"> <a href="#" class="badge_complete"> as at ' . date("d-m-Y") . '</a> </div>
                        </div>';
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Recent Activity</h3>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div class="Activity_timeline">
                            <ul>
                                <?php
                                if (!empty($recent)) {
                                    foreach ($recent as $activity) {
                                ?>
                                        <li>
                                            <div class="activity_bell"></div>
                                            <div class="timeLine_inner d-flex align-items-center">
                                                <div class="activity_wrap">
                                                    <h6> <?php echo $activity['rectime']; ?></h6>
                                                    <h6> <?php echo $activity['subject']; ?></h6>
                                                    <p>
                                                        <?php echo $activity['message']; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                <?php
                                    }
                                } else {
                                    echo '
                                <li>
                                    <div class="activity_bell "></div>
                                    <div class="timeLine_inner d-flex align-items-center">
                                        <div class="activity_wrap">
                                            <h6></h6>
                                            <p>No recent activity
                                            </p>
                                        </div>
                                    </div>
                                </li>
                                ';
                                } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="white_card card_height_100 mb_30">
                    <div class="date_picker_wrapper">
                        <div class="default-datepicker">
                            <div class="datepicker-here" data-language='en'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</section>
<?php
include '../include/footer.php';
?>