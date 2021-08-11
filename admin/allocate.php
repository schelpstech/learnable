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
$query = "SELECT * FROM lhpstaff where status = 1 ORDER BY staffname ASC";
$ssresult = $db_handle->runQuery($query);
?>

<?php
require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lpterm where status = 1 ";
$termed = $db_handle->runQuery($query);
?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Subject Allocation - Learn at Home</title>
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
										<h2>	 <?php						
    if (isset($_SESSION['clmessage']) && $_SESSION['clmessage'])
    {
      printf('<b>%s</b>', $_SESSION['clmessage']);
      unset($_SESSION['clmessage']);
    }
  ?></h2>
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
	
	 <div class="form-element-area">
        <div class="container">
            <div class="row">
                
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                        <div class="basic-tb-hd">
                            <h2>Allocate Subject</h2>
                            <p>Use the form below to allocate subject for each classes to an instructor </p>
					
                        </div>
					</div>
				</div>
                  </div>      
                        <br>
                        <br>
                        <br>
						<div class="row">
						<form method="POST" action="doallocate.php" class="form-element-area"  id="fupload" enctype="multipart/form-data">
                         
                              <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Select Term </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="term"  required="yes"  >
											
											<?php
foreach ($termed as $termd) {
    ?>
<option value="<?php echo $termd["term"]; ?>"><?php echo $termd["term"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
							 
							 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Select Class </label>
								<div class="form-group ic-cmp-int">
                                    
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <select type="text"  class="form-control" required="yes" name="classd" id="class-list" onChange="getclass();" >
										<option value="">Select Class</option>
<?php
foreach ($classresult as $classed) {
    ?>
<option value="<?php echo $classed["classid"]; ?>"><?php echo $classed["classname"]; ?></option>
<?php
}
?>
										</select>
										
                                    </div>
                                </div>
                            </div>
                            
                	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Select Subject </label>
								<div class="form-group ic-cmp-int">
                                    
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-mail"></i>
                                    </div>
                                    <div class="nk-int-st">
											<select  required="yes" type="text" class="form-control" name="sbj" id="sbj-list" class="demoInputBox" >
                <option value="">Select Subject</option>
                
										</select>
										
                                    </div>
                                </div>
                            </div>
                            
						<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Select Instructor / Teacher </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" required="yes" class="form-control"  name="instructor">
										<option value=""> Select Instructor</option>
										<?php
foreach ($ssresult as $ssed) {
    ?>
<option value="<?php echo $ssed["sname"]; ?>"><?php echo $ssed["staffname"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>	 
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
							 </div>
						<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label> Select Subject Supervisor </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" required="yes" class="form-control"  name="super">
										<option value=""> Select Supervisor</option>
										<?php
foreach ($ssresult as $ssed) {
    ?>
<option value="<?php echo $ssed["sname"]; ?>"><?php echo $ssed["staffname"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>	 
						
						
							
							
							
							
							<br>
							<br>
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                
								<div class="form-group ic-cmp-int">
                                    
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="allocate" value="Allocate Subject"/> 
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
                            <h2>Subject Allocation List
							
						</h2>
                            <p></p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                         <th>S/N</th>
                                         <th>Term</th>
										<th>Class name</th>
										<th>Subject</th>
										<th>Instructor</th>
                                        <th>Supervisor</th>
                                        <th>Modify</th>
										
										
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
			
			
			include_once './conn.php';
					$sql = "SELECT `term` FROM `lpterm` WHERE `status` = 1";
        $result = mysqli_query($con, $sql);

            if (mysqli_num_rows($result) > 0) {
             // output data of each row
            while($row = mysqli_fetch_assoc($result)) {
    
	        $curterm =  $row["term"];
	        
}
            }
            $count=1;
            $query=$conn->prepare("select * from lhpalloc where term = '$curterm' ORDER BY classname ASC ");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
				
            ?>
			<?php 
				
				$term = $row->term;
				$cname = $row->classname;
				$subject = $row->subject;
				$aid = $row->aid;
				$inst = $row->staffid;
				$supervisor = $row->supro;
				
			$sql = "SELECT * FROM `lhpstaff` WHERE `sname` = '".$inst."'";
        $result = mysqli_query($con, $sql);

            if (mysqli_num_rows($result) > 0) {
             // output data of each row
            while($row = mysqli_fetch_assoc($result)) {
    
	        $staff =  $row["staffname"];
	        
}
            }
            	?>
           
				
            <tr>
                <td><?php echo $count++ ?></td>
				<td><?php echo $term ?></td>
			    <td> <?php echo $cname ?> </td>
				<td> <?php echo $subject ?> </td>
				<td> <?php echo $staff; ?> </td>
				<td>  <?php 
$dql = "SELECT * FROM `lhpstaff` WHERE `sname` = '$supervisor'";
        $results = mysqli_query($con, $dql);

            if (mysqli_num_rows($results) > 0) {
             // output data of each row
            while($row = mysqli_fetch_assoc($results)) {
    
	        echo  $row["staffname"];
            }
            }
			?> </td>				
			<td><a href="allocatedit.php?ref=<?php echo $aid ?>" type="button"  class="btn btn-warning" >Modify</a></td>	
				
                
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                         <th>S/N</th>
                                         <th>Term</th>
										<th>Class name</th>
										<th>Subject</th>
										<th>Instructor</th>                        <th>Supervisor</th>
										<th>Modify</th>
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