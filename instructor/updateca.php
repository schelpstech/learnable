<?php

include "conf.php";


$cascore=$_POST['cascore'];
$recordid=$_POST['recordid'];
$maxscore = $_SESSION['maxca'];

$sql = "SELECT * FROM lhpresultrecord WHERE id  = '$recordid' ";
   $result=mysqli_query($con,$sql);
  $row=mysqli_fetch_array($result);
             
$examscore = $row["examscore"];

if($cascore <= $maxscore){
    $total = $cascore + $examscore;

    $sql="UPDATE `lhpresultrecord` SET 
     score = '$cascore',
     totalscore = '$total'
     where id = '$recordid'";
if ($con->query($sql) === TRUE) {
    echo " Successfully Updated CA Scores";
}
else 
{
    echo "failed";
}
}
else 
{
    echo "Continuous Assessment Score entered - ".$cascore." can't be greater than Continuous Assessment Score Obtainable - ".$maxscore;
}