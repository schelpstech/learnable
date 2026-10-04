<?php
include './query.php';
if ($portalAccess->role() !== 'administrator') { $portalAccess->deny(); }
$back_up->runBackup();