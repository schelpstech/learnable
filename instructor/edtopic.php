<?php
include "conf.php";

if (isset($_POST['edtopic'])){
  

	
	$topicid = mysqli_real_escape_string($con,$_POST['topicid']);  
	$topic = mysqli_real_escape_string($con,$_POST['topic']); 
	 $week = mysqli_real_escape_string($con,$_POST['week']);  
	 $term = mysqli_real_escape_string($con,$_POST['tterm']); 
	 $cname = mysqli_real_escape_string($con,$_POST['tclass']); 
	 $sbj = mysqli_real_escape_string($con,$_POST['tsbj']); 
		
	
	if ($topic != "" && $topicid != ""  ){
	   
            
	
	
		 
 $sql= "UPDATE  lhpscheme SET topic = '$topic', week = '$week' WHERE schmid = '$topicid'";
		if(mysqli_query($con, $sql)){	
		
		
		$feemessage = 'Status : Successfully changed topic to  '. $topic.' scheduled for '.$week;
		}

      else 
      {
        $feemessage = 'Error Modifying Topic' ;
      }
      
        }
		
	else 
      {
        $feemessage = 'Incomplete Topic Details' ;
      }
    }
	else 
      {
        $feemessage = 'Error Modifying Topic' ;
      }

$_SESSION['message'] = $feemessage;
$loc = 'Location: viewscheme.php?tmd='.$term.'&cls='.$cname.'&sbj='.$sbj;
header($loc);

?>