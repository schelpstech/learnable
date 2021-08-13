<?php
include "conf.php";

if(!empty($_GET['id'])) {         
        $ref = $_GET["id"];
}
if(!empty($_GET['sbjid'])) {         
    $sbjref = $_GET["sbjid"];
}
if(!empty($_GET['lid'])) {         
    $lref = $_GET["lid"];
}

$sql = "DELETE from lhpresultrecord  WHERE id = '$ref'";
	if(mysqli_query($con, $sql)){	
		
		$message = 'Status : Successfully deleted scores records in '.$sbjref.' for '.$lref;
		}

      else 
      {
        $message = 'Error deleting Scores record '.$sbjref.' for '.$lref ;
      }
      
$_SESSION['remessage'] = $message;
header("Location: mgresult.php");


?>
      
