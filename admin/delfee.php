<?php
include "conf.php";

if(!empty($_GET['ref'])) {         
        $ref = $_GET["ref"];
}


$sql = "UPDATE lhpfeelist SET status = 0 WHERE feeid = '$ref'";
	if(mysqli_query($con, $sql)){	
  }
  $sql = "UPDATE lhpassignedfee SET status = 0 WHERE feeid = '$ref'";
	if(mysqli_query($con, $sql)){	
		$feemessage = 'Status : Successfully deleted fee for all students with assigned fee.';
		}

      else 
      {
        $feemessage = 'Error deleting Fee' ;
      }
      
      $_SESSION['feemessage'] = $feemessage;
header("Location: mgfee.php");

?>
      
