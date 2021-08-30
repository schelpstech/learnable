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
$query = "SELECT * FROM lpterm where status = 1";
$termed = $db_handle->runQuery($query);
foreach ($termed as $td) {
    $term = $td["term"]; 
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
      
      $cclass = $row["classid"];
      $pix = $row["picture"];
	
  }
} 

mysqli_close($conn);
?>			

 <?php
 $lname = $_SESSION['studnamed']; 
 
 $sql = "SELECT SUM(amount) AS billed FROM `lhpassignedfee` WHERE stdid = '$lname' AND term = '$term' AND status = 1";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_assoc($result);
                       $bill = $row["billed"];
                       
                       
$sql = "SELECT SUM(amount) AS pay FROM `lhptransaction` WHERE stdid = '$lname' AND term = '$term' AND status = 1";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_assoc($result);
                       $paid = $row["pay"];
                       
 $sql = "SELECT SUM(amount) AS outs FROM `lhpassignedfee` WHERE stdid = '$lname' AND term != '$term' AND status = 1";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_assoc($result);
                       $out = $row["outs"];

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
							<div class="col-lg-9 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
								    
									<div class="breadcomb-icon">
										<i class="notika-icon notika-windows"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2> School Fees Payment and Records for <?php

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
?>

								</h2>
										<p><span class="bread-ntd"></span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-sm-6 col-xs-3">
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
<div class="breadcomb-area">
        <div class="notika-status-area">
        <div class="container">
            <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2>&#8358;<span class="counter"> <?php echo $bill; ?> </span></h2>
                          <h4><strong>Total Amount Billed for <?php echo $term; ?></strong></h4>
                        </div>
                        <div class="sparkline-bar-stats1">1,2,3,4,5</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2>-&#8358;<span class="counter">
                                <?php echo $out; ?>
                            </span></h2>
                     <h4><strong>Previous Term Outstanding Payment</strong></h4>
                        </div>
                        <div class="sparkline-bar-stats3">1,2,3,4,5</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2>&#8358;<span class="counter">
                              <?php echo $paid; ?>
                            </span></h2>
                           <h4><strong>Total Amount Paid For <?php echo $term; ?></strong></h4>
                        </div>
                        <div class="sparkline-bar-stats2">1,2,3,4,5</div>
                    </div>
                </div>
            
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2>&#8358;<span class="counter">
                                <?php echo  (($paid - $bill) -$out); ?>
                                 </span></h2>
                        <h4><strong>Total Outstanding Payment</strong></h4>
                        </div>
                        <div class="sparkline-bar-stats4">1,2,3,4,5</div>
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
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                            <h2>Your School Fees Payment History
							
						</h2>
                            <p>This table contains all payments you have made.</p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                          <th>S/N</th>
										<th>Payment Date</th>
										<th> Mode of Payment</th>
									    <th> Amount Paid</th>
									    <th> Payment Status</th>
									    <th> Generate Receipt</th>
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
			$lname = $_SESSION['studnamed'];
			$val = "1";
			include_once './conn.php';
				
            $count=1;
            $query=$conn->prepare("select * from lhptransaction WHERE stdid = '$lname' ORDER BY paydate");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
                $status = $row-> status ;
                $ref = $row-> transid;
                 if ($status == 1){
               $feestatus = '<a href="#" type="button"  class="btn btn-success" >Successful Confirmed</a>';
                $receipt = '<a href="myreceipt.php?ref='.$ref.'" type="button"  class="btn btn-success" >Generate Receipt</a>';
               }
               elseif ($status == 2){
                $feestatus = '<a href="#" type="button"  class="btn btn-warning" >Pending Confirmation</a>';  
                $receipt = '<a href="#" type="button"  class="btn btn-warning" > Receipt Pending</a>';   
               }
               elseif ($status == 0){
                $feestatus = '<a href="#" type="button"  class="btn btn-danger" >Unsuccessful</a>'; 
                $receipt = '<a href="#" type="button"  class="btn btn-danger" >No Receipt</a>';
                   
               }
              
            ?>
          
            <tr>
                <td><?php echo $count++ ?></td>
				<td><?php echo $row-> paydate ?> </td>
				<td> <?php echo strtoupper($row-> mode); ?> </td>
			    <td><strong> <?php echo $row-> amount; ?> </strong></td>
			    <td> <?php echo $feestatus; ?> </td>
			    <td>  <?php echo $receipt; ?> </td>
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
										<th>S/N</th>
										<th>Payment Date</th>
										<th> Mode of Payment</th>
									    <th> Amount Paid</th>
									    <th> Payment Status</th>
									    <th> Generate Receipt</th>
										
                                    </tr>
                                </tfoot>
                            </table>
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