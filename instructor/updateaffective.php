<?php

include "conf.php";

$termid=$_POST['term'];
$affid=$_POST['affid'];
$classid=$_POST['classid'];
$learnerid=$_POST['learnerid'];
$present =$_POST['present'];
$lead=$_POST['ratingl'];
$elo=$_POST['ratinge'];
$neat = $_POST['ratingn'];
$create = $_POST['ratingc'];
$response = $_POST['ratingr'];
$comment = $_POST['comment'];




$sql="UPDATE `lhpaffective` SET  total_present = '$present', 
comment = '$comment', 
rating1 = '$lead',
rating2 = '$elo', 
rating3 = '$neat', 
rating4 = '$create' , 
rating5 = '$response' where affid = '$affid'";

if ($con->query($sql) === TRUE) {
    echo " Successfully Updated Affective Domain Rating and Attendance records";
}
else 
{
    echo "failed";
}


?>