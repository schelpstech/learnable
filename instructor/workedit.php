<?php
include "conf.php";


if (isset($_POST['edtlsn']) && $_POST['edtlsn'] == 'Modify Learning Material Details')
{
	$sn = mysqli_real_escape_string($con,$_POST['lsnid']); 
	$snterm = mysqli_real_escape_string($con,$_POST['lsnterm']);  
	$snwk = mysqli_real_escape_string($con,$_POST['lsnweek']);  
	$snclass = mysqli_real_escape_string($con,$_POST['lsnclass']);  
	$snsbj = mysqli_real_escape_string($con,$_POST['lsnsubject']);
	$snact = mysqli_real_escape_string($con,$_POST['lsnact']);
	$sntopic = mysqli_real_escape_string($con,$_POST['lsntopic']);
	$sndate = mysqli_real_escape_string($con,$_POST['lsndate']);
	
	
	$sql = "SELECT classname FROM lhpclass WHERE classid='$snclass'";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
	$cname =$row["classname"];
  }
}
	
	 $sql= "UPDATE classact SET term = '$snterm', week = '$snwk', classid = '$snclass', classname = '$cname', actdate = '$sndate', subject = '$snsbj', topic = '$sntopic', activity = '$snact' WHERE id = '$sn'";
	 
	 
		if(mysqli_query($con, $sql)){	
		
		$mes = 'Status : Successfully modified learning material details.';
		}

      else 
      {
        $mes ='Status : Unable to modify learning material details.';
      }
    }
	
	

	$_SESSION['message'] = $mes;
	header("Location: upload.php");   
?>