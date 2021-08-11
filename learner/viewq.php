<?php
include "conf.php";

if(!empty($_GET['id'])) {         
        $viewid = $_GET["id"];
        $_SESSION['viewid'] = $viewid;
}
if(!empty($_GET['typ'])) {         
        $viewtype = $_GET["typ"];
}


if(!empty($_GET['due'])) {         
        $deadline = $_GET["due"];
        $_SESSION['deadline'] = $deadline;
}

$sql = "SELECT * FROM `lhpquestion` WHERE `questid` = '$viewid'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
    $viewnote = $row['content'];
	$_SESSION['rectime'] =  $row['rectime'];
	$_SESSION['grade'] = $row['grade'];
  }
}

if($viewtype == "text"  ){
    
    $_SESSION['notebook'] = $viewnote;
header("Location: viewquestion.php");
    
}
elseif ($viewtype == "file" xor $viewtype == "media"){
    
    $link = "/learnable/instructor/noteoflesson/";

      $_SESSION['notebook'] = $link.$viewnote;
header("Location: viewquestiondoc.php");
}

elseif ($viewtype == "online"){
     $_SESSION['notebook'] = $viewnote;
header("Location: viewquestionoc.php");
}
?>
