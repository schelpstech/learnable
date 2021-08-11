<?php
include "conf.php";


if (isset($_POST['updatenote']) && $_POST['updatenote'] == 'Update this Note of Lesson')
{
	
	$id = mysqli_real_escape_string($con,$_POST['notes']); 
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
	
	

	$_SESSION['message'] = $mes;
	$loc= 'Location: editnote.php?id='.$id;
	header($loc);  
?>