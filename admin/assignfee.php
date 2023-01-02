<?php

// Check user login or not
include "conf.php";
if (!isset($_SESSION['unamed'])) {
  header('Location: ../index.php');
}
?>
<?php
require_once("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpclass";
$classresult = $db_handle->runQuery($query);
?>

<?php
$sql = "SELECT sessionid FROM lhpsession WHERE `status`  = 1 ";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
$sess = $row['sessionid'];

require_once("DBController.php");
$db_handle = new DBController();
$query = "SELECT term FROM lpterm Order by `status` DESC ";
$feeresult = $db_handle->runQuery($query);
?>

<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Manage Reportsheets - LearnAble</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- favicon
		============================================ -->
  <link rel="shortcut icon" type="image/x-icon" href="images/icon.jpg">
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


  <script>
    function generatePDF() {
      // Choose the element that our invoice is rendered in.
      const element = document.getElementById("doc");
      // Choose the element and save the PDF for our user.
      html2pdf()
        .set({
          html2canvas: {
            scale: 4
          }
        })
        .from(element)
        .save();


    }
  </script>


  <script>
    function getstudent() {
      var str = '';
      var val = document.getElementById('classtn');
      for (i = 0; i < val.length; i++) {
        if (val[i].selected) {
          str += val[i].value + ',';
        }
      }
      var str = str.slice(0, str.length - 1);

      $.ajax({
        type: "GET",
        url: "get_fee.php",
        data: 'classid=' + str,
        success: function(data) {
          $("#std-list").html(data);
        }
      });
    }
  </script>

  <script>
    function getfeelist() {
      var str = '';
      var val = document.getElementById('class-list');
      for (i = 0; i < val.length; i++) {
        if (val[i].selected) {
          str += val[i].value + ',';
        }
      }
      var str = str.slice(0, str.length - 1);

      $.ajax({
        type: "GET",
        url: "get_fee.php",
        data: 'feetype=' + str,
        success: function(data) {
          $("#fee-list").html(data);
        }
      });


      var type = document.getElementById("class-list");
      var selectedValue = type.options[type.selectedIndex].value;
      if (selectedValue == "PreviousBalance") {
        $('#byout').show();
        $('#bynoout').hide();
        $('#amountb').attr('required', '');
        $('#amountb').attr('data-error', 'Enter Outstanding Balance.');
      } else if (selectedValue !== "PreviousBalance") {
        $('#bynoout').show();
        $('#byout').hide();
        $('#amounta').attr('required', '');
        $('#amounta').attr('data-error', 'Error Fetching Fee.');
      }


    }
  </script>

  <script>
    function getfeeamount() {
      var str = '';
      var val = document.getElementById('fee-list');
      for (i = 0; i < val.length; i++) {
        if (val[i].selected) {
          str += val[i].value + ',';
        }
      }
      var str = str.slice(0, str.length - 1);

      $.ajax({
        type: "GET",
        url: "get_fee.php",
        data: 'feeid=' + str,
        success: function(data) {
          $("#amounta").html(data);
        }
      });
    }
  </script>

  <script src="js/vendor/modernizr-2.8.3.min.js"></script>

  <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>

  <script type="text/javascript">
    function changeFunc() {
      var type = document.getElementById("type");
      var selectedValue = type.options[type.selectedIndex].value;
      if (selectedValue == "School-Based") {
        $('#byclass').hide();
        $('#bylearner').hide();
      } else if (selectedValue == "Class-Based") {
        $('#byclass').show();
        $('#bylearner').hide();
        $('#classtn').attr('required', '');
        $('#classtn').attr('data-error', 'Select Class to assign fee.');
      } else if (selectedValue == "Learner-Based") {
        $('#byclass').show();
        $('#bylearner').show();
        $('#classtn').attr('required', '');
        $('#classtn').attr('data-error', 'Select Class to assign fee.');
        $('#std-list').attr('required', '');
        $('#std-list').attr('data-error', 'Select Learner you are assigning fee to.');
      } else {
        alert("Select Who you are assigning to");
        $('#bylearner').hide();
        $('#byclass').hide();

      }
    }
  </script>


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
  <div class="breadcomb-area">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="breadcomb-list">
            <div class="row">
              <div class="col-lg-9 col-md-6 col-sm-6 col-xs-12">
                <div class="breadcomb-wp">
                  <div class="breadcomb-icon">
                    <i class="notika-icon notika-support"></i>
                  </div>
                  <div class="breadcomb-ctn">
                    <h2>Welcome Admin</h2>
                    <h2> <?php

                          if (isset($_SESSION['feemessage']) && $_SESSION['feemessage']) {
                            printf('<b>%s</b>', $_SESSION['feemessage']);
                            unset($_SESSION['feemessage']);
                          }
                          ?></h2>
                    <p><span class="bread-ntd"></span></p>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-6 col-xs-3">
                <div class="breadcomb-report">
                  <button type="button" onclick="generatePDF()" title="Download PDF" class="btn"><i class="notika-icon notika-sent"></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="form-element-area">
    <div class="container">
      <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="form-element-list">
            <div class="basic-tb-hd">
              <h2>Assign Fee</h2>
              <p> Assign Applicable Fee to a learner, a class or the whole school. </p>

            </div>
          </div>
        </div>
      </div>
      <br>
      <br>
      <br>
      <div class="row">
        <form method="POST" action="feeassign.php" class="form-element-area" id="fupload" enctype="multipart/form-data">


          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Assign Fee To :</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" required="yes" class="form-control" name="type" id="type" onchange="changeFunc();">
                  <option value=""> Select Who you want to assign fee to</option>
                  <option value="School-Based">Assign to All Students</option>
                  <option value="Class-Based"> Assign By Class</option>
                  <option value="Learner-Based"> Assign to Learner</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" id="byclass" hidden>
            <label>Select Class</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" class="form-control" name="classname" id="classtn" onChange="getstudent();">
                  <option value="">Select Class</option>

                  <?php
                  foreach ($classresult as $classd) {
                  ?>
                    <option value="<?php echo $classd["classid"]; ?>"><?php echo $classd["classname"]; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>



          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" id="bylearner" hidden>
            <label>Select Learner</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" class="form-control" name="learner" id="std-list">
                  <option value="">Select Learner</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Select Term</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" required="yes" class="form-control" name="term" id="term">

                  <?php
                  foreach ($feeresult as $termd) {
                  ?>
                    <option value="<?php echo $termd["term"]; ?>"><?php echo $termd["term"]; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Select Fee Type</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" class="form-control" name="feeclass" id="class-list" onChange="getfeelist();">
                  <option value="">Select Fee Type</option>
                  <?php

                  require_once("DBController.php");
                  $db_handle = new DBController();

                  $query = "SELECT DISTINCT classid FROM lhpfeelist WHERE `session` = '$sess' AND status = 1;";
                  $results = $db_handle->runQuery($query);
                  ?>

                  <option value="">Select Fee Type From List of Fees</option>
                  <option value="PreviousBalance">Previous Term Outstanding</option>
                  <?php
                  foreach ($results as $std) {

                    $cname = $std["classid"];
                    $dname = $std["classid"];



                    if (is_numeric($cname) == true) {
                      $sql = "SELECT classname FROM lhpclass WHERE classid  = '$cname'";
                      $result = mysqli_query($con, $sql);
                      $row = mysqli_fetch_assoc($result);
                      $feeclass = $row['classname'];
                    } else {
                      $feeclass = $cname;
                    }


                  ?>

                    <option value="<?php echo $dname; ?>"><?php echo $feeclass; ?> FEE </option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>




          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Select Fee Name</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-wifi"></i>
              </div>
              <div class="nk-int-st">
                <select type="text" required="yes" class="form-control" name="feeid" id="fee-list" onChange="getfeeamount();">
                  <option value="">Select Fee Name</option>

                </select>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" id="bynoout">
            <label>Fee Amount </label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-wifi"></i>
              </div>
              <div class="nk-int-st">
                <select type="text" class="form-control" name="feeamounta" id="amounta">
                  <option value="">Amount of Assigned Fee </option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" id="byout" hidden>
            <label>Enter Fee Amount </label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-wifi"></i>
              </div>
              <div class="nk-int-st">
                <input type="text" class="form-control" name="feeamountb" id="amountb" />

              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Payment Due Date </label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-calendar"></i>
              </div>
              <div class="nk-int-st">
                <input type="date" required="yes" class="form-control" name="feedate">

              </div>
            </div>
          </div>



          <br>
          <br>
          <div class="col-lg-12 col-md-4 col-sm-4 col-xs-12">

            <div class="form-group ic-cmp-int">

              <div class="nk-int-st">
                <input type="submit" class="form-control" name="assign" value="Assign Fee to Selected Customer" />
              </div>
            </div>
          </div>


        </form>

      </div>
    </div>
  </div>

  </div>
  </div>

  <!-- Breadcomb area End-->
  <!-- Data Table area Start-->
  <div id="doc" class="data-table-area">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="data-table-list">
            <div class="basic-tb-hd">
              <h2>Assigned Fee Table

              </h2>
              <p>Here is a list of all the fees assigned to learners </p>
            </div>
            <div class="table-responsive">
              <table id="data-table-basic" class="table table-striped">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Term</th>
                    <th>Class</th>
                    <th>Full name</th>
                    <th> Fee Type</th>
                    <th> Fee Name</th>
                    <th>Amount </th>
                    <th> Discount</th>
                    <th> Payable</th>
                    <th> Status</th>

                  </tr>
                </thead>


                <tbody>



                  <?php


                  include_once './conn.php';

                  $count = 1;
                  $query = $conn->prepare("select * from lhpassignedfee ORDER BY status and term  DESC");
                  $query->setFetchMode(PDO::FETCH_OBJ);
                  $query->execute();
                  while ($row = $query->fetch()) {
                    $assid =  $row->assid;
                    $term =  $row->term;
                    $stdid =  $row->stdid;
                    $feeid =  $row->feeid;
                    $classid =  $row->classid;
                    $discount =  $row->discount;
                    $type =  $row->type;
                    $status =  $row->status;
                    $feeamount = $row->amount;



                    if (is_numeric($classid) == true) {
                      $sql = "SELECT classname FROM lhpclass WHERE classid  = '$classid'";
                      $result = mysqli_query($con, $sql);
                      $row = mysqli_fetch_assoc($result);
                      $feeclass = $row['classname'];
                    } else {
                      $feeclass = $classid;
                    }

                    $sql = "SELECT * FROM lhpuser WHERE uname  = '$stdid'";
                    $result = mysqli_query($con, $sql);
                    $row = mysqli_fetch_array($result);
                    $std = $row['fname'] ?? "";




                    if ($feeid == "PreviousBalance") {
                      $usename = "PREVIOUS TERM BALANCE";
                    } else {
                      $sql = "SELECT * FROM lhpfeelist WHERE feeid  = '$feeid'";
                      $result = mysqli_query($con, $sql);
                      $row = mysqli_fetch_array($result);
                      $feename = $row['feename'];
                      $usename = $feename;
                    }
                    if ($status == 1) {
                      $feestatus = '<a href="deactfee.php?ref=' . $assid . '" type="button"  class="btn btn-success" >Active. Click to De-activate</a>';
                    } else {
                      $feestatus = '<a href="activatefee.php?ref=' . $assid . '" type="button"  class="btn btn-danger" >Inactive - Click to Activate</a>';
                    }

                  ?>
                    <tr><strong>
                        <td><?php echo $count++ ?></td>
                        <td><?php echo $term ?></td>
                        <td><?php echo $feeclass ?></td>
                        <td><?php echo $std ?></td>
                        <td><?php echo $type ?></td>
                        <td><?php echo  $usename ?></td>
                        <td><?php echo $feeamount ?></td>
                        <td><?php echo $discount ?></td>
                        <td><?php echo $pay = $feeamount - $discount;  ?></td>
                        <td><?php echo $feestatus ?></td>

                      </strong>
                    </tr>
                  <?php } ?>
                </tbody>

                </tbody>
                <tfoot>
                  <tr>
                    <th>S/N</th>
                    <th>Term</th>
                    <th>Class</th>
                    <th>Full name</th>
                    <th> Fee Type</th>
                    <th> Fee Name</th>
                    <th>Amount </th>
                    <th> Discount</th>
                    <th> Payable</th>
                    <th> Status</th>

                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Data Table area End-->
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
  <script src="js/main.js"></script>
  <!-- tawk chat JS
		============================================ -->
  <script src="js/tawk-chat.js"></script>
</body>

</html>