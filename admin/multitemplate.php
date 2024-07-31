<?php
$seriala = rand(11111111, 88888888);
$serialb = rand(11111111, 88888888);
$serial = $seriala . $serialb;
?>
<div id="doc<?php echo $serial ?>">
    <!-- Data Table area Start-->
    <div class="data-table-area" style="text-align: center;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="normal-table-list mg-t-30">

                        <div class="bsc-tbl-bdr">
                            <table class="table table-bordered" style="width:100%">
                                <tbody>
                                    <tr>
                                        <td style="text-align: center;">
                                            <image src="../learn/asset/img/school/<?php echo $schlogo; ?>" width="150" height="150" /><br>
                                            <strong>Founded:
                                                <?php echo $schyear; ?>
                                            </strong>
                                        </td>
                                        <td>

                                            <h1 style="text-align: center;">
                                                <?php echo $schname; ?>
                                            </h1>
                                            <h5 style="text-align: center;">
                                                <?php echo $schmotto; ?>
                                            </h5>
                                            <h5 style="text-align: center;">
                                                <?php echo $schaddress; ?>
                                            </h5>
                                            <h5 style="text-align: center;">
                                                <?php echo $schphone; ?> |
                                                <?php echo $schemail; ?> |
                                                <?php echo $schweb ?>
                                            </h5>
                                            <h4 style="text-align: center;">
                                                <?php echo $term . " " ?> Academic Reportsheets for
                                                <?php echo $dclass ?>
                                            </h4>
                                        </td>

                                        <td style="text-align: center;">
                                            <image src="../learn/asset/img/passport/<?php echo $pix; ?>" width="150" height="150" /><br>
                                            <strong>
                                                <?php echo $lname; ?>
                                            </strong>
                                        </td>


                                    </tr>

                                </tbody>

                            </table>

                            <table class="table table-bordered" style="width:100%; padding-top: 5px; padding-bottom: 5px; padding-left: 5px;padding-right: 5px;" border="1">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Learners ID</th>
                                        <th style="text-align: center;">Fullname</th>
                                        <th style="text-align: center;"> Gender</th>
                                        <th style="text-align: center;">Date of Birth</th>
                                        <th style="text-align: center;">Current Class </th>
                                        <th style="text-align: center;"> Class Teacher</th>
                                        <th style="text-align: center;"> Class Population</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $lname ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <h4 style="text-align: center;">
                                                    <?php echo $stname ?? ""; ?>
                                                </h4>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $gender ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $dob ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $dclass ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $tutorname ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $pop ?? ""; ?>
                                                </p>
                                            </strong></td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-hover" style="width:100%; padding-top: 5px; padding-bottom: 5px; padding-left: 5px;padding-right: 5px;" border="1">
                                <thead>
                                    <br>
                                    <strong>
                                        <p style="text-align: center;">Attendance and Affective Domain Ratings</p>
                                    </strong>
                                    <tr>
                                        <th style="text-align: center;">School Open</th>
                                        <th style="text-align: center;">Total Present</th>
                                        <th style="text-align: center;"> Total Absent</th>
                                        <th style="text-align: center;">Leadership</th>
                                        <th style="text-align: center;">Eloquency</th>
                                        <th style="text-align: center;"> Neatness </th>
                                        <th style="text-align: center;"> Creativity</th>
                                        <th style="text-align: center;"> Responsiveness </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="width: 1px;  height: 1px; padding: 0.5px;">
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $opendays ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $present ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $opendays - $present ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $lead ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $eloq ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $neat ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $create ?? ""; ?>
                                                </p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $response ?? ""; ?>
                                                </p>
                                            </strong></td>
                                    </tr>

                                </tbody>

                                </tbody>

                            </table>
                            <!--  Result-->
                            <table class="table table-bordered" style="width:100%; padding-top: 5px; padding-bottom: 5px; padding-left: 5px;padding-right: 5px;" border="1"><br>
                                <strong>
                                    <p style="text-align: center;">Academic Performance Report</p>
                                </strong>
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>1st Term Score</th>
                                        <th>2nd Term Score</th>
                                        <th>3rd Term CA Score</th>
                                        <th>3rd Term Exam Score</th>
                                        <th>3rd Term Total Score</th>
                                        <th>Cumulative Score</th>
                                        <th>Grade</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>


                                <tbody>



                                    <?php

                                    include_once './conn.php';

                                    $count = 1;
                                    $query = $conn->prepare('SELECT DISTINCT lhpresultrecord.subjid as subjectid , lhpsubject.sbjid, lhpsubject.sbjname as subjectname  from lhpresultrecord LEFT JOIN lhpsubject on lhpresultrecord.subjid = lhpsubject.sbjid WHERE lhpresultrecord.classid = ' . $cclass . ' and lhpsubject.sbjname != "" ORDER BY lhpsubject.sbjname ASC');
                                    $query->setFetchMode(PDO::FETCH_OBJ);
                                    $query->execute();
                                    while ($row = $query->fetch()) {
                                        $subjectname = $row->subjectname;
                                        $subjectid = $row->subjectid;
                                    ?>
                                        <?php
                                        $sql = "SELECT `session` FROM `lhpsession` WHERE `status`  = 1 ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $session = $row["session"];

                                        $sql = "SELECT tid FROM `lpterm` WHERE `term`  = '$term'";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $current_termid = $row["tid"];

                                        $secondtermid = $current_termid - 1;
                                        $firsttermid = $current_termid - 2;

                                        $sql = "SELECT term FROM `lpterm` WHERE `tid`  = '$firsttermid'";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $firsttermref = $row["term"];

                                        $sql = "SELECT term FROM `lpterm` WHERE `tid`  = '$secondtermid'";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $secondtermref = $row["term"];


                                        $termScoresData = getTermScores($con, $firsttermref, $secondtermref, $term, $subjectid, $lname);
                                        $cum = evaluatePerformance($termScoresData['y'], $termScoresData['x']);
                                        // Get scores for each term
                                        $firstTermData = getCumAverageScore($con, $lname, $firsttermref);
                                        $secondTermData = getCumAverageScore($con, $lname, $secondtermref);
                                        $thirdTermData = getCumAverageScore($con, $lname, $term);

                                        // Calculate totals
                                        $firstTerm = $firstTermData['score'];
                                        $secondTerm = $secondTermData['score'];
                                        $thirdTerm = $thirdTermData['score'];
                                        $t1 = $firstTermData['exists'] ? 1 : 0;
                                        $t2 = $secondTermData['exists'] ? 1 : 0;
                                        $t3 = $thirdTermData['exists'] ? 1 : 0;
                                        $y = $firstTerm + $secondTerm + $thirdTerm;
                                        $a = $t1 + $t2 + $t3;
                                        $result = evaluatePerformance($y, $a);
                                        ?>

                                        <tr>

                                            <td><strong>
                                                    <p style="text-align: left;">
                                                        <?php echo (!empty($subjectname) ? strtoupper($subjectname) : "") ?>
                                                    </p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;">
                                                        <?php echo
                                                        $termScoresData['termScores']['term1']['score'] =
                                                            $termScoresData['termScores']['term1']['score'] == 0 ? "" :
                                                            $termScoresData['termScores']['term1']['score'];
                                                        ?>
                                                    </p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;">
                                                        <?php echo
                                                        $termScoresData['termScores']['term2']['score'] =
                                                            $termScoresData['termScores']['term2']['score'] == 0 ? "" :
                                                            $termScoresData['termScores']['term2']['score'];
                                                        ?>
                                                    </p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;">
                                                        <?php echo $termScoresData['termScores']['ca'] =
                                                            $termScoresData['termScores']['ca'] == 0 ? " " :
                                                            $termScoresData['termScores']['ca']; ?>
                                                    </p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;">
                                                        <?php echo $termScoresData['termScores']['exam'] =
                                                            $termScoresData['termScores']['exam'] == 0 ? " " :
                                                            $termScoresData['termScores']['exam']; ?>
                                                    </p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;">
                                                        <?php echo
                                                        $termScoresData['termScores']['term3']['score'] =
                                                            $termScoresData['termScores']['term3']['score'] == 0 ? "" :
                                                            $termScoresData['termScores']['term3']['score'];
                                                        ?>
                                                    </p>
                                                </strong></td>
                                            <td><strong>
                                                    <h4 style="text-align: center;">
                                                        <?php echo $cum['score'] ?>
                                                    </h4>
                                                </strong></td>
                                            <td><strong>
                                                    <h4 style="text-align: center;">
                                                        <?php echo $cum['grade'] ?>
                                                    </h4>
                                                </strong></td>
                                            <td><strong>
                                                    <h4 style="text-align: center;">
                                                        <?php echo $cum['remarks'] ?>
                                                    </h4>
                                                </strong></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <!--Remarks-->
                            <table class="table table-bordered" style="width:100%; padding-top: 5px; padding-bottom: 5px; padding-left: 5px;padding-right: 5px;" border="1">
                                <br>
                                <strong>
                                    <p style="text-align: center;"> Performance Remarks</p>
                                </strong>
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">1st Term Cumuative</th>
                                        <th style="text-align: center;">2nd Term Cumulative</th>
                                        <th style="text-align: center;">3rd Term Cumulative</th>
                                        <th style="text-align: center;"> Cumulative Score</th>
                                        <th style="text-align: center;"> Grade</th>
                                        <th style="text-align: center;"> Remarks</th>
                                        <th style="text-align: center;"> Performance Remarks</th>
                                        <?php if (!is_null($comment)) {
                                            echo '<th style="text-align: center;"> Teacher' . "'s" . ' Comment</th>';
                                        }
                                        ?>

                                        <th style="text-align: center;"> School Resumes</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>
                                                <h4 style="text-align: center;">
                                                    <?php echo round($firstTerm, 2) ?>%
                                                </h4>
                                            </strong></td>
                                        <td><strong>
                                                <h4 style="text-align: center;">
                                                    <?php echo round($secondTerm, 2) ?>%
                                                </h4>
                                            </strong></td>

                                        <td><strong>
                                                <h4 style="text-align: center;">
                                                    <?php echo round($thirdTerm, 2) ?>%
                                                </h4>
                                            </strong></td>

                                        <td><strong>
                                                <h3 style="text-align: center;">
                                                    <?php echo $result['score'] ?>%
                                                </h3>
                                            </strong></td>
                                        <td><strong>
                                                <h4 style="text-align: center;">
                                                    <?php echo $result['grade'] ?>
                                                </h4>
                                            </strong></td>
                                        <td><strong>
                                                <h4 style="text-align: center;">
                                                    <?php echo $result['remarks'] ?>
                                                </h4>
                                            </strong></td>
                                        <td>
                                            <h5 style="text-align: center;">
                                                <?php echo $result['termRemarks'] ?>
                                            </h5>
                                        </td>
                                        <?php if (!is_null($comment)) {
                                            echo '<td><strong>
                                 <h5 style="text-align: center;">' . $comment . '</h5>
                                 </strong></td>';
                                        }
                                        ?>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <?php echo $resumedate ?>
                                                </p>
                                            </strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="breadcomb-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="breadcomb-list">
                        <div class="row">
                            <div class="breadcomb-icon">
                                <image src="../admin/archive/<?php echo $sign; ?>" height="100" width="100" />
                                <h3 style="text-align: left;">
                                    <?php echo $schowner; ?>
                                </h3>
                                <h4 style="text-align: left;"> Chief Learning Officer </h4>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
<button id="cmd" onclick="generatePDF<?php echo $serial ?>()" class="btn btn-default btn-icon-notika"><i class="notika-icon notika-down-arrow"></i>
    <h3>Download Result</h3>
</button>
<script>
    function generatePDF<?php echo $serial ?>() {


        var divContents = $("#doc<?php echo $serial; ?>").html();
        var printWindow = window.open('', '', 'height=800,width=1600');
        printWindow.document.write('<html><head><title>Academic Reportsheets for <?php echo $stname . "   " . $dclass ?></title>');
        printWindow.document.write('</head><body >');
        printWindow.document.write(divContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();

    }
</script>