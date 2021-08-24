<?php

include "conf.php";

 $message ="no action";
if (isset($_POST['createquestion']) && $_POST['createquestion'] == 'Add Question to This Topic ')
{
    $staff = mysqli_real_escape_string($con,$_POST['stname']);
    $term = mysqli_real_escape_string($con,$_POST['term']);
	$classname = mysqli_real_escape_string($con,$_POST['classname']);
	$subject = mysqli_real_escape_string($con,$_POST['subject']);
	$week = mysqli_real_escape_string($con,$_POST['week']);
    $topic = mysqli_real_escape_string($con,$_POST['topic']);
    $typed = mysqli_real_escape_string($con,$_POST['typed']);
    $status = 1;
    $due = mysqli_real_escape_string($con,$_POST['due']);
     $grade = mysqli_real_escape_string($con,$_POST['grade']);
   
   
  
   
    $topicid = $_SESSION['topicid'];
    $sql= "SELECT sbjname FROM `lhpsubject`  where sbjid = $subject";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $subjectname = $row['sbjname'];

  $sql= "SELECT topic FROM `lhpscheme`  where schmid = $topicid";
               $result=mysqli_query($con,$sql);
                $row=mysqli_fetch_array($result);
                      $topicname = $row['topic'];

    $update = 'A new assignment has been added to the TOPIC : '.$topicname.' scheduled for '. $week.' in '. $subjectname; 
               
   
   if($typed == "text"){
        $lesson = mysqli_real_escape_string($con,$_POST['lesson']);
        $sql= "INSERT INTO `lhpquestion`  (topicid, type, content, staffid, status,deadline, grade, sbjid, term) VALUES ('$topicid', '$typed', '$lesson', '$staff', '$status' , '$due', '$grade', '$subject' , '$term')";
		if(mysqli_query($con, $sql)){
      $sql= "INSERT INTO `lhpnotice`  ( subject, message, refid, term) VALUES ('New Assignment', '$update', '$classname','$term' )";
      if(mysqli_query($con, $sql)){
      }	
		$message ='Status : Question has been successfully added to the topic : '.$topic;
		}

      else 
      {
        $message = 'Status : Error adding Question to the topic.';
      }
   }
   
     elseif($typed == "online"){
          $online = mysqli_real_escape_string($con,$_POST['online']);
        $sql= "INSERT INTO `lhpquestion`  (topicid, type, content, staffid, status, deadline, grade, sbjid, term) VALUES ('$topicid', '$typed', '$online', '$staff', '$status' , '$due',  '$grade', '$subject', '$term')";
		if(mysqli_query($con, $sql)){	
      $sql= "INSERT INTO `lhpnotice`  ( subject, message, refid, term) VALUES ('New Assignment', '$update', '$classname','$term' )";
      if(mysqli_query($con, $sql)){
      }
		$message ='Status : Link to Online Question has been successfully added to the topic : '.$topic;
		}

      else 
      {
        $message = 'Status : Error adding Question to the topic.';
      }
   }
   
   elseif ($typed == "file"){
   if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK)
  {
    // get details of the uploaded file
    $fileTmpPath = $_FILES['document']['tmp_name'];
    $fileName = $_FILES['document']['name'];
    $fileSize = $_FILES['document']['size'];
    $fileType = $_FILES['document']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    
    $seriala = rand(11111111,88888888);
    $serialb = rand(11111111,88888888);
    $serial = $seriala.$serialb;
     // sanitize file-name
    $neoFileName = $serial.".".$fileExtension;
	 
    // check if file has one of the following extensions
    $allowedfileExtensions = array('pdf');

    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = 'noteoflesson/';
      $dest_path = $uploadFileDir . $neoFileName;

      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
          $sql= "INSERT INTO `lhpquestion`  (topicid, type, content, staffid, status, deadline, grade, sbjid, term) VALUES ('$topicid', '$typed', '$neoFileName', '$staff', '$status', '$due',  '$grade', '$subject', '$term')";
		
		if(mysqli_query($con, $sql)){	  $sql= "INSERT INTO `lhpnotice`  ( subject, message, refid, term) VALUES ('New Assignment', '$update', '$classname','$term' )";
      if(mysqli_query($con, $sql)){
      }

		$message ='Status : Question has been successfully added to the topic : '.$topic;
		}

      else 
      {
        $message = 'Status : Error adding question to the topic.';
      }
          
    }
    
    
      else 
      {
        $message = 'Status : File could not be uploaded, try again.';
      }
    
	}

      else 
      {
        $message = 'Status :File Format not supported.';
      }

}

      else 
      {
        $message = 'Status : File could not be uploaded, try again.';
      }
}


}
$_SESSION['message'] = $message;
$loc = "Location: addwork.php?id=".$topicid;
header($loc);
?>