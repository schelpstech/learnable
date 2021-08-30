<?php
include ("conf.php")
?>
<?php
require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['classid'])) {
        $class_id = $_GET["classid"];
			 
	$query ="SELECT * FROM lhpuser WHERE classid = '$class_id' ";
	$results = $db_handle->runQuery($query);
?>
	
<option value="" > Select Learner From List</option>
<?php
	foreach($results as $std) {
?>

	<option value="<?php echo $std["uname"]; ?>"><?php echo $std["fname"]; ?></option>
<?php
	}
}
?>


<?php

require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['feel'])) {
        $term = $_GET["feel"];    
	    $_SESSION['term'] = $term;
	$query ="SELECT DISTINCT classid FROM lhpfeelist WHERE term = '$term'  AND status = 1";
	$results = $db_handle->runQuery($query);
?>
	
<option value="">Select Fee Type From List of Fees</option>
<option value="PreviousBalance">Previous Term Outstanding</option>
<?php
	foreach($results as $std) {
	    
	    $cname = $std["classid"];
	    $dname = $std["classid"];
	  
	    
	       $sql = "SELECT * FROM lhpclass WHERE classid  = '$cname'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               if ($row['classname'] != ""){
               $feeclass = $row['classname'];
               }
               else {
                 $feeclass = $cname;
               }
	   
	   
?>

<option value="<?php echo $dname; ?>"><?php echo $feeclass; ?> FEE </option>
<?php
	}
}
?>




<?php
$termd = $_SESSION['term'];
require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['feetype'])) {
        $feetype = $_GET["feetype"];   
	$query ="SELECT * FROM `lhpfeelist` WHERE `classid` = '$feetype' AND `term` = '$termd' AND status = 1 ";
	$results = $db_handle->runQuery($query);
?>
	
<option value=""> Select Fee Reference Name</option>
<option value="PreviousBalance">Previous Term Outstanding Payment</option>
<?php
	foreach($results as $fee) {
?>

	<option value="<?php echo $fee["feeid"]; ?>"><?php echo $fee["feename"]; ?></option>
<?php
	}
}
?>

<?php

require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['feeid'])) {
        $feeid = $_GET["feeid"];   
	$query ="SELECT * FROM `lhpfeelist` WHERE `feeid` = '$feeid'";
	$results = $db_handle->runQuery($query);
?>

<?php
	foreach($results as $fd) {
?>

	<option selected value="<?php echo $fd["amount"]; ?>"><?php echo $fd["amount"]; ?></option>
<?php
	}
}
?>

