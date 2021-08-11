<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['stnamed'])){
     header('Location: ../index.php');
}

if(!empty($_GET['fid'])) {         
        $fid = $_GET["fid"];
      
}
if(!empty($_GET['stdid'])) {         
        $stdid = $_GET["stdid"];
      
}
if(!empty($_GET['tid'])) {         
        $tid = $_GET["tid"];
      
}

require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpscheme where schmid ='$tid'";
$book = $db_handle->runQuery($query);

require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpfeedback where fid ='$fid'";
$fdbk = $db_handle->runQuery($query);

?>

<?php
foreach ($fdbk as $fd) {
   $namedd = $fd["stdid"];
  $sql = "SELECT * FROM lhpuser WHERE uname  = '$namedd'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $viewname = $row['fname'];
    ?>

<?php
}
?>



<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>View Submission - Scheme of Work - LearnAble</title>
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
    <script src="https://cdn.ckeditor.com/4.15.0/standard-all/ckeditor.js"></script>
    <script>
function printPage(id)
{
   var html="<html>";
   html+= document.getElementById(id).innerHTML;

   html+="</html>";

   var printWin = window.open('','','left=0,top=0,width=1,height=1,toolbar=0,scrollbars=0,status  =0');
   printWin.document.write(html);
   printWin.document.close();
   printWin.focus();
   printWin.print();
   printWin.close();
}
</script>
    <script>
      function generatePDF() {
        // Choose the element that our invoice is rendered in.
        const element = document.getElementById("note");
        // Choose the element and save the PDF for our user.
        html2pdf()
        .set({ html2canvas: { scale: 4 } })
        .from(element)
        .save();
          
          
      }
    </script>
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
	
		<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
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
										<p>.<span class="bread-ntd"></span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button type="button" class="btn"><i class="notika-icon notika-sent"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
    <div class="inbox-area" id="note">
        <div class="container">
            <div class="row">
            
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="view-mail-list sm-res-mg-t-30">
                        <div class="view-mail-hd">
                            <div class="view-mail-hrd">
                                <h2>View Assessment Submission by <?php echo $viewname; ?></h2>
                            </div>
                            <div class="view-ml-rl">
                                <p>Submitted on : 
          <?php
foreach ($fdbk as $fd) {
    ?>
<?php echo $fd["rectime"]; ?>
<?php
}
?>
	</p>
                            </div>
                        </div>
                        <div class="mail-ads mail-vw-ph">
                            
<p class="first-ph"><b>Submitted by: </b>
<?php echo $viewname; ?>
</p>
<p class="first-ph"><b>Class: </b>
<?php
foreach ($book as $booked) {
   $cname = $booked["classname"];
  $sql = "SELECT * FROM lhpclass WHERE classid  = '$cname'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $viewc = $row['classname'];
    ?>
<?php echo $viewc; ?>
<?php
}
?>
	</p>
	 <p class="first-ph"><b>Subject: </b>
<?php
foreach ($book as $booked) {
    $sbj = $booked["subject"];
  $sql = "SELECT * FROM lhpsubject WHERE sbjid  = '$sbj'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $views = $row['sbjname'];
    ?>
<?php echo $views; ?>
<?php
}
?>
	</p>
	<p class="first-ph"><b>Week : </b>
<?php
foreach ($book as $booked) {
    ?>
<?php echo $booked["week"]; ?>
<?php
}
?>
	</p>

	<p class="first-ph"><b>Topic: </b>
<?php
foreach ($book as $booked) {
    ?>
<?php echo $booked["topic"]; ?>
<?php
}
?>
	</p>
	          
          <?php
foreach ($fdbk as $fd) {
    ?>
<?php $qid = $fd["qid"]; ?>
<?php
}
?>
               <?php

$sql = "SELECT * FROM `lhpquestion` WHERE `questid` = '$qid'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    $grade = $row["grade"];
	$cont = $row["content"];
  }
} 

?>
<p class="first-ph"><b>Mark Obtainable : </b> <?php echo $grade; ?>	</p>
<p class="first-ph"><b>Question : </b> <?php echo $cont; ?>	</p>	
                            
                            
                        </div>
                        
<?php
foreach ($fdbk as $fd) {
$content = $fd["content"]; ?>
<?php
}
?>
   
                        
                         <div class="view-mail-atn">
                       <p class="first-ph"><b>Response : </b></p><br><br>
<?php echo $content; ?>

                          
	
                        </div>
                        
                        <div class="vw-ml-action-ls text-right mg-t-20">
                            <div class="btn-group ib-btn-gp active-hook nk-email-inbox">
                                <button class="btn btn-default btn-sm waves-effect"  onclick="generatePDF()" title="Download PDF"><i class="notika-icon notika-next"></i> Download</button>
                                <button class="btn btn-default btn-sm waves-effect" onclick="printPage('note');"><i class="notika-icon notika-print"></i> Print</button>
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
			 </div>
		</div>
	</div>
   
   <div class="breadcomb-area">
        <div class="container">
            <div class="row">
                
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                         <div class="basic-tb-hd">
                              
                            <h2>Grade Assessment Feedback Submission</h2>
               
                        </div>
					</div>
				</div>
                  </div>      
                        <br>
                        <br>
                        <br>
                        
					 
                        <div class="row">
						<form method="POST" action="gradefeedback.php" class="form-element-area" id="fupload" enctype="multipart/form-data">
						    
						    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" hidden >
                                 <label> feedbackid </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="fid" value="<?php echo $fid; ?>" required="yes">
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" hidden >
                                 <label> questionid </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="qid" value="<?php echo $qid; ?>" required="yes">
                                    </div>
                                    
                                </div>
                            </div>
						    <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Mark Obtained </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="number" class="form-control" name="graded" placeholder="Enter scores here" min="1" max="<?php echo intval($grade); ?>" required="yes">
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
                                 <label> Instructor's Feedback / Correction </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
                                    
                              
									 <div class="cmp-int-box mg-t-20">
                                    <textarea class="form-control" name="response"  placeholder="Enter your note here" rows="10" id="editor2"  style="background-color:white; border: 1px solid #ccc;"></textarea>
                                    
                                    </div>
                                </div>
                            </div>
                            
                             
                       
                       
                           
                          
							<br>
							<br>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                
								<div class="form-group ic-cmp-int">
                                    
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="grade" value="Grade Assessment Submission"/> 
                                    </div>
                                </div>
                            </div>
							
                    
				</form>
				
				<
			</div>	
			
		</div>
	</div>
   <script>
     CKEDITOR.replace('editor2', {
      uiColor: '#CCEAEE',
      extraPlugins: 'editorplaceholder',
      editorplaceholder: 'Type Your Answer Here...'
    });
  </script>
    <!-- Start Footer area-->
   
   <?php include ("foot.php"); ?>
   
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

<!--End of Tawk.to Script-->
</body>

</html>