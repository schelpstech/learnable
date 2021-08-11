<?php
	session_start();
   if(isset($_POST['log'])){	
    session_destroy();
    header('Location: ../index.php');
}

?>