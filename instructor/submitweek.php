<?php

include "conf.php";

$name=$_POST['nameid'];
$subjectid=$_POST['subject'];
$cascore=$_POST['cascore'];
$classid=$_POST['classid'];
$termid=$_POST['term'];
$weekid =$_POST['week'];
$maxscore = 10;

if($cascore <= $maxscore){
    
 $sql_query = "select count(*) as cntcascore from `lhpweekrecord` where term ='$termid' AND subjid = '$subjectid' AND lid = '$name' AND classid = '$classid' AND week = '$weekid' ";
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntcascore'];
		
		

        if($count == 0){

$sql="INSERT INTO `lhpweekrecord` (term, classid, subjid, score, lid, week) VALUES ('$termid', '$classid', '$subjectid', '$cascore', '$name', '$weekid')";

if ($con->query($sql) === TRUE) {
    echo " Successfully Recorded Weekly Assessment Score";
}
else 
{
    echo "failed to record weekly assessment";
}
}

else 
{
    echo "Weekly Assessment Score has already been recorded for learner with ID : ".$name.". Kindly contact school admin to modify Continuous Assessment Score. ";
}

}
else 
{
    echo "Weekly Assessment Score entered - ".$cascore." can't be greater than Weekly Assessment Mark Obtainable - ".$maxscore;
}
?>