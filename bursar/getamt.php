<?php
require __DIR__ . '/conf.php';
$query = array_intersect_key($_GET, array_flip(['term', 'stdid', 'class_id']));
header('Location: ../admin/getamt.php?' . http_build_query($query));
exit;
