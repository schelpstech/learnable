<?php
include "conf.php";

if(!empty($_GET['ref'])) {         
        $ref = $_GET["ref"];
}
if(!empty($_GET['id'])) {         
    $id = $_GET["id"];
}
if(!empty($_GET['sbjid'])) {         
        $sbjid = $_GET["sbjid"];
      
}
if(!empty($_GET['term'])) {         
        $term = $_GET["term"];
    
}
if(!empty($_GET['cid'])) {         
        $cid = $_GET["cid"];
       
}
    
  $sql = "SELECT midterm FROM lhpresultconfig WHERE term  = '$term' ";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_assoc($result);
                $resultstatus = $row["midterm"];
                
      if($resultstatus == 0){
// sql to delete a record
$sql = "DELETE FROM `lhpweekrecord` WHERE `id` = '$ref'";

    if(mysqli_query($con, $sql)){	
		$message ='Status : Learner Weekly Score record has been deleted successfully';
		}

      else 
      {
        $message = 'Status : Error Deleting Weekly Score Record .';
      }
      }

      else 
      {
        $message = 'Status : Mid Term Scores has been locked and can not be edited. Contact School Administrator .';
      }

$_SESSION['remessage'] = $message;
$loc = 'Location: viewweekly.php?id='.$id.'&term='.$term.'&sbjid='.$sbjid.'&cid='.$cid;
header($loc);      
      
?>