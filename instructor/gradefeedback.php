<?php
include "conf.php";

 $message ="no action";
if (isset($_POST['grade']) && $_POST['grade'] == 'Grade Assessment Submission')
{
    $fid = mysqli_real_escape_string($con,$_POST['fid']);
    $qid = mysqli_real_escape_string($con,$_POST['qid']);
    $response = mysqli_real_escape_string($con,$_POST['response']);
	$graded = mysqli_real_escape_string($con,$_POST['graded']);

$sql = "UPDATE lhpfeedback SET score = '$graded', feedback = '$response' WHERE fid = '$fid'";

    if(mysqli_query($con, $sql)){	
		$message ='Status : Feedback has been successfully added';
		}

      else 
      {
        $message = 'Status : Error adding feedback.';
      }
}

$_SESSION['message'] = $message;
$loc = "Location: submission.php?qid=".$qid;
header($loc);      
     
?>
        