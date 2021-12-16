<?php

include "conf.php";

$name=$_POST['nameid'];
$subjectid=$_POST['subject'];
$cascore=$_POST['cascore'];
$classid=$_POST['classid'];
$termid=$_POST['term'];
$maxscore = $_SESSION['maxca'];

if($cascore <= $maxscore){
    
 $sql_query = "select count(*) as cntcascore from lhpresultrecord where term ='$termid' AND subjid = '$subjectid' AND lid = '$name' AND classid = '$classid' ";
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntcascore'];
		
		

        if($count == 0){

$sql="INSERT INTO `lhpresultrecord` (term, classid, subjid, totalscore, score, lid) VALUES ('$termid', '$classid', '$subjectid', '$cascore', '$cascore', '$name')";

if ($con->query($sql) === TRUE) {
    echo " Successfully Recorded Continuous Assessment Score";
}
else 
{
    echo "failed";
}
}


else 
{
   $sql_query = "select * from lhpresultrecord where term ='$termid' AND subjid = '$subjectid' AND lid = '$name' AND classid = '$classid' ";
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $casscore = $row['score'];
        $examsscore = $row['examscore'];
        $total = $cascore + $examsscore;
        
        if($casscore == 0){
$sql="UPDATE lhpresultrecord set score = '$cascore', totalscore = '$total'  where term ='$termid' AND subjid = '$subjectid' AND lid = '$name' AND classid = '$classid' ";

if ($con->query($sql) === TRUE) {
    echo "Continuous Assessment Scores Successfully Recorded";
}
else 
{
    echo "failed update";
}
}
else 
{
    echo "Continuous Assessment Score has already been recorded for learner with ID : ".$name.". Kindly contact school admin to modify Continuous Assessment Score. ";
}
}
}
else 
{
    echo "Continuous Assessment Score entered - ".$cascore." can't be greater than Continuous Assessment Score Obtainable - ".$maxscore;
}
?>