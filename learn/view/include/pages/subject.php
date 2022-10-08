
<div class="main_content_iner ">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="dashboard_header mb_50">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="dashboard_header_title">
                                <h4> Subjects for <?php echo $active_term['term'] ?></h4>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
            if($_SESSION['user_type'] === "Instructor"){
                $data = $report;
            }elseif($_SESSION['user_type'] === "Learner"){
                $data = $subject_list;
            }
            if (!empty($data)) {
                foreach ($data as $subject_list) {
            ?>

                    <div class="col-xl-4 ">
                        <div class="white_card card_height_100 mb_30 social_media_card">
                            <div class="white_card_header">
                                <div class="main-title">
                                    <h3 class="m-0"><?php echo ucwords($subject_list['sbjname'].' - '.$subject_list['classname'] ) ?></h3>
                                    <p class="m-0"><?php echo ucwords($subject_list['staffname']) ?></p>
                                </div>
                            </div>

                            <div class="media_card_body">
                                <div class="media_card_list">
                                    <div class="single_media_card">
                                        <span>Outlined Topics</span>
                                            <a href="../../app/router.php?pageid=scheme&ref=<?php echo $subject_list['sbjid'] ?>">
                                                <h3><?php echo $subject_list['topic'] ?> </h3>
                                            </a>
                                    </div>
                                    <div class="single_media_card">
                                        <span>Notes Uploaded</span>
                                            <a href="../../app/router.php?pageid=note&subjectid=<?php echo $subject_list['sbjid'] ?>">
                                                <h3><?php echo $subject_list['note'] ?> </h3>
                                            </a>
                                    </div>
                                    <div class="single_media_card">
                                        <span>Assessments</span>
                                            <a href="../../app/router.php?pageid=task&subjectid=<?php echo $subject_list['sbjid'] ?>">
                                                <h3><?php echo $subject_list['task'] ?> </h3>
                                            </a>
                                    </div>
                                    <div class="single_media_card">
                                        <span>Submitted Assignments</span>
                                            <a href="../../app/router.php?pageid=work&subjectid=<?php echo $subject_list['sbjid'] ?>">
                                                <h3><?php echo $subject_list['feedback'] ?> </h3>
                                            </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '
                        <div class="col-md-4">
                            <div class="card mb-3 widget-chart">
                                <div class="icon-wrapper rounded-circle">
                                    <div class="icon-wrapper-bg bg-danger"></div>
                                    <i class="ti-na text-danger"></i>
                                </div>
                                <div class="widget-numbers"><span>No Subject Allocated Yet</span></div>
                            </div>
                        </div>';
            }
            ?>

        </div>
    </div>
</div>

</section>
