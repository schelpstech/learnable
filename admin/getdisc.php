<?php
include ("conf.php");
require_once("DBController.php");
$db_handle = new DBController();
?>
<?php

if(!empty($_GET['classid'])) {
        $classid = $_GET["classid"];
  
        $query = "SELECT DISTINCT stdid FROM `lhpassignedfee` WHERE classid = '$classid' and `status` = 1";
        
        $result = $db_handle->runQuery($query);
        
        ?>
        <option value="">Select Learner</option>
        
        <?php   
            foreach($result as $row) {

            $sql = "SELECT * FROM lhpuser WHERE uname  = '$row[stdid]'";
            $result=mysqli_query($con,$sql);
             $row=mysqli_fetch_array($result);
                     
?>
<option value="<?php echo $row['uname']?>"><?php echo $row['fname']." - ".$row['uname']?></option>

<?php
            }
        }

        ?>


<?php

require_once("DBController.php");
$db_handle = new DBController();
if(!empty($_GET['lid'])) {
        $lid = $_GET["lid"];
        $sql = "SELECT term FROM lpterm WHERE `status` = 1";
		$result=mysqli_query($con,$sql);
		 $row=mysqli_fetch_array($result);
               $term = $row['term'];
        $query = "SELECT  lhpassignedfee.feeid as feeid, lhpassignedfee.assid as assid, lhpassignedfee.amount as amount, lhpfeelist.feename as feename FROM `lhpassignedfee` LEFT JOIN lhpfeelist ON lhpassignedfee.feeid = lhpfeelist.feeid WHERE lhpassignedfee.stdid = '$lid' and lhpassignedfee.term = '$term' and lhpassignedfee.status = 1";
        
        $result = $db_handle->runQuery($query);  

        ?>
        <option value="">Select Fee Name and Amount</option>
        
        <?php    
            foreach($result as $rw) {
                $feeid = $rw['feeid'];
                $assignid = $rw['assid'];
                $amount = $rw['amount'];
                $feename = $rw['feename'];  
                if ($feename != ""){
                        $feetitle = $feename;
                        }
                        else {
                      $feetitle = $feeid;  
                            
                        }


?>
<option value="<?php echo $assignid?>"><?php echo $feetitle." - " ?>&#8358;<?php echo$amount ?> </option>

<?php
            }
        }

        ?>