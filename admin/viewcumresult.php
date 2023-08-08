<?php

// Check user login or not
include "conf.php";
if (!isset($_SESSION['unamed'])) {
  header('Location: ../index.php');
}
if (!empty($_GET['term'])) {
  $term = $_GET["term"];
  $_SESSION['term'] = $term;
}
if (!empty($_GET['lid'])) {
  $lname = $_GET["lid"];
  $_SESSION['lname'] = $lname;
}
?>


<?php

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


$present = $row["total_present"];
$lead = $row["rating1"];
$eloq = $row["rating2"];
$neat = $row["rating3"];
$create = $row["rating4"];
$response = $row["rating5"];
$comment = $row["comment"];


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
                      }


                      if ($cum != '') {
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
                        } elseif ($cum >= 1) {
                          $grade = "F";
                        }
                      } else {
                        $grade = "";
                      }
                      if ($cum != '') {
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
                        }
                      } else {
                        $remarks = "";
                      }

                      //cumulatives

                      $sql = "SELECT AVG(totalscore) AS score FROM lhpresultrecord WHERE  lid = '$lname' AND term ='$firsttermref' ";
                      $result = mysqli_query($con, $sql);
                      $row = mysqli_fetch_array($result);
                      if (!empty($row["score"])) {
                        $firstterm = $row["score"];
                        $t1 = 1;
                      } else {
                        $firstterm = 0;
                        $t1 = 0;
                      }

                      $sql = "SELECT AVG(totalscore) AS score FROM lhpresultrecord WHERE  lid = '$lname' AND term ='$secondtermref' ";
                      $result = mysqli_query($con, $sql);
                      $row = mysqli_fetch_array($result);
                      if (!empty($row["score"])) {
                        $secondterm = $row["score"];
                        $t2 = 1;
                      } else {
                        $secondterm = 0;
                        $t2 = 0;
                      }

                      $sql = "SELECT AVG(totalscore) AS score FROM lhpresultrecord WHERE  lid = '$lname' AND term = '$term' ";
                      $result = mysqli_query($con, $sql);
                      $row = mysqli_fetch_array($result);
                      if (!empty($row["score"])) {
                        $thirdterm = $row["score"];
                        $t3 = 1;
                      } else {
                        $thirdterm = 0;
                        $t3 = 0;
                      }

                      $y = $firstterm + $secondterm + $thirdterm;
                      $a = $t1 + $t2 + $t3;


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
                        $tremarks = "Congratulations! You have been promoted to the next. Your academic performance this term is excellent; you need to keep up the good work to sustain this excellent performance in subsequent terms. Keep it Up!";
                      } elseif (($y / $a) >= 65) {
                        $tremarks = "Congratulations! You have been promoted to the next. Your academic performance this term is impressive but you need to work harder to achieve higher grades next term. Well done!";
                      } elseif (($y / $a) >= 50) {
                        $tremarks = "Congratulations! You have been promoted to the next. Your academic performance this term is moderate but with more effort towards studying, you will achieve higher grades next term. Cheer up!";
                      } elseif (($y / $a) >= 45) {
                        $tremarks = "Congratulations! You have been promoted to the next. Your academic performance this term is fair. You can do better if you can commit more effort and time to studying thoroughly next term.";
                      } elseif (($y / $a) >= 40) {
                        $tremarks = "Congratulations! You have been promoted to the next. Your academic performance this term is fair. You can do better if you can commit more effort and time to studying thoroughly next term.";
                      } else {
                        $tremarks = "Your academic performance this term is below the pass grade. You can do better if you can commit more effort and time to studying thoroughly next term.";
                      }
                      ?>

                      <tr>

                        <td><strong>
                            <p style="text-align: left;">
                              <?php echo strtoupper($subjectname) ?>
                            </p>
                          </strong></td>
                        <td><strong>
                            <p style="text-align: left;">
                              <?php echo $term1 ?>
                            </p>
                          </strong></td>
                        <td><strong>
                            <p style="text-align: left;">
                              <?php echo $term2 ?>
                            </p>
                          </strong></td>
                        <td><strong>
                            <p style="text-align: left;">
                              <?php echo $ca ?>
                            </p>
                          </strong></td>
                        <td><strong>
                            <p style="text-align: left;">
                              <?php echo $exam ?>
                            </p>
                          </strong></td>
                        <td><strong>
                            <p style="text-align: left;">
                              <?php echo $term3 ?>
                            </p>
                          </strong></td>
                        <td><strong>
                            <h4 style="text-align: center;">
                              <?php echo $cum ?>
                            </h4>
                          </strong></td>
                        <td><strong>
                            <h4 style="text-align: center;">
                              <?php echo $grade ?>
                            </h4>
                          </strong></td>
                        <td><strong>
                            <h4 style="text-align: center;">
                              <?php echo $remarks ?>
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
                            <?php echo round($firstterm, 2) ?>%
                          </h4>
                        </strong></td>
                      <td><strong>
                          <h4 style="text-align: center;">
                            <?php echo round($secondterm, 2) ?>%
                          </h4>
                        </strong></td>

                      <td><strong>
                          <h4 style="text-align: center;">
                            <?php echo round($thirdterm, 2) ?>%
                          </h4>
                        </strong></td>

                      <td><strong>
                          <h3 style="text-align: center;">
                            <?php echo round(($y / $a), 2) ?>%
                          </h3>
                        </strong></td>
                      <td><strong>
                          <h4 style="text-align: center;">
                            <?php echo $cgrade ?>
                          </h4>
                        </strong></td>
                      <td><strong>
                          <h4 style="text-align: center;">
                            <?php echo $cremarks ?>
                          </h4>
                        </strong></td>
                      <td>
                        <h5 style="text-align: center;">
                          <?php echo $tremarks ?>
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
  <?php include "foot.html"; ?>

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