<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="col-xl-12">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Modify Assessment in the scheme of work for <?php echo $active_term['term'] ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="classid">Select Class</label>
                                </div>
                                <select class="form-select" id="classid" tabindex="1" required="yes">
                                   <option value="<?php echo $modify_task['classid'] ?>"><?php echo $modify_task['classname'] ?></option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="subject">Select Subject</label>
                                </div>
                                <select class="form-select" tabindex="2" id="subject" required="yes">
                                    <option value="<?php echo $modify_task['sbjid'] ?>"><?php echo $modify_task['sbjname'] ?></option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="inputGroupSelect02">Select Topic</label>
                                </div>
                                <select class="form-select" id="topic_list" tabindex="3" required="yes">
                                    <option value="<?php echo $modify_task['topicid'] ?>"><?php echo $modify_task['topic'] ?></option>
                                </select>
                            </div>

                            <div  class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Submission Deadline</span>
                                </div>
                                <input type="date" id="due_date" min="<?php echo date("Y-m-d") ?>" value="<?php echo $modify_task['deadline'] ?>" class="form-control" tabindex="4" aria-label="Topic" aria-describedby="basic-addon1">
                            </div>

                            <div  class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Mark Obtainable</span>
                                </div>
                                <input type="number" min="1" id="grade" class="form-control" tabindex="4" value="<?php echo $modify_task['grade'] ?>" aria-label="Topic" aria-describedby="basic-addon1">
                            </div>
                            
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="inputGroupSelect02">Select Note Type</label>
                                </div>
                                <select class="form-select" id="note_type"  tabindex="3" required="yes">
                                    <option value="<?php echo $modify_task['type'] ?>"><?php echo $modify_task['type'] ?></option>
                                </select>
                            </div>
                            <?php
                                if($modify_task['type'] == 'text'){
                                    echo '
                                    <div id="summernote_div" class="col-12">
                                        <textarea type="text" id="summernote" class="form-control" tabindex="4"   placeholder="Enter Note here">'.$modify_task['content'].'</textarea>
                                    </div>
                                    ';
                                }elseif($modify_task['type'] == 'online'){
                                    echo'
                                    <div id="weblink_div" class="input-group mb-3">
                                        <div class="input-group-text">
                                            <span class="" id="basic-addon1">Web Link</span>
                                        </div>
                                        <input type="url" id="weblink" class="form-control" tabindex="4" value="'.$modify_task['content'].'" aria-label="Topic"  aria-describedby="basic-addon1">
                                    </div>
                                    ';
                                }
                            ?>
                            
                            <div class="input-group mb-3" style="display: none;">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Question ID</span>
                                </div>
                                <input type="text" id="questid" class="form-control" tabindex="4" value="<?php echo $modify_task['questid'] ?? "" ?>" aria-label="Topic" required="yes" aria-describedby="basic-addon1">
                            </div>

                            

                            <button tabindex="5" class="btn_4 full_width text-center" onclick="modify_task()">Modify Selected Assessment </button>
                            <button tabindex="6" class="btn_2 full_width text-center" onclick="remove_task()">Delete Selected Assessment </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center" id="notedata">
               
        </div>
    </div>
</div>
