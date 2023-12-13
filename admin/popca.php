<?php
include "conf.php";

if(!empty($_GET['term'])) {         
        $ref = $_GET["term"];
}


$sql = "UPDATE lhpresultrecord lr
JOIN (
    SELECT
        lid,
        subjid,
        (SUM(score) / COUNT(id)) * 3 AS calculated_score
    FROM
        lhpweekrecord
    WHERE
        term = '$ref'
    GROUP BY
        lid,
        subjid
) ag ON lr.lid = ag.lid AND lr.subjid = ag.subjid
SET
    lr.score = ag.calculated_score,
    lr.totalscore = ag.calculated_score + lr.examscore
WHERE
    lr.term = '$ref'";
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
      
