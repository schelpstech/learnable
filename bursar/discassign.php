<?php
include "conf.php";


if (isset($_POST['assign']) && $_POST['assign'] == 'Award Discount to Selected Customer')
{
	$learner = mysqli_real_escape_string($con,$_POST['learnerid']);
    $feeid = mysqli_real_escape_string($con,$_POST['feeid']);
    $discount = mysqli_real_escape_string($con,$_POST['discount']);

	if ( $discount != "" && $feeid != "" && $learner != ""){

		$sql = "SELECT term FROM lpterm WHERE `status` = 1";
		$result=mysqli_query($con,$sql);
		 $row=mysqli_fetch_array($result);
               $term = $row['term'];
	    
			   $sql = "SELECT amount FROM lhpassignedfee WHERE `assid` = '$feeid'";
			   $result=mysqli_query($con,$sql);
				$row=mysqli_fetch_array($result);
					  $amount = $row['amount'];
	    if ( $discount < $amount){
	    
	    	    
	    
$sql = "UPDATE `lhpassignedfee` set discount = '$discount'  WHERE `assid` = '$feeid'";
				if(mysqli_query($con, $sql)){	
		
		$feemessage = 'Status : Successfully awarded discount to the selected learner';
		}
		else{
		    $feemessage = 'Status : Unable to award discount to the selected learner';
		}
}
    else{
                $feemessage = 'Status : Unable to award discount. Discount given amount : '.$discount.'" can not be more than fee assigned :"'.$amount;
            }
		}
		else{
					$feemessage = 'Status : Select All required Field  to assigned fee';
				}

}

else{
		    $feemessage = 'Status : No action Requested';
		}

$_SESSION['feemessage'] = $feemessage;
header("Location: mgdiscount.php");

?>