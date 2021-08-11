<?php
include "conf.php";

if(!empty($_GET['id'])) {         
        $schmid = $_GET["id"];
}
if(!empty($_GET['tmd'])) {         
        $term = $_GET["tmd"];
 
}
if(!empty($_GET['cls'])) {         
        $cname = $_GET["cls"];
 
}
if(!empty($_GET['sbj'])) {         
        $sbjt = $_GET["sbj"];
 
}

// sql to delete a record
$sql = "UPDATE lhpscheme SET status = 0 WHERE schmid = '$schmid'";

    if(mysqli_query($con, $sql)){	
		$message ='Status : Topic has been deleted successfully';
		}

      else 
      {
        $message = 'Status : Error Deleting Topic .';
      }

$_SESSION['message'] = $message;
$loc = 'Location: viewscheme.php?tmd='.$term.'&cls='.$cname.'&sbj='.$sbjt;
header($loc);      
      
?>
          