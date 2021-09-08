<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['unamed'])){
   header('Location: ../index.php');
}


$sql = "SELECT term from lpterm where `status` = 1";
$result=mysqli_query($con,$sql);
$row=mysqli_fetch_array($result);
$term = "$row[term]";
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Payment Dashboard - Learn at Home</title>
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
										<i class="notika-icon notika-support"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Welcome Admin</h2>
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
	 <!-- Start Status area -->
    <div class="notika-status-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2>&#8358;<span class="counter">
                                <?php
                                
                              	include_once './conf.php';
                                  $sql = " SELECT Sum(`amount`) as total_bill FROM `lhpassignedfee` where `status` = 1 and term = '$term'";
                                  $result=mysqli_query($con,$sql);
                                 $row=mysqli_fetch_array($result);
                                 echo "$row[total_bill]";
                             
                                ?>
                                </span></h2>
                         <a href="#">   <h4><strong>Expected Income</strong></h4></a>
                        </div>
                        <div class="sparkline-bar-stats1">1,2,3,4,5</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2>&#8358;<span class="counter">
                             <?php
                                
                              	include_once './conf.php';
                          $sql = " SELECT Sum(amount)  as total_payment FROM `lhptransaction` where `status` = 1  and term = '$term'";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        echo "$row[total_payment]";
                             
                                ?>
                            </span></h2>
                         <a href="#">   <h4><strong>Actual Income</strong></h4></a>
                        </div>
                        <div class="sparkline-bar-stats2">1,2,3,4,5</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter">
                                
                                 <?php
                                
                                $sql = " SELECT COUNT(DISTINCT stdid)  as num_bill FROM `lhpassignedfee` where `status` = 1  and term = '$term'";
                                $result=mysqli_query($con,$sql);
                               $row=mysqli_fetch_array($result);
                               echo "$row[num_bill]";
                             
                                ?>
                            </span></h2>
                           <a href="#"> <h4><strong>Number of Students</strong></h4></a>
                        </div>
                        <div class="sparkline-bar-stats3">1,2,3,4,5</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter"><?php
                                
                                $sql = " SELECT COUNT(DISTINCT stdid)  as num_payment FROM `lhptransaction` where `status` = 1 and term = '$term'";
                                $result=mysqli_query($con,$sql);
                               $row=mysqli_fetch_array($result);
                               echo "$row[num_payment]";
                             
                                ?> </span></h2>
                          <a href="#">  <h4><strong>Paying Students</strong></h4></a>
                        </div>
                        <div class="sparkline-bar-stats4">1,2,3,4,5</div>
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
    
	<div id="doc" class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                           
                            <h2>Payment Records By Class 
							
						</h2>
                            <p>A breakdown of payment expected and received by class</p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-bordered">
                                <thead>
                                    <tr>
                      <th>S/N</th>
                      <th>Class </th>
					<th>Total Amount Expected</th>
          <th>Total Discount Allowed</th>
                    <th>Total Payment Received</th>
                    <th>Number of Billed Students</th>	
                    <th>Learners that have made Payment</th>
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
			
			
			include_once './conn.php';
				
            $count=1;
            $query=$conn->prepare("SELECT lhpclass.classid, lhpclass.classname, lhpassignedfee.cnt_bill, lhptransaction.cnt_pay,
             IFNULL(lhpassignedfee.classbill, 0) AS classbill,  IFNULL(lhpassignedfee.disc, 0) AS disc, IFNULL(lhptransaction.classrev, 0) AS classrev FROM lhpclass
             LEFT JOIN ( SELECT classid, sum(amount) AS classbill , sum(discount) AS disc , 
             COUNT(DISTINCT lhpassignedfee.stdid) as cnt_bill FROM lhpassignedfee where lhpassignedfee.status = 1  and term = '$term' GROUP BY classid ) 
             lhpassignedfee ON (lhpclass.classid = lhpassignedfee.classid) 
            LEFT JOIN ( SELECT classid, sum(amount) AS classrev, 
            COUNT(DISTINCT lhptransaction.stdid) as cnt_pay FROM lhptransaction where lhptransaction.status = 1  and term = '$term' GROUP BY classid ) 
            lhptransaction ON (lhpclass.classid = lhptransaction.classid)");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
              $classname = $row->classname;
              $classbill = $row->classbill;
              $classdisc = $row->disc;
              $classrev = $row->classrev;     
              $cntbill = $row->cnt_bill; 
              $cntpay = $row->cnt_pay;
              
            ?>
            <tr>
            <td><?php echo $count++ ?></td>
            <td><strong><?php echo $classname?></strong></td>
            <td><strong>&#8358;<?php echo intval($classbill) ?></strong></td>   
              <td><strong>&#8358;<?php echo intval($classdisc) ?></strong></td>

            <td><strong>&#8358;<?php echo intval($classrev) ?></strong></td>
			<td><strong><?php echo intval($cntbill) ?></strong></td>	
            <td><strong><?php echo intval($cntpay) ?></strong></td>   
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                    <th>S/N</th>
                      <th>Class </th>
					<th>Total Amount Expected</th>
                    <th>Total Payment Received</th>
                    <th>Number of Billed Students</th>	
                    <th>Learners that have made Payment</th>
						
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


