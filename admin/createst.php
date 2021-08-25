<?php
include "conf.php";


if (isset($_POST['createst']) && $_POST['createst'] == 'Create Staff Account')
{
  
	
	
	$stname = mysqli_real_escape_string($con,$_POST['stname']);  
	$stuname = mysqli_real_escape_string($con,$_POST['stuname']);  
	$stpwd = mysqli_real_escape_string($con,$_POST['stpwd']);  
	$stmail = mysqli_real_escape_string($con,$_POST['stmail']);  
	$stfone = mysqli_real_escape_string($con,$_POST['stfone']);  
	$role = mysqli_real_escape_string($con,$_POST['role']); 
	
	


		 
		  $sql= "INSERT INTO lhpstaff (sname, staffname, spwd, semail, sfone, role)  VALUES ('$stuname', '$stname', '$stpwd', '$stmail', '$stfone' , '$role')";
		if(mysqli_query($con, $sql)){	
		
		$ssmessaged = 'Status : Staff Account successfully created.';
		}

      else 
      {
        $ssmessaged = 'Error Creating Staff Account' ;
      }
    }
	
$_SESSION['ssmessaged'] = $ssmessaged;
header("Location: mgstaff.php");

?>