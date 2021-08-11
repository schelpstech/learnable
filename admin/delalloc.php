<?php
include "conf.php";

if(!empty($_GET['ref'])) {         
        $ref = $_GET["ref"];
}


$sql = "DELETE from lhpalloc  WHERE aid = '$ref'";
	if(mysqli_query($con, $sql)){	
		
		$message = 'Status : Successfully deleted Subject Allocation.';
		}

      else 
      {
        $message = 'Error deleting Subject Allocation.' ;
      }
      
$_SESSION['clmessage'] = $message;
header("Location: allocate.php");


?>
      
