<div class="row">
    <div class="col-xl-10 offset-1">
        <div class="col-12 QA_section">
            <div class="card QA_table ">
                <div class="card-header">
                    <strong>Class List </strong>



                    <span class="float-end">
                        <?php
                        if ($result_config['status'] != 1 &&  $scoresheet_type == 'AFFECTIVE') {
                            echo '
                        <button class="btn_3 full_width text-center"
                            onclick="submit_affective()">Record Attendance and Teachers Comment for ' . $class_details['classname'] . '
                        </button>';
                        } else {
                            echo ' 
                        <button class="btn_3 full_width text-center"
                            onclick="window.print();">Print Attendance and Teachers Comment for ' . $class_details['classname'] . '
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
                                    <th>Present Days</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                if (!empty($affective_recorder)) {
                                    foreach ($affective_recorder as $data) {
                                ?>
                                        <form>
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

                                                    <div class="input-group mb-3">
                                                        <input type="text" hidden name="userid[]" id="users" value="<?php echo $data['uname']; ?>" class="form-control" tabindex="4" aria-describedby="basic-addon1">
                                                        <input type="number" name="total_present[]" id="total_present" value="<?php echo $data['present'] ?? ""; ?>" class="form-control" max="<?php echo $result_config['sch_open'] ?>" aria-label="Input Present Days" aria-describedby="basic-addon1" <?php if ($result_config['status'] == 1) {
                                                                                                                                                                                                                                                                                                                    echo 'disabled';
                                                                                                                                                                                                                                                                                                                } ?>>

                                                </td>
                                                <td class="left">
                                                    <textarea type="text" name="comment[]" id="comment" rows="4" class="form-control" aria-label="Comment" aria-describedby="basic-addon1" <?php if ($result_config['status'] == 1) {
                                                                                                                                                                                                echo 'disabled';
                                                                                                                                                                                            } ?>><?php echo $data['comment'] ?? ""; ?> </textarea>

                                                </td>
                                            </tr>
                                        </form>
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
</div>

<div class="row">
    <div class="col-xl-10 offset-1">
        <div class="col-12 QA_section">
            <div class="card QA_table ">
                <div class="card-header">
                    <strong>Class List </strong>



                    <span class="float-end">
                        <?php
                        if ($result_config['status'] != 1 &&  $scoresheet_type == 'AFFECTIVE') {
                            echo '
                        <button class="btn_3 full_width text-center"
                            onclick="submit_ratings()">Record AFFECTIVE DOMAIN for ' . $class_details['classname'] . '
                        </button>';
                        } else {
                            echo ' 
                        <button class="btn_3 full_width text-center"
                            onclick="window.print();">Print Affective Domain for ' . $class_details['classname'] . '
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
                                    <th>Leadership</th>
                                    <th>Eloquency</th>
                                    <th>Neatness</th>
                                    <th>Creativity</th>
                                    <th>Responsiveness</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                if (!empty($affective_recorder) &&  $scoresheet_type == 'AFFECTIVE') {
                                    foreach ($affective_recorder as $data) {
                                ?>
                                        <form>
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
                                                    <div class="input-group mb-3">
                                                        <input type="text" hidden name="userid[]" id="users" value="<?php echo $data['uname']; ?>" class="form-control" tabindex="4" aria-describedby="basic-addon1">
                                                        <input type="number" name="rating1[]" id="rating1" value="<?php echo $data['rating1'] ?? ""; ?>" class="form-control" max="5" aria-label="Rate" aria-describedby="basic-addon1" <?php if ($result_config['status'] == 1) {
                                                                                                                                                                                                                                            echo 'disabled';
                                                                                                                                                                                                                                        } ?>>
                                                </td>

                                                <td class="left">
                                                    <input type="number" name="rating2[]" id="rating2" value="<?php echo $data['rating2'] ?? ""; ?>" class="form-control" max="5" aria-label="Rate" aria-describedby="basic-addon1" <?php if ($result_config['status'] == 1) {
                                                                                                                                                                                                                                        echo 'disabled';
                                                                                                                                                                                                                                    } ?>>

                                                </td>

                                                <td class="left">
                                                    <input type="number" name="rating3[]" id="rating3" value="<?php echo $data['rating3'] ?? ""; ?>" class="form-control" max="5" aria-label="Rate" aria-describedby="basic-addon1" <?php if ($result_config['status'] == 1) {
                                                                                                                                                                                                                                        echo 'disabled';
                                                                                                                                                                                                                                    } ?>>

                                                </td>

                                                <td class="left">
                                                    <input type="number" name="rating4[]" id="rating4" value="<?php echo $data['rating4'] ?? ""; ?>" class="form-control" max="5" aria-label="Rate" aria-describedby="basic-addon1" <?php if ($result_config['status'] == 1) {
                                                                                                                                                                                                                                        echo 'disabled';
                                                                                                                                                                                                                                    } ?>>

                                                </td>

                                                <td class="left">
                                                    <input type="number" name="rating5[]" id="rating5" value="<?php echo $data['rating5'] ?? ""; ?>" class="form-control" max="5" aria-label="Rate" aria-describedby="basic-addon1" <?php if ($result_config['status'] == 1) {
                                                                                                                                                                                                                                        echo 'disabled';
                                                                                                                                                                                                                                    } ?>>

                                                </td>
                                            </tr>
                                        </form>
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
</div>