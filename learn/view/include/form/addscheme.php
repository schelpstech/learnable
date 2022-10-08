<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="col-xl-12">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Scheme of Work for <?php echo $active_term['term'] ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="classid">Select Class</label>
                                </div>
                                <select class="form-select" id="classid" tabindex="1" required="yes" onchange="fetchsubject()">
                                    <option value="">select</option>
                                    <?php
                                    foreach ($class_subject_allocated as $data) {
                                    ?>
                                        <option value="<?php echo $data['classid'] ?>"><?php echo $data['classname'] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="subject">Select Subject</label>
                                </div>
                                <select class="form-select" tabindex="2" id="subject" onchange="fetchscheme()" required="yes">
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="inputGroupSelect02">Select Week</label>
                                </div>
                                <select class="form-select" id="week" tabindex="3" required="yes">
                                    <option value="">select</option>
                                    <?php
                                    $i = 0;
                                    while ($i < 12) {
                                        $i = $i + 1;
                                    ?>
                                        <option value="Week <?php echo $i ?>">Week <?php echo $i ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Topic</span>
                                </div>
                                <input type="text" id="topic" class="form-control" tabindex="4" value="" aria-label="Topic" required="yes" aria-describedby="basic-addon1">
                            </div>

                            <button tabindex="5" class="btn_1 full_width text-center" onclick="add_topic_to_scheme()">Add Topic To Scheme of Work</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="schemedata">

        </div>
    </div>
</div>