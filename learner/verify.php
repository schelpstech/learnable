<?php
include "conf.php";

if (isset($_POST['verify']) && $_POST['verify'] == 'Check_Result')
{
  
	
	
	$rname = mysqli_real_escape_string($con,$_POST['learner']);  
	$rterm = mysqli_real_escape_string($con,$_POST['termd']);  
	$rpin = mysqli_real_escape_string($con,$_POST['pin']);  
	
	//verify pin if it is correct
	
	$sql = " SELECT * from archive WHERE learn = '$rname' AND term = '$rterm'";
		$result = mysqli_query($con, $sql);
		if (mysqli_num_rows($result) > 0) {
  // output data of each row
	while($row = mysqli_fetch_assoc($result)) {
    
	$pinre =$row["pinref"];
	$loc = $row["refdoc"];
	$val = '1';
  }
  
		if ($pinre == $rpin) {
	  $sql =" UPDATE  archive SET status = '$val'  WHERE pinref =  '$rpin' ";
			if(mysqli_query($con, $sql)){	
		
			$res ='Status : Pin is Valid to Check the '.$rterm.' result';
		}

      else 
      {
        $res =' Status: ' .$rpin.' is an invalid Pin, Kindly Contact the school for the correct Pin';
      }
  }
	  
	else 
      {
        $res = 'Status : Result for the Selected Term hasnt been published yet, Kindly Contact the school' ;
      }
	  
		}
	  
	  
				
}

else 
      {
        $res = 'Status : Error in details, Kindly Contact the school' ;
      }
	
$_SESSION['res'] = $res;
$_SESSION['linked'] = $loc;
header("Location: chkresult.php");

?>