<?php
include("conf.php")
	?>
<?php
require_once("DBController.php");
$db_handle = new DBController();
if (!empty($_GET['classid'])) {
	$class_id = $_GET["classid"];

	$query = "SELECT * FROM lhpuser WHERE classid = '$class_id' ";
	$results = $db_handle->runQuery($query);
	?>

	<option value=""> Select Learner From List</option>
	<?php
	foreach ($results as $std) {
		?>

		<option value="<?php echo $std["uname"]; ?>">
			<?php echo $std["fname"]; ?>
		</option>
	<?php
	}
}
?>






<?php
$sql = "SELECT term FROM lpterm WHERE `status` = 1 ";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
$sess = $row['term'];

require_once("DBController.php");
$db_handle = new DBController();
if (!empty($_GET['feetype'])) {
	$feetype = $_GET["feetype"];
	$query = "SELECT * FROM lhpfeelist WHERE classid = '$feetype' AND term = '$sess' AND status = 1 ";
	$results = $db_handle->runQuery($query);
	?>

	<option value=""> Select Fee Reference Name</option>
	<option value="PreviousBalance">Previous Term Outstanding Payment</option>
	<?php
	foreach ($results as $fee) {
		?>

		<option value="<?php echo $fee["feeid"]; ?>">
			<?php echo $fee["feename"]; ?>
		</option>
	<?php
	}
}
?>

<?php

require_once("DBController.php");
$db_handle = new DBController();
if (!empty($_GET['feeid'])) {
	$feeid = $_GET["feeid"];
	$query = "SELECT * FROM `lhpfeelist` WHERE `feeid` = '$feeid'";
	$results = $db_handle->runQuery($query);
	?>

	<?php
	foreach ($results as $fd) {
		?>

		<option selected value="<?php echo $fd["amount"]; ?>">
			<?php echo $fd["amount"]; ?>
		</option>
	<?php
	}
}
?>
