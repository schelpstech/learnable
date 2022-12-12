<div class="col-xl-10 offset-1">
    <div class="col-12 QA_section">
        <div class="card QA_table ">
            <div class="card-header">
                <strong>Class List </strong>
                <span class="float-end"> <strong>Class name:</strong> <?php echo $classname['classname']; ?></span>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="center"></th>
                                <th>Passport</th>
                                <th>Userid</th>
                                <th>Full name</th>
                                <th>Gender</th>
                                <th class="center">Date of Birth</th>
                                <th class="center">Status</th>
                                <th class="center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            if (!empty($learner_list)) {
                                foreach ($learner_list as $data) {
                            ?>
                                    <tr>
                                        <td class="center"><?php echo $count++; ?></td>
                                        <td class="center">
                                        <img src="<?php
                                            if (!empty($data['picture'])) {
                                                echo '../../asset/img/passport/'.$data['picture'];
                                            }else {
                                                echo '../../asset/img/passport/nopix.jpg';
                                            }
                                            ?> " width="100" />
                                        </td>
                                        <td class="center"><?php echo $data['uname']; ?></td>
                                        <td class="left strong"><?php echo $data['fname']; ?></td>
                                        <td class="left"><?php echo $data['gender']; ?></td>
                                        <td class="right"><?php echo  $data['dob']; ?></td>
                                        <td class="right"><?php if ($data['status'] == 1) {
                                                                echo 'Active';
                                                            } else {
                                                                echo 'Inactive';
                                                            } ?>
                                                            
                                        </td>
                                        <td>
                                            <div class="action_btns d-flex">
                                                <a href="../../app/router.php?pageid=manage_learner&instance=<?php echo $data['uname'] ?>" class="action_btn mr_10"> <i class="ti-hand-point-up"></i> </a>
                                            </div>
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
                <div class="row">
                    <div class="col-lg-4 col-sm-5">
                    </div>
                    <div class="col-lg-4 col-sm-5 ms-auto QA_section">
                        <table class="table table-clear QA_table">
                            <tbody>
                                <tr>
                                    <td class="left">
                                        <strong>Active Male Learners</strong>
                                    </td>
                                    <td class="right"><?php echo $learner_statistics['male']; ?></td>
                                </tr>
                                <tr>
                                    <td class="left">
                                        <strong>Active Female Learners</strong>
                                    </td>
                                    <td class="right"><?php echo $learner_statistics['female']; ?></td>
                                </tr>
                                <tr>
                                    <td class="left">
                                        <strong>Total Active Learners in class</strong>
                                    </td>
                                    <td class="right">
                                        <strong><?php echo $learner_statistics['total']; ?></strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>