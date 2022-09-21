
<div class="main_content_iner ">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">

            <div class="col-lg-8">
                <div class="card_box box_shadow position-relative mb_30">
                    <div class="white_box_tittle ">
                        <div class="main-title2 ">
                            <h4 class="mb-2 nowrap">Scheme of Work for <?php echo $active_term['term'] ?></h4>
                            <p>This page consist of all topic outlined for each subject for this current term</p>
                        </div>
                    </div>
                    <?php
                    if (!empty($outline)) {
                        $count = 1;
                        foreach ($outline as $outline) {
                    ?>
                            <div class="box_body">
                                <div class="default-according arrow_style" id="accordionclose">
                                    <div class="card">
                                        <div class="card-header" id="heading1">
                                            <h5 class="mb-0">
                                                <button class="btn " data-bs-toggle="collapse" data-bs-target="#sbj<?php echo $outline['sbjid'] ?>" aria-expanded="true" aria-controls="heading1"> :: <?php echo ucwords($outline['sbjname']) ?>
                                                </button>
                                            </h5>
                                        </div>
                                        <div class="collapse" id="sbj<?php echo $outline['sbjid'] ?>" aria-labelledby="heading1" data-parent="#accordionclose">
                                            <div class="card-body" class="col-xl-12">
                                                <div class="white_card card_height_100 mb_30 ">
                                                    <div class="white_card_header">
                                                        <div class="box_header m-0">
                                                            <div class="main-title">
                                                                <h3 class="m-0">Topic Outline</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="white_card_body QA_section">
                                                        <div class="todo_wrapper">

                                                            <?php
                                                            //Scheme of Work
                                                            $count = 01;
                                                            $tblName = 'lhpscheme';
                                                            $conditions = array(
                                                                'where' => array(
                                                                    'classname' => $learner_profile['classid'],
                                                                    'subject' => $outline['sbjid'],
                                                                    'term' => $active_term['term'],
                                                                    'status' => 1
                                                                ),
                                                            );
                                                            $scheme = $model->getRows($tblName, $conditions);
                                                            if (!empty($scheme)) {
                                                                foreach ($scheme as $scheme) {
                                                            ?>
                                                                    <div class="single_todo d-flex justify-content-between align-items-center">
                                                                        <div class="lodo_left d-flex align-items-center">
                                                                            <div class="bar_line mr_10"></div>
                                                                            <div class="todo_box">
                                                                                <label class="form-label primary_checkbox  d-flex mr_10 ">
                                                                                    <p class="f_s_18 f_w_900 mb-0"><small><?php echo $count++ ?></small></p>
                                                                                    <input type="checkbox" disabled="yes">
                                                                                    <span class="checkmark"></span>
                                                                                </label>
                                                                            </div>
                                                                            <div class="todo_head">
                                                                                <h6><?php echo ucwords($scheme['topic']) ?> </h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="lodo_right"> <a href="#" class="badge_complete">
                                                                                <p class="f_s_18 f_w_900 mb-0"><small><?php echo ucwords($scheme['week']) ?></small></p>
                                                                            </a> </div>
                                                                    </div>
                                                            <?php
                                                                }
                                                            } else {
                                                                echo '
                                                                        <div class="single_todo d-flex justify-content-between align-items-center">
                                                                            <div class="lodo_left d-flex align-items-center">
                                                                                <div class="bar_line mr_10"></div>
                                                                                
                                                                                <div class="todo_head">
                                                                                    <h5 class="f_s_18 f_w_900 mb-0">No Scheme of work for this subject  </h5>
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
                                <div class="widget-numbers"><span>No Scheme of work yet</span></div>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</div>

</section>
