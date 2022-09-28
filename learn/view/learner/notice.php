<?php
include '../include/header.php';
include '../include/nav.php';
include '../include/navigator.php';
?>
<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">


        <div class="row ">
            
            <div class="col-xl-6">
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

            <div class="col-xl-6">
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
            
        </div>
    </div>
</div>


</section>
<?php
include '../include/footer.php';
?>