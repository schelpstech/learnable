<?php
include "conf.php";


if (isset($_POST['createclass']) && $_POST['createclass'] == 'Create Class')
{
  
	
	
	$clname = mysqli_real_escape_string($con,$_POST['crclass']);  
	
	
	


		 
		  $sql= "INSERT INTO lhpclass (classname)  VALUES ('$clname')";
		if(mysqli_query($con, $sql)){	
		
		$clmessage = 'Status : Class successfully created.';
		}

      else 
      {
        $clmessage = 'Error Creating Class' ;
      }
    }
	
$_SESSION['clmessage'] = $clmessage;
header("Location: cclass.php");

?>