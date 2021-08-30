<?php
include "conf.php";
$lname = $_SESSION['studnamed'];

if (isset($_POST['profile']) && $_POST['profile'] == 'Change Log in Password')
{
	
	$edit = mysqli_real_escape_string($con,$_POST['edpwd']);  
  $edgender = mysqli_real_escape_string($con,$_POST['edgender']); 
  $eddob = mysqli_real_escape_string($con,$_POST['eddob']); 
  $edfone = mysqli_real_escape_string($con,$_POST['edfone']); 


	 $sql= "UPDATE lhpuser SET upwd = '$edit', gender = '$edgender', dob = '$eddob', numb = '$edfone' WHERE uname = '$lname'";
	 
	 
		if(mysqli_query($con, $sql)){	
		
		$mes = 'Status : Successfully changed modified learners account.';
		}

      else 
      {
        $mes ='Status : Unable to modify learners account';
      }
    }
	
	if (isset($_POST['profile']) && $_POST['profile'] == 'Change Profile Picture')
{
   if (isset($_FILES['picx']) && $_FILES['picx']['error'] === UPLOAD_ERR_OK)
  {
    // get details of the uploaded file
    $fileTmpPath = $_FILES['picx']['tmp_name'];
    $fileName = $_FILES['picx']['name'];
    $fileSize = $_FILES['picx']['size'];
    $fileType = $_FILES['picx']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $nref = preg_replace("/\s+/", "", $lname);
    $nname = str_replace("/", "", $nref);
    
    $neoFileName = $nname.".".$fileExtension;
	 
	 
    // check if file has one of the following extensions
    $allowedfileExtensions = array('jpg', 'jpeg', 'png');

    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = 'images/profilepix/';
      $dest_path = $uploadFileDir . $neoFileName;

      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
          
          $sql= "UPDATE lhpuser SET picture = '$neoFileName' WHERE uname = '$lname'";
		
		if(mysqli_query($con, $sql)){	
		$message ='Status : Profile Picture has been successfully changed';
		}

      else 
      {
        $message ='Status : Unable to change Profile Picture ';
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
	$_SESSION['message'] = $mes;
	header("Location: profile.php");   
?>
