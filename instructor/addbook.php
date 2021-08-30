<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['stnamed'])){
     header('Location: ../index.php');
}

?>

<?php
if(!empty($_GET['id'])) {         
        $viewid = $_GET["id"];
        $_SESSION['topicid'] = $viewid;
}

require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpscheme where schmid ='$viewid'";
$book = $db_handle->runQuery($query);

?>





<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>View LessonNote - Scheme of Work - LearnAble</title>
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
    <script src="js/hide-show-fields-form.js"></script>
    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
     <script src="https://cdn.ckeditor.com/4.15.0/standard-all/ckeditor.js"></script>
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

</head>

<body>

<script type="text/javascript">
function changeFunc() {
var typed = document.getElementById("typed");
var selectedValue = typed.options[typed.selectedIndex].value;
if (selectedValue=="text"){
$('#ldiv').show();
$('#odiv').hide();
$('#adiv').hide();
$('#ddiv').hide();
$('#ldiv').attr('required', '');
$('#ldiv').attr('data-error', 'Note of Lesson field is required.');
}
else if (selectedValue=="online"){
$('#odiv').show();
$('#ldiv').hide();
$('#adiv').hide();
$('#ddiv').hide();
$('#odiv').attr('required', '');
$('#odiv').attr('data-error', 'Link to Online Note of Lesson field is required.');
}
else if (selectedValue=="media"){
$('#adiv').show();
$('#odiv').hide();
$('#ldiv').hide();
$('#ddiv').hide();
$('#adiv').attr('required', '');
$('#adiv').attr('data-error', 'Audio-Visual File  field is required.');
}
else if (selectedValue=="file"){
$('#ddiv').show();
$('#odiv').hide();
$('#adiv').hide();
$('#ldiv').hide();
$('#ddiv').attr('required', '');
$('#ddiv').attr('data-error', 'Note of Lesson Document  field is required.');
}
else {
alert("Select a Learning Material Type");
$('#ldiv').hide();
$('#odiv').hide();
$('#adiv').hide();
$('#ddiv').hide();
}
}
</script>
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
                            <h2>Add Learning Materials to Topic</h2>
                            <p>Use the form below to add learning materials to the topics in your scheme of work.</p>
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
                        <br>
                        <br>
                        <br>
						<div class="row">
						<form method="POST" action="createnote.php" class="form-element-area" id="fupload" enctype="multipart/form-data">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" hidden="yes">
                                <div class="form-group ic-cmp-int">
                                    <div class="form-ic-cmp">
                                        <i class="notika-icon notika-support"></i>
                                    </div>
                                    <div class="nk-int-st">
                                        <input type="text"   class="form-control" name ="stname" placeholder="Full Name" value= "<?php
							echo $_SESSION['stnamed'];   // now, print the Session variable ?>">
                                    
									</div>
                                </div>
                            </div>
						
					
                           
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Term </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="term"  required="yes"  >
<?php
foreach ($book as $booked) {
    ?>
<option value="<?php echo $booked["term"]; ?>"><?php echo $booked["term"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
							
							
							 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Class </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="classname"  required="yes"  >
<?php
foreach ($book as $booked) {
  $cname = $booked["classname"];
  $sql = "SELECT * FROM lhpclass WHERE classid  = '$cname'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $viewc = $row['classname'];
    ?>
<option value="<?php echo $booked["classname"]; ?>"><?php echo $viewc; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
                        
                        	 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Subject </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="subject"  required="yes"  >
<?php
foreach ($book as $booked) {
     $sbj = $booked["subject"];
  $sql = "SELECT * FROM lhpsubject WHERE sbjid  = '$sbj'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $views = $row['sbjname'];
    ?>
<option value="<?php echo $sbj; ?>"><?php echo $views; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
                            
                            	 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Week </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="week"  required="yes"  >
<?php
foreach ($book as $booked) {
    ?>
<option value="<?php echo $booked["week"]; ?>"><?php echo $booked["week"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
                            
                            	 <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Topic </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="topic"  required="yes"  >
<?php
foreach ($book as $booked) {
    ?>
<option value="<?php echo $booked["topic"]; ?>"><?php echo $booked["topic"]; ?></option>
<?php
}
?>
										</select>
                                    </div>
                                </div>
                            </div>
                        
                     <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> Select Learning Material Type </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="typed" id="typed" required="yes" onchange="changeFunc();" >
                                <option value="none">Select Learning Material Type</option>            
                                <option value="text">Type in Text</option>
                                 <option value="file">Document Upload</option>
                                  <option value="media">Audio - Visual Upload</option>
                                   <option value="online">Online Material</option>
										</select>
                                    </div>
                                </div>
                            </div>
						
				
                             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ldiv" style="display: none">
                                 <label> Type in Note of Lesson </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
                                    
                              
									 <div class="cmp-int-box mg-t-20">
                                    
                                        <textarea  class="form-control" name="lesson"  placeholder="Enter your note here" rows="10" id="editor2"  style="background-color:white; border: 1px solid #ccc;"></textarea>
                                    
                                    </div>
                                </div>
                            </div>
                            
                             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none" id="ddiv">
                                 <label> Upload Note of Lesson - PDF Only </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="file" accept =".pdf" class="form-control" name="document" id="document" placeholder="Select document file your note of lesson" >
                                        
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none" id="adiv">
                                 <label> Upload Audio - Visual Note of Lesson </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="file" class="form-control" name="audiovisual" id="audiovisual" " placeholder="Select media file your note of lesson" >
                                        
                                    </div>
                                </div>
                            </div>
                            
                             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none" id="odiv">
                                 <label> Enter link to the Online Learning Material </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="url" class="form-control" name="online" id="online" placeholder="Enter  link to your online learning material here" >
                                    </div>
                                </div>
                            </div>
                            
                         
							<br>
							<br>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                
								<div class="form-group ic-cmp-int">
                                    
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="createnote" value="Add Note of Lesson To Topic "/> 
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
                            <h2>View Learning Materials for the topic 
                            <?php
foreach ($book as $booked) {
    ?>
<?php echo $booked["topic"]." scheduled for ". $booked["week"] ; ?>
<?php
}
?>

							
						</h2>
                            <p>This table contains all the learning materials you have added to this topic </p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                
                                    <tr>
                                         <th>S/N</th>
                                         <th>Type</th>
										<th>Learning Material</th>
										<th>Edit</th>
										<th>Remove</th>
										
										
									
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
		    if(!empty($_GET['id'])) {         
        $viewid = $_GET["id"];
        }
        $_SESSION['topicid'] = $viewid;
		$lname = $_SESSION['stnamed'];
			
			include_once './conn.php';
				
            $count=1;
            $query=$conn->prepare("SELECT * from lhpnote  WHERE (topicid = '$viewid' AND staffid = '$lname' AND status = 1) ORDER BY rectime ASC ");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
            ?>
            <tr>
                <td><?php echo $count++ ?></td>
               	<td><strong>
				   <?php echo strtoupper( $row->type) ?> </strong>
				</td>
				
				<td>
          
				<a href="noteview.php?id=<?php echo $row->noteid?>&typ=<?php echo $row->type?>" type="button"  class="btn btn-success" >View this Learning Material</a>
				    
				</td>
					<td>
				<a href="editnote.php?id=<?php echo $row->noteid?>" type="button"  class="btn btn-warning" >Modify this Learning Material</a>
				    
				</td>
                
				<td>
				<a href="deletenote.php?id=<?php echo $row->noteid?>" type="button"  class="btn btn-danger" >Delete this Learning Material</a>
				    
				</td>
				
             </tr>
            <?php }?>  
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                       <th>S/N</th>
										<th>Type</th>
										<th>Learning Material</th>
										<th>Edit</th>
										<th>Remove</th>
										
									
									
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
 <?php include ("foot.php"); ?>
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
 
<!--End of Tawk.to Script-->
</body>

</html>