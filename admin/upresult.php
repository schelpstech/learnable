<?php

include "conf.php";

 $message ="no action";
if (isset($_POST['uploadresult']) && $_POST['uploadresult'] == 'Upload Learner Result')
{
  if (isset($_FILES['resultfile']) && $_FILES['resultfile']['error'] === UPLOAD_ERR_OK)
  {
    // get details of the uploaded file
    $fileTmpPath = $_FILES['resultfile']['tmp_name'];
    $fileName = $_FILES['resultfile']['name'];
    $fileSize = $_FILES['resultfile']['size'];
    $fileType = $_FILES['resultfile']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
	
	$rterm = mysqli_real_escape_string($con,$_POST['term']);
    $rclass = mysqli_real_escape_string($con,$_POST['lclass']);
	$rlearn = mysqli_real_escape_string($con,$_POST['learner']);
	$refpin = rand(11223344,99887766);
	$ref = "rs".rand(00000,99999);
	$rpin = $ref.$refpin;
	
	$sql = "SELECT fname FROM lhpuser WHERE uname ='$rlearn'";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
	$fname =$row["fname"];
  }
} 

	 $sql_query = "select count(*) as cntresult from archive where term ='".$rterm."' and classref ='".$rclass."' and learn ='".$rlearn."'" ;
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntresult'];
		
		

        if($count == 0){
          




    // sanitize file-name
    $newFileNamed = $ref.$fileName;
	 $newFileName = strtolower(str_replace(' ','' ,$newFileNamed));
	 $neoFileName = strtolower(str_replace('/','' ,$newFileName));
    // check if file has one of the following extensions
    $allowedfileExtensions = array('pdf');

    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = 'archive/';
      $dest_path = $uploadFileDir . $neoFileName;

      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
		 
		  $sql= "INSERT INTO archive  (refid, term, classref, learn, pinref, refdoc, learner ) VALUES ('$ref', '$rterm', '$rclass', '$rlearn', '$rpin', '$neoFileName', '$fname' )";
		if(mysqli_query($con, $sql)){	
		$remessage ='Status : ReportSheet is successfully uploaded.';
		}

      else 
      {
        $remessage = 'Status : There was some error in writin on server.';
      }
    }
	else 
      {
        $remessage = 'Status : There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
      }
	}
    else
    {
      $remessage = 'Status : Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
    }
        }
  else
  {
   $remessage = 'Status : Report for '.$fname.' in '.$rterm.' has already been uploaded, Kindly delete the existing report sheet to upload another.';
}
  }
  else
  {
    $remessage = 'Status : There is some error in the file upload. Please check the following error.<br>';
    $remessage .= 'Status : Error:' . $_FILES['resultfile']['error'];
  }

}
$_SESSION['remessage'] = $remessage;
header("Location: mgreport.php");

?>