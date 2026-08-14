<?php

$sharedClasses = array(
    'DBController.php',
    'Model.php',
    'User.php',
    'Utility.php',
    'DBBackup.php',
    'PortalRoute.php',
);

foreach ($sharedClasses as $classFile) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . $classFile;
}

