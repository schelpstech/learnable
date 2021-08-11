<?php
include "conf.php";
$stf = $_SESSION['stnamed'];
require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['term_id'])) {
        $coun_id = $_GET["term_id"];  
        	$_SESSION['termd'] = $coun_id; 
	$query ="SELECT DISTINCT classid FROM lhpalloc WHERE term = '$coun_id' and staffid = '$stf'";
	$results = $db_handle->runQuery($query);
?>
	<option value="">Select Class From List</option>

<?php
	foreach($results as $cls) {
	    $clas =  $cls["classid"];
	    $sql = "SELECT classname from lhpclass where classid = '$clas'";
	    $result=mysqli_query($con,$sql);
		$row=mysqli_fetch_array($result);
        $clasname = $row['classname'];
	    
?>
    
	<option value="<?php echo $clas; ?>"><?php echo $clasname; ?></option>
<?php
	}
}
?>


<?php
$stf = $_SESSION['stnamed'];
$tmd = $_SESSION['termd'];

require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['class_id'])) {
        $count_id = $_GET["class_id"];           
	$query ="SELECT * FROM lhpalloc WHERE classid = '$count_id' and term = '$tmd' and staffid = '$stf'";
	$results = $db_handle->runQuery($query);
?>
	<option value="">Select Subject From List</option>

<?php
	foreach($results as $sbj) {
?>
    
	<option value="<?php echo $sbj["sbjid"]; ?>"><?php echo $sbj["subject"]; ?></option>
<?php
	}
}
?>