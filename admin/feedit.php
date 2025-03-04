<?php
// Check user login or not
include "conf.php";
if (!isset($_SESSION['unamed'])) {
  header('Location: ../index.php');
}
?>
<?php
if (!empty($_GET['ref'])) {
  $ref = $_GET["ref"];
}

require_once("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpfeelist WHERE feeid = '$ref'";
$feedit = $db_handle->runQuery($query);
?>
<?php
foreach ($feedit as $ed) {
  $cname = $ed["classid"];
?>
<?php
}
?>
<?php
$sql = "SELECT * FROM lhpclass WHERE classid  = '$cname'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
if ($row['classname'] != "") {
  $feeclass = $row['classname'];
} else {
  $feeclass = $cname;
}
?>

<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Modify Fees - LearnAble</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- favicon
		============================================ -->
  <link rel="shortcut icon" type="image/x-icon" href="http://rabbischools.com.ng/press/wp-content/uploads/2020/04/icon.jpg">
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


  <div class="form-element-area">
    <div class="container">
      <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="form-element-list">
            <div class="basic-tb-hd">
              <h2>Edit Fee Details</h2>
              <p>Use the form below to modify fee details, only amount and fee name can be modified </p>
              <h2> <?php

                    if (isset($_SESSION['eds']) && $_SESSION['eds']) {
                      printf('<b>%s</b>', $_SESSION['eds']);
                      unset($_SESSION['eds']);
                    }
                    ?></h2>
            </div>
          </div>
        </div>
      </div>
      <br>
      <br>
      <br>
      <div class="row">
        <form method="POST" action="edfee.php?ref=<?php echo $ref; ?>" class="form-element-area" id="fupload" enctype="multipart/form-data">


          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Term</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" required="yes" class="form-control" name="term">
                  <?php
                  foreach ($feedit as $ed) {
                    $sql = "SELECT * FROM lhpsession WHERE sessionid  = '$ed[session]'";
                    $result = mysqli_query($con, $sql);
                    $row = mysqli_fetch_array($result);
                    $sess = $row['session'];
                  ?>
                    <option value="<?php echo $sess; ?>"><?php echo $sess; ?></option>
                  <?php
                  }
                  ?>

                </select>
              </div>
            </div>
          </div>


          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label>Fee For :</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <select type="text" required="yes" class="form-control" name="feeclass">


                  <option value="<?php echo $cname; ?>"><?php echo $feeclass; ?></option>

                </select>
              </div>
            </div>
          </div>







          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label> Change Feename</label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <input type="text" required="yes" class="form-control" name="feename" <?php
                                                                                      foreach ($feedit as $ed) {
                                                                                      ?>
                  value="<?php echo $ed["feename"]; ?>">
              <?php
                                                                                      }
              ?>

              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <label> Change Amount </label>
            <div class="form-group ic-cmp-int">
              <div class="form-ic-cmp">
                <i class="notika-icon notika-support"></i>
              </div>

              <div class="nk-int-st">
                <input type="number" required="yes" class="form-control" name="feeamount" <?php
                                                                                          foreach ($feedit as $ed) {
                                                                                          ?>
                  value="<?php echo $ed["amount"]; ?>">
              <?php
                                                                                          }
              ?>

              </div>
            </div>
          </div>
          <br>
          <br>
          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">

            <div class="form-group ic-cmp-int">

              <div class="nk-int-st">
                <button type="submit" class="btn btn-success" class="form-control" name="editfee"> Modify Fee Details</button>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">

            <div class="form-group ic-cmp-int">

              <div class="nk-int-st">
                <a href="delfee.php?ref=<?php echo $ref; ?>" type="button" class="btn btn-danger"><strong>Delete Fee Details</strong> </a>
              </div>
            </div>
          </div>
        </form>

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