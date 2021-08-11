<?php
include "conf.php";


if (isset($_POST['paybill']) && $_POST['paybill'] == '  Record Payment for  Selected Learner  ')
{
  


	$classname = mysqli_real_escape_string($con,$_POST['classname']);  
	$learner = mysqli_real_escape_string($con,$_POST['learner']); 
	$term = mysqli_real_escape_string($con,$_POST['term']);
	$mode = mysqli_real_escape_string($con,$_POST['mode']);  
    $reference = mysqli_real_escape_string($con,$_POST['payref']); 
    $dated = mysqli_real_escape_string($con,$_POST['paydate']);
     $status = mysqli_real_escape_string($con,$_POST['status']);
      $amount = mysqli_real_escape_string($con,$_POST['amountpaid']);
	if ($amount != "" && $reference != "" && $mode != ""){
         $stat = 0;
	     $sql = "SELECT count('assid') FROM `lhptransaction` WHERE term  = '$term' AND classid = '$classname' AND stdid = '$learner' AND reference = '$reference'  AND status = 1";
			$result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        $count = "$row[0]";

        if($count == $stat){

$sql = "INSERT into `lhptransaction` (term, classid, stdid, reference, mode, paydate, amount, status)

VALUES('$term', '$classname', '$learner', '$reference', '$mode', '$dated', '$amount', $status)";
				if(mysqli_query($con, $sql)){	
		
		$feemessage = 'Status : Successfully recorded payment for the selected learner';
		}
		else{
		    $feemessage = 'Status : Unable to record payment for the selected learner';
		}
}
else{
		    $feemessage = 'Status : Unable to  record payment for selected learner. Transaction reference has already been used for another  learner';
		}
}

else{
		    $feemessage = 'Status :Unable to  record payment. Kindly provide all required information';
		}
}

else{
		    $feemessage = 'Status : Unable to  record paymnent. No action Requested';
		}

$_SESSION['feemessage'] = $feemessage;
header("Location: payrecord.php");

?>