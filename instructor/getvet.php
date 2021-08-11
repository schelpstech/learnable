<?php
include "conf.php";
$lname = $_SESSION['stnamed'];
$viewid = $_SESSION['viewid']
?>
<ul class="conversation-list">

<?php


include "config.php";

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT lhpvet.msg, lhpvet.rectime, lhpvet.userid, lhpstaff.staffname
 FROM lhpvet
 LEFT JOIN lhpstaff ON lhpvet.userid = lhpstaff.sname
  WHERE lhpvet.noteid  = '$viewid' ORDER BY lhpvet.rectime ASC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
 
    $message = $row['msg'];
    $msgtime = $row['rectime'];
    $username = $row['staffname'];
    $user = $row['userid'];
         if($user != $lname){
           echo '<li class="clearfix">
           <div class="chat-avatar">
           <img src="img/post/1.jpg" alt="male">
           <i>'.$msgtime.'</i>
           </div>
           <div class="conversation-text">
           <div class="ctext-wrap">
           <i>'.$username.'</i>
           <p>'
           .$message.'
           </p>
           </div>
           </div>
           </li>';
         }
      else {
      echo '<li class="clearfix odd">
       <div class="chat-avatar">
       <img src="img/post/2.jpg" alt="male">
       <i>'.$msgtime.'</i>
       </div>
       <div class="conversation-text">
       <div class="ctext-wrap chat-widgets-cn">
        <i>'.$username.'</i>
       <p>
       '.$message.'
       </p>
       </div>
       </div>
       </li>';
         }
	
  }
} 

mysqli_close($conn);
?>	


</ul>