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
$query = "SELECT * FROM lpterm where status = 1";
$termed = $db_handle->runQuery($query);
foreach ($termed as $td) {
    $curterm = $td["term"]; 
}
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Manage Learning Resources- LearnAble</title>
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
										<i class="notika-icon notika-form"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Learning Resources Dashboard</h2>
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
                            <h2><span class="counter">
                                <?php
                                
                              	include_once './conf.php';
                          $sql = " SELECT count('noteid') FROM `lhpnote` WHERE term = '$curterm' and status = 1 ";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        echo "$row[0]";
                             
                                ?>
                                </span></h2>
                            <h4><strong>Notes Created in  <br><?php echo $curterm; ?> </strong></h4>
                        </div>
                        <div class="sparkline-bar-stats1">1,2,3,4,5</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter">
                             <?php
                                
                              	include_once './conf.php';
                         $sql = " SELECT count(distinct staffid) as dist FROM `lhpnote` WHERE term = '$curterm' ";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        echo "$row[dist]";
                             
                                ?>
                            </span></h2>
                           <h4><strong>Number of Teachers that created Notes this <br><?php echo $curterm; ?> </strong></h4>
                        </div>
                        <div class="sparkline-bar-stats2">1,2,3,4,5</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                          <div class="website-traffic-ctn">
                            <h2><span class="counter">
                                <?php
                                
                              	include_once './conf.php';
                          $sql = " SELECT count('questid') FROM `lhpquestion` WHERE term = '$curterm' and status = 1 ";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        echo "$row[0]";
                             
                                ?>
                                </span></h2>
                            <h4><strong>Assessments Created in  <br><?php echo $curterm; ?> </strong></h4>
                        </div>
                        <div class="sparkline-bar-stats3">1,2,3,4,5</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter">
                             <?php
                                
                              	include_once './conf.php';
                         $sql = " SELECT count(distinct staffid) as dist FROM `lhpquestion` WHERE term = '$curterm' ";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        echo "$row[dist]";
                             
                                ?>
                            </span></h2>
                           <h4><strong>Number of Teachers that created Assessment this <br><?php echo $curterm; ?> </strong></h4>
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
     </div
	<div id="doc" class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                      	<div class="breadcomb-report">
									<button type="button" onclick="generatePDF()" title="Download PDF" class="btn"><i class="notika-icon notika-sent"></i></button>
								</div>
                        <div class="basic-tb-hd">
                            <br>
                            <br>
                            <h2>Learning Materials 
							
						</h2>
						<h2>
							 <?php
							
    if (isset($_SESSION['mes']) && $_SESSION['mes'])
    {
      printf('<b>%s</b>', $_SESSION['mes']);
      unset($_SESSION['mes']);
    }
  ?>
						</h2>
                            <p>A list of all successfully uploaded Learning Materials on the School E-learning Platform</p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                         <th>S/N</th>
                                         <th>Term</th>
										<th>Class</th>
										<th>Subject</th>
										<th>Tutor</th>
										<th>Number of Lesson Notes</th>
										<th>Number of Assessments</th>
										
										
										
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
			
			
			include_once './conn.php';
				
            $count=1;
            $query=$conn->prepare("select * from lhpalloc where term =  '$curterm' ORDER BY classid ASC ");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
            ?>
            <?php
			$allocterm = $row->term;
			$allocsbj = $row->sbjid;
			$alloclass = $row->classname;
			$allocsubject = $row->subject;
			$alloctutor = $row->staffid;
			
				include_once './conf.php';
			$sql = "SELECT * FROM lhpstaff  WHERE sname  = '$alloctutor'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
              $staffname = $row["staffname"];
              
           	include_once './conf.php';
    $sql = " SELECT count('noteid') FROM `lhpnote` WHERE term = '$curterm' and staffid = '$alloctutor' and sbjid = '$allocsbj' and status = 1 ";
       $result=mysqli_query($con,$sql);
      $row=mysqli_fetch_array($result);
       $numnotes = '<p><strong>'.$row[0].' Notes Uploaded</strong></p>';
             
        
        include_once './conf.php';
    $sql = " SELECT count('questid') FROM `lhpquestion` WHERE term = '$curterm' and staffid = '$alloctutor' and sbjid = '$allocsbj' and status = 1 ";
       $result=mysqli_query($con,$sql);
      $row=mysqli_fetch_array($result);
       $numquest = '<p><strong>'.$row[0].' Assessment Created</strong></p>';
             
			?>
            <tr>
                <td><?php echo $count++ ?></td> 
				<td><?php echo $allocterm ?></td>
				<td><?php echo $alloclass ?></td>
				<td><?php echo $allocsubject ?></td>
				<td><?php echo $staffname ?></td>
			    <td><?php echo $numnotes ?></td>
			    <td><?php echo $numquest ?></td>
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>S/N</th>
                                         <th>Term</th>
										<th>Class</th>
										<th>Subject</th>
										<th>Tutor</th>
										<th>Number of Lesson Notes</th>
										<th>Number  of Assessments</th>
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


