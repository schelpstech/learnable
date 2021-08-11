<?php
include "conf.php";

if(!empty($_GET['id'])) {         
        $viewid = $_GET["id"];
        $_SESSION['viewid'] = $viewid;
}
if(!empty($_GET['typ'])) {         
        $viewtype = $_GET["typ"];
}



$sql = "SELECT * FROM `lhpnote` WHERE `noteid` = '$viewid'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    
    $viewnote = $row['content'];
	$_SESSION['rectime'] =  $row['rectime'];
  }
} 



if($viewtype == "text"  ){
    
    $_SESSION['notebook'] = $viewnote;
header("Location: viewnote.php");
    
}
elseif ($viewtype == "file" xor $viewtype == "media"){
    
    $link = "/learnable/instructor/noteoflesson/";

      $_SESSION['notebook'] = $link.$viewnote;
header("Location: viewdoc.php");
}

elseif ($viewtype == "online"){
     $_SESSION['notebook'] = $viewnote;
header("Location: viewdoc.php");
}
?>
