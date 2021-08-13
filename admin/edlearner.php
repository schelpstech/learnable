<?php
include "conf.php";


if (isset($_POST['edstdt']) && $_POST['edstdt'] == 'Modify Learner Details')
{
	$ln = mysqli_real_escape_string($con,$_POST['named']); 
	$lnn = mysqli_real_escape_string($con,$_POST['npwd']);  
	$lnnn = mysqli_real_escape_string($con,$_POST['nname']);  
	$lnnnn = mysqli_real_escape_string($con,$_POST['nemail']);  
	$lnnnnn = mysqli_real_escape_string($con,$_POST['nclass']);
	$gender = mysqli_real_escape_string($con,$_POST['gender']);
  $dob = mysqli_real_escape_string($con,$_POST['dob']);
	 $sql= "UPDATE lhpuser SET upwd = '$lnn', email = '$lnnnn', classid = '$lnnnnn', fname = '$lnnn', gender = '$gender', dob = '$dob' WHERE uname = '$ln'";
	 
	 
		if(mysqli_query($con, $sql)){	
		
		$lsmessaged = 'Status : Successfully modified learner record.';
		}

      else 
      {
        $lsmessaged ='Status : Unable to modify learner record.';
      }
    }
	
	
	
if (isset($_POST['del']) && $_POST['del'] == 'Delete Learner Details')
{
    $ln = mysqli_real_escape_string($con,$_POST['named']);
    
     $sql= "DELETE FROM lhpuser  WHERE uname = '$ln'";
    
    	if(mysqli_query($con, $sql)){	
		
		$lsmessaged = 'Status : Successfully DELETED learner record.';
		}

      else 
      {
        $lsmessaged ='Status : Unable to DELETE learner record.';
      }
    }
    
    	
if (isset($_POST['chg']) && $_POST['chg'] == 'Change Status')
{
    $ln = mysqli_real_escape_string($con,$_POST['named']);
    $chg = mysqli_real_escape_string($con,$_POST['status']);
    
      $sql= "UPDATE lhpuser SET status = '$chg' WHERE uname = '$ln'";
    
    	if(mysqli_query($con, $sql)){	
		
		$lsmessaged = 'Status : Successfully Changed Learner Status.';
		}

      else 
      {
        $lsmessaged ='Status : Unable to Change Learners Status.';
      }
    }
	$_SESSION['lsmessaged'] = $lsmessaged;
	header("Location: mglearners.php");   
?>