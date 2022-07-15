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
                                <thead>
                                    <tr>

                                    </tr>
                                </thead>


                                <tbody>





                                    <tr>
                                        <td>
                                            <image src="../admin/images/<?php echo $schlogo; ?>" width="150" height="150" /><br>
                                            <strong>Founded: <?php echo $schyear; ?></strong>
                                        </td>
                                        <td>

                                            <h1 style="text-align: center;"> <?php echo $schname; ?> </h1>
                                            <h5 style="text-align: center;"> <?php echo $schmotto; ?> </h5>
                                            <h5 style="text-align: center;"> <?php echo $schaddress; ?> </h5>
                                            <h5 style="text-align: center;"> <?php echo $schphone; ?> | <?php echo $schemail; ?> | <?php echo $schweb ?> </h5>
                                            <h4 style="text-align: center;"> <?php echo $term . " " ?> Academic Reportsheets for <?php echo $dclass ?></h4>
                                        </td>

                                    </tr>

                                </tbody>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><br>


    <div class="data-table-area" style="text-align: center;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="normal-table-list mg-t-30">
                        <div class="basic-tb-hd">
                            <strong>
                                <h3 style="text-align: center;">Learners Details</h3>
                            </strong>

                        </div>

                        <div class="bsc-tbl-bdr">
                            <table class="table table-bordered" style="width:100%;" border="1">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Learners ID</th>
                                        <th style="text-align: center;">Fullname</th>
                                        <th style="text-align: center;"> Gender</th>
                                        <th style="text-align: center;">Date of Birth</th>
                                        <th style="text-align: center;">Current Class </th>
                                        <th style="text-align: center;"> Class Teacher</th>
                                        <th style="text-align: center;"> Class Population</th>
                                        <th style="text-align: center;">Passport</th>
                                    </tr>
                                </thead>


                                <tbody>





                                    <tr>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $lname ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <h4 style="text-align: center;"> <?php echo $stname ?></h4>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $gender ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $dob ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $dclass; ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $tutorname; ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $pop; ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;">
                                                    <image src="images/profilepix/<?php echo $pix; ?>" height="100" width="100" />
                                                </p>
                                            </strong></td>
                                    </tr>

                                </tbody>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="doc" class="data-table-area" style="text-align: center;">
        <div class="container">
            <div class="row">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="normal-table-list mg-t-30">
                        <div class="basic-tb-hd">
                            <strong>
                                <h3 style="text-align: center;">Attendance and Affective Domain Ratings</h3>
                            </strong>

                        </div>

                        <div class="bsc-tbl-bdr">
                            <table class="table table-hover" style="width:100%" border="1">
                                <thead>
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





                                    <tr>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $opendays ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $present ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $opendays - $present; ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $lead ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $eloq ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $neat ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $create ?></p>
                                            </strong></td>
                                        <td><strong>
                                                <p style="text-align: center;"><?php echo $response ?></p>
                                            </strong></td>
                                    </tr>

                                </tbody>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  Result-->
    <div id="doc" class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="normal-table-list mg-t-30">
                        <div class="basic-tb-hd">
                            <strong>
                                <h3 style="text-align: center;">Academic Performance Report</h3>
                            </strong>

                        </div>

                        <div class="bsc-tbl-bdr">
                            <table class="table table-bordered" style="width:100%" border="1">
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
                                    $query = $conn->prepare("SELECT DISTINCT lhpresultrecord.subjid as subjectid , lhpsubject.sbjid, lhpsubject.sbjname as subjectname  from lhpresultrecord LEFT JOIN lhpsubject on lhpresultrecord.subjid = lhpsubject.sbjid WHERE lhpresultrecord.classid = '$cclass' ORDER BY lhpsubject.sbjname ASC");
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

                                        $sql =  "SELECT tid FROM `lpterm` WHERE `term`  = '$term'";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $current_termid = $row["tid"];

                                        $secondtermid =  $current_termid - 1;
                                        $firsttermid =  $current_termid - 2;

                                        $sql =  "SELECT term FROM `lpterm` WHERE `tid`  = '$firsttermid'";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $firsttermref = $row["term"];

                                        $sql =  "SELECT term FROM `lpterm` WHERE `tid`  = '$secondtermid'";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $secondtermref = $row["term"];

                                        //1st Term Score

                                        $sql = "SELECT `totalscore` FROM `lhpresultrecord` WHERE `term`  = '$firsttermref' and `subjid`  = '$subjectid' and lid = '$lname'  ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        if (!empty($row["totalscore"])) {
                                            $term1 = $row["totalscore"];
                                            $sum1 = $row["totalscore"];
                                            $rate1 = 1;
                                        } else {
                                            $term1 = '';
                                            $sum1 = 0;
                                            $rate1 = 0;
                                        }
                                        //2nd Term Score

                                        $sql = "SELECT `totalscore` FROM `lhpresultrecord` WHERE `term`  = '$secondtermref' and `subjid`  = '$subjectid' and lid = '$lname' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        if (!empty($row["totalscore"])) {
                                            $term2 = $row["totalscore"];
                                            $sum2 = $row["totalscore"];
                                            $rate2 = 1;
                                        } else {
                                            $term2 = '';
                                            $sum2 = 0;
                                            $rate2 = 0;
                                        }

                                        //3rd Term Score

                                        $sql = "SELECT score, examscore, `totalscore` FROM `lhpresultrecord` WHERE `term`  = '$term' and `subjid`  = '$subjectid' and lid = '$lname'  ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        if (!empty($row["score"])) {
                                            $ca = $row["score"];
                                        } else {
                                            $ca = '';
                                        }

                                        if (!empty($row["examscore"])) {
                                            $exam = $row["examscore"];
                                        } else {
                                            $exam = '';
                                        }
                                        if (!empty($row["totalscore"])) {
                                            $term3 = $row["totalscore"];
                                            $sum3 = $row["totalscore"];
                                            $rate3 = 1;
                                        } else {
                                            $term3 = '';
                                            $sum3 = 0;
                                            $rate3 = 0;
                                        }

                                        $x = $rate1 + $rate2 + $rate3;
                                        if ($x == 0) {
                                            $cum = '';
                                        } else {
                                            $cum = round((($sum1 + $sum2 + $sum3) / $x), 2);
                                            $a += 1;
                                            $y += $cum;
                                        }


                                        if ($cum >= 75) {
                                            $grade = "A";
                                        } elseif ($cum >= 65) {
                                            $grade = "B";
                                        } elseif ($cum >= 50) {
                                            $grade = "C";
                                        } elseif ($cum >= 45) {
                                            $grade = "D";
                                        } elseif ($cum >= 40) {
                                            $grade = "E";
                                        } elseif ($cum >= 0) {
                                            $grade = "F";
                                        } else {
                                            $grade = "";
                                        }

                                        if ($cum >= 75) {
                                            $remarks = "Excellent";
                                        } elseif ($cum >= 65) {
                                            $remarks = "Very Good";
                                        } elseif ($cum >= 50) {
                                            $remarks = "Moderate";
                                        } elseif ($cum >= 45) {
                                            $remarks = "Fair";
                                        } elseif ($cum >= 40) {
                                            $remarks = "Needs Help";
                                        } elseif ($cum >= 0) {
                                            $remarks = "Needs Help";
                                        } else {
                                            $remarks = "";
                                        }

                                        //cumulatives
                                        
                                        $sql = "SELECT AVG(totalscore) AS score FROM lhpresultrecord WHERE  lid = '$lname' AND term ='$firsttermref' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $firstterm = $row["score"];
                                        
                                        $sql = "SELECT AVG(totalscore) AS score FROM lhpresultrecord WHERE  lid = '$lname' AND term ='$secondtermref' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $secondterm = $row["score"];
                                        
                                        $sql = "SELECT AVG(totalscore) AS score FROM lhpresultrecord WHERE  lid = '$lname' AND term = '$term' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);
                                        $thirdterm = $row["score"];

                                        if (($y / $a) >= 75) {
                                            $cgrade = "A";
                                        } elseif (($y / $a) >= 65) {
                                            $cgrade = "B";
                                        } elseif (($y / $a) >= 50) {
                                            $cgrade = "C";
                                        } elseif (($y / $a) >= 45) {
                                            $cgrade = "D";
                                        } elseif (($y / $a) >= 40) {
                                            $cgrade = "E";
                                        } else {
                                            $cgrade = "F";
                                        }

                                        if (($y / $a) >= 75) {
                                            $cremarks = "Excellent";
                                        } elseif (($y / $a) >= 65) {
                                            $cremarks = "Very Good";
                                        } elseif (($y / $a) >= 50) {
                                            $cremarks = "Moderate";
                                        } elseif (($y / $a) >= 45) {
                                            $cremarks = "Fair";
                                        } elseif (($y / $a) >= 40) {
                                            $cremarks = "Needs Help";
                                        } else {
                                            $cremarks = "Needs Help";
                                        }


                                        if (($y / $a) >= 75) {
                                            $tremarks = "Your academic performance this term is excellent; you need to keep up the good work to sustain this excellent performance in subsequent terms. Keep it Up!";
                                        } elseif (($y / $a) >= 65) {
                                            $tremarks = "Your academic performance this term is impressive but you need to work harder to achieve higher grades next term. Well done!";
                                        } elseif (($y / $a) >= 50) {
                                            $tremarks = "Your academic performance this term is moderate but with more effort towards studying, you will achieve higher grades next term. Cheer up!";
                                        } elseif (($y / $a) >= 45) {
                                            $tremarks = "Your academic performance this term is fair. You can do better if you can commit more effort and time to studying thoroughly next term.";
                                        } elseif (($y / $a) >= 40) {
                                            $tremarks = "Your academic performance this term is fair. You can do better if you can commit more effort and time to studying thoroughly next term.";
                                        } else {
                                            $tremarks = "Your academic performance this term is below the pass grade. You can do better if you can commit more effort and time to studying thoroughly next term.";
                                        }
                                        ?>

                                        <tr>

                                            <td><strong>
                                                    <p style="text-align: left;"> <?php echo strtoupper($subjectname) ?></p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;"> <?php echo $term1 ?></p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;"> <?php echo $term2 ?></p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;"> <?php echo $ca ?></p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;"> <?php echo $exam ?></p>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: left;"> <?php echo $term3 ?></p>
                                                </strong></td>
                                            <td><strong>
                                                    <h4 style="text-align: center;">
                                                        <?php echo $cum ?></h4>
                                                </strong></td>
                                            <td><strong>
                                                    <h4 style="text-align: center;">
                                                        <?php echo $grade ?></h4>
                                                </strong></td>
                                            <td><strong>
                                                    <h4 style="text-align: center;">
                                                        <?php echo $remarks ?></h4>
                                                </strong></td>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Remarks-->

    <div class="breadcomb-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <!-- Data Table area Start-->
                    <div id="doc" class="data-table-area">
                        <div class="container">
                            <div class="row">

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="normal-table-list mg-t-30">
                                        <div class="basic-tb-hd">
                                            <strong>
                                                <h3 style="text-align: center;">Performance Remarks</h3>
                                            </strong>

                                        </div>

                                        <div class="bsc-tbl-bdr">
                                            <table class="table table-bordered" border="1">
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
                                                                <h4 style="text-align: center;"><?php echo round($firstterm, 2) ?>%</h4>
                                                            </strong></td>
                                                        <td><strong>
                                                                <h4 style="text-align: center;"><?php echo  round($secondterm, 2) ?>%</h4>
                                                            </strong></td>

                                                        <td><strong>
                                                                <h4 style="text-align: center;"><?php echo round($thirdterm, 2) ?>%</h4>
                                                            </strong></td>

                                                        <td><strong>
                                                                <h3 style="text-align: center;"><?php echo round(($y / $a), 2) ?>%</h3>
                                                            </strong></td>
                                                        <td><strong>
                                                                <h4 style="text-align: center;"><?php echo $cgrade ?></h4>
                                                            </strong></td>
                                                        <td><strong>
                                                                <h4 style="text-align: center;"><?php echo $cremarks ?></h4>
                                                            </strong></td>
                                                        <td>
                                                            <h5 style="text-align: center;"><?php echo $tremarks ?></h5>
                                                        </td>
                                                        <?php if (!is_null($comment)) {
                                                            echo '<td><strong>
                                 <h5 style="text-align: center;">' . $comment . '</h5>
                                 </strong></td>';
                                                        }
                                                        ?>

                                                        <td><strong>
                                                                <p style="text-align: center;"><?php echo $resumedate ?></p>
                                                            </strong></td>

                                                    </tr>

                                                </tbody>

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
    <div class="breadcomb-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="breadcomb-list">
                        <div class="row">


                            <div class="breadcomb-icon">
                                <image src="../admin/archive/<?php echo $sign; ?>" height="100" width="100" />
                                <h3 style="text-align: left;"> <?php echo $schowner; ?> </h3>
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