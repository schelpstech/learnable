<?php
include "conf.php";


if (isset($_POST['profile']) && $_POST['profile'] == 'Modify Instructor Profile')
{
	$id = mysqli_real_escape_string($con,$_POST['edid']); 
	$named = mysqli_real_escape_string($con,$_POST['edname']);  
	$fone = mysqli_real_escape_string($con,$_POST['edfone']);  
	$pwd = mysqli_real_escape_string($con,$_POST['edpwd']);  
	$mail = mysqli_real_escape_string($con,$_POST['edmail']);
	

	
	 $sql= "UPDATE lhpstaff SET staffname = '$named', sfone = '$fone', semail = '$mail', spwd = '$pwd' WHERE sname = '$id'";
	 
	 
		if(mysqli_query($con, $sql)){	
		
		$mes = 'Status : Successfully modified Instructor Profile.';
		}

      else 
      {
        $mes ='Status : Unable to modify Instructor Profile.';
      }
    }
	
	

	$_SESSION['message'] = $mes;
	header("Location: profile.php");   
?>