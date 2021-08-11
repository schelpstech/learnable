<?php
include "conf.php";


if(!empty($_GET['bid'])) {         
        $noteid = $_GET["bid"];
        $_SESSION['notetopic'] = $noteid;
}
header("Location: viewbook.php");
?>