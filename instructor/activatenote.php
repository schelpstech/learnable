<?php
include "conf.php";

if(!empty($_GET['ref'])) {         
        $ref = $_GET["ref"];
}
if(!empty($_GET['subject'])) {         
  $subject = $_GET["subject"];

}
if(!empty($_GET['sbjid'])) {         
  $sbjid = $_GET["sbjid"];

}
if(!empty($_GET['term'])) {         
  $term = $_GET["term"];

}
if(!empty($_GET['teacher'])) {         
  $teacherid = $_GET["teacher"];
 
}

$sql = "UPDATE lhpnote SET vet = 1 WHERE noteid = '$ref'";
	if(mysqli_query($con, $sql)){	
		
		$message = 'Status : Successfully activated note';
		}

      else 
      {
        $message = 'Error activating note' ;
      }
      
$_SESSION['remessage'] = $message;
header('Location: viewlessonnote.php?teacher='.$teacherid.'&term='.$term.'&sbjid='.$sbjid.'&subject='.$subject);


?>
      
