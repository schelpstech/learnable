<?php 
$seriala = rand(11111111,88888888);
$serialb = rand(11111111,88888888);
$serial = $seriala.$serialb;
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

    <!-- Data Table area Start-->
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
                                        <th> Ca Score</th>
                                        <th>Exam Score</th>
                                        <th>Total Score</th>
                                        <th>Grade</th>
                                        <th>Remarks</th>
                                        <th>Lowest Score</th>
                                        <th>Average Score</th>
                                        <th>Highest Score</th>
                                    </tr>
                                </thead>


                                <tbody>



                                    <?php

                                    include_once './conn.php';

                                    $count = 1;
                                    $query = $conn->prepare("SELECT *  from lhpresultrecord WHERE lid = '$lname' AND term = '$term' ORDER BY rectime DESC");
                                    $query->setFetchMode(PDO::FETCH_OBJ);
                                    $query->execute();
                                    while ($row = $query->fetch()) {


                                    ?>
                                        <?php

                                        $subjectid =  $row->subjid;
                                        $score =  $row->score;
                                        $examscore =  $row->examscore;
                                        $totalscore =  $row->totalscore;
                                        $scorebar = array($totalscore);
                                        if ($totalscore >= 75) {
                                            $grade = "A";
                                        } elseif ($totalscore >= 65) {
                                            $grade = "B";
                                        } elseif ($totalscore >= 50) {
                                            $grade = "C";
                                        } elseif ($totalscore >= 45) {
                                            $grade = "D";
                                        } elseif ($totalscore >= 40) {
                                            $grade = "E";
                                        } else {
                                            $grade = "F";
                                        }

                                        if ($totalscore >= 75) {
                                            $remarks = "Excellent";
                                        } elseif ($totalscore >= 65) {
                                            $remarks = "Very Good";
                                        } elseif ($totalscore >= 50) {
                                            $remarks = "Moderate";
                                        } elseif ($totalscore >= 45) {
                                            $remarks = "Fair";
                                        } elseif ($totalscore >= 40) {
                                            $remarks = "Needs Help";
                                        } else {
                                            $remarks = "Needs Help";
                                        }

                                        $sql = "SELECT sbjname FROM lhpsubject WHERE sbjid  = '$subjectid' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);

                                        $subject = $row["sbjname"];
                                        $subjbar = array($subject);

                                        $sql = "SELECT MIN(totalscore) AS minscore FROM lhpresultrecord WHERE subjid  = '$subjectid' AND term = '$term'  ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);

                                        $subjectmin = $row["minscore"];

                                        $sql = "SELECT MAX(totalscore) AS maxscore FROM lhpresultrecord WHERE subjid  = '$subjectid' AND term = '$term' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);

                                        $subjectmax = $row["maxscore"];

                                        $sql = "SELECT AVG(totalscore) AS avgscore FROM lhpresultrecord WHERE subjid  = '$subjectid' AND term = '$term' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);

                                        $subjectavg = $row["avgscore"];

                                        $sql = "SELECT AVG(score) AS caavgscore FROM lhpresultrecord WHERE  lid = '$lname' AND term = '$term' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);

                                        $casubjectavg = $row["caavgscore"];

                                        $sql = "SELECT AVG(examscore) AS exavgscore FROM lhpresultrecord WHERE  lid = '$lname' AND term = '$term' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);

                                        $exsubjectavg = $row["exavgscore"];


                                        $sql = "SELECT AVG(totalscore) AS toavgscore FROM lhpresultrecord WHERE  lid = '$lname' AND term = '$term' ";
                                        $result = mysqli_query($con, $sql);
                                        $row = mysqli_fetch_array($result);

                                        $tsubjectavg = $row["toavgscore"];

                                        if ($tsubjectavg >= 75) {
                                            $cgrade = "A";
                                        } elseif ($tsubjectavg >= 65) {
                                            $cgrade = "B";
                                        } elseif ($tsubjectavg >= 50) {
                                            $cgrade = "C";
                                        } elseif ($tsubjectavg >= 45) {
                                            $cgrade = "D";
                                        } elseif ($tsubjectavg >= 40) {
                                            $cgrade = "E";
                                        } else {
                                            $cgrade = "F";
                                        }

                                        if ($tsubjectavg >= 75) {
                                            $cremarks = "Excellent";
                                        } elseif ($tsubjectavg >= 65) {
                                            $cremarks = "Very Good";
                                        } elseif ($tsubjectavg >= 50) {
                                            $cremarks = "Moderate";
                                        } elseif ($tsubjectavg >= 45) {
                                            $cremarks = "Fair";
                                        } elseif ($tsubjectavg >= 40) {
                                            $cremarks = "Needs Help";
                                        } else {
                                            $cremarks = "Needs Help";
                                        }


                                        if ($tsubjectavg >= 75) {
                                            $tremarks = "Your academic performance this term is excellent; you need to keep up the good work to sustain this excellent performance in subsequent terms. Keep it Up!";
                                        } elseif ($tsubjectavg >= 65) {
                                            $tremarks = "Your academic performance this term is impressive but you need to work harder to achieve higher grades next term. Well done!";
                                        } elseif ($tsubjectavg >= 50) {
                                            $tremarks = "Your academic performance this term is moderate but with more effort towards studying, you will achieve higher grades next term. Cheer up!";
                                        } elseif ($tsubjectavg >= 45) {
                                            $tremarks = "Your academic performance this term is fair. You can do better if you can commit more effort and time to studying thoroughly next term.";
                                        } elseif ($tsubjectavg >= 40) {
                                            $tremarks = "Your academic performance this term is fair. You can do better if you can commit more effort and time to studying thoroughly next term.";
                                        } else {
                                            $tremarks = "Your academic performance this term is below the pass grade. You can do better if you can commit more effort and time to studying thoroughly next term.";
                                        }
                                        ?>
                                        <tr>

                                            <td><strong>
                                                    <p style="text-align: left;"><?php echo strtoupper($subject) ?></p>
                                                </strong></td>
                                            <td>
                                                <h4 style="text-align: center;"><?php echo $score ?></h4>
                                            </td>
                                            <td>
                                                <h4 style="text-align: center;"><?php echo $examscore ?></h4>
                                            </td>
                                            <td><strong>
                                                    <h3 style="text-align: center;"><?php echo $totalscore ?></h3>
                                                </strong></td>
                                            <td><strong>
                                                    <p style="text-align: center;"><?php echo $grade ?></p><strong></td>
                                            <td><strong>
                                                    <p style="text-align: center;"><?php echo $remarks ?></p>
                                                </strong></td>
                                            <td>
                                                <p style="text-align: center;"><?php echo $subjectmin ?></p>
                                            </td>
                                            <td>
                                                <p style="text-align: center;"><?php echo round($subjectavg) ?></p>
                                            </td>
                                            <td>
                                                <p style="text-align: center;"><?php echo $subjectmax ?></p>
                                            </td>
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
    <!-- Data Table area End-->

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
                                                        <th style="text-align: center;">CA Cumuative</th>
                                                        <th style="text-align: center;">Exam Cumulative</th>
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
                                                                <h4 style="text-align: center;"><?php echo round($casubjectavg, 2) ?>%</h4>
                                                            </strong></td>
                                                        <td><strong>
                                                                <h4 style="text-align: center;"><?php echo round($exsubjectavg, 2) ?>%</h4>
                                                            </strong></td>
                                                        <td><strong>
                                                                <h3 style="text-align: center;"><?php echo round($tsubjectavg, 2) ?>%</h3>
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

    <div class="breadcomb-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <style type="text/css">
                        #chart-container {
                            width: auto;
                            height: auto;
                        }
                    </style>
                    <div id="chart-container">
                        <canvas id="mycanvas"></canvas>
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