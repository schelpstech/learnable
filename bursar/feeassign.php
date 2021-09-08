<?php
include "conf.php";


if (isset($_POST['assign']) && $_POST['assign'] == 'Assign Fee to Selected Customer')
{
  

	$type = mysqli_real_escape_string($con,$_POST['type']);  
	$classname = mysqli_real_escape_string($con,$_POST['classname']);  
	$learner = mysqli_real_escape_string($con,$_POST['learner']); 
	$term = mysqli_real_escape_string($con,$_POST['term']);
	$feeid = mysqli_real_escape_string($con,$_POST['feeid']);  
    $feedate = mysqli_real_escape_string($con,$_POST['feedate']); 
    $feeamounta = mysqli_real_escape_string($con,$_POST['feeamounta']);
	$feeamountb = mysqli_real_escape_string($con,$_POST['feeamountb']);
if($feeamounta != ""){
$feeamount = $feeamounta;
}
if($feeamountb != ""){
	$feeamount = $feeamountb;
	}
	if ($type != "" && $term != "" && $feeid != ""){
	    
	    
	    
	    	if ($type == "Learner-Based"){
	    	    
	    	    $stat = 0;
            
	     $sql = "SELECT count('assid') FROM `lhpassignedfee` WHERE term  = '$term' AND classid = '$classname' AND stdid = '$learner' AND feeid = '$feeid'  AND status = 1";
			$result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        $count = "$row[0]";

        if($count == $stat){

$sql = "INSERT into `lhpassignedfee` (feeid, classid, stdid, term, type, due, amount, status)

VALUES('$feeid', '$classname', '$learner', '$term', '$type', '$feedate', '$feeamount', 1)";
				if(mysqli_query($con, $sql)){	
		
		$feemessage = 'Status : Successfully assigned fee to the selected learner';
		}
		else{
		    $feemessage = 'Status : Unable to assigned fee to the selected learner';
		}
}
else{
		    $feemessage = 'Status : Unable to assign fee as this selected Fee has already been assigned to the selected learner';
		}
}

	    	elseif ($type == "Class-Based"){
	    	    
	    	   require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpuser WHERE classid  = '$classname' AND status = 1";
$assignclass = $db_handle->runQuery($query);
foreach ($assignclass as $std) {
    $stdid = $std["uname"];
      $stat = 0;
            
	     $sql = "SELECT count('assid') FROM `lhpassignedfee` WHERE term  = '$term' AND classid = '$classname' AND stdid = '$stdid' AND feeid = '$feeid'  AND status = 1";
			$result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        $count = "$row[0]";

        if($count == $stat){
    
    $sql = "INSERT into `lhpassignedfee` (feeid, classid, stdid, term, type, due, amount, status)

VALUES('$feeid', '$classname', '$stdid', '$term', '$type', '$feedate', '$feeamount', 1)";
				if(mysqli_query($con, $sql)){	
		
		$feemessage = 'Status : Successfully assigned fee to all learners in the class selected';
		}
		else{
		    $feemessage = 'Status : Unable to assigned fee to learners in selected class';
		}

        }
else{
		    $feemessage = 'Status : Unable to assign fee to the selected class. This fee has already been assigned to some learners. Kindly assign fee individually for learners in the selected class.';
		}            
        }

}

	    	elseif ($type == "School-Based"){
	    	    
	    	   require_once ("DBController.php");
$db_handle = new DBController();
$query = "SELECT * FROM lhpuser where status = 1";
$assignclass = $db_handle->runQuery($query);
foreach ($assignclass as $std) {
    $stdid = $std["uname"];
    $classid = $std["classid"];
    
    $stat = 0;
            
	     $sql = "SELECT count('assid') FROM `lhpassignedfee` WHERE term  = '$term' AND classid = '$classid' AND stdid = '$stdid' AND feeid = '$feeid' AND status = 1";
			$result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        $count = "$row[0]";

        if($count == $stat){
            
    $sql = "INSERT into `lhpassignedfee` (feeid, classid, stdid, term, type, due, amount, status)

VALUES('$feeid', '$classid', '$stdid', '$term', '$type',  '$feedate', '$feeamount', 1)";
				if(mysqli_query($con, $sql)){	
		
		$feemessage = 'Status : Successfully assigned fee to all learners in the school';
		}
		else{
		    $feemessage = 'Status : Unable to assigned fee to learners in the school';
		}
}
else{
		    $feemessage = 'Status : Unable to assign fee to all learners in the school. This fee has already been assigned to some learners. Kindly assign fee individually for learners or by class.';
		} 
}
}
else{
		    $feemessage = 'Status : Fee not assigned to anyone';
		}
}
else{
		    $feemessage = 'Status : Select All required Field  to assigned fee';
		}
}

else{
		    $feemessage = 'Status : No action Requested';
		}

$_SESSION['feemessage'] = $feemessage;
header("Location: assignfee.php");

?>