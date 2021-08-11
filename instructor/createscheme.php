<?php
include "conf.php";

 $message ="no action";
if (isset($_POST['scheme']) && $_POST['scheme'] == 'Add Topic To Scheme of Work')
{
  	$lstname = mysqli_real_escape_string($con,$_POST['stname']);
    $lclassd = mysqli_real_escape_string($con,$_POST['classd']);
	$lterm = mysqli_real_escape_string($con,$_POST['term']);
	$lweek = mysqli_real_escape_string($con,$_POST['week']);
	$lsbj = mysqli_real_escape_string($con,$_POST['sbj']);
    $ltopic = mysqli_real_escape_string($con,$_POST['topic']);
	

 $sql= "INSERT INTO `lhpscheme`  (term, classname, subject, week, topic, staffid) VALUES ('$lterm', '$lclassd', '$lsbj', '$lweek','$ltopic','$lstname')";
		if(mysqli_query($con, $sql)){	
		$message ='Status : Topic has been added to Scheme of work.';
		}

      else 
      {
        $message = 'Status : Error adding topic to scheme of work.';
      }
    }

$_SESSION['message'] = $message;
header("Location: scheme.php");

?>