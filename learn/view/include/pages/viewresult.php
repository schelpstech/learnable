

<div class="main_content_iner overly_inner" id="print_area">
    <div class="container-fluid p-0 ">
        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex align-items-center justify-content-between">
                    <button  class="white_btn3" onclick="printTable();">Print</button>
                    <div class="page_title_left">
                        <div class="card-profile"><img src="../../asset/img/school/<?php echo $sch_details['logo'] ?>" style="display: block; margin-left: auto;margin-right: auto;" alt="" width="100"></div>
                        <h2 class="f_s_30 f_w_700 dark_text" style="text-align:center;"><?php echo ucwords($sch_details['schname']) ?></h2>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Address </a></li>
                            <li class="breadcrumb-item active"><?php echo ucwords($sch_details['address']) ?></li>
                        </ol>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Contact </a></li>
                            <li class="breadcrumb-item active"><?php echo ucwords($sch_details['email'] . " | " . $sch_details['phone']) ?></li>
                        </ol>
                    </div>
                    <div class="page_title_left">
                        <div class="card-profile"><img class="rounded-circle" src="
                            <?php
                            if (isset($learner_profile['picture'])) {
                                echo '../../asset/img/passport/' . $learner_profile['picture'];
                            } else {
                                echo '../../asset/img/passport/nopix.jpg';
                            }
                            ?>" alt="" data-original-title="" title="" width="100"></div>
                        <h4 class="f_s_30 f_w_700 dark_text"><?php echo ucwords($learner_profile['fname']) ?></h4>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Learner ID </a></li>
                            <li class="breadcrumb-item active"><?php echo ucwords($learner_profile['uname']) ?></li>
                        </ol>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Class </a></li>
                            <li class="breadcrumb-item active"><?php if (isset($show_affective['classname'])) {
                                                                    echo ucwords($show_affective['classname']);
                                                                } else {
                                                                    echo '';
                                                                } ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-12 QA_section">
                <div class="card QA_table ">
                    <div class="card-header" style="text-align:center;">
                        <h4> Academic Performance Report Sheet for
                            <strong><?php echo ucwords($_SESSION['ref']) ?></strong>
                        </h4>
                        <span class="float-end"> <strong>Generated:</strong> <?php echo date("d-m-Y") ?></span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive-sm">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="center">#</th>
                                        <th>Subject</th>
                                        <th class="center">CA Score<br></th>
                                        <th class="center">Exam Score<br></th>
                                        <th class="center">Total Score<br></th>
                                        <th class="center">Remarks<br></th>
                                        <th class="center">Lowest Score<br></th>
                                        <th class="center">Average Score<br></th>
                                        <th class="center">Highest Score<br></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    if (!empty($show_result)) {
                                        foreach ($show_result as $show_result) {
                                            $tblName = 'lhpresultrecord';
                                            $conditions = array(
                                                'return_type' => 'single',
                                                'select' => ' 
                                                            (SELECT MIN(totalscore) FROM lhpresultrecord where subjid ="' . $show_result["subjid"] . '" and term ="' . $show_result["term"] . '") as min,
                                                            (SELECT AVG(totalscore) FROM lhpresultrecord where subjid ="' . $show_result["subjid"] . '" and term ="' . $show_result["term"] . '") as avg, 
                                                            (SELECT MAX(totalscore) FROM lhpresultrecord where subjid ="' . $show_result["subjid"] . '" and term ="' . $show_result["term"] . '") as max
                                                            ',
                                            );
                                            $minscore = $model->getRows($tblName, $conditions);
                                    ?>
                                            <tr>
                                                <td class="center"><?php echo $count++; ?></td>
                                                <td class="center"><?php echo $show_result['sbjname']; ?></td>
                                                <td class="center"><?php echo $show_result['score']; ?></td>
                                                <td class="center"><?php echo $show_result['examscore']; ?></td>
                                                <td class="center"><?php echo $show_result['totalscore']; ?></td>
                                                <td class="center"><small><?php echo $utility->grader($show_result['totalscore']) ?? "" ?></small></td>
                                                <td class="center"><?php echo round($minscore['min'], 2); ?></td>
                                                <td class="center"><?php echo round($minscore['avg'], 2); ?></td>
                                                <td class="center"><?php echo round($minscore['max'], 2); ?></td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo 'No scores recorded';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-4">
                                <table class="table table-clear QA_table">
                                    <thead>
                                        <tr>
                                            <th class="center">Affective Domain</th>
                                            <th class="center">5</th>
                                            <th class="center">4</th>
                                            <th class="center">3</th>
                                            <th class="center">2</th>
                                            <th class="center">1</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="left">
                                                <strong> Leadership </strong>
                                            </td>
                                            <td class="center"><?php if (isset($show_affective['rating1']) && $show_affective['rating1'] == 5) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating1']) && $show_affective['rating1'] == 4) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating1']) && $show_affective['rating1'] == 3) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating1']) && $show_affective['rating1'] == 2) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating1']) && $show_affective['rating1'] == 1) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong> Eloquency </strong>
                                            </td>
                                            <td class="center"><?php if (isset($show_affective['rating2']) && $show_affective['rating2'] == 5) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating2']) && $show_affective['rating2'] == 4) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating2']) && $show_affective['rating2'] == 3) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating2']) && $show_affective['rating2'] == 2) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating2']) && $show_affective['rating2'] == 1) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong> Neatness </strong>
                                            </td>
                                            <td class="center"><?php if (isset($show_affective['rating3']) && $show_affective['rating3'] == 5) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating3']) && $show_affective['rating3'] == 4) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating3']) && $show_affective['rating3'] == 3) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating3']) && $show_affective['rating3'] == 2) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating3']) && $show_affective['rating3'] == 1) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong> Creativity </strong>
                                            </td>
                                            <td class="center"><?php if (isset($show_affective['rating4']) && $show_affective['rating4'] == 5) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating4']) && $show_affective['rating4'] == 4) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating4']) && $show_affective['rating4'] == 3) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating4']) && $show_affective['rating4'] == 2) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating4']) && $show_affective['rating4'] == 1) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong> Responsiveness </strong>
                                            </td>
                                            <td class="center"><?php if (isset($show_affective['rating5']) && $show_affective['rating5'] == 5) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating5']) && $show_affective['rating5'] == 4) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating5']) && $show_affective['rating5'] == 3) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating5']) && $show_affective['rating5'] == 2) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                            <td class="center"><?php if (isset($show_affective['rating5']) && $show_affective['rating5'] == 1) {
                                                                    echo '*';
                                                                } else {
                                                                    echo "";
                                                                } ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-3 col-sm-3">
                                <table class="table table-clear QA_table">
                                    <thead>
                                        <tr>
                                            <th class="center">Attendace</th>
                                            <th class="center">Days</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="left">
                                                <strong> Present Days </strong>
                                            </td>
                                            <td class="right"><?php if (isset($show_affective['total_present'])) {
                                                                    echo $show_affective['total_present'];
                                                                } else {
                                                                    echo 'N/A';
                                                                } ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Absent Days </strong>
                                            </td>
                                            <td class="right"><?php if (isset($show_affective['sch_open']) && isset($show_affective['total_present'])) {
                                                                    echo $show_affective['sch_open'] - $show_affective['total_present'];
                                                                } else {
                                                                    echo 'N/A';
                                                                }  ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Total School </strong>
                                            </td>
                                            <td class="right">
                                                <strong><?php if (isset($show_affective['sch_open'])) {
                                                            echo $show_affective['sch_open'];
                                                        } else {
                                                            echo 'N/A';
                                                        } ?></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>School resumes: </strong>
                                            </td>
                                            <td class="right">
                                                <strong><?php if (isset($show_affective['resumption'])) {
                                                            echo $show_affective['resumption'];
                                                        } else {
                                                            echo 'N/A';
                                                        } ?></strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-5 col-sm-5 ms-auto QA_section">
                                <table class="table table-clear QA_table">
                                    <thead>
                                        <tr>
                                            <th class="center">Cumulative</th>
                                            <th class="center">Ratings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="left">
                                                <strong>Total Score Obtainable</strong>
                                            </td>
                                            <td class="left"><?php echo round(($aggregate['countscore'] * 100), 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Total Score Obtained</strong>
                                            </td>
                                            <td class="right"><?php echo round($aggregate['sumscore'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Cumulative Average Score</strong>
                                            </td>
                                            <td class="right"><?php echo  round($aggregate['avgscore'], 2); ?>%</td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong> Performance Remarks</strong>
                                            </td>
                                            <td class="right"><?php echo $utility->grader($aggregate['avgscore']) ?? "" ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong>Teacher's Comment</strong>
                                            </td>
                                            <td class="left"><?php echo $show_affective['comment'] ?? "N/A"; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 ">
            <div class="white_card mb_30 card_height_100">
                <div class="white_card_header">
                    <div class="box_header m-0">
                        <div class="main-title">
                            <h3 class="m-0">Academic Performance Chart</h3>
                        </div>
                        <div class="float-lg-right float-none common_tab_btn2 justify-content-end">
                            <ul class="nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="#"><?php echo ucwords($_SESSION['ref']) ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="white_card_body">
                    <div>
                        <canvas id="mycanvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printTable() {
    

    // Print the table
    var printContents = document.getElementById("print_area").innerHTML;
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