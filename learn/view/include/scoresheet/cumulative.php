<div class="col-xl-10 offset-1">
    <div class="col-12 QA_section">
        <div class="card QA_table ">
            <div class="card-header">
                <strong>Cumulative </strong>



                <span class="float-end">
                    <button class="btn_3 full_width text-center" onclick="window.print();">Print Scoresheet for
                        <?php echo $class_details['sbjname'] . ' - ' . $class_details['classname'] ?>
                    </button>
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
                                <th>CA Score</th>
                                <th>Exam Score</th>
                                <th>Total Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            if (!empty($cumulative_score)) {
                                foreach ($cumulative_score as $data) {
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
                                        <td class="left strong">
                                            <?php echo $data['score'] ?? ""; ?>
                                        </td>
                                        <td class="left strong">
                                            <?php echo $data['examscore'] ?? ""; ?>
                                        </td>
                                        <td class="left strong"><strong>
                                                <?php echo $data['totalscore'] ?? ""; ?></strong>
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