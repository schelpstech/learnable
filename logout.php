<?php
include "conf.php";
// Check user login or not
if(!isset($_SESSION['uname'])){
    header('Location: http://dwatschools.com.ng/login/index.php');
}

// logout
if(isset($_POST['but_logout'])){	
    session_destroy();
    header('Location: http://dwatschools.com.ng/login/index.php');
}
?>