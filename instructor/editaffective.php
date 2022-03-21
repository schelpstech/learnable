<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['stnamed'])){
     header('Location: ../index.php');
}
$lname = $_SESSION['stnamed'];

if(!empty($_GET['recordid'])) {
    $recordid = $_GET["recordid"]; 
}
else{
    header('Location: myclass.php');
}
?>
<?php
$sql = " SELECT term from lpterm where `status` = 1";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result);
 $term = $row["term"];
 $_SESSION['term'] =$term;

?>

<?php

$sql = " SELECT * from lhpclassalloc  WHERE `tutorid` = '$lname' and term = '$term'";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result);
 $classid = $row["classid"];
 $_SESSION['classalloc'] =$classid;

?>

<?php

$sql = " SELECT * from lhpaffective  WHERE `affid` = '$recordid'";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result);
 $uname = $row["uname"];
 $total_present = $row["total_present"];
 $rating1 = $row["rating1"];
 $rating2 = $row["rating2"];
 $rating3 = $row["rating3"];
 $rating4 = $row["rating4"];
 $rating5 = $row["rating5"];
 $comment = $row["comment"];
 $sql = " SELECT fname from lhpuser  WHERE `uname` = '$uname'";
 $result = mysqli_query($con,$sql);
 $row = mysqli_fetch_array($result);
  $fname = $row["fname"];
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Affective Domain Ratings - LearnAble</title>
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

     <script>
	function loadtable(){
		
		$.ajax({
			url: 'getaffective.php',
			success: function(data){
				
				$("#cadata").html(data);
			}
		})
	};
</script>

<script>
      function recordaffective(){
                  var termid="<?php echo $term ?>";
                  var classid="<?php echo $classid ?>";
                  var affid="<?php echo $recordid ?>";
                    var learnerid=$("#learnerid").val();
                    var presentid=$("#present").val();
                    var leadr=$("#lead").val();
                    var eloquentr=$("#eloquent").val();
                    var neatr=$("#neat").val();
                    var creater=$("#creative").val();
                    var respond=$("#response").val();
                    var comment=$("#comment").val();
                $.ajax({
                    url:'updateaffective.php',
                    method:'POST',
                    data:{
                        affid:affid,
                        term:termid,
                        classid:classid,
                        learnerid:learnerid,
                        present:presentid,
                        ratingl:leadr,
                        ratinge:eloquentr,
                        ratingn:neatr,
                        ratingc:creater,
                        ratingr:respond,
                        comment:comment
                    },
                   success:function(data){
                       alert(data);
                   }
                });
                $(document).ready(function(){
		
		$.ajax({
			url: 'getaffective.php',
			success: function(data){
				
				$("#cadata").html(data);
			}
		})
	});
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
	
	
	<!-- Breadcomb area End-->
   
    <!-- Data Table area End-->
    <div class="breadcomb-area">
		<div class="container">
			<div class="row">

            </div>
                </div>
			</div>
    <div class="form-element-area">
        <div class="container">
            <div class="row">
                
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                        <div class="basic-tb-hd">
                            <h2>Record Affective Domain and Attendance Records  <br>	 <?php
							
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
											<option value="<?php echo $uname ?>"><?php echo $fname ?></option>
		
										</select>
                                    </div>
                                </div>
                            </div>
							
							<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                                <label>Total Present Days  </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                            <input type="number" required="yes" value="<?php echo $total_present ?>" class="form-control" min="0"  id="present" >
			
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                                <label>Affective Domain - Leadership  </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                    <select type="text" required="yes" class="form-control" id="lead" >
											<option value="<?php echo $rating1 ?>">
                                            <?php 
                                            $rating =$rating1;
                                            if($rating == 5){
                                                echo'Excellent - 5';} 
                                            elseif($rating == 4){
                                                echo'Very Good - 4';} 
                                            elseif($rating == 3){
                                                echo'Moderate - 3';} 
                                            elseif($rating == 2){
                                                echo'Fair - 2';} 
                                            elseif($rating == 1){
                                                echo'Poor - 1';} 
                                            ?>
                                        </option>
                      <option value="5">Excellent - 5</option>
                      <option value="4">Very Good - 4</option>
                      <option value="3">Moderate - 3</option>
                      <option value="2">Fair - 2</option>
                      <option value="1">Poor - 1</option>
                                    </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                                <label>Affective Domain - Eloquence  </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                           
                            <select type="text" required="yes" class="form-control" id="eloquent" >
                            <option value="<?php echo $rating2 ?>">
                                            <?php 
                                            $rating =$rating2;
                                            if($rating == 5){
                                                echo'Excellent - 5';} 
                                            elseif($rating == 4){
                                                echo'Very Good - 4';} 
                                            elseif($rating == 3){
                                                echo'Moderate - 3';} 
                                            elseif($rating == 2){
                                                echo'Fair - 2';} 
                                            elseif($rating == 1){
                                                echo'Poor - 1';} 
                                            ?>
                                        </option>
                      <option value="5">Excellent - 5</option>
                      <option value="4">Very Good - 4</option>
                      <option value="3">Moderate - 3</option>
                      <option value="2">Fair - 2</option>
                      <option value="1">Poor - 1</option>
                                    </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                                <label>Affective Domain - Neatness  </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                    <select type="text" required="yes" class="form-control" id="neat" >
                                    <option value="<?php echo $rating3 ?>">
                                            <?php 
                                            $rating =$rating3;
                                            if($rating == 5){
                                                echo'Excellent - 5';} 
                                            elseif($rating == 4){
                                                echo'Very Good - 4';} 
                                            elseif($rating == 3){
                                                echo'Moderate - 3';} 
                                            elseif($rating == 2){
                                                echo'Fair - 2';} 
                                            elseif($rating == 1){
                                                echo'Poor - 1';} 
                                            ?>
                                        </option>
                      <option value="5">Excellent - 5</option>
                      <option value="4">Very Good - 4</option>
                      <option value="3">Moderate - 3</option>
                      <option value="2">Fair - 2</option>
                      <option value="1">Poor - 1</option>
                                    </select>
			
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                                <label>Affective Domain - Creativity </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                    <select type="text" required="yes" class="form-control" id="creative" >
                                    <option value="<?php echo $rating4 ?>">
                                            <?php 
                                            $rating =$rating4;
                                            if($rating == 5){
                                                echo'Excellent - 5';} 
                                            elseif($rating == 4){
                                                echo'Very Good - 4';} 
                                            elseif($rating == 3){
                                                echo'Moderate - 3';} 
                                            elseif($rating == 2){
                                                echo'Fair - 2';} 
                                            elseif($rating == 1){
                                                echo'Poor - 1';} 
                                            ?>
                                        </option>
                      <option value="5">Excellent - 5</option>
                      <option value="4">Very Good - 4</option>
                      <option value="3">Moderate - 3</option>
                      <option value="2">Fair - 2</option>
                      <option value="1">Poor - 1</option>
                                    </select>
			
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                                <label>Affective Domain - Responsiveness  </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                    <select type="text" required="yes" class="form-control" id="response" >
                                    <option value="<?php echo $rating5 ?>">
                                            <?php 
                                            $rating =$rating5;
                                            if($rating == 5){
                                                echo'Excellent - 5';} 
                                            elseif($rating == 4){
                                                echo'Very Good - 4';} 
                                            elseif($rating == 3){
                                                echo'Moderate - 3';} 
                                            elseif($rating == 2){
                                                echo'Fair - 2';} 
                                            elseif($rating == 1){
                                                echo'Poor - 1';} 
                                            ?>
                                        </option>
                      <option value="5">Excellent - 5</option>
                      <option value="4">Very Good - 4</option>
                      <option value="3">Moderate - 3</option>
                      <option value="2">Fair - 2</option>
                      <option value="1">Poor - 1</option>
                                    </select>
			
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label>Affective Domain - Class Teacher's Comment  </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="cmp-int-box mg-t-20">
                                    
                                        <textarea  class="form-control" name="comment" id ='comment' placeholder="Enter your comment here" rows="3"  style="background-color:white; border: 1px solid #ccc;"> <?php echo $comment ?></textarea>
                                    
                                    </div>
                                </div>
                            </div>

                            
                           
							
					
							<br>
							<br>
							<div class="col-lg-12 col-md-4 col-sm-4 col-xs-12">
                                
								<div class="form-group ic-cmp-int">
                                    
                      <div class="nk-int-st">
                          <button  type="submit" class="form-control" class="btn btn-success" onclick="recordaffective()" > <strong>UPDATE Affective Domain and Attendance Records</strong></button> 
                                    </div>
                                </div>
                            </div>
				
				
				</div>
                </div>
			</div>
            
      <div id="cadata" >
        
        </div>

      
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

</body>

</html>