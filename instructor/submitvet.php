<?php

include "conf.php";

$userid=$_POST['user'];
$noteid=$_POST['note'];
$msg=$_POST['message'];


if($msg != ""){
    
$sql="INSERT INTO `lhpvet` ( userid, noteid, msg) VALUES ('$userid', '$noteid', '$msg')";

if ($con->query($sql) === TRUE) {
    echo " Message Sent";
}
else 
{
    echo "Message failed";
}
}


else 
   
{
    echo "Feedback cannot be empty";
}
?>
