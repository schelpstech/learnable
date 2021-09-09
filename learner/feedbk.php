<?php

$stdid = $_SESSION['studnamed'];
$quest = $_SESSION['viewid'];
$grade = $_SESSION['grade'];
$subjectid = $_SESSION['subject'];

$sql = "SELECT * FROM `lpterm` WHERE `status` = 1";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      $term = $row["term"];
	
  }
} 

$sql = "SELECT * FROM `lhpfeedback` WHERE `stdid` = '$stdid' AND qid = '$quest'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      $response = $row["content"];  
      $score = $row["score"];
     if($row["feedback"] == ""){
        $feedback = "No response yet";
     }
         else{
              $feedback = $row["feedback"];
         
         }
      
  }
}

if (!isset($response)){
echo '
<!doctype html>
<html>

<div class="breadcomb-area">
        <div class="container">
            <div class="row">
                
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                         <div class="basic-tb-hd">
                              
                            <h2>Assessment Feedback Submission</h2>
               
                        </div>
					</div>
				</div>
                  </div>      
                        <br>
                        <br>
                        <br>
                        
					 
                        <div class="row">
						<form method="POST" action="feedback.php" class="form-element-area" id="fupload" enctype="multipart/form-data">
						     <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" hidden >
                                 <label> Term </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="term"  required="yes" 
                                        value="'.$term.'">
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12" hidden >
                                 <label> Subject </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="text" class="form-control" name="subjectid"  required="yes" 
                                        value="'.$subjectid.'">
                                    </div>
                                    
                                </div>
                            </div>
						    
						  <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                 <label> How do you wish to send your answers? </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <select type="text" class="form-control" name="typed" id="typed" required="yes" onchange="changeFunc();" >
                                <option value="none">Select Feedback format</option>            
                                <option value="text">Type in Text</option>
                                 <option value="file">Document Upload</option>
                                  
										</select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ldiv" style="display: none;">
                                 <label> Type in Your Answers </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
                                    
                              
									 <div class="cmp-int-box mg-t-20">
                                    
                                        <textarea class="form-control" name="response"  placeholder="Enter your note here" rows="10" id="editor2"  style="background-color:white; border: 1px solid #ccc;"></textarea>
                                    
                                    </div>
                                </div>
                            </div>
                            
                             <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none" id="ddiv">
                                 <label> Submit Answer Documents - PDF and Image (JPG or PNG)  Only </label>
								<div class="form-group ic-cmp-int">
                                
									<div class="form-ic-cmp">
                                        <i class="notika-icon notika-calendar"></i>
                                    </div>
									
                                    <div class="nk-int-st">
                                        <input type="file" accept =".pdf, .jpg, .png, .jpeg" class="form-control" name="documented" id="document" placeholder="Select document file your note of lesson" >
                                        
                                    </div>
                                </div>
                            </div>
                       
                       
                           
                          
							<br>
							<br>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="display:none;" id="subbtn">
                                
								<div class="form-group ic-cmp-int">
                                    
                                    <div class="nk-int-st">
                                       <input type="submit" class="form-control" name="subfeedback"  value="Submit Assessment Feedback"/> 
                                    </div>
                                </div>
                            </div>
							
                    
				</form>
				
				</div>
                </div>
			</div>	
			
		</div>
	</div>

  </html>';
 
}
else
{
    echo'
 <!doctype html>
<html>
   <div class="inbox-area" id="note">
        <div class="container">
            <div class="row">
            
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="view-mail-list sm-res-mg-t-30">
                        <div class="view-mail-hd">
                            <div class="view-mail-hrd">
                                <h2>Instructor Feedback</h2>
                            </div>
                            
                        </div>
                        <div class="mail-ads mail-vw-ph">

                          
	<p class="first-ph"><b>Score Awarded: </b> '.$score.' / '.$grade.'</p>

	
                            
                            
                        </div>
                        
                          <div class="view-mail-atn">
                           <br>
                           <p><b> Your Response</b></p><br><br>
                           '.$response.'<br>
                            
	
                        </div>
                        <br><br><br>
                        <div class="view-mail-atn">
                           <br>
                           <p><b> Instructors Feedback </b></p><br><br>
                           '.$feedback.'<br>
                            
	
                        </div>
                        
                        
                        
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</html>';
}
?>