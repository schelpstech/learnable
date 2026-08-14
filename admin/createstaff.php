<?php
include "conf.php";


if (isset($_POST['createst']) && $_POST['createst'] == 'Create Staff Account')
{
  
	
	
	$stname = mysqli_real_escape_string($con,$_POST['stname']);  
	$stuname = mysqli_real_escape_string($con,$_POST['stuname']);  
	$plainPassword = isset($_POST['stpwd']) ? (string) $_POST['stpwd'] : '';
	$stpwd = strlen($plainPassword) >= 8 ? mysqli_real_escape_string($con, password_hash($plainPassword, PASSWORD_DEFAULT)) : '';
	$stmail = mysqli_real_escape_string($con,$_POST['stmail']);  
	$stfone = mysqli_real_escape_string($con,$_POST['stfone']);  
	$role = mysqli_real_escape_string($con,$_POST['role']); 
		if ($stpwd === '') {
			$ssmessaged = 'Password must contain at least 8 characters.';
			$_SESSION['ssmessaged'] = $ssmessaged;
			header('Location: mgstaff.php');
			exit;
		}

	


		 
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
