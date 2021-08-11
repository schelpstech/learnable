<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['studnamed'])){
     header('Location: ../index.php');
     
}
$lname = $_SESSION['studnamed'];
$sql = "SELECT * FROM `lpterm` WHERE `status` = 1";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
      $term = $row["term"];
	
  }
} 
?>
<?php

//setting header to json
header('Content-Type: application/json');

//query to get data from the table
$query = sprintf("SELECT lhpsubject.sbjname, lhpresultrecord.totalscore 
FROM lhpresultrecord
LEFT JOIN lhpsubject ON lhpresultrecord.subjid = lhpsubject.sbjid
WHERE lid = '$lname' AND term = '$term' ORDER BY rectime DESC");

//execute query
$result = $con->query($query);

//loop through the returned data
$data = array();
foreach ($result as $row) {
  $data[] = $row;
}

//free memory associated with result
$result->close();

//close connection
$con->close();

//now print the data
print json_encode($data);