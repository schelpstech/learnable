<?php

include "conf.php";


$examscore = $_POST['examscore'];
$recordid = $_POST['recordid'];
$maxscore = $_SESSION['maxexam'];

$sql = "SELECT * FROM lhpresultrecord WHERE id  = '$recordid' ";
   $result=mysqli_query($con,$sql);
  $row=mysqli_fetch_array($result);
             
$score = $row["score"];

if($examscore <= $maxscore){
    $total = $score + $examscore;

    $sql="UPDATE `lhpresultrecord` SET 
     examscore = '$examscore',
     totalscore = '$total'
     where id = '$recordid'";
if ($con->query($sql) === TRUE) {
    echo " Successfully Updated Exam Scores";
}
else 
{
    echo "failed";
}
}
else 
{
    echo "Exam Score entered - ".$examscore." can't be greater than Exam Score Obtainable - ".$maxscore;
}