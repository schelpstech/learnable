<?php
include "conf.php";

 $message ="no action";
if (isset($_POST['modifyquestion']) && $_POST['modifyquestion'] == 'Modify Assessment Details')
{
    $qid = mysqli_real_escape_string($con,$_POST['qid']);
    $due = mysqli_real_escape_string($con,$_POST['due']);
	$grade = mysqli_real_escape_string($con,$_POST['grade']);

$sql = "UPDATE lhpquestion SET grade = '$grade', deadline = '$due' WHERE questid = '$qid'";

    if(mysqli_query($con, $sql)){	
		$message ='Status : Question details has been successfully';
		}

      else 
      {
        $message = 'Status : Error modifying question.';
      }
}
$topicid =$_SESSION['topicid'];
$_SESSION['message'] = $message;
$loc = "Location: addwork.php?id=".$topicid;
header($loc);      
     
?>
        