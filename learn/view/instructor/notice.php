<?php
include '../include/header.php';
include '../include/nav.php';
include '../include/navigator.php';
?>
<div class="col-xl-12">
    <div class="white_card card_height_100 mb_30 ">
        <div class="row">
            <div class="col-lg-9">
                <div class="white_card_header">
                    <div class="box_header m-0">
                        <div class="main-title">
                            <h3 class="m-0">e-Note</h3>
                            <span>Overview</span>
                        </div>
                    </div>
                </div>
                <div class="white_card_body QA_section">
                    <div class="QA_table ">

                        <table class="table lms_table_active2 p-0">
                            <thead>
                                <tr>
                                    <th scope="col">Class</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Outlined Topics</th>
                                    <th scope="col">e-Notes</th>
                                    <th scope="col">Assessment</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                if (!empty($report)) {
                                    foreach ($report as $data) {
                                ?>
                                        <tr>
                                            
                                            <td>
                                                <div class="customer d-flex align-items-center">
                                                    <div class="social_media">
                                                        <i class="fab fa-wpforms"></i>
                                                    </div>
                                                    <div class="ml_18">
                                                        <h3 class="f_s_18 f_w_900 mb-0"><?php echo ucwords($data['classname']) ?>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <p> <small class="f_s_18 f_w_900 mb-0"><?php echo ucwords($data['sbjname']) ?></small></p>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h3 class="f_s_18 f_w_800 mb-0"><?php echo $data['topic'] ?></h3>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h3 class="f_s_18 f_w_800 mb-0"><?php echo $data['note'] ?></h3>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h3 class="f_s_18 f_w_800 mb-0"><?php echo $data['task'] ?></h3>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action_btns d-flex">
                                                    <a href="../../app/router.php?pageid=selectnote&subjectid=<?php echo $data['sbjid'] ?>" class="action_btn mr_10"> <i class="ti-hand-point-up"></i> </a>
                                                </div>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo '<div class="widget-numbers"><span>No Subject Allocated Yet</span></div>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 white_card_body pt_25">
                <div class="project_complete">
                    <div class="single_pro d-flex">
                        <div class="probox"></div>
                        <div class="box_content">
                            <h4><?php echo $statistics['subject'] ?? 0; ?></h4>
                            <span> Subjects</span>
                        </div>
                    </div>
                    <div class="single_pro d-flex">
                        <div class="probox blue_box"></div>
                        <div class="box_content">
                            <h4 class="bluish_text"><?php echo $statistics['topic'] ?? 0; ?></h4>
                            <span class="grayis_text">Outlined Topics</span>
                        </div>
                    </div>
                    <div class="single_pro d-flex">
                        <div class="probox blue_box"></div>
                        <div class="box_content">
                            <h4 class="bluish_text"><?php echo $statistics['note'] ?? 0; ?></h4>
                            <span class="grayis_text">e-Notes Uploaded</span>
                        </div>
                    </div>
                    <div class="single_pro d-flex">
                        <div class="probox blue_box"></div>
                        <div class="box_content">
                            <h4 class="bluish_text"><?php echo $statistics['task'] ?? 0; ?></h4>
                            <span class="grayis_text">e-assessment Uploaded</span>
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