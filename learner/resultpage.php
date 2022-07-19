<?php

// Check user login or not
include "conf.php";
if (!isset($_SESSION['studnamed'])) {
  header('Location: ../index.php');
}
if (!empty($_GET['term'])) {
  $term = $_GET["term"];
}
?>


<?php
$lname = $_SESSION['studnamed'];


$sql = "SELECT * FROM `lhpuser` WHERE `uname` = '$lname'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while ($row = mysqli_fetch_assoc($result)) {

    $stname = $row["fname"];
    $gender = $row["gender"];
    $dob = $row["dob"];

    $cclass = $row["classid"];
    $pix = $row["picture"];
  }
}


$sql = "SELECT classid FROM lhpresultrecord WHERE lid = '$lname' AND term = '$term'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while ($row = mysqli_fetch_assoc($result)) {



    $cclass = $row["classid"];
  }
}
?>


<?php
$sql = "SELECT classname FROM `lhpclass` WHERE `classid` = '$cclass'";;
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while ($row = mysqli_fetch_assoc($result)) {

    $dclass = $row["classname"];
  }
}

// Class Population
$sql = "SELECT COUNT( DISTINCT lid) AS pop FROM lhpresultrecord WHERE `classid` = '$cclass' AND term = '$term'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);

$pop = $row["pop"];

//Result Configuration
$sql = "SELECT * FROM lhpresultconfig WHERE term = '$term' ";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);

$resumedate = $row["resumption"];
$opendays = $row["sch_open"];
$sign = $row["signature"];

//School Information
$sql = "SELECT * FROM lhpschool ";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);

$schname = $row["schname"];
$schmotto = $row["motto"];
$schyear = $row["founded"];
$schphone = $row["phone"];
$schemail = $row["email"];
$schweb = $row["website"];
$schaddress = $row["address"];
$schlogo = $row["logo"];
$schowner = $row["proprietor"];

//Get Affective Domain
$sql = "SELECT *  from lhpaffective WHERE uname = '$lname' AND term = '$term'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);


$present =  $row["total_present"];
$lead =  $row["rating1"];
$eloq =  $row["rating2"];
$neat =  $row["rating3"];
$create =  $row["rating4"];
$response =  $row["rating5"];


//Get Class Teacher's name
$sql = "SELECT * FROM `lhpclassalloc` WHERE term = '$term' and classid = '$cclass'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
$tutor = $row["tutorid"];

$sql = "SELECT * FROM `lhpstaff` WHERE  sname = '$tutor'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
$tutorname = $row["staffname"];
?>





<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Learner's Profile - Learnable</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- favicon
		============================================ -->
  <link rel="shortcut icon" type="image/x-icon" href="https://rabbischools.com.ng/press/wp-content/uploads/2020/04/icon.jpg">
  <!-- Google Fonts
		============================================ -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
  <!-- Bootstrap CSS
		============================================ -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- font awesome CSS
		============================================ -->
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <!-- owl.carousel CSS
		============================================ -->
  <link rel="stylesheet" href="css/owl.carousel.css">
  <link rel="stylesheet" href="css/owl.theme.css">
  <link rel="stylesheet" href="css/owl.transitions.css">
  <!-- meanmenu CSS
		============================================ -->
  <link rel="stylesheet" href="css/meanmenu/meanmenu.min.css">
  <!-- animate CSS
		============================================ -->
  <link rel="stylesheet" href="css/animate.css">
  <!-- normalize CSS
		============================================ -->
  <link rel="stylesheet" href="css/normalize.css">
  <!-- wave CSS
		============================================ -->
  <link rel="stylesheet" href="css/wave/waves.min.css">
  <link rel="stylesheet" href="css/wave/button.css">
  <!-- mCustomScrollbar CSS
		============================================ -->
  <link rel="stylesheet" href="css/scrollbar/jquery.mCustomScrollbar.min.css">
  <!-- Notika icon CSS
		============================================ -->
  <link rel="stylesheet" href="css/notika-custom-icon.css">
  <!-- Data Table JS
		============================================ -->
  <link rel="stylesheet" href="css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
  <!-- main CSS
		============================================ -->
  <link rel="stylesheet" href="css/main.css">
  <!-- style CSS
		============================================ -->
  <link rel="stylesheet" href="style.css">
  <!-- responsive CSS
		============================================ -->
  <link rel="stylesheet" href="css/responsive.css">
  <!-- modernizr JS
		============================================ -->
  <script src="js/html2pdf.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.0/chart.min.js" integrity="sha512-asxKqQghC1oBShyhiBwA+YgotaSYKxGP1rcSYTDrB0U6DxwlJjU59B67U8+5/++uFjcuVM8Hh5cokLjZlhm3Vg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script type="text/javascript" src="./chartload.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.2.0/jspdf.umd.min.js"></script>

  <script>
    function generatePDF() {


      var divContents = $("#doc").html();
      var printWindow = window.open('', '', 'height=800,width=1600');
      printWindow.document.write('<html><head><title>Academic Reportsheets for <?php echo $stname . "   " . $dclass ?></title>');
      printWindow.document.write('</head><body >');
      printWindow.document.write(divContents);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.print();

    }
  </script>


  <script src=”https://d3js.org/d3.v5.min.js”></script>

  <script src="js/vendor/modernizr-2.8.3.min.js"></script>

  <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>

  <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.33/vfs_fonts.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.4/angular.min.js"></script>
  <script src="./script.js"></script>
</head>

<body>
  <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
  <!-- Start Header Top Area -->
  <div class="header-top-area">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <div class="logo-area">
            <a href="#"><img src="img/logo/logo.png" alt="" /></a>
          </div>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">

        </div>
      </div>
    </div>
  </div>
  <!-- End Header Top Area -->
  <!-- Mobile Menu start -->
  <?php include "nav.html"; ?>
  <!-- Main Menu area End-->
  <!-- Breadcomb area Start-->
  <div id="doc">


    <!-- Data Table area Start-->
    <div class="data-table-area" style="text-align: center;">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="normal-table-list mg-t-30">

              <div class="bsc-tbl-bdr">
                <table class="table table-bordered" style="width:100%" border="1">
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
                <table class="table table-bordered" style="width:100%" border="1">
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
                <table class="table table-hover" style="width:100%">
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
                              <th style="text-align: center;">Teacher's Comment</th>
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




  <button id="cmd" onclick="generatePDF()" class="btn btn-default btn-icon-notika"><i class="notika-icon notika-down-arrow"></i>
    <h3>Download Results</h3>
  </button>
  <!-- Start Footer area-->
  <?php include "foot.php"; ?>

  <!-- End Footer area-->
  <!-- jquery
		============================================ -->
  <script src="js/vendor/jquery-1.12.4.min.js"></script>
  <!-- bootstrap JS
		============================================ -->
  <script src="js/bootstrap.min.js"></script>
  <!-- wow JS
		============================================ -->
  <script src="js/wow.min.js"></script>
  <!-- price-slider JS
		============================================ -->
  <script src="js/jquery-price-slider.js"></script>
  <!-- owl.carousel JS
		============================================ -->
  <script src="js/owl.carousel.min.js"></script>
  <!-- scrollUp JS
		============================================ -->
  <script src="js/jquery.scrollUp.min.js"></script>
  <!-- meanmenu JS
		============================================ -->
  <script src="js/meanmenu/jquery.meanmenu.js"></script>
  <!-- counterup JS
		============================================ -->
  <script src="js/counterup/jquery.counterup.min.js"></script>
  <script src="js/counterup/waypoints.min.js"></script>
  <script src="js/counterup/counterup-active.js"></script>
  <!-- mCustomScrollbar JS
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
		============================================ -->
  <script src="js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
  <!-- sparkline JS
		============================================ -->
  <script src="js/sparkline/jquery.sparkline.min.js"></script>
  <script src="js/sparkline/sparkline-active.js"></script>
  <!-- flot JS
		============================================ -->
  <script src="js/flot/jquery.flot.js"></script>
  <script src="js/flot/jquery.flot.resize.js"></script>
  <script src="js/flot/flot-active.js"></script>
  <!-- knob JS
		============================================ -->
  <script src="js/knob/jquery.knob.js"></script>
  <script src="js/knob/jquery.appear.js"></script>
  <script src="js/knob/knob-active.js"></script>
  <!--  Chat JS
		============================================ -->
  <script src="js/chat/jquery.chat.js"></script>
  <!--  todo JS
		============================================ -->
  <script src="js/todo/jquery.todo.js"></script>
  <!--  wave JS
		============================================ -->
  <script src="js/wave/waves.min.js"></script>
  <script src="js/wave/wave-active.js"></script>
  <!-- plugins JS
		============================================ -->
  <script src="js/plugins.js"></script>
  <!-- Data Table JS
		============================================ -->
  <script src="js/data-table/jquery.dataTables.min.js"></script>
  <script src="js/data-table/data-table-act.js"></script>
  <!-- main JS
		============================================ -->
  <script src="js/charts/Chart.js"></script>
  <script src="js/charts/bar-chart.js"></script>
  <script src="js/main.js"></script>


</body>

</html>