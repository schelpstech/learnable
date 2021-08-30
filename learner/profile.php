<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['studnamed'])){
     header('Location: ../index.php');
}

?>

<?php
require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpclass";
$classresult = $db_handle->runQuery($query);
?>

<?php
require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lpterm";
$termed = $db_handle->runQuery($query);
?>

<?php
$lname = $_SESSION['studnamed'];

include "config.php";

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM `lhpuser` WHERE `uname` = '$lname'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      $stname = $row["fname"];
      $fone = $row["numb"];
      $email = $row["email"];
      $pass = $row["upwd"];
      $cclass = $row["classid"];
      $pix = $row["picture"];
      $dob = $row["dob"];
      $gender = $row["gender"];

  }
} 

mysqli_close($conn);
?>		
<!doctype html>
<html class="no-js" lang="">

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
<script>
function getclass() {
        var str='';
        var val=document.getElementById('class-list');
        for (i=0;i< val.length;i++) { 
            if(val[i].selected){
                str += val[i].value + ','; 
            }
        }         
        var str=str.slice(0,str.length -1);
        
	$.ajax({          
        	type: "GET",
        	url: "get_state.php",
        	data:'class_id='+str,
        	success: function(data){
        		$("#sbj-list").html(data);
        	}
	});
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
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="notika-icon notika-windows"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>  <?php echo $stname; ?>'s Profile

								</h2>
										<p><span class="bread-ntd"></span></p>
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
                              
                            <h2><image src="images/profilepix/<?php echo $pix; ?>" width ="100"/></h2>
                            <h5>Welcome <?php echo $stname; ?></h5>
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
						<form method="POST" action="editprofile.php" class="form-element-area" id="fupload" enctype="multipart/form-data">

                           <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Select Profile Picture</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="file" accept ="image/png, image/jpg, image/jpeg"class="form-control" required="yes"  name ="picx"   >
                                    </div>
                                </div>
                            </div>
                            
                            	<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
								<div class="form-group ic-cmp-int">
                                      
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="profile" value="Change Profile Picture"/> 
                                    </div>
                                </div>
                            </div>
							
                    
				</form>
				
				</div>
                        
                        <div class="row">
						<form method="POST" action="editprofile.php" class="form-element-area" id="fupload" enctype="multipart/form-data">

                            
                         <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Fullname</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" required="yes" maxlength="100" name ="edname" disabled value = "<?php echo $stname; ?>" >
                                    </div>
                                </div>
                            </div>
                           
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Current Class</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" required="yes" maxlength="100"  disabled value =  
                                        "<?php

include "config.php";

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT classname FROM `lhpclass` WHERE `classid` = '$cclass'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      $dclass = $row["classname"];
      
    echo $dclass;
  }
} 

mysqli_close($conn);
?>" >
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Gender</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" required="yes" name ="edgender"  >
                                        <option value = "<?php echo $gender; ?>"><?php echo $gender; ?></option>
                                        <option value = "Male">Male</option>
                                        <option value = "Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Date of Birth</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="date" class="form-control" required="yes" name ="eddob" value = "<?php echo $dob; ?>" >
                                    </div>
                                </div>
                            </div>
                            
                             <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Phone Number</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" required="yes" maxlength="11" name ="edfone" value = "<?php echo $fone; ?>" >
                                    </div>
                                </div>
                            </div>
                            
                             <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Email Address</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" required="yes" maxlength="64" name ="edmail" disabled value = "<?php echo $email; ?>" >
                                    </div>
                                </div>
                            </div>
                            
                             <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Change Log in Password</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-edit"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" required="yes" minlength ="8" maxlength="8" name ="edpwd" value = "<?php echo $pass; ?>" >
                                    </div>
                                </div>
                            </div>
                           
                          
							<br>
							<br>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                
								<div class="form-group ic-cmp-int">
                                    
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="profile" value="Change Log in Password"/> 
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
	
</body>

</html>