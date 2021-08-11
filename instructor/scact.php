<?php
include "conf.php";

$messaged = 'NOTIUN'; 
if (isset($_POST['submitsc']) && $_POST['submitsc'] == 'create')
{

  
	
	$lstname = mysqli_real_escape_string($con,$_POST['stname']);
    $lclassd = mysqli_real_escape_string($con,$_POST['classd']);
	$lterm = mysqli_real_escape_string($con,$_POST['term']);
	$lstdate = mysqli_real_escape_string($con,$_POST['schstdate']);
	$lsttime = mysqli_real_escape_string($con,$_POST['schsttime']);
	$lspdate = mysqli_real_escape_string($con,$_POST['schspdate']);
	$lsptime = mysqli_real_escape_string($con,$_POST['schsptime']);
	$lschact = mysqli_real_escape_string($con,$_POST['scact']);
	$lschdetails = mysqli_real_escape_string($con,$_POST['scdetails']);
	$lsfone = mysqli_real_escape_string($con,$_POST['sfone']);
	
	

$sql = "SELECT classname FROM lhpclass WHERE classid='$lclassd'";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
	$cname =$row["classname"];
  }
} 


		  $sql= "INSERT INTO schedule  (schdate, schact, start_time, schdetails, stop_time, scterm, scclass, stp_date, scfone, classname,scstaff) 
		  VALUES ('$lstdate', '$lschact', '$lsttime', '$lschdetails', '$lsptime', '$lterm','$lclassd' ,'$lspdate', '$lsfone' ,'$cname','$lstname' )";
		if(mysqli_query($con, $sql)){	
		$messaged ='Status : Schedule successfully created.';
		}

      else 
      {
        $messaged = 'Status : There was some error in writing your Schedule on server.';
      }
    	
  
}
$_SESSION['messaged'] = $messaged;
header("Location: timeline.php");

?>