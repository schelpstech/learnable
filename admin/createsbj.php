<?php

include "conf.php";


if (isset($_POST['createsb']) && $_POST['createsb'] == 'Create Subject')
{
  
	$sbclass = mysqli_real_escape_string($con,$_POST['sbjclass']);  
	$sbname = mysqli_real_escape_string($con,$_POST['sbjname']);  
	
	
	$sql = "SELECT classname FROM lhpclass WHERE classid ='$sbclass'";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
	$cname =$row["classname"];
  }
} 


		 
		  $sql= "INSERT INTO lhpsubject (sbjname, classid, classname)  VALUES ( '$sbname', '$sbclass', '$cname')";
		if(mysqli_query($con, $sql)){	
		
		$clmessage = 'Status : Subject successfully created.';
		}

      else 
      {
        $clmessage = 'Error Creating Subject' ;
      }
    }
	
$_SESSION['clmessage'] = $clmessage;
header("Location: csubj.php");

?>