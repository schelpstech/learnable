<?php
include "conf.php";


if (isset($_POST['createl']) && $_POST['createl'] == 'Create Learner Account')
{
  
	
	
	$stname = mysqli_real_escape_string($con,$_POST['lname']);  
	$stuname = mysqli_real_escape_string($con,$_POST['luname']);  
	$stpwd = mysqli_real_escape_string($con,$_POST['lpwd']);  
	$stmail = mysqli_real_escape_string($con,$_POST['lmail']);  
	$stclass = mysqli_real_escape_string($con,$_POST['lclass']);  
	$gender = mysqli_real_escape_string($con,$_POST['gender']);  
	$dob = mysqli_real_escape_string($con,$_POST['dob']);  
	


		 
		  $sql= "INSERT INTO lhpuser (fname, uname, upwd, email, classid, gender, dob)  VALUES ('$stname', '$stuname', '$stpwd', '$stmail', '$stclass', '$gender', '$dob')";
		if(mysqli_query($con, $sql)){	
		
		$lsmessaged = 'Status : Learner Account successfully created.';
		}

      else 
      {
        $lsmessaged = 'Learner Creating Staff Account' ;
      }
    }
	
$_SESSION['lsmessaged'] = $lsmessaged;
header("Location: mglearners.php");

?>