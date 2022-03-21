<?php

include "conf.php";

$termid=$_POST['term'];
$classid=$_POST['classid'];
$learnerid=$_POST['learnerid'];
$present =$_POST['present'];
$lead=$_POST['ratingl'];
$elo=$_POST['ratinge'];
$neat = $_POST['ratingn'];
$create = $_POST['ratingc'];
$response = $_POST['ratingr'];
$comment = $_POST['comment'];

 $sql_query = "select count(affid) as rating from `lhpaffective` where term ='$termid' AND uname = '$learnerid' ";
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['rating'];
		
		

        if($count == 0){

$sql="INSERT INTO `lhpaffective` (term, uname, classid, total_present, rating1, rating2, rating3, rating4, rating5, comment ) VALUES ('$termid', '$learnerid', '$classid', '$present', '$lead', '$elo', '$neat', '$create', '$response', '$comment')";

if ($con->query($sql) === TRUE) {
    echo " Successfully Recorded Affective Domain Rating and Attendance records";
}
else 
{
    echo "failed";
}
}



else 
{
    echo "Affective Domain Rating and Attendance records has already been recorded for learner with ID : ".$learnerid.". Kindly contact school admin to modify. ";
}

?>