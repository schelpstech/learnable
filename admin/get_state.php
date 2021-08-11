<?php
require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['class_id'])) {
        $coun_id = $_GET["class_id"];           
	$query ="SELECT * FROM lhpsubject WHERE classid IN ($coun_id)";
	$results = $db_handle->runQuery($query);
?>
	<option value="">Select Subject From List</option>
	<option value="All Subject">All Subject</option>
<?php
	foreach($results as $sbj) {
?>
    
	<option value="<?php echo $sbj["sbjid"]; ?>"><?php echo $sbj["sbjname"]; ?></option>
<?php
	}
}
?>