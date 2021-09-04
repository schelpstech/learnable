<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['studnamed'])){
     header('Location: ../index.php');
     
}

if(!empty($_GET['ref'])) {
    $payid = $_GET["ref"]; 
}

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
      $email = $row["email"];
      $cclass = $row["classid"];
      $pix = $row["picture"];
      $numb = $row["numb"];
  }
} 

$sql = "SELECT classname FROM `lhpclass` WHERE `classid` = '$cclass'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    $dclass = $row["classname"];
     
  }
}



$sql = "SELECT * FROM `lhptransaction` WHERE `transid` = '$payid'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
    $term = $row["term"];
    $receipt = $row["transid"];
    $refd = $row["reference"];
    $mode = $row["mode"];
    $paydate = $row["paydate"];
    $amount = $row["amount"];
    $classref = $row["classid"];
  }
}

$sql = "SELECT classname FROM `lhpclass` WHERE `classid` = '$classref'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    $refclass = $row["classname"];
}
}

mysqli_close($conn);


   //School Information
   $sql = "SELECT * FROM lhpschool ";
   $result=mysqli_query($con,$sql);
    $row=mysqli_fetch_array($result);
         
          $schname = $row["schname"];
          $schphone = $row["phone"];
          $schemail = $row["email"];
          $schaddress = $row["address"];
          $schowner = $row["proprietor"];
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
}s
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


	
            <div class="invoice-area">
<div class="container">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="invoice-wrap">
<div class="invoice-img">
<h1 style="color:white;"> Payment Receipt for <?php echo $stname ?></h1>
</div>
<div class="invoice-hds-pro">
<div class="row">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="invoice-cmp-ds ivc-frm">
<div class="invoice-frm">
<span>Receipt issued by</span>
</div>
<div class="comp-tl">
<h2><?php echo $schname; ?></h2>
<p><?php echo $schaddress; ?></p>
</div>
<div class="cmp-ph-em">
<span><?php echo $schphone; ?></span>
<span><?php echo $schemail; ?></span>
</div>
</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="invoice-cmp-ds ivc-to">
<div class="invoice-frm">
<span>Receipt issued to</span>
</div>
<div class="comp-tl">
<h2><?php echo $lname. ' - '.$stname; ?></h2>
<p><?php echo $dclass; ?></p>
</div>
<div class="cmp-ph-em">
<span><?php echo $numb; ?></span>
<span><?php echo $email; ?></span>
</div>
 </div>
</div>
</div>
</div>
<div class="row">
<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
<div class="invoice-hs">
<span>Receipt #</span>
<h2>#<?php echo $receipt; ?></h2>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
<div class="invoice-hs date-inv sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
<span>Date</span>
<h2><?php echo $paydate; ?></h2>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
<div class="invoice-hs wt-inv sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
<span>Class</span>
<h2><?php echo $refclass; ?></h2>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
<div class="invoice-hs gdt-inv sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
<span>Term</span>
<h2><?php echo $term; ?></h2>
</div>
</div>
</div>
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="invoice-sp">
<table class="table table-hover">
<thead>
<tr>
<th>#</th>
<th>Transaction ID</th>
<th>Mode of Payment</th>
<th>Amount Paid</th>
<th>Status </th>
</tr>
</thead>
<tbody>
<tr>
<td>1</td>
<td><?php echo $refd; ?></td>
<td><?php echo $mode; ?></td>
<td><?php echo $amount; ?></td>
<td><a href="#" type="button"  class="btn btn-success" >Successful Confirmed</a></td>
</tr>

</tbody>
</table>
</div>
</div>
</div>
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

<div class="invoice-ds-int invoice-last">
<h2>Signature</h2>
<p class="tab-mg-b-0">School Bursar </p>
<p class="tab-mg-b-0"><?php echo $schname; ?> </p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
   
 
      
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