<?php
include "conf.php";
require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['termid'])) {
        $termid = $_GET["termid"];  
		$_SESSION['term']  = $termid; 
	$query ="SELECT DISTINCT(classid) FROM lhpweekrecord WHERE term = '$termid'";
	$results = $db_handle->runQuery($query);
?>
	<option value=""> Select Class </option>

<?php
	foreach($results as $cd) {
		$classid = $cd["classid"];
		$sql = "SELECT classname FROM `lhpclass` WHERE `classid` = '$classid'";
		$result = mysqli_query($con, $sql);
		$row = mysqli_fetch_assoc($result); 
		$classname = $row["classname"];

?>

	<option value="<?php echo $classid; ?>"><?php echo $classname; ?></option>
<?php
	}
}
?>

<?php
require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['cld'])) {
        $classid = $_GET["cld"];           
	$query ="SELECT DISTINCT(lid) FROM lhpweekrecord WHERE term = '$_SESSION[term]' and classid = '$classid'";
	$results = $db_handle->runQuery($query);
?>
	<option value=""> Select Learner </option>

<?php
	foreach($results as $std) {
		$lid = $std["lid"];
		$sql = "SELECT fname FROM `lhpuser` WHERE `uname` = '$lid'";
		$result = mysqli_query($con, $sql);
		$row = mysqli_fetch_assoc($result); 
		$fname = $row["fname"];
?>

	<option value="<?php echo $lid; ?>"><?php echo $fname; ?></option>
<?php
	}
}
?>