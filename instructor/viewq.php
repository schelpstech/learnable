<?php
include "conf.php";

if(!empty($_GET['id'])) {         
        $viewid = $_GET["id"];
        $_SESSION['viewid'] = $viewid;
}
if(!empty($_GET['typ'])) {         
        $viewtype = $_GET["typ"];
}

 $sql = "SELECT * FROM lhpquestion WHERE questid  = '$viewid'";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $viewnote = $row[content];
               $rectime = $row[rectime];

if($viewtype == "text"  ){
    
    
     $_SESSION['rectime'] = $rectime;
    $_SESSION['notebook'] = "<h2><strong>QUESTION :  </strong></h2><br><br>".$viewnote;
header("Location: viewnote.php");
    
}
elseif ($viewtype == "file" xor $viewtype == "media"){
    
    $link = "./noteoflesson/";

      $_SESSION['notebook'] = $link.$viewnote;
header("Location: viewdoc.php");
}

elseif ($viewtype == "online"){
     $_SESSION['notebook'] = $viewnote;
header("Location: viewdoc.php");
}
?>
