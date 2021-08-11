<?php
include "conf.php";

$clmessage = 'Status : Classteacher  not assigned.';

	$allclass = mysqli_real_escape_string($con,$_POST['curclass']);  
	$supervisor = mysqli_real_escape_string($con,$_POST['supervisor']);  
	$term = mysqli_real_escape_string($con,$_POST['term']); 
	


		 
		  $sql= "INSERT INTO lhpclassalloc (classid, tutorid, term )  VALUES ('$allclass', '$supervisor', '$term')";
		if(mysqli_query($con, $sql)){	
		
		$clmessage = 'Status : Classteacher  successfully assigned.';
		}

      else 
      {
        $clmessage = 'Error assigning Class teacher' ;
      }
    
	
$_SESSION['clmessage'] = $clmessage;
header("Location: cclass.php");

?>