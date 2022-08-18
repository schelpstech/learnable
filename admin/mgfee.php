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
require_once("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpsession WHERE status = 1";
$termresult = $db_handle->runQuery($query);
?>


<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Create Fees - LearnAble</title>
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







  <script src="js/vendor/modernizr-2.8.3.min.js"></script>

  <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>


  <script type="text/javascript">
    function changeFunc() {
      var feetype = document.getElementById("feetype");
      var selectedValue = feetype.options[feetype.selectedIndex].value;
      if (selectedValue == "sbase") {
        $('#school').show();
        $('#classb').hide();
        $('#school').attr('required', '');
        $('#school').attr('data-error', 'School Fee Type  is required.');
      } else if (selectedValue == "cbase") {
        $('#school').hide();
        $('#classb').show();
        $('#classb').attr('required', '');
        $('#classb').attr('data-error', 'Class  is required.');
      } else {
        alert("Select A Fee Association Type");
        $('#school').hide();
        $('#classb').hide();

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
              <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12">
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
                    <p>.<span class="bread-ntd"></span></p>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-6 col-sm-6 col-xs-3">
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
              <h2>Manage Fees and Transactions</h2>
              <p> Create and Assign Applicable Fees, View and Authorize transactions for each learners </p><br><br>
              <h2>Create Fee</h2>

            </div>
          </div>
        </div>
      </div>
      <br>
      <br>
      <br>
      <div class="row">
        <form method="POST" action="createfee.php" class="form-element-area" id="fupload" enctype="multipart/form-data">




          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Select Session</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" required="yes" class="form-control" name="session">
                  <?php
                  foreach ($termresult as $termd) {
                  ?>
                    <option value="<?php echo $termd["sessionid"]; ?>"><?php echo $termd["session"]; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Select Fee Association Type</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" required="yes" class="form-control" id="feetype" name="feetype" onchange="changeFunc();">
                  <option value=""> Select Fee Type</option>
                  <option value="cbase"> Class Based Fee</option>
                  <option value="sbase"> School-wide Fee</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" id="classb" hidden>
            <label>Select Class</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" class="form-control" name="feeclass1">
                  <option value=""> Select Class</option>
                  <?php
                  foreach ($classresult as $classed) {
                  ?>
                    <option value="<?php echo $classed["classid"]; ?>"><?php echo $classed["classname"]; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>


          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" id="school" hidden>
            <label>Enter Fee Type </label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-wifi"></i>
              </div>
              <div class="nk-int-st">
                <input type="text" class="form-control" name="feeclass2">
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Enter Fee Name </label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-wifi"></i>
              </div>
              <div class="nk-int-st">
                <input type="text" required="yes" class="form-control" name="feename">
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Enter Fee Amount </label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-wifi"></i>
              </div>
              <div class="nk-int-st">
                <input type="number" required="yes" class="form-control" name="feeamount">
              </div>
            </div>
          </div>



          <br>
          <br>
          <div class="col-lg-12 col-md-4 col-sm-4 col-xs-12">

            <div class="form-group ic-cmp-int">

              <div class="nk-int-st">
                <input type="submit" class="form-control" name="cfee" value="Create Fee For Selected Class" />
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
              <h2>Fee List

              </h2>
              <p>A reference list for all the fees created and chargeable</p>
            </div>
            <div class="table-responsive">
              <table id="data-table-basic" class="table table-striped">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Session</th>
                    <th>Class</th>
                    <th>Fee Name</th>
                    <th>Amount</th>
                    <th>Modify</th>
                  </tr>
                </thead>


                <tbody>



                  <?php


                  include_once './conn.php';

                  $count = 1;
                  $query = $conn->prepare("select * from lhpfeelist where status = 1 ORDER BY session DESC ");
                  $query->setFetchMode(PDO::FETCH_OBJ);
                  $query->execute();
                  while ($row = $query->fetch()) {
                    $ref = $row->feeid;
                    $session = $row->session;
                    $cname = $row->classid;
                    $feename = $row->feename;
                    $amount = $row->amount;
                    $sql = "SELECT * FROM lhpsession WHERE sessionid  = '$session'";
                    $result = mysqli_query($con, $sql);
                    $row = mysqli_fetch_array($result);
                    $sess = $row['session'];

                    if (is_numeric($cname) == true) {
                      $sql = "SELECT classname FROM lhpclass WHERE classid  = '$cname'";
                      $result = mysqli_query($con, $sql);
                      $row = mysqli_fetch_assoc($result);
                      $feeclass = $row['classname'];
                    } else {
                      $feeclass = $cname;
                    }
                  ?>
                    <tr>
                      <td><?php echo $count++ ?></td>
                      <td><?php echo $sess ?></td>
                      <td><?php echo $feeclass ?></td>
                      <td><?php echo $feename ?></td>
                      <td><?php echo $amount ?></td>
                      <td><a href="feedit.php?ref=<?php echo $ref ?>" type="button" class="btn btn-warning">Modify</a></td>

                    </tr>
                  <?php } ?>
                </tbody>

                </tbody>
                <tfoot>
                  <tr>
                    <th>S/N</th>
                    <th>Session</th>
                    <th>Class</th>
                    <th>Fee Name</th>
                    <th>Amount</th>
                    <th>Modify</th>
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