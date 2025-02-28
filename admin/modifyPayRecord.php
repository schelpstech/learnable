<?php

// Check user login or not
include 'conf.php';
if (!isset($_SESSION['unamed'])) {
    header('Location: ../index.php');
}
?>
<?php
require_once('DBController.php');
$db_handle = new DBController();
$query = 'SELECT * FROM lhpclass';
$classresult = $db_handle->runQuery($query);
?>

<?php
require_once('DBController.php');
$db_handle = new DBController();
$query = 'SELECT DISTINCT term FROM lhpfeelist';
$feeresult = $db_handle->runQuery($query);
?>

<?php
require_once('DBController.php');
$db_handle = new DBController();
$query = 'SELECT * FROM lpterm where status = 1';
$termd = $db_handle->runQuery($query);
?>
<?php
if (isset($_GET['transref'])) {
    $_SESSION['transref'] = $_GET['transref'];
    require_once('DBController.php');
    $db_handle = new DBController();
    $transref = $_GET['transref'];
    $transquery = "SELECT lhptransaction.term as nterm, lhpclass.classname as className, 
    lhpuser.fname as fullName, lhpclass.classid as classid, lhptransaction.stdid as stdid,
    lhptransaction.amount as amount, lhptransaction.status as tstatus
        FROM lhptransaction
        LEFT JOIN lhpclass ON lhptransaction.classid = lhpclass.classid
        LEFT JOIN lhpuser ON lhptransaction.stdid = lhpuser.uname
        WHERE lhptransaction.transid = '$transref'";
    $refTransaction = $db_handle->runQuery($transquery);
}
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Payment Records - LearnAble</title>
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
    <?php include 'nav.html'; ?>
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
                            <h2>Payment Records</h2>
                            <p> You can record payments of cash received, bank transfer or deposit on this page. </p>

                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="row">
                <form method="POST" action="recordpayment.php" class="form-element-area" id="fupload" enctype="multipart/form-data">

                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                        <label>Current Term</label>
                        <div class="form-group ic-cmp-int">
                            <div class="form-ic-cmp">
                                <i class="notika-icon notika-support"></i>
                            </div>

                            <div class="nk-int-st">
                                <select type="text" class="form-control" name="term" required="yes">
                                    <?php
                                    foreach ($refTransaction as $tm) {
                                    ?>
                                        <option value="<?php echo $tm['nterm']; ?>"><?php echo $tm['nterm']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                        <label>Select Class</label>
                        <div class="form-group ic-cmp-int">
                            <div class="form-ic-cmp">
                                <i class="notika-icon notika-support"></i>
                            </div>

                            <div class="nk-int-st">
                                <select type="text" class="form-control" name="classname" required="yes">
                                    <?php
                                    foreach ($refTransaction as $tm) {
                                    ?>
                                        <option value="<?php echo $tm['classid']; ?>"><?php echo $tm['className']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                        <label>Select Learner</label>
                        <div class="form-group ic-cmp-int">
                            <div class="form-ic-cmp">
                                <i class="notika-icon notika-support"></i>
                            </div>

                            <div class="nk-int-st">
                                <select type="text" class="form-control" name="learner" required="yes">
                                    <?php
                                    foreach ($refTransaction as $tm) {
                                    ?>
                                        <option value="<?php echo $tm['stdid']; ?>"><?php echo $tm['fullName']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>



                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                        <label>Payment Status </label>
                        <div class="form-group ic-cmp-int">
                            <div class="form-ic-cmp">
                                <i class="notika-icon notika-support"></i>
                            </div>
                            <div class="nk-int-st">
                                <select type="text" class="form-control" name="status">
                                    <option value="">Select Current Payment Status</option>
                                    <option value="1">Payment Value Successfully Received</option>
                                    <option value="2">Payment Value is Pending</option>
                                    <?php
                                    foreach ($refTransaction as $tm) {
                                    ?>
                                        <option value="<?php echo $tm['tstatus']; ?>"><?php
                                                                                        if ($tm['tstatus'] == 1) {
                                                                                            echo "Payment Value Successfully Received";
                                                                                        } elseif ($tm['tstatus'] == 2) {
                                                                                            echo "Payment Value is Pending";
                                                                                        } else {
                                                                                            echo "Unknown Payment Status";
                                                                                        }; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                        <label>Amount Paid </label>
                        <div class="form-group ic-cmp-int">
                            <div class="form-ic-cmp">
                                <i class="notika-icon notika-calendar"></i>
                            </div>
                            <div class="nk-int-st">
                                <?php
                                foreach ($refTransaction as $tm) {
                                ?>
                                    <input type="number" required="yes" class="form-control" value="<?php echo $tm['amount']; ?>" name="amountpaid" min="100">
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="col-lg-12 col-md-4 col-sm-4 col-xs-12">
                        <div class="form-group ic-cmp-int">
                            <div class="nk-int-st">
                                <input type="submit" class="form-control" name="updatepaybill" value="  Modify Record Payment for  Selected Learner  " />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Breadcomb area End-->

    <!-- Start Footer area-->
    <?php include 'foot.html'; ?>
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