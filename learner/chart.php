<?php

// Check user login or not
include "conf.php";
if(!isset($_SESSION['studnamed'])){
     header('Location: ../index.php');
     
}
if(!empty($_GET['term'])) {
  $term = $_GET["term"]; 
}
$lname = $_SESSION['studnamed'];
 
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