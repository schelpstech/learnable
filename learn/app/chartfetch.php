<?php
include '../app/query.php';
header('Content-Type: application/json');
$data = array();
foreach ($show_result as $row) {
  $data[] = $row;
}
print json_encode($data);
?>