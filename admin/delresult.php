<?php
include "conf.php";

if(!empty($_GET['ref'])) {         
        $ref = $_GET["ref"];
}


$sql = "DELETE from archive  WHERE refid = '$ref'";
	if(mysqli_query($con, $sql)){	
		
		$message = 'Status : Successfully deleted Report Sheet.';
		}

      else 
      {
        $message = 'Error deleting Report Sheet.' ;
      }
      
$_SESSION['remessage'] = $message;
header("Location: mgreport.php");


?>
      
   
