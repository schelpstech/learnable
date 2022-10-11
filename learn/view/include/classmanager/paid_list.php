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
                                <th>Learner ID</th>
                                <th>Fullname</th>
                                <th class="center">Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            if (!empty($paid_list)) {
                                foreach ($paid_list as $data) {
                            ?>
                                    <tr>
                                        <td class="center"><?php echo $count++; ?></td>
                                        <td class="center strong"><?php echo $data['uname']; ?></td>
                                        <td class="left "><?php echo $data['fname']; ?></td>
                                        <td class="left ">
                                            <?php if ( $data['paid'] >= $data['bill']){
                                                    echo 'Paid in Full';
                                            }else{
                                                echo $data['paid'] - $data['bill'];
                                            } 
                                            ?></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo 'No Learner has paid in full in your class yet';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>