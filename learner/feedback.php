<?php

include "conf.php";

 $message ="Answer Submission Not Processed";
if (isset($_POST['subfeedback']) && $_POST['subfeedback'] == 'Submit Assessment Feedback')
{
        $term = mysqli_real_escape_string($con,$_POST['term']);
        $subjectid = mysqli_real_escape_string($con,$_POST['subjectid']);
       $typed = mysqli_real_escape_string($con,$_POST['typed']);
    $due = $_SESSION['deadline'];
    $today = date("Y-m-d");;
    $topicid = $_SESSION['worktopic'];
    $classid = $_SESSION['cclass'];
    $quest = $_SESSION['viewid'];
    $std = $_SESSION['studnamed'];
    $status = 0;
    
    if( $due >= $today && $typed == "file"){
        
        $sql = " SELECT count('stdid') FROM lhpfeedback WHERE qid = '$quest' AND  tid = '$topicid' AND stdid = '$std'";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        $count = "$row[0]";

        if($count == $status){
    
     if (isset($_FILES['documented']) && $_FILES['documented']['error'] === UPLOAD_ERR_OK)
  {
    
    
    // get details of the uploaded file
    $fileTmpPath = $_FILES['documented']['tmp_name'];
    $fileName = $_FILES['documented']['name'];
    $fileSize = $_FILES['documented']['size'];
    $fileType = $_FILES['documented']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    
    $seriala = date("Y");
    $serialb = rand(11111111,88888888);
    $serial = $seriala.$serialb;
     // sanitize file-name
    $neoFileName = $serial.".".$fileExtension;
	 
    // check if file has one of the following extensions
    $allowedfileExtensions = array('pdf', 'jpg', 'png', 'jpeg');

    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = 'feedback/';
      $dest_path = $uploadFileDir . $neoFileName;

      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
       $sql= "INSERT INTO `lhpfeedback` 
        (qid, tid, classid, stdid, type, content, term, sbjid) 
        VALUES 
        ('$quest','$topicid', '$classid', '$std', '$typed', '$neoFileName' ,'$term', '$subjectid' )";
		if(mysqli_query($con, $sql)){	
		$message ='Status : Your Answer has been successfully submitted';
		}

      else 
      {
        $message = 'Status : Error submitting Answer.';
      }
          
    }
    
    
      else 
      {
        $message = 'Status : Assignment File could not be uploaded, try again.';
      }
    
	}

      else 
      {
        $message = 'Status :File Format not supported.';
      }

}

      else 
      {
        $message = 'Status : Assignment File could not be uploaded, try again.';
      }
      
        }
         else 
      {
        $message = 'Status : Submission Denied : Only One Assignment submission allowed.';
      }
    }
         else 
      {
        $message = 'Status : Submission Denied : Assignment submission ended on '.$due;
      }
      //submitting text only
       if( $due >= $today && $typed == "text"){ 
        $text = mysqli_real_escape_string($con,$_POST['response']);
        
        $sql = " SELECT count('stdid') FROM lhpfeedback WHERE qid = '$quest' AND  tid = '$topicid' AND stdid = '$std'";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        $count = "$row[0]";

        if($count == $status){
       
        $sql= "INSERT INTO `lhpfeedback` 
        (qid, tid, classid, stdid, type, content, term, sbjid) 
        VALUES 
        ('$quest','$topicid', '$classid', '$std', '$typed', '$text' ,'$term', '$subjectid' )";
		if(mysqli_query($con, $sql)){	
		$message ='Status : Your Answer has been successfully submitted';
		}

      else 
      {
        $message = 'Status : Error submitting Answer.';
      }
   }
    
      else 
      {
        $message = 'Status : Submission Denied : Only One Assignment submission allowed.';
      }
      
    }
       else 
      {
        $message = 'Status : Submission Denied : Assignment submission ended on '.$due;
      }
}
        
     
$_SESSION['message'] = $message;
$loc = "Location: viewquestion.php";
header($loc);
?>