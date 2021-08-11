<?php
include "conf.php";

if(!empty($_GET['recordid'])) {         
        $recordid = $_GET["recordid"];
}

if(!empty($_GET['sbj'])) {         
        $sbjid = $_GET["sbj"];
      
}
if(!empty($_GET['term'])) {         
        $term = $_GET["term"];
    
}
if(!empty($_GET['classid'])) {         
        $classid = $_GET["classid"];
       
}
    
  $sql = "SELECT status FROM lhpresultconfig WHERE term  = '$term' ";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
                $resultstatus = $row["status"];
                
      if($resultstatus == 0){
// sql to delete a record
$sql = "DELETE FROM `lhpresultrecord` WHERE `id` = '$recordid'";

    if(mysqli_query($con, $sql)){	
		$message ='Status : Learner Score record has been deleted successfully';
		}

      else 
      {
        $message = 'Status : Error Deleting Score Record .';
      }
      }

      else 
      {
        $message = 'Status : CA Scores has been locked and can not be edited. Contact School Administrator .';
      }

$_SESSION['remessage'] = $message;
$loc = 'Location: recordca.php?sbj='.$sbjid.'term='.$term.'&classid='.$classid;
header($loc);      
      
?>