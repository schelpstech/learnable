<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['stnamed'])){
     header('Location: ../index.php');
}

?>
<?php

if(!empty($_GET['tid'])) {         
        $viewid = $_GET["tid"];
}
require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpscheme where schmid ='$viewid'";
$book = $db_handle->runQuery($query);

?>
<?php
if(!empty($_GET['qid'])) {         
        $qid = $_GET["qid"];
}
require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpquestion where questid ='$qid'";
$dead = $db_handle->runQuery($query);

?>



<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>View Assessment -  LearnAble</title>
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
    <script src="js/hide-show-fields-form.js"></script>
    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
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
                                
                             <li><a  href="profile.php"><i class="notika-icon notika-windows"></i> My Profile</a>
                        </li>
						<li class="active"><a  href="scheme.php"><i class="notika-icon notika-form"></i> Scheme</a>
                        </li>
						 <li><a href="#"><i class="notika-icon notika-form"></i> Create Link</a>
                        </li>
						<li><a  href="#"><i class="notika-icon notika-app"></i> Submission</a>
                        </li>
                        <li><a  href="#"><i class="notika-icon notika-mail"></i> E-mail</a>
                        </li>
                        
                        <li><a  href="#"><i class="notika-icon notika-house"></i> Home</a>
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
                         <li><a  href="profile.php"><i class="notika-icon notika-windows"></i> My Profile</a>
                        </li>
						<li class="active"><a  href="scheme.php"><i class="notika-icon notika-form"></i> Scheme</a>
                        </li>
						 <li><a href="#"><i class="notika-icon notika-form"></i> Create Link</a>
                        </li>
						<li><a  href="#"><i class="notika-icon notika-app"></i> Submission</a>
                        </li>
                        <li><a  href="#"><i class="notika-icon notika-mail"></i> E-mail</a>
                        </li>
                        
                        <li><a  href="#"><i class="notika-icon notika-house"></i> Home</a>
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
	<div class="breadcomb-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="notika-icon notika-windows"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Welcome

<?php
$lname = $_SESSION['stnamed'];

include "config.php";

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT `staffname` FROM `lhpstaff` WHERE `sname` = '".$lname."'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
	echo $row["staffname"];
  }
} else {
  echo ".$lname.";
}

mysqli_close($conn);
?>													</h2>
										<p>.<span class="bread-ntd"></span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
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
                            <h2>Modify Assessment Features</h2>
                            <p>Use the form below to change Mark Obtainable and Submission Deadline Date for this assessment.</p>
						<h2>	 <?php
							
    if (isset($_SESSION['message']) && $_SESSION['message'])
    {
      printf('<b>%s</b>', $_SESSION['message']);
      unset($_SESSION['message']);
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
						<form method="POST" action="modquestion.php" class="form-element-area" id="fupload" enctype="multipart/form-data">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" hidden="yes">
                                <div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text"   class="form-control" name ="qid"  value= "<?php
							echo $qid;   // now, print the question id ?>">
                                    
									</div>
                                </div>
                            </div>
						
					
                           
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Term </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" disabled="yes" class="form-control" name="term"  required="yes"  >
<?php
foreach ($book as $booked) {
    ?>
<option value="<?php echo $booked["term"]; ?>"><?php echo $booked["term"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
							
							
							 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Class </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="classname" disabled="yes" required="yes"  >
<?php
foreach ($book as $booked) {
    $cname = $booked["classname"];
  $sql = "SELECT * FROM lhpclass WHERE classid  = '$cname'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $viewc = $row[classname];
    ?>
<option value="<?php echo $booked["classname"]; ?>"><?php echo $viewc; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
                        
                        	 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Subject </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="subject" disabled="yes" required="yes"  >
<?php
foreach ($book as $booked) {
    $sbj = $booked["subject"];
  $sql = "SELECT * FROM lhpsubject WHERE sbjid  = '$sbj'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $views = $row[sbjname];
    ?>
<option value="<?php echo $booked["subject"]; ?>"><?php echo $views; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
                            
                            	 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Week </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="week" disabled="yes" required="yes"  >
<?php
foreach ($book as $booked) {
    ?>
<option value="<?php echo $booked["week"]; ?>"><?php echo $booked["week"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
                            
                            	 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Topic </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="topic" disabled="yes"  required="yes"  >
<?php
foreach ($book as $booked) {
    ?>
<option value="<?php echo $booked["topic"]; ?>"><?php echo $booked["topic"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
                        
                        	 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Set Submission Deadline Date </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="date" class="form-control" name="due"  required="yes"
                            value="<?php
foreach ($dead  as $deadd) {
    ?>
<?php echo $deadd["deadline"]; ?>
<?php
}
?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Set Mark Obtainable </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="number" class="form-control" name="grade" min="1" required="yes" value="<?php
foreach ($dead  as $deadd) {
    ?>
<?php echo $deadd["grade"]; ?>
<?php
}
?>">
                                    </div>
                                </div>
                            </div>
                            
                  
							<br>
							<br>
							 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                
								<div class="form-group ic-cmp-int">
                                    
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="modifyquestion" value="Modify Assessment Details"/> 
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
   
    <!-- Start Footer area-->
    <div class="footer-copyright-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="footer-copy-right">
                        <p><strong>Rabbi Model Schools - Powered by <a href="https://schelps.com.ng">SCHELPS</a></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Footer area-->
    <!-- jquery
		============================================ -->
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
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
     <!-- summernote JS
		============================================ -->
   
    <!-- Data Table JS
		============================================ -->
    <script src="js/data-table/jquery.dataTables.min.js"></script>
    <script src="js/data-table/data-table-act.js"></script>
    <!-- main JS
		============================================ -->
    <script src="js/main.js"></script>
    
	<!-- tawk chat JS
		============================================ -->
      <!--Start of Tawk.to Script-->
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/59ea285dc28eca75e4627337/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
<!--End of Tawk.to Script-->
</body>

</html>