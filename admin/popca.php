<?php
include "conf.php";

if(!empty($_GET['term'])) {         
        $ref = $_GET["term"];
}


$sql = "UPDATE lhpresultrecord SET 
lhpresultrecord.score = ( SELECT ((SUM(lhpweekrecord.score) / COUNT(lhpweekrecord.id))*3) FROM lhpweekrecord WHERE lid = lhpresultrecord.lid and subjid = lhpresultrecord.subjid and lhpresultrecord.term = '$ref'), 
lhpresultrecord.totalscore =  (( SELECT ((SUM(lhpweekrecord.score) / COUNT(lhpweekrecord.id))*3) FROM lhpweekrecord where lid = lhpresultrecord.lid and subjid = lhpresultrecord.subjid and lhpresultrecord.term = '$ref') + lhpresultrecord.examscore)   
WHERE lid = lhpresultrecord.lid and subjid = lhpresultrecord.subjid and term = '$ref'";
	if(mysqli_query($con, $sql)){	
		
		$message = 'Status : Successfully populated Continuous Assessment Scores From Weekly Assement Records for '.$ref;
		}

      else 
      {
        $message = 'Error populating Continuous Assessment Scores From Weekly Assement Records  for '.$ref ;
      }
      
$_SESSION['remessage'] = $message;
header("Location: mgconfig.php");


?>
      
