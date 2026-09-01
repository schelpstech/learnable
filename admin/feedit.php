<?php
require __DIR__.'/conf.php';
$id=filter_input(INPUT_GET,'ref',FILTER_VALIDATE_INT);
header('Location: index.php?route=fees'.($id?'&id='.$id:''));exit;
