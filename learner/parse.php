<?php
include "conf.php";

if(!empty($_GET['sub'])) {         
        $subject = $_GET["sub"];
        $_SESSION['subject'] = $subject;
}

header("Location: viewtopics.php");
?>
