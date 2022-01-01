<?php
include "conf.php";


if (isset($_POST['cfee']) && $_POST['cfee'] == 'Create Fee For Selected Class')
{
  

	
	$session = mysqli_real_escape_string($con,$_POST['session']);  
	$feeclass1 = mysqli_real_escape_string($con,$_POST['feeclass1']); 
	$feeclass2 = mysqli_real_escape_string($con,$_POST['feeclass2']);
	$fname = mysqli_real_escape_string($con,$_POST['feename']);  
	$feeamount = mysqli_real_escape_string($con,$_POST['feeamount']);

    if($feeclass1 != ""){
        $feeclass = $feeclass1;}
        else{
        $feeclass = $feeclass2;}    
        
	  
		
	
	if ($session != "" && $feeclass != "" && $fname != "" && $feeamount != "" ){
	    
	  $fename = strtoupper($fname);  
	  $nref = preg_replace("/\s+/", "", $fename);
    $feename = str_replace("/", "", $nref);
	  $stat = 0;
            
	     $sql = "SELECT count('feeid') FROM lhpfeelist WHERE `session`  = '$session' AND classid = '$feeclass' AND feename = '$feename'";
			$result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        $count = "$row[0]";

        if($count == $stat){
            
	
	
		 
		  $sql= "INSERT INTO  lhpfeelist (feename, `session`, classid, amount, status)  VALUES ('$feename','$session','$feeclass', '$feeamount', 1)";
		if(mysqli_query($con, $sql)){	
		
		$feemessage = 'Status : Successfully created '. $feename. ' fee for selected class.';
		}

      else 
      {
        $feemessage = 'Error Creating Fee' ;
      }
      
        }
	else 
      {
        $feemessage = 'Fee already exist. Kindly check selected class or Fee name' ;
      }
    }
	else 
      {
        $feemessage = 'Incomplete Fee Details' ;
      }
}
$_SESSION['feemessage'] = $feemessage;
header("Location: mgfee.php");

?>