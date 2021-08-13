<?php
include "conf.php";

if(!empty($_GET['id'])) {         
        $ref = $_GET["id"];
}
if(!empty($_GET['sbjref'])) {         
    $sbjref = $_GET["sbjref"];
}
if(!empty($_GET['classref'])) {         
    $lref = $_GET["classref"];
}

$sql = "DELETE from lhpsubject  WHERE sbjid = '$ref'";
	if(mysqli_query($con, $sql)){	
		
		$message = 'Status : Successfully deleted '.$sbjref.' for '.$lref;
		}

      else 
      {
        $message = 'Error deleting  '.$sbjref.' for '.$lref ;
      }
      
$_SESSION['clmessage'] = $message;
header("Location: csubj.php");


?>
      
