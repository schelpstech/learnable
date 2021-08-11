<?php

include "conf.php";

$name=$_POST['nameid'];
$subjectid=$_POST['subject'];
$examscore=$_POST['examscore'];
$classid=$_POST['classid'];
$termid=$_POST['term'];
$maxscore = $_SESSION['maxexam'];

if($examscore <= $maxscore){
    
 $sql_query = "select count(*) as cntexamscore from lhpresultrecord where term ='$termid' AND subjid = '$subjectid' AND lid = '$name' AND classid = '$classid' ";
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntexamscore'];
		
		

        if($count == 0){

$sql="INSERT INTO `lhpresultrecord` (term, classid, subjid, totalscore, examscore, lid) VALUES ('$termid', '$classid', '$subjectid', '$examscore', '$examscore', '$name')";

if ($con->query($sql) === TRUE) {
    echo " Successfully Recorded Exam Score";
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
        $total = $casscore + $examscore;
        
        if($examsscore == 0){
$sql="UPDATE lhpresultrecord set examscore = '$examscore', totalscore = '$total'  where term ='$termid' AND subjid = '$subjectid' AND lid = '$name' AND classid = '$classid' ";

if ($con->query($sql) === TRUE) {
    echo "Exam Scores Successfully Recorded";
}
else 
{
    echo "failed update";
}
}
else 
{
    echo "Exam Score has already been recorded for learner with ID : ".$name.". Kindly contact school admin to modify Exam Score. ";
}
}
}
else 
{
    echo "Exam Score entered - ".$examscore." can't be greater than Exam Score Obtainable - ".$maxscore;
}
?>