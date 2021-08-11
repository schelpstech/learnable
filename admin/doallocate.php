<?php

include "conf.php";


if (isset($_POST['allocate']) && $_POST['allocate'] == 'Allocate Subject')
{
  
	$term = mysqli_real_escape_string($con,$_POST['term']);  
	$classd = mysqli_real_escape_string($con,$_POST['classd']);  
	$subject = mysqli_real_escape_string($con,$_POST['sbj']);  
	$instructor = mysqli_real_escape_string($con,$_POST['instructor']); 
	$super = mysqli_real_escape_string($con,$_POST['super']);
	
	$sql = "SELECT classname FROM lhpclass WHERE classid='$classd'";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
	$cname =$row["classname"];
  }
} 

	$sql = "SELECT sbjname FROM lhpsubject WHERE sbjid='$subject'";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
	$sbname =$row["sbjname"];
  }
} 
	
	 $sql_query = "select count(*) as cntalloc from lhpalloc where term ='".$term."' and classid ='".$classd."' and sbjid ='".$subject."'" ;
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);

        $count = $row['cntalloc'];
		
		

        if($count == 1){
           $message = 'Status : Subject Already Allocated, Kindly Modify.';
		} 
		else {
		  $sql= "INSERT INTO lhpalloc (term, classname, subject, staffid, classid, sbjid, supro)  VALUES ( '$term', '$cname', '$sbname', '$instructor', '$classd', '$subject', '$super' )";
		if(mysqli_query($con, $sql)){	
		
		$message = 'Status : Subject Successfully Allocated.';
		}

      else 
      {
        $message = 'Error Allocating Subject' ;
      }
    }
}
	
$_SESSION['clmessage'] = $message;
header("Location: allocate.php");

?>