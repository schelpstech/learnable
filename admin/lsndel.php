<?php
// Check user login or not
include "conf.php";
if(!isset($_SESSION['unamed'])){
   header('Location: http://dwatschools.com.ng/login/index.php');
}
?>
<?php
if(!empty($_GET['lsnd'])) {         
        $edt = $_GET["lsnd"];
 
}
require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM classact WHERE id = '".$edt."'";
$editresult = $db_handle->runQuery($query);
?>



<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Manage Learning Materials - Learn at Home</title>
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
        .set({ html2canvas: { scale: 4 } })
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
    <div class="mobile-menu-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="mobile-menu">
                        <nav id="dropdown">
                            <ul class="mobile-menu-nav">
                                
								<li ><a  href="dashboard.php"><i class="notika-icon notika-windows"></i> Dashboard</a>
                        </li>
						<li ><a  href="mgstaff.php"><i class="notika-icon notika-refresh"></i> Staff</a>
                        </li>
						 <li><a href="mglearners.php"><i class="notika-icon notika-form"></i> Learners</a>
                        </li>
						<li><a  href="mgclass.php"><i class="notika-icon notika-app"></i> Class Details</a>
                        </li>
                        <li class="active"><a  href="mglesson.php"><i class="notika-icon notika-form"></i> Learning Resources</a>
                        </li>
						<li><form method="post" action= "logout.php">  <button name="log">  <i class="notika-icon notika-close"></i> Log Out</button></form>
                        </li>
						
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Menu end -->
    <!-- Main Menu area start-->
    <div class="main-menu-area mg-tb-40">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                       <li ><a  href="dashboard.php"><i class="notika-icon notika-windows"></i> Dashboard</a>
                        </li>
						<li><a  href="mgstaff.php"><i class="notika-icon notika-refresh"></i> Staff</a>
                        </li>
						 <li ><a href="mglearners.php"><i class="notika-icon notika-form"></i> Learners</a>
                        </li>
						<li><a  href="mgclass.php"><i class="notika-icon notika-app"></i> Class Details</a>
                        </li>
                         <li class="active"><a  href="mglesson.php"><i class="notika-icon notika-form"></i> Learning Resources</a>
                        </li>
						<li><form method="post" action= "logout.php">  <button name="log">  <i class="notika-icon notika-close"></i> Log Out</button></form>
                        </li>
                    </ul>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Main Menu area End-->
	<!-- Breadcomb area Start-->

	
	 <div class="form-element-area">
        <div class="container">
            <div class="row">
                
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                        <div class="basic-tb-hd">
                            <h2>Delete Learning Material Details</h2>
                            <p>Use the form below to modify details of Learning Materials </p>
						<h2>	 <?php
							
    if (isset($_SESSION['eds']) && $_SESSION['eds'])
    {
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
						<form method="POST" action="edmaterial.php" class="form-element-area" id="fupload" enctype="multipart/form-data">
                         
                         <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Change Term</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select  disabled type="text" required="yes" class="form-control" name="lsnterm">
<?php
foreach ($editresult as $ed) {
    ?>
<option value="<?php echo $ed["term"]; ?>"><?php echo $ed["term"]; ?></option>
<?php
}
?>

                            </select>
                                    </div>
                                </div>
                            </div>
                         
                          <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Change Week</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select   disabled type="text" required="yes" class="form-control" name="lsnweek">
<?php
foreach ($editresult as $ed) {
    ?>
<option value="<?php echo $ed["week"]; ?>"><?php echo $ed["week"]; ?></option>
<?php
}
?>
                    	               
                            </select>
                                    </div>
                                </div>
                            </div>
                            
                            	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Change Class</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select   disabled  type="text" required="yes" class="form-control" name="lsnclass" id="class-list" onChange="getclass();" >
<?php
foreach ($editresult as $ed) {
    ?>
<option value="<?php echo $ed["classid"]; ?>"><?php echo $ed["classname"]; ?></option>
<?php
}
?>

                            </select>
                                    </div>
                                </div>
                            </div>
							
								<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Change Subject</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select  disabled   type="text" required="yes" class="form-control" name="lsnsubject"  id="sbj-list" >
<?php
foreach ($editresult as $ed) {
    ?>
<option value="<?php echo $ed["subject"]; ?>"><?php echo $ed["subject"]; ?></option>
<?php
}
?>

                            </select>
                                    </div>
                                </div>
                            </div>
                            
                            	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Select Class Activity Type</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select  disabled  type="text" required="yes" class="form-control" name="lsnact">
<?php
foreach ($editresult as $ed) {
    ?>
<option value="<?php echo $ed["activity"]; ?>"><?php echo $ed["activity"]; ?></option>
<?php
}
?>
                        	   
											
                            </select>
                                    </div>
                                </div>
                            </div>
                            
							 	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" hidden>
                                <label> Term</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" required="yes" class="form-control" name="lsnid"  value="<?php echo $edt; ?>">
                                    </div>
                                </div>
                            </div>
							 
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Change Topic</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                         <input  disabled type="text" required="yes" class="form-control" name="lsntopic" <?php
foreach ($editresult as $ed) {
    ?>
 value="<?php echo $ed["topic"]; ?>">
<?php
}
?>
   
                                    </div>
                                </div>
                            </div>
                            
                            	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Change Scheduled or Deadline Date</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                         <input  disabled  type="date" required="yes" class="form-control" name="lsndate" <?php
foreach ($editresult as $ed) {
    ?>
 value="<?php echo $ed["actdate"]; ?>">
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
                                       <input type="submit" class="form-control" name="dellsn" value= "Delete Learning Material Details"/> 
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
    <div class="footer-copyright-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="footer-copy-right">
                        <p><strong>Powered by <a href="https://schelps.com.ng">SCHELPS</a></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
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