<div class="main_content_iner ">
    <div class="container-fluid p-0 sm_padding_15px">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card_box box_shadow position-relative mb_30     ">
                    <div class="white_box_tittle     ">
                        <div class="main-title2 ">
                            <span>
                                <h4 class="mb-2 nowrap"><?php echo $active_term['term'] ?? "-"; ?> Scheme of Work </h4><br>
                                <p><?php if (!empty($alist_scheme['sbjname'])) {echo ucwords($alist_scheme['sbjname']); }else{echo "-";}   ?> </p><br>
                                <p> <?php if (!empty($alist_scheme['staffname'])) {echo ucwords($alist_scheme['staffname']); }else{echo "-";}  ?> </p>
                            </span>
                        </div>
                    </div>
                    <div class="box_body">
                        <div class="white_card_body QA_section">
                            <div class="todo_wrapper">
                                <?php
                                if (!empty($list_scheme)) {
                                    $count = 1;
                                    foreach ($list_scheme as $list_scheme) {
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
                                                <div class="lodo_right">
                                                    <a class="badge_complete">
                                                        <p class="f_s_18 f_w_900 mb-0">
                                                            <small><?php echo ucwords($list_scheme['week']) ?></small>
                                                        </p>
                                                    </a>
                                                </div>
                                                <div class="todo_head">
                                                    <h6><?php echo ucwords($list_scheme['topic']) ?> </h6>
                                                </div>
                                            </div>

                                            <?php
                                            if ((isset($_SESSION['user_type']) && $_SESSION['user_type'] === "Instructor")) {
                                                echo '
                                                   <div class="lodo_right">
                                                        <a href="../../app/router.php?pageid=resources&item=modify_topic&item_ref=' . $list_scheme['schmid'] . '" class="mark_complete">
                                                                <p class="f_s_18 f_w_900 mb-0">Modify</p>
                                                        </a>
                                                    </div>';
                                            }
                                            ?>
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