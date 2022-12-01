<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row ">
            <div class="col-md-6 col-lg-6 col-xl-6 box-col-6">
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
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Allocated Subjects</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="subject">Select Subject</label>
                                </div>
                                <select class="form-select" tabindex="2" id="allocated_subject" onchange="scoresheet_dashboard()" required="yes">
                                    <option value="">Select Allocated Subject</option>
                                    <?php
                                    if (!empty($all_subject_allocated)) {
                                        foreach ($all_subject_allocated as $data) {
                                    ?>
                                            <option value="<?php echo $data['sbjid'] ?>"><?php echo $data['classname'].' - '. $data['subject']?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 " id="myscoresheet_dashboard">
                <div class="white_card card_height_100 mb_30 social_media_card">
                    <div class="white_card_header">
                        <div class="main-title">
                            <h3 class="m-0"> Scoresheet</h3>
                        </div>
                    </div>
                    <div class="media_thumb ml_25">
                        <img src="../../asset/img/media.svg" alt="">
                    </div>
                    <div class="media_thumb ml_25" id="loader" style="display: none;">
                        <img src="../../asset/img/app/giphy.gif" alt="">
                    </div>
                    <div class="media_card_body" id="board">
                        <div class="media_card_list">
                            <div class="single_media_card">
                                <span>CA Scoresheet</span>
                                <h3>-</h3>
                            </div>
                            <div class="single_media_card">
                                <span>Exam Scoresheet </span>
                                <h3>-</h3>
                            </div>
                            <div class="single_media_card">
                                <span>Weekly Assessment</span>
                                <h3>-</h3>
                            </div>
                            <div class="single_media_card">
                                <span>Cumulative Scoresheet</span>
                                <h3>-</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="media_thumb ml_25" id="response_loader" style="display: none;">
                <img src="../../asset/img/app/giphy.gif" alt="">
            </div>
        <div class="row " id="response">
            
        </div>

    </div>
</div>