<?php
include './query.php';
$tablename = 'lpterm,lhpuser';
$initiate_backup = $back_up->run_backup($tablename);
?>