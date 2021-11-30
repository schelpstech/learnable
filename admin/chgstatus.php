<?php
include "conf.php";
if (isset($_POST['chg']) && $_POST['chg'] == 'Change Status')
{
    $term = mysqli_real_escape_string($con,$_POST['term']);
    $type = mysqli_real_escape_string($con,$_POST['type']);
    $status = mysqli_real_escape_string($con,$_POST['status']);
    
    if ($type == 'Termly Result'){
        $sql= "UPDATE lhpresultconfig SET `status` = '$status' WHERE term = '$term'";
        
       if(mysqli_query($con, $sql)){	
		$remessage = 'Status : Successfully Changed Termly Result Status.';
    
    }

    else 
    {
      $remessage ='Status : Unable to Change Termly Result Status.';
   
    }
    }
    elseif ($type == 'Midterm Result'){
        $sql= "UPDATE lhpresultconfig SET midterm = '$status' WHERE term = '$term'";
       if(mysqli_query($con, $sql)){	
		$remessage = 'Status : Successfully Changed Mid-Term Result Status.';
      
    }

    else 
    {
      $remessage ='Status : Unable to Change Mid-Term Result Status.';
     
    }
    }
    ; 
    
}
$_SESSION['remessage'] = $remessage;
header("Location: mgconfig.php");       
?>
