<?php
include "conf.php";

if(!empty($_GET['id'])) {         
        $ref = $_GET["id"];
}

if(!empty($_GET['lid'])) {         
    $lref = $_GET["lid"];
}

$sql = "DELETE from lhpaffective  WHERE affid = '$ref'";
	if(mysqli_query($con, $sql)){	
		
		$message = 'Status : Successfully deleted Affective Domain and Attendance Records  for '.$lref;
		}

      else 
      {
        $message = 'Error deleting Affective Domain and Attendance Records  for '.$lref ;
      }
      
$_SESSION['remessage'] = $message;
header("Location: mgaffective.php");


?>
      
