<?php
include "conf.php";

if(!empty($_GET['id'])) {         
        $noteid = $_GET["id"];
}

// sql to delete a record
$sql = "UPDATE lhpnote SET status = 0 WHERE noteid = '$noteid'";

    if(mysqli_query($con, $sql)){	
		$message ='Status : Note has been deleted successfully';
		}

      else 
      {
        $message = 'Status : Error Deleting Note.';
      }
$topicid =$_SESSION['topicid'];
$_SESSION['message'] = $message;
$loc = "Location: addbook.php?id=".$topicid;
header($loc);      
      
?>
          