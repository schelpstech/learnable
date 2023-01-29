<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="col-xl-12">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Add Assessment to a topic in the scheme of work for <?php echo $active_term['term'] ?></h3>
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
                                <select class="form-select" tabindex="2" id="subject" onchange="fetchtask()" required="yes">
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="inputGroupSelect02">Select Topic</label>
                                </div>
                                <select class="form-select" id="topic_list" tabindex="3" required="yes">

                                </select>
                            </div>

                            <div  class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Submission Deadline</span>
                                </div>
                                <input type="date" id="due_date" min="<?php echo date("Y-m-d") ?>" class="form-control" tabindex="4" aria-label="Topic" aria-describedby="basic-addon1">
                            </div>

                            <div  class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Mark Obtainable</span>
                                </div>
                                <input type="number" min="1" id="grade" class="form-control" tabindex="4" aria-label="Topic" aria-describedby="basic-addon1">
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="inputGroupSelect02">Select Note Type</label>
                                </div>
                                <select class="form-select" id="note_type" onchange="switch_type()" tabindex="3" required="yes">
                                    <option value="">select</option>
                                    <option value="text">Text</option>
                                    <option value="online">Web Link</option>
                                </select>
                            </div>

                            <div id='summernote_div' class="col-12" style="display: none;">
                                <textarea type="text" id='summernote' rows="5" class="form-control" tabindex="4" placeholder="Enter Note here"></textarea>
                            </div>

                            <div id="weblink_div" class="input-group mb-3" style="display: none;">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Web Link</span>
                                </div>
                                <input type="url" id="weblink" class="form-control" tabindex="4" aria-label="Topic" aria-describedby="basic-addon1">
                            </div>

                            <button tabindex="5" class="btn_1 full_width text-center" onclick="add_task()">Add Assessment To Selected Topic</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center" id="notedata">

        </div>
    </div>
</div>