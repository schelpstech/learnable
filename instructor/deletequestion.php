<?php
include "conf.php";

if(!empty($_GET['id'])) {         
        $noteid = $_GET["id"];
}

// sql to delete a record
$sql = "UPDATE lhpquestion SET status = 0 WHERE questid = '$noteid'";

    if(mysqli_query($con, $sql)){	
		$message ='Status : Question has been deleted successfully';
		}

      else 
      {
        $message = 'Status : Error Deleting Question.';
      }
$topicid =$_SESSION['topicid'];
$_SESSION['message'] = $message;
$loc = "Location: addwork.php?id=".$topicid;
header($loc);      
      
?>
          