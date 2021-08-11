<?php
session_start();
$lclass = strtoupper($_SESSION['classd']);
require_once("DBController.php");
$db_handle = new DBController();

if(!empty($_GET['td_id'])) {
        $coun_id = $_GET["td_id"]; 
if(!empty($_GET['wk_id'])) {         
        $conn_id = $_GET["wk_id"];    
		
		
	$query = "SELECT `subject` FROM `classact` WHERE `term` = '".$coun_id."' AND `week` = '".$conn_id."' AND `classname` = '".$lclass."' ";
	
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
}

?>