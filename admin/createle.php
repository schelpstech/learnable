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
	
	$sql = "SELECT COUNT(`uname`) as CNT FROM lhpuser WHERE uname = '$stuname' ";
	$result=mysqli_query($con,$sql);
	 $row=mysqli_fetch_assoc($result);
	$exist = $row["CNT"];
	
if($exist == 0){

		 
		  $sql= "INSERT INTO lhpuser (fname, uname, upwd, email, classid, gender, dob, `status`)  VALUES ('$stname', '$stuname', '$stpwd', '$stmail', '$stclass', '$gender', '$dob', 1)";
		if(mysqli_query($con, $sql)){	
		
		$lsmessaged = 'Status : Learner Account successfully created.';
		}

      else 
      {
        $lsmessaged = 'Status : Error Creating Learning.' ;
      }
    }
	else 
	{
	  $lsmessaged = 'Status : Error Creating Learning. Username exist already.' ;
	}
}	

else 
	{
	  $lsmessaged = 'Status : Unknown request denied by firewall.' ;
	}
$_SESSION['lsmessaged'] = $lsmessaged;
header("Location: mglearners.php");

?>