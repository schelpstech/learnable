<?php
include_once "../conf.php";

if (!isset($_SESSION['unamed'])) {
    header('Location: ../admin.php');
    exit;
}
?>
