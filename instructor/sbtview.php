<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['unamed'])){
     header('Location: http://dwatschools.com.ng/login/index.php');
}

?>


<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Staff Assignments - Learn at Home</title>
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
                                
                          <li ><a  href="timeline.php"><i class="notika-icon notika-windows"></i> My Timeline</a>
                        </li>
						<li><a  href="upload.php"><i class="notika-icon notika-refresh"></i> Upload</a>
                        </li>
						 <li><a href="testlink.php"><i class="notika-icon notika-form"></i> Create Link</a>
                        </li>
						<li class="active"><a  href="sbtview.php"><i class="notika-icon notika-app"></i> Submission</a>
                        </li>
                        <li><a  href="stemail.php"><i class="notika-icon notika-mail"></i> E-mail</a>
                        </li>
                        
                        <li><a  href="http://dwatschools.com.ng"><i class="notika-icon notika-house"></i> Home</a>
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
                          <li ><a  href="timeline.php"><i class="notika-icon notika-windows"></i> My Timeline</a>
                        </li>
						<li><a  href="upload.php"><i class="notika-icon notika-refresh"></i> Upload</a>
                        </li>
						 <li><a href="testlink.php"><i class="notika-icon notika-form"></i> Create Link</a>
                        </li>
						<li class="active"><a  href="sbtview.php"><i class="notika-icon notika-app"></i> Submission</a>
                        </li>
                        <li><a  href="stemail.php"><i class="notika-icon notika-mail"></i> E-mail</a>
                        </li>
                        
                        <li><a  href="http://dwatschools.com.ng"><i class="notika-icon notika-house"></i> Home</a>
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
										<i class="notika-icon notika-app"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Welcome

<?php
$lname = $_SESSION['unamed'];

$servername = "localhost";
$username = "dwatsch1_root";
$password = "UNYOpat2017";
$dbname = "dwatsch1_lhp";


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
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
?>												</h2>
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
                            <h2>Schedule Class Activities</h2>
                            <p>Use the form below to create a class schedule for students.</p>
						<h2>	 <?php
							
    if (isset($_SESSION['grades']) && $_SESSION['grades'])
    {
      printf('<b>%s</b>', $_SESSION['grades']);
      unset($_SESSION['grades']);
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
						<form method="POST" action="grade.php" class="form-element-area" id="fupload" enctype="multipart/form-data">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" hidden="yes">
                                <div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text"   class="form-control" name ="stname" placeholder="Full Name" value= "<?php
							echo $_SESSION['unamed'];   // now, print the Session variable ?>">
                                    
									</div>
                                </div>
                            </div>
						
					
                           
                          
                           
						    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Enter Submission Reference Number</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" required="yes" maxlength="6" name ="ref" placeholder="Enter Submission Reference Number" required="yes">
                                    </div>
                                </div>
                            </div>
							
							 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Enter Assignment Score</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="number" class="form-control" required="yes" maxlength="3" min="1" max="100" name ="score" placeholder="Enter Assignment Score" required="yes">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-4 col-sm-4 col-xs-12">
                                <label>Assignment Review </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text area" class="form-control"  name ="review" placeholder="Feedback regards Assignment" required="yes">
                                    </div>
                                </div>
                            </div>
						   
                            
							<br>
							<br>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                
								<div class="form-group ic-cmp-int">
                                    
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="submitsc" value="Grade Assignment"/> 
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
                            <h2>View Submitted Assignments
							
						</h2>
                            <p>This table contains all assignments submitted by your students.</p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                         <th>S/N</th>
										 <th>Ref Num</th>
										<th>Name </th>
										<th>Term </th>
										<th>Week </th>
										<th> Subject</th>
										<th> View Assignment</th>
										<th> Grade</th>
										<th> Review</th>
										
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
			$lname = $_SESSION['unamed'];
			
			include_once './conn.php';
				
            $count=1;
            $query=$conn->prepare("select * from lhpsubmit WHERE `sbteach` = '".$lname."' ORDER BY sbdate DESC ");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
            ?>
            <tr>
				<td><?php echo $count++ ?></td>
				<td><?php echo $row->sref ?></td>
                <td><?php echo $row->stdname ?></td>
                <td><?php echo $row->sbterm ?></td>
				<td><?php echo $row->sbwk ?></td>
				<td><?php echo $row->sbsbj ?></td>
				
				<td><a href="submission/<?php echo $row->sbfile ?>">Click to View </a> </td>
				<td><?php echo $row->sbgrade ?></td>
				<td><?php echo $row->sbremarks ?></td>
                
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                         <th>S/N</th>
										<th>Ref Num</th>
										<th>Name </th>
										<th>Term </th>
										<th>Week </th>
										<th> Subject</th>
										<th> View Assignment</th>
										<th> Grade</th>
										<th> Review</th>
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
    <div class="footer-copyright-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="footer-copy-right">
                        <p><strong>DWAT Schools - Powered by <a href="https://schelps.com.ng">SCHELPS</a></strong></p>
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
</body>

</html>