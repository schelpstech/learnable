<?php
include "conf.php";

if(!empty($_GET['ref'])) {         
        $ref = $_GET["ref"];
}

if (isset($_POST['editfee']))
{
  

	
	$term = mysqli_real_escape_string($con,$_POST['term']);  
	$feeclass = mysqli_real_escape_string($con,$_POST['feeclass']);  
	$fname = mysqli_real_escape_string($con,$_POST['feename']);  
	$feeamount = mysqli_real_escape_string($con,$_POST['feeamount']);

	  
		
	
	if ($term != "" && $feeclass != "" && $fname != "" && $feeamount != "" ){
	    
	  $fename = strtoupper($fname);  
	  $nref = preg_replace("/\s+/", "", $fename);
    $feename = str_replace("/", "", $nref);
	  
            
	    
            
	
	
		 
 $sql= "UPDATE  lhpfeelist SET feename = '$feename', amount = '$feeamount' WHERE feeid = '$ref'";
		if(mysqli_query($con, $sql)){	
    }
    $sql= "UPDATE  lhpassignedfee SET  amount = '$feeamount' WHERE feeid = '$ref'";
		if(mysqli_query($con, $sql)){	
    

		$feemessage = 'Status : Successfully modified  '. $feename. ' fee for selected class.';
  }

      else 
      {
        $feemessage = 'Error Modifying Fee' ;
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