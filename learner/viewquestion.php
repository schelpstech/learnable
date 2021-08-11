<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['studnamed'])){
     header('Location: ../index.php');
}

?>
<?php

$topd = $_SESSION['worktopic'];
$notebook = $_SESSION['notebook'];
$viewid = $_SESSION['viewid'];

require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpscheme where schmid ='$topd'";
$book = $db_handle->runQuery($query);

?>

<?php
$lname = $_SESSION['studnamed'];
$viewsubject = $_SESSION['subject'];


$sql = "SELECT * FROM `lhpuser` WHERE `uname` = '$lname'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      $stname = $row["fname"];  
      $cclass = $row["classid"];
      $_SESSION['cclass'] = $cclass;
      $pix = $row["picture"];
	
  }
} 

$sql = "SELECT * FROM `lpterm` WHERE `status` = 1";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      $term = $row["term"];
	
  }
} 

?>			

	
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>View Assessment Questions for <?php echo  $_SESSION['subjected']." in ". $_SESSION['dclass'];?> - LearnAble</title>
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
		
		<script type="text/javascript">
function changeFunc() {
var typed = document.getElementById("typed");
var selectedValue = typed.options[typed.selectedIndex].value;
if (selectedValue=="text"){
$('#ldiv').show();
$('#ddiv').hide();
$('#subbtn').show();
$('#ldiv').attr('required', '');
$('#ldiv').attr('data-error', ' You need to type in your answers.');
}

else if (selectedValue=="file"){
$('#ddiv').show();
$('#ldiv').hide();
$('#subbtn').show();
$('#ddiv').attr('required', '');
$('#ddiv').attr('data-error', 'You need to select a document containing your answers.');
}
else {
alert("Select an assignment submission Type");
$('#ldiv').hide();
$('#ddiv').hide();
$('#subbtn').hide();
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
                      
					
                </div>
			</div>
	
	
	
    <div class="inbox-area" id="note">
        <div class="container">
            <div class="row">
            
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="view-mail-list sm-res-mg-t-30">
                        <div class="view-mail-hd">
                            <div class="view-mail-hrd">
                                <h2>Assessment Question</h2>
                            </div>
                            <div class="view-ml-rl">
                                <p>Posted on :  

<?php echo $_SESSION['rectime']; ?>

	</p>
                            </div>
                        </div>
                        <div class="mail-ads mail-vw-ph">
                                    <p class="first-ph"><b>Class: </b>
<?php
echo $_SESSION['dclass'];
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
                            <p class="first-ph"><b>Subject: </b>
<?php
echo $_SESSION['subjected'];
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
		<p class="first-ph"><b>Mark Obtainable: </b>
<?php
echo $_SESSION['grade'];
    ?> Marks
	</p>
	
	<p class="first-ph"><b>Submission Deadline: </b>
<?php
echo $_SESSION['deadline'];
    ?>
	</p>
	          <p class="first-ph"><b>Instructor: </b>
               <?php
foreach ($book as $boo) {
    ?>
<?php $staff = $boo["staffid"]; ?>
<?php
}
?>
               <?php

$sql = "SELECT `staffname` FROM `lhpstaff` WHERE `sname` = '".$staff."'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
	echo $row["staffname"];
  }
} else {
  echo ".$lname.";
}
?>
	</p>
	
                            
                            
                        </div>
                        <div class="view-mail-atn">
                           <br>
                           <p><b> Question: </b></p><br><br>
                            <?php echo $notebook; ?><br>
                            
	
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
    
    
   <?php include "feedbk.php"; ?>
   

     <script>
     CKEDITOR.replace('editor2', {
      uiColor: '#CCEAEE',
      extraPlugins: 'editorplaceholder',
      editorplaceholder: 'Type Your Answer Here...'
    });
  </script>
    <!-- Start Footer area-->
   <?php include "foot.php"; ?>
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