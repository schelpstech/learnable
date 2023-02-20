<?php

include "conf.php";

 $message ="no action";
if (isset($_POST['configresult']) && $_POST['configresult'] == 'Save Result Configuration')
{
  if (isset($_FILES['signature']) && $_FILES['signature']['error'] === UPLOAD_ERR_OK)
  {
    // get details of the uploaded file
    $fileTmpPath = $_FILES['signature']['tmp_name'];
    $fileName = $_FILES['signature']['name'];
    $fileSize = $_FILES['signature']['size'];
    $fileType = $_FILES['signature']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
	
	$resterm = mysqli_real_escape_string($con,$_POST['term']);
    $cascore = mysqli_real_escape_string($con,$_POST['cascore']);
   $examscore = mysqli_real_escape_string($con,$_POST['examscore']);
   $schopen = mysqli_real_escape_string($con,$_POST['opendays']);
   $resume = mysqli_real_escape_string($con,$_POST['resume']);
   $sign = rand(11223344,99887766);
   
   $totscore = $cascore+$examscore;
   
      if($totscore == 100){

	 $sql_query = "select count(*) as cntconfig from lhpresultconfig where term ='$resterm' ";
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntconfig'];
		
		

        if($count == 0){
          

    // sanitize file-name
    $newFileName = $sign.".".$fileExtension;
	 
    // check if file has one of the following extensions
    $allowedfileExtensions = array('jpg', 'jpeg','png');

    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = 'archive/';
      $dest_path = $uploadFileDir . $newFileName;

      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
		 
		  $sql= "INSERT INTO lhpresultconfig  (term, ca_score, exam_score, sch_open, resumption, signature, status, midterm ) VALUES ('$resterm', '$cascore', '$examscore', '$schopen', '$resume', '$newFileName','0','0')";
		if(mysqli_query($con, $sql)){	
		$remessage ='Status : ReportSheet configuration successfully saved.';
		}

      else 
      {
        $remessage = 'Status : There was some error in writing on server.';
      }
    }
	else 
      {
        $remessage = 'Status : There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
      }
	}
    else
    {
      $remessage = 'Status : Error with Signature File. Allowed file types: ' . implode(',', $allowedfileExtensions);
    }
	}
    else
    {
      $remessage = 'Status : A configuration has been saved for this term already, kindly click on edit to change term parameters.';
    }
  }
  else
  {
    $remessage = 'Status : Cumulative of CA Score and Exam Score MUST be 100';
  }
  
  }
  else
  {
    $remessage = 'Status : There is some error in the file upload. Please check the following error.<br>';
    $remessage .= 'Status : Error:' . $_FILES['resultfile']['error'];
  }

}
$_SESSION['remessage'] = $remessage;
header("Location: mgconfig.php");

?>