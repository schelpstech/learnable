<?php
include "conf.php";

if(!empty($_GET['class_id'])) {         
    $classid = $_GET["class_id"];
}
$term = $_SESSION['termed'];
?>

<div class="notika-status-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2>&#8358;<span class="counter">
                                <?php
                              	include_once './conf.php';
                                  $sql = " SELECT Sum(`amount`) as total_bill FROM `lhpassignedfee` where classid = '$classid' and `status` = 1 and term = '$term'";
                                  $result=mysqli_query($con,$sql);
                                 $row=mysqli_fetch_array($result);
                                 echo intval($row['total_bill']);
                             
                                ?>
                                </span></h2>
                         <a href="#">   <h6><strong><?php echo $term." "; ?>Expected Income </strong></h6></a>
                        </div>
                        
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2>&#8358;<span class="counter">
                             <?php
                                
                              	include_once './conf.php';
                          $sql = " SELECT Sum(amount)  as total_payment FROM `lhptransaction` where classid = '$classid' and  `status` = 1 and term = '$term'";
                         $result=mysqli_query($con,$sql);
                        $row=mysqli_fetch_array($result);
                        
                        echo intval($row['total_payment']);     
                                ?>
                            </span></h2>
                         <a href="#">   <h6><strong><?php echo $term." "; ?>Actual Income</strong></h6></a>
                        </div>
                    
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter">
                                
                                 <?php
                                
                                $sql = " SELECT COUNT(DISTINCT stdid)  as num_bill FROM `lhpassignedfee` where classid = '$classid' and  `status` = 1 and term = '$term'";
                                $result=mysqli_query($con,$sql);
                               $row=mysqli_fetch_array($result);
                               echo intval($row['num_bill']);
                             
                                ?>
                            </span></h2>
                           <a href="#"> <h6><strong><?php echo $term." "; ?>Number of Students</strong></h6></a>
                        </div>
                  
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter"><?php
                                
                                $sql = " SELECT COUNT(DISTINCT stdid)  as num_payment FROM `lhptransaction` where classid = '$classid' and  `status` = 1 and term = '$term'";
                                $result=mysqli_query($con,$sql);
                               $row=mysqli_fetch_array($result);
                               
                               echo intval($row['num_payment']);
                                ?> </span></h2>
                          <a href="#">  <h6><strong><?php echo $term." "; ?>Paying Students</strong></h6></a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="breadcomb-area">
		<div class="container">
			<div class="row">
             </div>
        </div>
    </div>
    
	<div id="doc" class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                           
                            <h2>Payment View By Class 
							
						</h2>
                            <p>A breakdown of payment expected and received by class</p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-bordered">
                                <thead>
                                    <tr>
                      <th>S/N</th>
                      <th>Class </th>
					<th>Full Name</th>
                    <th>Contact Phone</th>
                    <th>Total Amount Billed</th>
                    <th>Total Discount Allowed</th>
                    <th>Total Amount Paid</th>	
                    <th>Total Amount Owing</th>
                    <th>Status</th>
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
			
			
			include_once './conn.php';
				
            $count=1;
            $query=$conn->prepare("SELECT * from lhpuser where classid = '$classid'");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
              $fname = $row->fname;
              $uname = $row->uname;
              $classid = $row->classid;
              $phone = $row->numb;
              

            $sql = "SELECT classname from lhpclass where classid = '$classid'";
            $result=mysqli_query($con,$sql);
           $row=mysqli_fetch_array($result);
           $classname = "$row[classname]";

            $sql = " SELECT Sum(`amount`) as tbill FROM `lhpassignedfee` where stdid = '$uname' and  `status` = 1 and term = '$term'";
            $result=mysqli_query($con,$sql);
           $row=mysqli_fetch_array($result);
           $tbill = "$row[tbill]";

           $sql = " SELECT Sum(`discount`) as tdiscount FROM `lhpassignedfee` where stdid = '$uname' and  `status` = 1 and term = '$term'";
           $result=mysqli_query($con,$sql);
          $row=mysqli_fetch_array($result);
          $discount = "$row[tdiscount]";

           $sql = " SELECT Sum(`amount`) as trev FROM `lhptransaction` where stdid = '$uname' and  `status` = 1 and term = '$term'";
            $result=mysqli_query($con,$sql);
           $row=mysqli_fetch_array($result);
           $trev = "$row[trev]";
            ?>
            <tr>
            <td><strong><?php echo $count++ ?></strong></td>
            <td><strong><?php echo $classname?></strong></td>
            <td><strong><?php echo $fname ?></strong></td>
            <td><strong><?php echo $phone ?></strong></td>
            <td><strong>&#8358;<?php echo intval($tbill) ?></strong></td>
            <td><strong>&#8358;<?php echo intval($discount) ?></strong></td>
			<td><strong>&#8358;<?php echo intval($trev) ?></strong></td>	
            <td><strong>&#8358;<?php echo intval($tbill) - (intval($trev) + intval($discount))?></strong></td>  
            <td><?php 
            
            $balance = intval($trev) - (intval($tbill) + intval($discount));
            if( $balance >= 0){
                echo '<button type="button"  class="btn btn-success" ><strong>Full Paid</strong></button>';
            }

            else if( $balance < 0){
                echo '<button type="button"  class="btn btn-danger" ><strong>Owing</strong></button>';
            }
            
            ?></td> 
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                        <th>S/N</th>
                      <th>Class </th>
					<th>Full Name</th>
                    <th>Contact Phone</th>
                    <th>Total Amount Billed</th>
                    <th>Total Discount Allowed</th>
                    <th>Total Amount Paid</th>	
                    <th>Total Amount Owing</th>
                    <th>Status</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 