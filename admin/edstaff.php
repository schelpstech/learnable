<?php
include "conf.php";


if (isset($_POST['edstf']) && $_POST['edstf'] == 'Modify Staff Details')
{
	$ln = mysqli_real_escape_string($con,$_POST['stnamed']); 
	$lnn = mysqli_real_escape_string($con,$_POST['stpwd']);  
	$lnnn = mysqli_real_escape_string($con,$_POST['stname']);  
	$lnnnn = mysqli_real_escape_string($con,$_POST['stemail']);  
	$lnnnnn = mysqli_real_escape_string($con,$_POST['stfone']);
	
	 $sql= "UPDATE lhpstaff SET spwd = '$lnn', semail = '$lnnnn', sfone = '$lnnnnn', staffname = '$lnnn' WHERE sname = '$ln'";
	 
	 
		if(mysqli_query($con, $sql)){	
		
		$ssmessaged = 'Status : Successfully modified Staff record.';
		}

      else 
      {
        $ssmessaged ='Status : Unable to modify Staff record.';
      }
    }


if (isset($_POST['chg']) && $_POST['chg'] == 'Change Status')
{
   $ln = mysqli_real_escape_string($con,$_POST['named']);
    $chg = mysqli_real_escape_string($con,$_POST['status']);
    
      $sql= "UPDATE lhpstaff SET status = '$chg' WHERE sname = '$ln'";
    
    	if(mysqli_query($con, $sql)){	
		
		$ssmessaged = 'Status : Successfully Changed Staff Status.';
		}

      else 
      {
        $ssmessaged ='Status : Unable to Change Staff Status.';
      }
    }	
	
	
if (isset($_POST['del']) && $_POST['del'] == 'Delete Staff Details')
{
    $ln = mysqli_real_escape_string($con,$_POST['stnamed']);
    
     $sql= "DELETE FROM lhpstaff  WHERE sname = '$ln'";
    
    	if(mysqli_query($con, $sql)){	
		
		$ssmessaged = 'Status : Successfully DELETED Staff record.';
		}

      else 
      {
        $ssmessaged ='Status : Unable to DELETE Staff record.';
      }
    }
	$_SESSION['ssmessaged'] = $ssmessaged;
	header("Location: mgstaff.php");   
?>