<?php
include ("conf.php")
?>
<?php
require_once("DBController.php");
$db_handle = new DBController();

if(!empty($_GET['stdid'])) {
        $stdid = $_GET["stdid"];
        

        $query= "SELECT SUM(amount) AS bill FROM `lhpassignedfee` WHERE stdid = '$stdid' AND status = 1";
	$results = $db_handle->runQuery($query);
	foreach($results as $pam) {
	$bil = $pam["bill"];
	}
	
	$query= "SELECT SUM(amount) AS pay FROM `lhptransaction` WHERE stdid = '$stdid' AND status = 1";
	$result = $db_handle->runQuery($query);
	foreach($result as $paym) {
	$paid = $paym["pay"];
	}
	
	$owe = $paid - $bil;
}	
?>
	<option value="<?php echo $owe; ?>"><?php echo $owe; ?></option>
