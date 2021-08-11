<?php
include "conf.php";

if(!empty($_GET['refid'])) {         
        $ref = $_GET["refid"];
}


$sql = "DELETE from lhpclassalloc  WHERE classlocid = '$ref'";
	if(mysqli_query($con, $sql)){	
		
		$clmessage = 'Status : Successfully deleted Class Allocation.';
		}

      else 
      {
        $clmessage = 'Error deleting Class Allocation.' ;
      }
      
      $_SESSION['clmessage'] = $clmessage;
      header("Location: cclass.php");


?>
    