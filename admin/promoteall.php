<?php
include "conf.php";


if (isset($_POST['promotion']) && $_POST['promotion'] == 'Promote All Students in Class')
{
	$pclass = mysqli_real_escape_string($con,$_POST['pclass']); 
	$nclass = mysqli_real_escape_string($con,$_POST['nclass']); 
	
	
	if ($pclass !== $nclass){
	    
	
	 $sql= "UPDATE lhpuser SET classid = '$nclass' WHERE classid = '$pclass'";
	 
	 
		if(mysqli_query($con, $sql)){	
		
		$lsmessaged = 'Status : Successfully promoted learners to new class.';
		}

      else 
      {
        $lsmessaged ='Status : Unable to complete promotion, please try again.';
      }
    }
    
     else 
      {
        $lsmessaged ='Status : Unable to promote students, You selected thesame class.';
      }
    
}   	
	$_SESSION['lsmessaged'] = $lsmessaged;
	header("Location: promote.php");   
?>