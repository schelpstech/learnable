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
$query = "SELECT * FROM lpterm where status = 1";
$termresult = $db_handle->runQuery($query);
?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Manage Reportsheets - LearnAble</title>
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
function getstd() {
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
        	url: "get_std.php",
        	data:'cld='+str,
        	success: function(data){
        		$("#std-list").html(data);
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
	
	 <div class="form-element-area">
        <div class="container">
            <div class="row">
                
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                        <div class="basic-tb-hd">
                            <h2>Termly Report Sheet Configuration</h2>
                            <p>Use the form below to configure this term's  report sheet</p>
						<h2>	 <?php
							
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
						<form method="POST" action="configres.php" class="form-element-area" id="fupload" enctype="multipart/form-data">
                         
							 
							
							
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Current Term</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" required="yes" class="form-control" name="term" >
										
										<?php
foreach ($termresult as $termd) {
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
                                <label>CA Score Obtainable</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="number" required="yes" class="form-control" min="0" name="cascore" >
			
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Exam Score Obtainable</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="number" required="yes" class="form-control" min="0" name="examscore" >
			
                                    </div>
                                </div>
                            </div>
							
						<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Number of School Open Days <?php echo date("d/m/Y");?> </label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="number" required="yes" class="form-control" min="0" name="opendays"  >
			
                                    </div>
                                </div>
                            </div>
							
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Next Term Begins</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="date" required="yes" class="form-control" name="resume" 
                                      min ="<?php echo date("Y-m-d");?>">
			
                                    </div>
                                </div>
                            </div>
                            
							<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                <label>Signature Line</label>
								<div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-wifi"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="file" required="yes" class="form-control" name="signature">
                                    </div>
                                </div>
                            </div>
							
							<br>
							<br>
							<div class="col-lg-12 col-md-4 col-sm-4 col-xs-12">
                                
								<div class="form-group ic-cmp-int">
                                    
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="configresult" value="Save Result Configuration"/> 
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
                            <h2>
                                Report Sheet Configurations
						</h2>
                         
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                        <th>S/N</th>
										  <th>Term</th>
										<th>CA Score Obtainable</th>
										<th>Exam Score Obtainable</th>
										<th>Total School Open Days</th>
										<th>Next Term Resumes</th>
										<th>Signature Line</th>
										<th>Mid-Term</th>
										<th>Populate CA</th>
										<th>End of the Term</th>
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
			
			
			include_once './conn.php';
				
            $count=1;
            $query=$conn->prepare("select * from lhpresultconfig ORDER BY id DESC ");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
                
                $term = $row->term;
                $ca = $row->ca_score;
                $exam = $row->exam_score;
                $schopens = $row->sch_open;
                $resume = $row->resumption;
                $sign = $row->signature;
                $status = $row->status;
                $midterm = $row->midterm;
?>
<?php
    $sql = "SELECT COUNT('id') as numrec FROM lhpweekrecord WHERE `term` = '$term'";
    $result=mysqli_query($con,$sql);
    $row=mysqli_fetch_array($result);
     
    $numca = $row["numrec"];

if ($status == 1){
    $butt = '<a href="resultstatus.php?term='.$term.'&type=Termly Result&val=1" type="button"  class="btn btn-success" >Termly Results Activated</button>';
}
if ($status == 0){
    $butt = '<a href="resultstatus.php?term='.$term.'&type=Termly Result&val=0" type="button"  class="btn btn-warning" >Termly Result Inactive</button>';
}
if ($midterm == 1){
  $midbuttn = '<a href="resultstatus.php?term='.$term.'&type=Midterm Result&val=1" type="button"  class="btn btn-success" >Mid Term Results Activated</button>';
}
if ($midterm == 0){
  $midbuttn = '<a href="resultstatus.php?term='.$term.'&type=Midterm Result&val=0" type="button"  class="btn btn-warning" >Mid Term Result Inactive</button>';
}

if ($midterm == 1 && $numca >= 100){
  $popca = '<a href="popca.php?term='.$term.'" type="button"  class="btn btn-success" >  Populate CA scores</button>';
}elseif ($midterm == 0 && $numca < 100){
  $popca = '<a  type="button"  class="btn btn-warning" disabled> Unable to Populate</button>';
}else{
  $popca = '<a  type="button"  class="btn btn-warning" disabled> Unable to Populate</button>';
}
 ?>
            <tr>
        <td><?php echo $count++ ?></td>
				<td><?php echo $term  ?></td>
				<td><?php echo $ca ?></td>
				<td><?php echo $exam ?></td>
				<td><?php echo $schopens ?></td>
				<td><?php echo $resume ?></td>
				<td><a href="archive/<?php echo $sign ?>">Authorised Signature Line </a></td>
        
        <td><?php echo $midbuttn ?></td>
        <td><?php echo $popca ?></td>
        <td><?php echo $butt ?></td>
			
				
                
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                    <th>S/N</th>
										<th>Term</th>
										<th>CA Score Obtainable</th>
										<th>Exam Score Obtainable</th>
										<th>Total School Open Days</th>
										<th>Next Term Resumes</th>
										<th>Signature Line</th>
                    <th>Mid-Term</th>
                    <th>Populate CA</th>
										<th>End of the Term</th>
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