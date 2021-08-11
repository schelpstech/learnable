<?php

include "conf.php";

if(!empty($_GET['ref'])) {         
        $ref = $_GET["ref"];
}
if (isset($_POST['editalloc']))
{
    
	$instructor = mysqli_real_escape_string($con,$_POST['allinstructor']); 
		$supervisor = mysqli_real_escape_string($con,$_POST['supervisor']); 
	
		  $sql= "UPDATE lhpalloc SET staffid = '$instructor', supro = '$supervisor' where aid ='$ref'";
		if(mysqli_query($con, $sql)){	
		
		$message = 'Status : Subject  Allocated Successfully Modified.';
		}

      else 
      {
        $message = 'Error Modifying Subject Allocating ' ;
      }
    
}
	
$_SESSION['clmessage'] = $message;
header("Location: allocate.php");

?>