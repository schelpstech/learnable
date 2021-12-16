<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['stnamed'])){
     header('Location: ../index.php');
}


if(!empty($_GET['sbj'])) {         
        $sbjid = $_GET["sbj"];
        $_SESSION['ssid'] =$sbjid;
}
if(!empty($_GET['term'])) {         
        $term = $_GET["term"];
        $_SESSION['ttid'] =$term;
}
if(!empty($_GET['classid'])) {         
        $classid = $_GET["classid"];
        $_SESSION['ccid'] =$classid;
}



$sql = "SELECT * FROM lhpalloc WHERE sbjid = '$sbjid' AND classid  = '$classid' AND term  = '$term' ";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $subject = $row['subject'];
               $classname = $row["classname"];
               
$sql = "SELECT exam_score FROM lhpresultconfig WHERE term  = '$term' ";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
              
               $max = $row["exam_score"];
               $_SESSION['maxexam'] = $max;
?>

<?php
require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM `lhpuser` where classid = '$classid' ";
$studentlist = $db_handle->runQuery($query);
?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>View Scoresheet for <?php echo $subject. " ". $classname; ?> - LearnAble</title>
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
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
      function generatePDF() {
     var divContents = $("#examdata").html();
            var printWindow = window.open('', '', 'height=400,width=800');
            printWindow.document.write('<html><head><title>Examination Scoresheet</title>');
            printWindow.document.write('</head><body >');
            printWindow.document.write(divContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
          
      }
    </script>
    
    	  <script>
      function recordexamscores (){
               
                var subjectid="<?php echo $sbjid ?>";
                 var classid="<?php echo $classid ?>";
                  var termid="<?php echo $term ?>";
                   var examscore=$("#examscores").val();
                    var learnerid=$("#learnerid").val();
                    if (learnerid != ""){
                    
                    
                $.ajax({
                    url:'submitexam.php',
                    method:'POST',
                    data:{
                        nameid:learnerid,
                        examscore:examscore,
                        subject:subjectid,
                        classid:classid,
                        term:termid
                    },
                   success:function(data){
                       alert(data);
                       $("#examscores").val("");
                      
                   }
                });
                
             	$(document).ready(function(){
		
		$.ajax({
			url: 'getexamscore.php',
			success: function(data){
				
				$("#examdata").html(data);
			}
		})
	});
            }
          
            else{
              alert("Select Learner")
             }
            }
            ;
    </script>
    <script>
	function loadtable(){
		
		$.ajax({
			url: 'getexamscore.php',
			success: function(data){
				
				$("#examdata").html(data);
			}
		})
	};
</script>
    
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
	
		<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script> 
</head>

<body  onload="loadtable()">
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
<?php include ("nav.php"); ?>
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
                            <h2>Record Examination Scores  <br>	 <?php
							
    if (isset($_SESSION['remessage']) && $_SESSION['remessage'])
    {
      printf('<b>%s</b>', $_SESSION['remessage']);
      unset($_SESSION['remessage']);
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
			
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Select Student </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" required="yes" class="form-control" id="learnerid" >
											<option value="">Select Learner Id</option>
										<?php
foreach ($studentlist as $stlist) {
    ?>
<option value="<?php echo $stlist["uname"]; ?>"><?php echo $stlist["uname"]." - ".$stlist["fname"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
							
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Enter Examination Score - Maximum score obtainable =  <?php echo $max ?> </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                            <input type="number" required="yes" class="form-control" min="0"  max = "<?php echo $max?>" id="examscores" >
			
                                    </div>
                                </div>
                            </div>
                            
                           
							
					
							<br>
							<br>
							<div class="col-lg-12 col-md-4 col-sm-4 col-xs-12">
                                
								<div class="form-group ic-cmp-int">
                                    
                      <div class="nk-int-st">
                          <button type="submit" class="form-control" id="saveexam"class="btn btn-success" onclick="recordexamscores()" > <strong>RECORD Examination SCORE</strong></button> 
                                    </div>
                                </div>
                            </div>
				
				
				</div>
                </div>
			</div>	
				
	<!-- Breadcomb area End-->
    <!-- Data Table area Start-->
 
<div id="examdata" >
        
    </div>
    <!-- Data Table area End-->
    <!-- Start Footer area-->
  <?php include ("foot.php"); ?>
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

<!--End of Tawk.to Script-->
</body>

</html>