<div class="col-xl-10 offset-1">
    <div class="col-12 QA_section">
        <div class="card QA_table ">
            <div class="card-header">
                <strong>Class List </strong>



                <span class="float-end">
                    <?php
                    if ($result_config['status'] != 1 &&  $scoresheet_type == 'CA_SCORE') {
                        echo '
                        <button class="btn_3 full_width text-center"
                            onclick="submit_ca_scores()">Record CA Scores for 
                            ' . $class_details['sbjname'] . ' - ' . $class_details['classname'] . '
                        </button>';
                    } elseif ($result_config['status'] != 1 &&  $scoresheet_type == 'EXAM_SCORE') {
                        echo '
                        <button class="btn_3 full_width text-center"
                            onclick="submit_exam_scores()">Record Exam Scores for 
                            ' . $class_details['sbjname'] . ' - ' . $class_details['classname'] . '
                        </button>';
                    } elseif ($result_config['midterm'] != 1 &&  $scoresheet_type == 'WEEKLY') {
                        echo '
                        <button class="btn_2 full_width text-center"
                            onclick="record_weekly_scores_for_all()">Record ' . $week . ' Scores for 
                            ' . $class_details['sbjname'] . ' - ' . $class_details['classname'] . '
                        </button>';
                    } else {
                        echo ' 
                        <button class="btn_3 full_width text-center"
                            onclick="window.print();">Print Scoresheet for 
                            ' . $class_details['sbjname'] . ' - ' . $class_details['classname'] . '
                        </button>';
                    }
                    ?>
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="center"></th>
                                <th>Passport</th>
                                <th>Full name</th>
                                <th>Input Scores</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            if (!empty($ca_scores_recorder) &&  $scoresheet_type == 'CA_SCORE') {
                                foreach ($ca_scores_recorder as $data) {
                            ?>
                                    <tr>
                                        <td class="center"><?php echo $count++; ?></td>
                                        <td class="center">
                                            <img src="<?php
                                                        if (!empty($data['picture'])) {
                                                            $dir = '../../../asset/img/passport/' . $data['picture'];
                                                            if (file_exists('"' . $dir . '"')) {
                                                                echo '../../asset/img/passport/' . $data['picture'];
                                                            } else {
                                                                echo '../../asset/img/passport/nopix.jpg';
                                                            }
                                                        } else {
                                                            echo '../../asset/img/passport/nopix.jpg';
                                                        }
                                                        ?> " width="50" />
                                        </td>
                                        <td class="left strong">
                                            <?php echo $data['uname'] . ' - ' . $data['fname']; ?>
                                        </td>

                                        <td class="left">
                                            <form>
                                                <div id="weblink_div" class="input-group mb-3">
                                                    <input type="text" hidden name="userid[]" id="users" value="<?php echo $data['uname']; ?>" class="form-control" tabindex="4" aria-describedby="basic-addon1">
                                                    <input type="number" name="score[]" id="scores" value="<?php echo $data['score'] ?? ""; ?>" class="form-control" max="<?php echo $result_config['ca_score'] ?>" aria-label="Input CA Score" aria-describedby="basic-addon1" <?php if ($result_config['status'] == 1) {
                                                                                                                                                                                                                                                                                    echo 'disabled';
                                                                                                                                                                                                                                                                                } ?>>
                                                    <div class="input-group-text">
                                                        <span class="" id="basic-addon1">CA Score</span>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } elseif (!empty($exam_scores_recorder) &&  $scoresheet_type == 'EXAM_SCORE') {
                                foreach ($exam_scores_recorder as $data) {
                                ?>
                                    <tr>
                                        <td class="center"><?php echo $count++; ?></td>
                                        <td class="center">
                                            <img src="<?php
                                                        if (!empty($data['picture'])) {
                                                            $dir = '../../../asset/img/passport/' . $data['picture'];
                                                            if (file_exists('"' . $dir . '"')) {
                                                                echo '../../asset/img/passport/' . $data['picture'];
                                                            } else {
                                                                echo '../../asset/img/passport/nopix.jpg';
                                                            }
                                                        } else {
                                                            echo '../../asset/img/passport/nopix.jpg';
                                                        }
                                                        ?> " width="50" />
                                        </td>
                                        <td class="left strong">
                                            <?php echo  $data['uname'] . ' - ' . $data['fname']; ?>
                                        </td>

                                        <td class="left">
                                            <form>
                                                <div id="weblink_div" class="input-group mb-3">
                                                    <input type="text" hidden name="userid[]" id="users" value="<?php echo $data['uname']; ?>" class="form-control" tabindex="4" aria-describedby="basic-addon1">
                                                    <input type="number" name="score[]" id="scores" value="<?php echo $data['score'] ?? ""; ?>" class="form-control" max="<?php echo $result_config['exam_score'] ?>" aria-label="Input Exam Score" aria-describedby="basic-addon1" <?php if ($result_config['status'] == 1) {
                                                                                                                                                                                                                                                                                        echo 'disabled';
                                                                                                                                                                                                                                                                                    } ?>>
                                                    <div class="input-group-text">
                                                        <span class="" id="basic-addon1">Exam Score</span>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } elseif (!empty($week_scores_recorder) &&  $scoresheet_type == 'WEEKLY') {
                                foreach ($week_scores_recorder as $data) {
                                ?>
                                    <tr>
                                        <td class="center"><?php echo $count++; ?></td>
                                        <td class="center">
                                            <img src="<?php
                                                        if (!empty($data['picture'])) {
                                                            $dir = '../../../asset/img/passport/' . $data['picture'];
                                                            if (file_exists('"' . $dir . '"')) {
                                                                echo '../../asset/img/passport/' . $data['picture'];
                                                            } else {
                                                                echo '../../asset/img/passport/nopix.jpg';
                                                            }
                                                        } else {
                                                            echo '../../asset/img/passport/nopix.jpg';
                                                        }
                                                        ?> " width="50" />
                                        </td>
                                        <td class="left strong">
                                            <?php echo  $data['uname'] . ' - ' . $data['fname']; ?>
                                        </td>

                                        <td class="left">
                                            <form>
                                                <div id="weblink_div" class="input-group mb-3">
                                                    <input type="text" hidden name="userid[]" id="users" value="<?php echo $data['uname']; ?>" class="form-control" tabindex="4" aria-describedby="basic-addon1">
                                                    <input type="number" name="score[]" id="scores" value="<?php echo $data['score'] ?? ""; ?>" class="form-control" max="10" aria-label="Input Weekly Test Score" aria-describedby="basic-addon1" <?php if (isset($result_config['midterm'] ) && $result_config['midterm'] == 1) {
                                                                                                                                                                                                                                                        echo 'disabled';
                                                                                                                                                                                                                                                    } else{echo "";} ?>>
                                                    <input type="text" hidden id="week_num" value="<?php echo $week ?? ""; ?>" class="form-control">
                                                    <div class="input-group-text">
                                                        <span class="" id="basic-addon1"><?php echo $week; ?></span>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo 'No learner added to class yet';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>