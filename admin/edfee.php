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
	  $stat = 0;
            
	     $sql = "SELECT count('feeid') FROM lhpfeelist WHERE term  = '$term' AND classid = '$feeclass' AND feename = '$feename'";
			$result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        $count = "$row[0]";

        if($count == $stat){
            
	
	
		 
 $sql= "UPDATE  lhpfeelist SET feename = '$feename', amount = '$feeamount' WHERE feeid = '$ref'";
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