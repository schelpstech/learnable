<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['studnamed'])){
    header('Location: ../index.php');
 }
if(!empty($_GET['term'])) {
  $term = $_GET["term"]; 
  $_SESSION['term']  = $term; 
}
$lname = $_SESSION['studnamed'];
?>


<?php



$sql = "SELECT * FROM `lhpuser` WHERE `uname` = '$lname'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      $stname = $row["fname"];
      $gender = $row["gender"]; 
      $dob = $row["dob"];

      $cclass = $row["classid"];
      $pix = $row["picture"];
	
  }
} 


$sql = "SELECT classid FROM lhpresultrecord WHERE lid = '$lname' AND term = '$term'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      
      
      $cclass = $row["classid"];
    
	
  }
}
?>			


<?php
$sql = "SELECT classname FROM `lhpclass` WHERE `classid` = '$cclass'";;
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      $dclass = $row["classname"];
	
  }
}

// Class Population
$sql = "SELECT COUNT( DISTINCT lid) AS pop FROM lhpweekrecord WHERE `classid` = '$cclass' AND term = '$term'";
               $result=mysqli_query($con,$sql);
                $row=mysqli_fetch_array($result);
                     
                      $pop = $row["pop"];
                      
//Result Configuration
$sql = "SELECT * FROM lhpresultconfig WHERE term = '$term' ";
$result=mysqli_query($con,$sql);
 $row=mysqli_fetch_array($result);
      
       $resumedate = $row["resumption"];
       $opendays = $row["sch_open"];
       $sign = $row["signature"];

       //School Information
$sql = "SELECT * FROM lhpschool ";
$result=mysqli_query($con,$sql);
 $row=mysqli_fetch_array($result);
      
       $schname = $row["schname"];
       $schmotto = $row["motto"];
       $schyear = $row["founded"];
       $schphone = $row["phone"];
       $schemail = $row["email"];
       $schweb = $row["website"];
       $schaddress = $row["address"];
       $schlogo = $row["logo"];
       $schowner = $row["proprietor"];

 //Get Class Teacher's name
$sql = "SELECT * FROM `lhpclassalloc` WHERE term = '$term' and classid = '$cclass'";
$result=mysqli_query($con,$sql);
$row=mysqli_fetch_array($result);
        $tutor = $row["tutorid"];
 
        $sql = "SELECT * FROM `lhpstaff` WHERE  sname = '$tutor'";
   $result=mysqli_query($con,$sql);
        $row=mysqli_fetch_array($result);
                $tutorname = $row["staffname"];
?>




	
<!doctype html>
<html>

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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.0/chart.min.js" integrity="sha512-asxKqQghC1oBShyhiBwA+YgotaSYKxGP1rcSYTDrB0U6DxwlJjU59B67U8+5/++uFjcuVM8Hh5cokLjZlhm3Vg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="./chartload.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.2.0/jspdf.umd.min.js"></script>
  
    <script  >
 
      function generatePDF() {
       
       
     var divContents = $("#doc").html();
            var printWindow = window.open('', '', 'height=800,width=1600');
            
            printWindow.document.write('<html><head><title>Academic Reportsheets for <?php echo $stname."   ".$dclass?></title>');
            printWindow.document.write('</head><body >');
            printWindow.document.write(divContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
          
      }
    </script>

  
          
     
    <script src="https://d3js.org/d3.v5.min.js"></script>

    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
	
		<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>

<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.33/vfs_fonts.js"></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.4/angular.min.js"></script>
<script src="./script.js"></script>
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
<div id="doc" >
  

    <!-- Data Table area Start-->
    <div  class="data-table-area" style ="text-align: center;">
        <div class="container">
            <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="normal-table-list mg-t-30">
                      
                        <div class="bsc-tbl-bdr">
                        <table class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                    
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				

           
            <tr>
            <td><image  src="../admin/images/<?php echo $schlogo; ?>" width="150" height="150"/><br>
                  <strong>Founded: <?php echo $schyear; ?></strong></td>     
				<td>
        
              <h1 style ="text-align: center;"> <?php echo $schname; ?> </h1>
              <p style ="text-align: center;"> <?php echo $schmotto; ?> <br>
               <?php echo $schaddress; ?> <br>
               <?php echo $schphone; ?> |  <?php echo $schemail; ?> <br> <?php echo $schweb ?> </p>
              <h4 style ="text-align: center;"> <?php echo $term." "?> <br>Mid - Term Academic Reportsheets for <?php echo $dclass?></h4>
            </td>
            <td><image  src="./images/profilepix/<?php echo $pix?>" width="150" height="150"/><br>
                  <strong>Learners ID : <?php echo $lname; ?></strong></td> 
            </tr>
           
            </tbody>
                                   
                                </tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><br>
    
				
    <div class="data-table-area" style ="text-align: center;">
        <div class="container">
            <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="normal-table-list mg-t-30">
                        <div class="basic-tb-hd">
 <strong><h3 style ="text-align: center;">Learners Details</h3></strong>
                            
                        </div>
                
                        <div class="bsc-tbl-bdr">
                        <table   class="table table-bordered" style="width:100%;"border="1">
                                <thead>
                                    <tr>
                                    
                                        <th style ="text-align: center;">Fullname</th>
										<th style ="text-align: center;"> Gender</th>
                      <th style ="text-align: center;">Date of Birth</th>
                        <th style ="text-align: center;">Current Class </th>
						<th style ="text-align: center;"> Class Teacher</th>
						 <th style ="text-align: center;"> Class Population</th>
				
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				

           
            <tr>
            
	<td><strong><h4 style ="text-align: center;">  <?php echo $stname ?></h4></strong></td>
	<td><strong><p style ="text-align: center;"><?php echo $gender ?></p></strong></td>      
				<td><strong><p style ="text-align: center;"><?php echo $dob ?></p></strong></td>
				<td><strong><p style ="text-align: center;"><?php echo $dclass; ?></p></strong></td>
				<td><strong><p style ="text-align: center;"><?php echo $tutorname; ?></p></strong></td>
				<td><strong><p style ="text-align: center;"><?php echo $pop; ?></p></strong></td>
				   </tr>
           
            </tbody>
                                   
                                </tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Data Table area Start-->
    <div id="doc" class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="normal-table-list mg-t-30">
                        <div class="basic-tb-hd">
 <strong><h3 style ="text-align: center;">Academic Performance Report</h3></strong>
                            
                        </div>
                
                        <div class="bsc-tbl-bdr">
<table class="table table-bordered" style="width:100%" border="1">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
									                    	<th> Week 1</th>
									                    	<th> Week 2</th>
									                    	<th> Week 3</th>
									                    	<th> Week 4</th>
									                    	<th> Week 5</th>
									                    	<th> Week 6</th>
									                    	<th> Total</th>
									                    	<th> Grade</th>
									                    	<th> Remarks</th>
									
										
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
                                     <?php
		
    include_once './conn.php';

          $count=1;
          $query=$conn->prepare("SELECT  DISTINCT subjid from lhpweekrecord where lid = '$lname' AND term = '$term'");
         $query->setFetchMode(PDO::FETCH_OBJ);
         $query->execute();
          while($row=$query->fetch())
          {
              
              
          ?>
	            <?php
                
                $sbjref =  $row->subjid;

$sql = "SELECT sbjname from lhpsubject where sbjid = '$sbjref'" ;
 $result=mysqli_query($con,$sql);
 $row=mysqli_fetch_assoc($result);
  // output data of each row
 $sbjname = $row["sbjname"];

 $sql = "SELECT score  from lhpweekrecord where subjid = '$sbjref' and lhpweekrecord.lid = '$lname' AND lhpweekrecord.term = '$term' AND week = 'Week 1'" ;
 $result=mysqli_query($con,$sql);
 $row=mysqli_fetch_assoc($result);
  // output data of each row
  if(!empty($row["score"])){
    $weeka = $row["score"];
  }
  else{
    $weeka = 0;
  }
 

 $sql = "SELECT score  from lhpweekrecord where subjid = '$sbjref' and lhpweekrecord.lid = '$lname' AND lhpweekrecord.term = '$term' AND week = 'Week 2'" ;
 $result=mysqli_query($con,$sql);
 $row=mysqli_fetch_assoc($result);
  // output data of each row
  if(!empty($row["score"])){
    $weekb = $row["score"];
  }
  else{
    $weekb = 0;
  }

 $sql = "SELECT score  from lhpweekrecord where subjid = '$sbjref' and lhpweekrecord.lid = '$lname' AND lhpweekrecord.term = '$term' AND week = 'Week 3'" ;
 $result=mysqli_query($con,$sql);
 $row=mysqli_fetch_assoc($result);
  // output data of each row
  if(!empty($row["score"])){
    $weekc = $row["score"];
  }
  else{
    $weekc = 0;
  }

 $sql = "SELECT score  from lhpweekrecord where subjid = '$sbjref' and lhpweekrecord.lid = '$lname' AND lhpweekrecord.term = '$term' AND week = 'Week 4'" ;
 $result=mysqli_query($con,$sql);
 $row=mysqli_fetch_assoc($result);
  // output data of each row
  if(!empty($row["score"])){
    $weekd = $row["score"];
  }
  else{
    $weekd = 0;
  }

  $sql = "SELECT score  from lhpweekrecord where subjid = '$sbjref' and lhpweekrecord.lid = '$lname' AND lhpweekrecord.term = '$term' AND week = 'Week 5'" ;
 $result=mysqli_query($con,$sql);
 $row=mysqli_fetch_assoc($result);
  // output data of each row
  if(!empty($row["score"])){
    $weeke = $row["score"];
  }
  else{
    $weeke = 0;
  }


  $sql = "SELECT score  from lhpweekrecord where subjid = '$sbjref' and lhpweekrecord.lid = '$lname' AND lhpweekrecord.term = '$term' AND week = 'Week 6'" ;
 $result=mysqli_query($con,$sql);
 $row=mysqli_fetch_assoc($result);
  // output data of each row
  if(!empty($row["score"])){
    $weekf = $row["score"];
  }
  else{
    $weekf = 0;
  }

  $total = $weeka + $weekb + $weekc + $weekd + $weeke + $weekf ;
  $totalscore = $total/2;
  if($totalscore >= 22.5){
    $grade = "A";
}
elseif($totalscore >= 19.5){
    $grade = "B";
}
 elseif($totalscore >= 15){
    $grade = "C";
}
 elseif($totalscore >= 13.5){
    $grade = "D";
}
 elseif($totalscore >= 12){
    $grade = "E";
}
 else{
    $grade = "F";
}

    if($totalscore >= 22.5){
    $remarks = "Excellent";
}
elseif($totalscore >= 19.5){
    $remarks = "Very Good";
}
 elseif($totalscore >= 15){
    $remarks = "Moderate";
}
 elseif($totalscore >= 13.5){
    $remarks = "Fair";
}
 elseif($totalscore >= 12){
    $remarks = "Needs Help";
}
 else{
    $remarks = "Needs Help";
}
         echo'<tr>
                <td><strong><p style ="text-align: left;">'.strtoupper($sbjname).'</p></strong></td>
                <td><strong><p style ="text-align: center;">'.strtoupper($weeka).'</p></strong></td>
                <td><strong><p style ="text-align: center;">'.strtoupper($weekb).'</p></strong></td>
                <td><strong><p style ="text-align: center;">'.strtoupper($weekc).'</p></strong></td>
                <td><strong><p style ="text-align: center;">'.strtoupper($weekd).'</p></strong></td>
                <td><strong><p style ="text-align: center;">'.strtoupper($weeke).'</p></strong></td>
                <td><strong><p style ="text-align: center;">'.strtoupper($weekf).'</p></strong></td>
                <td><strong><p style ="text-align: center;">'.strtoupper($totalscore).'</p></strong></td>
                <td><strong><p style ="text-align: center;">'.strtoupper($grade).'</p></strong></td>
                <td><strong><p style ="text-align: center;">'.strtoupper($remarks).'</p></strong></td>
                </tr>';
                   
            ?>
                        
            
            <?php }?>
            </tbody>		
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="breadcomb-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							
								    
									<div class="breadcomb-icon">
                  <h4 style ="text-align: left;"> Authorised by </h4>
                  <image  src="../admin/archive/<?php echo $sign; ?>"  height="100" width="100"/>
                  <h3 style ="text-align: left;"> <?php echo $schowner; ?> </h3>
                 <br>
                 <br> 
                  <strong> <small style ="text-align: center;"> Grade : Mark Obtainable - 30 **** 0 - 11.9 :: Need Help (F) **** 12 - 13.4 :: Needs Help (E) **** 13.5 - 14.9 :: Fair (D) **** 15 - 19.4 :: Moderate (C) **** 19.5 - 22.4 :: Very Good (B) **** 22.5 - 30 :: Excellent (A)</small> </strong>
                </div>
									
								</div>
							</div>
						
						</div>
					</div>
				</div>
			</div>
   
    </div>
		
  
    
   
    <button id="cmd" onclick="generatePDF()" class="btn btn-default btn-icon-notika"><i class="notika-icon notika-down-arrow"></i><h3>Download Results</h3> </button>
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
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
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
    <script src="js/charts/Chart.js"></script>
<script src="js/charts/bar-chart.js"></script>
    <script src="js/main.js"></script>
  

</body>

</html>