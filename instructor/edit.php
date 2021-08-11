<?php
include "conf.php";


if (isset($_POST['updatenote']) && $_POST['updatenote'] == 'Update this Note of Lesson')
{
	if(!empty($_GET['noteid'])) {         
        $noteid = $_GET["noteid"];
	$id = mysqli_real_escape_string($con,$_POST['noteid']); 
	$content = mysqli_real_escape_string($con,$_POST['content']);
	

	
	 $sql= "UPDATE lhpnote SET content = '$content' WHERE noteid = '$id'";
	 
	 
		if(mysqli_query($con, $sql)){	
		
		$mes = 'Status : Successfully modified Note of Lesson.';
		}

      else 
      {
        $mes ='Status : Unable to modify  Note of Lesson.';
      }
    }
	
}	

	$_SESSION['message'] = $mes;
	
	header('Location: editnote.php?id='.$noteid); 