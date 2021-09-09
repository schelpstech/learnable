<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['unamed'])){
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
$query = "SELECT DISTINCT term FROM lhpfeelist";
$feeresult = $db_handle->runQuery($query);
?>

<?php
require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lpterm where status = 1";
$termd = $db_handle->runQuery($query);
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Payment Records - LearnAble</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="images/icon.jpg">
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
function getstudent() {
        var str='';
        var val=document.getElementById('classtn');
        for (i=0;i< val.length;i++) { 
            if(val[i].selected){
                str += val[i].value + ','; 
            }
        }         
        var str=str.slice(0,str.length -1);
        
	$.ajax({          
        	type: "GET",
        	url: "get_fee.php",
        	data:'classid='+str,
        	success: function(data){
        		$("#std-list").html(data);
        	}
	});
}
</script>

    <script>
function getamount() {
        var str='';
        var val=document.getElementById('std-list');
        for (i=0;i< val.length;i++) { 
            if(val[i].selected){
                str += val[i].value + ','; 
            }
        }         
        var str=str.slice(0,str.length -1);
        
	$.ajax({          
        	type: "GET",
        	url: "getamt.php",
        	data:'stdid='+str,
        	success: function(data){
        		$("#amount").html(data);
        	}
	});
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
										<i class="notika-icon notika-support"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Welcome Admin</h2>
			<h2>	 <?php
							
    if (isset($_SESSION['feemessage']) && $_SESSION['feemessage'])
    {
      printf('<b>%s</b>', $_SESSION['feemessage']);
      unset($_SESSION['feemessage']);
    }
  ?></h2>
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
	

 <div class="form-element-area">
        <div class="container">
            <div class="row">
                
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                        <div class="basic-tb-hd">
                            <h2>Payment Records</h2>
                            <p>  You can record payments of cash received, bank transfer or deposit on this page. </p>
			
                        </div>
					</div>
				</div>
                  </div>      
                        <br>
                        <br>
                        <br>
						<div class="row">
						<form method="POST" action="recordpayment.php" class="form-element-area" id="fupload" enctype="multipart/form-data">
                         
							 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" >
                                <label>Current Term</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="term" required="yes">
										<?php
foreach ($termd as $tm) {
    ?>
<option value="<?php echo $tm["term"]; ?>"><?php echo $tm["term"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
							   
                            	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" >
                                <label>Select Class</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="classname" id="classtn"  onChange="getstudent();" required="yes">
										<option value="">Select Class</option>
								
										<?php
foreach ($classresult as $classd) {
    ?>
<option value="<?php echo $classd["classid"]; ?>"><?php echo $classd["classname"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
							
                          
							
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Select Learner</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="learner" id="std-list"  onChange="getamount();" required="yes">
											<option value="">Select Learner</option>
										</select>
                                    </div>
                                </div>
                            </div>
							
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Mode of Payment</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="mode" required="yes">
											<option value="">Select Mode of Payment</option>
											<option value="transfer">Bank Transfer</option>
											<option value="deposit">Bank Deposit</option>
											<option value="epayment">E-Payment</option>
										<option value="cash">Cash Payment</option>
										</select>
                                    </div>
                                </div>
                            </div>
                            
                            	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Transaction Reference Number </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
                                    <div class="nk-int-st">
                                         <input type="text" required="yes" class="form-control" name="payref">
								
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Payment Date </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
                                    <div class="nk-int-st">
                                         <input type="date" required="yes" class="form-control" name="paydate" max="<?php echo date("Y-m-d")?>">
								
                                    </div>
                                </div>
                            </div>
                            
                            	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Payment Status </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="status">
											<option value="">Select Current Payment Status</option>
											<option value="1">Payment Value Successfully Received</option>
											<option value="2">Payment Value is Pending</option>
										</select>
                                    </div>
                                </div>
                            </div>
							
				
                            
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Total Outstanding Payment </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="paid" id="amount">
											<option value="">Select Student to show current balance</option>
										</select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Amount Paid </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
                                    <div class="nk-int-st">
                                         <input type="number" required="yes" class="form-control" name="amountpaid" min= "100">
								
                                    </div>
                                </div>
                            </div>
					
							
							<br>
							<br>
							<div class="col-lg-12 col-md-4 col-sm-4 col-xs-12">
                                
								<div class="form-group ic-cmp-int">
                                    
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="paybill" value="  Record Payment for  Selected Learner  "/> 
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
                            <h2>School Fees Payment History 
							
						</h2>
                            <p>Here is a list of all school fees made by learners </p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                          <th>S/N</th>
										  <th>Payment Date</th>
										  <th>Term</th>
										<th>Class</th>
										<th>Full name</th>
										<th> Amount Paid</th>
										<th> Payment Mode</th>
									    <th>Transaction Reference </th>
										<th> Status</th>
										
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
			
			
			include_once './conn.php';
				
            $count=1;
            $query=$conn->prepare("select * from lhptransaction ORDER BY status ASC");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
                $tid =  $row->transid;
                $dated =  $row->paydate;
                $term =  $row->term;
                $classid =  $row->classid;
                $stdid =  $row->stdid;
                $amount =  $row->amount;
                $ref =  $row->reference;
                 $mode =  $row->mode;
                $status =  $row->status;
                
                $sql = "SELECT * FROM lhpclass WHERE classid  = '$classid'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               if ($row['classname'] != ""){
               $feeclass = $row['classname'];
               }
               else {
                 $feeclass = $classid;  
                   
               }
               
               $sql = "SELECT * FROM lhpuser WHERE uname  = '$stdid'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $std = $row['fname'];
                
              
                
                 if ($status == 1){
               $feestatus = '<a href="#" type="button"  class="btn btn-success" >Successfully Confirmed</a>';
               }
               elseif ($status == 2){
                $feestatus = '<a href="#" type="button"  class="btn btn-warning" >Pending Confirmation</a>';  
                   
               }
               elseif ($status == 0){
                $feestatus = '<a href="#" type="button"  class="btn btn-danger" >Unsuccessful Transaction</a>';  
                   
               }
                
            ?>
            <tr><strong>
                <td><?php echo $count++ ?></td>
				<td><?php echo $dated?></td>
				<td><?php echo $term?></td>
				<td><?php echo $feeclass ?></td>
				<td><?php echo $std ?></td>
				<td><?php echo $amount ?></td>
				<td><?php echo $mode ?></td>
				<td><?php echo $ref ?></td>
				<td><?php echo $feestatus ?></td>
				
                </strong>
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>S/N</th>
										<th>Payment Date</th>
										<th>Term</th>
										<th>Class</th>
										<th>Full name</th>
										<th> Amount Paid</th>
										<th> Payment Mode</th>
									    <th>Transaction Reference </th>
										<th> Status</th>
										
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
  <?php include "foot.html"; ?>
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
    <script src="js/tawk-chat.js"></script>
</body>

</html>