<?php
include "conf.php";


if (isset($_POST['sch']) && $_POST['sch'] == 'Modify School Profile')
{
    
  
$schname = mysqli_real_escape_string($con,$_POST['schname']);  
$schowner = mysqli_real_escape_string($con,$_POST['schowner']);
  $schmotto = mysqli_real_escape_string($con,$_POST['schmotto']);
  $schaddress = mysqli_real_escape_string($con,$_POST['schaddress']);
  $schphone = mysqli_real_escape_string($con,$_POST['schphone']);
  $schemail = mysqli_real_escape_string($con,$_POST['schemail']);
  $schyear = mysqli_real_escape_string($con,$_POST['schyear']);
  $schweb = mysqli_real_escape_string($con,$_POST['schweb']);
 
//check existing school record
        $sql_query = "select count(schid) as cntrecord from lhpschool" ;
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntrecord'];
        if($count == 0){		

            if (isset($_FILES['schlogo']) && $_FILES['schlogo']['error'] === UPLOAD_ERR_OK)
            {
                // get details of the uploaded file
    $fileTmpPath = $_FILES['schlogo']['tmp_name'];
    $fileName = $_FILES['schlogo']['name'];
    $fileSize = $_FILES['schlogo']['size'];
    $fileType = $_FILES['schlogo']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    
    
    $schlogo = "schlogo.".$fileExtension;
	 
	 
    // check if file has one of the following extensions
    $allowedfileExtensions = array('jpg', 'jpeg', 'png');

    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = '../learn/asset/img/school/';
      $dest_path = $uploadFileDir . $schlogo;

      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
		  $sql= "INSERT INTO `lhpschool`(`schname`, `address`, `phone`, `email`, `website`, `proprietor`, `founded`, `motto`, `logo`)
           VALUES ('$schname','$schaddress','$schphone','$schemail','$schweb','$schowner','$schyear','$schmotto','$schlogo')";
		if(mysqli_query($con, $sql)){	
		
		$clmessage = 'Status : School Profile Successfully Modified.';
		}

      else 
      {
        $clmessage = 'Error Modifying School Profile' ;
      }
    }
    else 
    {
      $clmessage = 'Error Saving School Logo' ;
    }
}
else 
{
  $clmessage = 'Uploaded Logo is not in the acceptable picture format' ;
}	

            }
            else 
            {
              $clmessage = 'Unable to upload School Logo' ;
            }
        }
        if (isset($_FILES['schlogo']) && $_FILES['schlogo']['error'] === UPLOAD_ERR_OK)
        {
            // get details of the uploaded file
$fileTmpPath = $_FILES['schlogo']['tmp_name'];
$fileName = $_FILES['schlogo']['name'];
$fileSize = $_FILES['schlogo']['size'];
$fileType = $_FILES['schlogo']['type'];
$fileNameCmps = explode(".", $fileName);
$fileExtension = strtolower(end($fileNameCmps));



$schlogo = "schlogo.".$fileExtension;
 
 
// check if file has one of the following extensions
$allowedfileExtensions = array('jpg', 'jpeg', 'png');

if (in_array($fileExtension, $allowedfileExtensions))
{
  // directory in which the uploaded file will be moved
  $uploadFileDir = '../learn/asset/img/school/';
  $dest_path = $uploadFileDir . $schlogo;

  if(move_uploaded_file($fileTmpPath, $dest_path)) 
  {
      $sql= "UPDATE `lhpschool` SET 
      `schname`='$schname',
      `address`='$schaddress',
      `phone`='$schphone',
      `email`='$schemail',
      `website`= '$schweb',
      `proprietor`='$schowner',
      `founded`='$schyear',
      `motto`='$schmotto',
      `logo`='$schlogo'";
    if(mysqli_query($con, $sql)){	
    
    $clmessage = 'Status : School Profile Successfully Modified.';
    }

  else 
  {
    $clmessage = 'Error Modifying School Profile' ;
  }
}
else 
{
  $clmessage = 'Error Saving School Logo' ;
}
}
else 
{
$clmessage = 'Uploaded Logo is not in the acceptable picture format' ;
}	

        }
        else 
        {
            $sql= "UPDATE `lhpschool` SET 
            `schname`='$schname',
            `address`='$schaddress',
            `phone`='$schphone',
            `email`='$schemail',
            `website`= '$schweb',
            `proprietor`='$schowner',
            `founded`='$schyear',
            `motto`='$schmotto'";
          if(mysqli_query($con, $sql)){	
          
          $clmessage = 'Status : School Profile Successfully Modified.';
          }
      
        else 
        {
          $clmessage = 'Error Modifying School Profile' ;
        }
        }
    }
$_SESSION['eds'] = $clmessage;
header("Location: profile.php");

?>