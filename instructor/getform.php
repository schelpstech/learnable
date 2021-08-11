<?php
session_start();
$lclass = strtoupper($_SESSION['classd']);
$type = "Assignment";
$typed = "Examination";
require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['term_id'])) {
        $coun_id = $_GET["term_id"];
        $ldate = date("Y-m-d");
		$_SESSION['td'] = $coun_id;   
	$query = "SELECT DISTINCT `week` FROM `classact` WHERE `term` = '".$coun_id."' AND `classname` = '".$lclass."'  AND `actdate` >= '".$ldate."' AND (`activity` = '".$type."' OR `activity` = '".$typed."')  ";
	
	$results = $db_handle->runQuery($query);
?>
	<option value="">Select Week</option>
<?php
	foreach($results as $wk) {
?>
	<option value="<?php echo $wk["week"]; ?>"><?php echo $wk["week"]; ?></option>
<?php
	}
}

?>

<?php
$ldate = date("Y-m-d");
$type = "Assignment";
$typed = "Examination";
$lclass = strtoupper($_SESSION['classd']);
$ltd = strtoupper($_SESSION['td']);
require_once("DBController.php");
$db_handle = new DBController();


if(!empty($_GET['wk_id'])) {         
        $conn_id = $_GET["wk_id"];    
		$_SESSION['tp'] = $conn_id; 
		
	$query = "SELECT DISTINCT `subject` FROM `classact` WHERE `term` = '".$ltd."' AND `week` = '".$conn_id."' AND `classname` = '".$lclass."' AND `actdate` >= '".$ldate."' AND (`activity` = '".$type."' OR `activity` = '".$typed."')   ";
	
	$results = $db_handle->runQuery($query);
?>
	<option value="">Select Subject</option>
<?php
	foreach($results as $kk) {
?>
	<option value="<?php echo $kk["subject"]; ?>"><?php echo $kk["subject"]; ?></option>
<?php
	}
}

?>

<?php
$ddate = date("Y-m-d");
$type = "Assignment";
$typed = "Examination";
$lclass = strtoupper($_SESSION['classd']);
$ltd = strtoupper($_SESSION['td']);
$ltid = strtoupper($_SESSION['tp']);
require_once("DBController.php");
$db_handle = new DBController();


if(!empty($_GET['sbj_id'])) {         
        $con_id = $_GET["sbj_id"];    
		$_SESSION['topp'] = $con_id; 
		
	$query = "SELECT DISTINCT `topic` FROM `classact` WHERE `term` = '".$ltd."' AND ( `week` = '".$ltid."' AND `classname` = '".$lclass."' AND `subject` = '".$con_id."'  AND `actdate` >= '".$ddate."' AND (`activity` = '".$type."' OR `activity` = '".$typed."') ) ";
	
	$resul = $db_handle->runQuery($query);
?>
	<option value="">Select Topic</option>
<?php
	foreach($resul as $fttk) {
?>
	
	<option value="<?php echo $fttk["topic"]; ?>"><?php echo $fttk["topic"]; ?></option>
<?php
	}

}
?>


<?php
$ldate = date("Y-m-d");
$type = "Assignment";
$typed = "Examination";
$lclass = strtoupper($_SESSION['classd']);
$ltd = strtoupper($_SESSION['td']);
$ltid = strtoupper($_SESSION['tp']);
$ltpid = strtoupper($_SESSION['topp']);
require_once("DBController.php");
$db_handle = new DBController();


if(!empty($_GET['top_id'])) {         
        $cun_id = $_GET["top_id"];    
		
		
	$query = "SELECT DISTINCT `staffid` FROM `classact` WHERE `term` = '".$ltd."' AND `week` = '".$ltid."'   AND `classname` = '".$lclass."' AND `subject` = '".$ltpid."' AND `topic` = '".$cun_id."'  AND `actdate` >= '".$ldate."' AND (`activity` = '".$type."' OR `activity` = '".$typed."') ";
	
	$results = $db_handle->runQuery($query);
?>
	<option value="">Select Teacher</option>
<?php
	foreach($results as $tfk) {
?>
	<option value="<?php echo $tfk["staffid"]; ?>"><?php echo $tfk["staffid"]; ?></option>
<?php
	}
}

?>



