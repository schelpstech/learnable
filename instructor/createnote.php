<?php

include "conf.php";

 $message ="no action";
if (isset($_POST['createnote']) && $_POST['createnote'] == 'Add Note of Lesson To Topic ')
{
    $staff = mysqli_real_escape_string($con,$_POST['stname']);
    $term = mysqli_real_escape_string($con,$_POST['term']);
	$classname = mysqli_real_escape_string($con,$_POST['classname']);
	$subject = mysqli_real_escape_string($con,$_POST['subject']);
	$week = mysqli_real_escape_string($con,$_POST['week']);
    $topic = mysqli_real_escape_string($con,$_POST['topic']);
    $typed = mysqli_real_escape_string($con,$_POST['typed']);
    $status = 1;
    $topicid = $_SESSION['topicid'];
  $sql= "SELECT sbjname FROM `lhpsubject`  where sbjid = $subject";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $subjectname = $row['sbjname'];

  $sql= "SELECT topic FROM `lhpscheme`  where schmid = $topicid";
               $result=mysqli_query($con,$sql);
                $row=mysqli_fetch_array($result);
                      $topicname = $row['topic'];

    $update = 'A new learning material has been added to the TOPIC : '.$topicname.' scheduled for '. $week.' in '. $subjectname; 
               
   
    
   if($typed == "text"){
        $lesson = mysqli_real_escape_string($con,$_POST['lesson']);
        $sql= "INSERT INTO `lhpnote`  (topicid, type, content, staffid, status, sbjid, term) VALUES ('$topicid', '$typed', '$lesson', '$staff', '$status', '$subject', '$term' )";
		if(mysqli_query($con, $sql)){	
      $sql= "INSERT INTO `lhpnotice`  ( subject, message, refid, term) VALUES ('New Lesson Note ', '$update', '$classname','$term' )";
		if(mysqli_query($con, $sql)){
    }	
		$message ='Status : Note has been successfully added to the topic : '.$topic;
		}
    
      else 
      {
        $message = 'Status : Error adding note to the topic.';
      }
   }
   
     elseif($typed == "online"){
          $online = mysqli_real_escape_string($con,$_POST['online']);
        $sql= "INSERT INTO `lhpnote`  (topicid, type, content, staffid, status, sbjid, term) VALUES ('$topicid', '$typed', '$online', '$staff', '$status', '$subject', '$term')";
		if(mysqli_query($con, $sql)){	
      $sql= "INSERT INTO `lhpnotice`  ( subject, message, refid, term) VALUES ('New Lesson Note ', '$update', '$classname','$term' )";
      if(mysqli_query($con, $sql)){
      }
		$message ='Status : Link to Online Learning Material has been successfully added to the topic : '.$topic;
		}

      else 
      {
        $message = 'Status : Error adding note to the topic.';
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

    
      // directory in which the uploaded file will be moved
      $uploadFileDir = 'noteoflesson/';
      $dest_path = $uploadFileDir . $neoFileName;

      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
          $sql= "INSERT INTO `lhpnote`  (topicid, type, content, staffid, status, sbjid, term) VALUES ('$topicid', '$typed', '$neoFileName', '$staff', '$status', '$subject', '$term')";
		
		if(mysqli_query($con, $sql)){	
      $sql= "INSERT INTO `lhpnotice`  ( subject, message, refid, term) VALUES ('New Lesson Note ', '$update', '$classname','$term' )";
      if(mysqli_query($con, $sql)){
      }if (in_array($fileExtension, $allowedfileExtensions))
    {
		$message ='Status : Learning Material has been successfully added to the topic : '.$topic;
		}

      else 
      {
        $message = 'Status : Error adding learning material to the topic.';
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

 elseif ($typed == "media"){
   if (isset($_FILES['audiovisual']) && $_FILES['audiovisual']['error'] === UPLOAD_ERR_OK)
  {
    // get details of the uploaded file
    $fileTmpPath = $_FILES['audiovisual']['tmp_name'];
    $fileName = $_FILES['audiovisual']['name'];
    $fileSize = $_FILES['audiovisual']['size'];
    $fileType = $_FILES['audiovisual']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    
     $seriala = rand(11111111,88888888);
    $serialb = rand(11111111,88888888);
    $serial = $seriala.$serialb;
     // sanitize file-name
    $neoFileName = $serial.".".$fileExtension;
	 
	 
    // check if file has one of the following extensions
    $allowedfileExtensions = array('jpg', 'gif', 'png','mp4','mp3','3gp' ,'wav');

    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = 'noteoflesson/';
      $dest_path = $uploadFileDir . $neoFileName;

      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
          $sql= "INSERT INTO `lhpnote`  (topicid, type, content, staffid, status, sbjid, term) VALUES ('$topicid', '$typed', '$neoFileName', '$staff', '$status', '$subject', '$term')";
		
		if(mysqli_query($con, $sql)){
      $sql= "INSERT INTO `lhpnotice`  ( subject, message, refid, term) VALUES ('New Lesson Note ', '$update', '$classname','$term' )";
      if(mysqli_query($con, $sql)){
      }	
		$message ='Status : Audio-Visual file has been successfully added to the topic : '.$topic;
		}

      else 
      {
        $message = 'Status : Error adding learning material to the topic.';
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
$loc = "Location: addbook.php?id=".$topicid;
header($loc);
?>