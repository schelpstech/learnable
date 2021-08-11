<?php
require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['cld'])) {
        $count_id = $_GET["cld"];           
	$query ="SELECT * FROM lhpuser WHERE classid = '$count_id' ORDER BY fname ASC";
	$results = $db_handle->runQuery($query);
?>
	<option value=""> Select Learner </option>

<?php
	foreach($results as $std) {
?>

	<option value="<?php echo $std["uname"]; ?>"><?php echo $std["fname"]; ?></option>
<?php
	}
}
?>