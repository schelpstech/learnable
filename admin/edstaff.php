<?php
include "conf.php";


if (isset($_POST['edstf']) && $_POST['edstf'] == 'Modify Staff Details')
{
	$ln = mysqli_real_escape_string($con,$_POST['stnamed']); 
	$passwordInput = isset($_POST['stpwd']) ? (string) $_POST['stpwd'] : '';
	$lnnn = mysqli_real_escape_string($con,$_POST['stname']);  
	$lnnnn = mysqli_real_escape_string($con,$_POST['stemail']);  
	$lnnnnn = mysqli_real_escape_string($con,$_POST['stfone']);
	
	 $passwordSql = '';
	 if ($passwordInput !== '') {
		 if (strlen($passwordInput) < 8) {
			 $ssmessaged = 'Status: Password must contain at least 8 characters.';
			 $_SESSION['ssmessaged'] = $ssmessaged;
			 header('Location: mgstaff.php');
			 exit;
		 }
		 $passwordHash = mysqli_real_escape_string($con, password_hash($passwordInput, PASSWORD_DEFAULT));
		 $passwordSql = ", spwd = '$passwordHash'";
	 }
	 $sql= "UPDATE lhpstaff SET semail = '$lnnnn', sfone = '$lnnnnn', staffname = '$lnnn'$passwordSql WHERE sname = '$ln'";
	 
	 
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
