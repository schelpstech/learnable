<?php
include "conf.php";
if(!empty($_GET['wid'])) {         
        $workid = $_GET["wid"];
        $_SESSION['worktopic'] = $workid;
}

header("Location: viewwork.php");
    

?>