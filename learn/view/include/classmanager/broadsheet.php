
<div class="main_content_iner overly_inner" id="table-container">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0" style="display: block; margin-left: auto;margin-right: auto;">      
                            <div class="card-profile">
                                <img src="../../asset/img/school/<?php echo $sch_details['logo'] ?>" style="display: block; margin-left: auto;margin-right: auto;" alt="" width="100">
                                <h2 class="f_s_30 f_w_700 dark_text" style="text-align:center;">
                                    <?php echo ucwords($sch_details['schname']) ?>
                                </h2>
                                <h4 style="text-align:center;">
                                    <strong>
                                        <?php echo ucwords($active_term['term']) ?> Academic Performance Report Broadsheet  for <?php echo $classname['classname'] ?>
                                    </strong>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div class="QA_section">
                            <div class="QA_table mb_30">
                                <div class="col-xl-12">
                                    <div class="col-12 QA_section">
                                        <div class="card QA_table">
                                            <div class="card-header">
                                            
                                                <span class="float-end">
                                                    <button class="btn_3 full_width text-center" onclick="printTable();">Print</button>
                                                </span>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive-sm">
                                                    <table class="table table-striped" id="print-table">

                                                        <!-- table content here -->
                                                        <thead>
                                                                <tr>
                                                                    <th class="center">S/N</th>
                                                                    <th>Full name</th>
                                                                    <?php
                                                                    // Build the table rows with the student scores
                                                                    if (!empty($subject_header)) {
                                                                        foreach ($subject_header as $table_header) {
                                                                            echo '<th>' . substr($table_header['sbjname'], 0, 5) . '</th>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $count = 1;
                                                                if (!empty($class_list)) {
                                                                    foreach ($class_list as $data) {
                                                                        ?>
                                                                        <tr>
                                                                            <td class="center">
                                                                                <?php echo $count++; ?>
                                                                            </td>
                                                                            <td class="left strong">
                                                                                <?php echo $data['uname'] . ' - ' . $data['fname']; ?>
                                                                            </td>

                                                                            <?php
                                                                            if (!empty($subject_header)) {
                                                                                foreach ($subject_header as $gen_scores) {
                                                                                    $tblName = 'lhpresultrecord';
                                                                                    $conditions = array(
                                                                                        'where' => array(
                                                                                            'classid' => $data['classid'],
                                                                                            'term' => $data["term"],
                                                                                            'lid' => $data['uname'],
                                                                                            'subjid' => $gen_scores['sbjid'],
                                                                                        ),
                                                                                        'return_type' => 'single',
                                                                                    );
                                                                                    $total_score = $model->getRows($tblName, $conditions);
                                                                                    echo '<td class="left strong">' . $total_score['totalscore'] . '</td>';
                                                                                }
                                                                            }
                                                                            ?>
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
                            </div>
                        </div>
                    </div>                
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printTable() {
    

    // Print the table
    var printContents = document.getElementById("table-container").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();

    // Restore the original page contents
    document.body.innerHTML = originalContents;
    elementsToHide.forEach(function(element) {
        element.style.display = '';
    });
}
</script>
