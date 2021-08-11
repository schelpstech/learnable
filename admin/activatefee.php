<?php
include ("conf.php");

if(!empty($_GET['ref'])) {
        $ref = $_GET["ref"];
        
        $sql = "UPDATE lhpassignedfee SET status = 1 where assid = $ref";
        	if(mysqli_query($con, $sql)){	
		
		$feemessage = 'Status : Successfully activated this assigned fee.';
		}

      else 
      {
        $feemessage = 'Error activating Fee' ;
      }
      
      $_SESSION['feemessage'] = $feemessage;
header("Location: assignfee.php");
}
        
?>