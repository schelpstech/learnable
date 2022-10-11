<div class="col-xl-8 offset-2">
    <div class="col-12 QA_section">
        <div class="card QA_table ">
            <div class="card-header">
                <strong>Subject List </strong>
                <span class="float-end"> <strong>Class name:</strong> <?php echo $classname['classname']; ?></span>
            </div>
            <div class="card-body">

                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="center"></th>
                                <th>Subject</th>
                                <th>Subject Teacher</th>
                                <th class="center">Scheme of Work</th>
                                <th class="center">e-Note</th>
                                <th class="center">Assessment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            if (!empty($subject_list)) {
                                foreach ($subject_list as $data) {
                            ?>
                                    <tr>
                                        <td class="center"><?php echo $count++; ?></td>
                                        <td class="center strong"><?php echo $data['sbjname']; ?></td>
                                        <td class="left "><?php echo $data['staffname']; ?></td>
                                        <td class="left"><?php echo $data['topic']; ?></td>
                                        <td class="right"><?php echo  $data['note']; ?></td>
                                        <td class="right"><?php echo $data['task']; ?></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo 'No Subject added to class yet';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>