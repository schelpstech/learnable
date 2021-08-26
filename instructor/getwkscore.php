<?php
include "conf.php";
        	$term = $_SESSION['ttid'];
			$sbjid = $_SESSION['ssid'];
			$classid = $_SESSION['ccid'];
			
			$sql = "SELECT * FROM lhpalloc WHERE sbjid = '$sbjid' AND classid  = '$classid' AND term  = '$term' ";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $subject = $row['subject'];
               $classname = $row["classname"];

?>
    <div id="cadata" class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                       <h4 style="text-align:center;"><?php echo $term ?> Weekly Scoresheet for <?php echo $subject. ' in '. $classname ?>.</h4><br>
					     
                            
            <p><strong> Term  :  </strong> <?php echo $term?></p>
                <p><strong> Class  : </strong> <?php echo $classname ?></p>
        <p><strong> Subject  :</strong> <?php echo $subject?></p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                       <th>S/N</th>
                                       <th >Learners' Details</th>
										<th>Number of Weeks</th>
                                        <th>Number of Records</th>
                                        <th>Mark Obtainable</th>
                                        <th>Scored Awarded</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
		
			include_once './conn.php';

            $count=1;
            $query=$conn->prepare("SELECT DISTINCT (lid)  from `lhpweekrecord` WHERE subjid = '$sbjid' AND term = '$term'  ORDER BY rectime DESC");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
                
                
            ?>
            <?php
               
                 $studentid =  $row->lid;
                  
                  
             $sql = "SELECT fname FROM lhpuser WHERE uname  = '$studentid' ";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
               $studentname = $row["fname"];   
            $sql = "SELECT COUNT(DISTINCT week) as numweek  from `lhpweekrecord` WHERE subjid = '$sbjid' AND term = '$term' ";
               $result=mysqli_query($con,$sql);
                $row=mysqli_fetch_array($result);
              $numwk= $row["numweek"];   
              $sql = "SELECT COUNT(id) as numsbt  from `lhpweekrecord` WHERE subjid = '$sbjid' AND term = '$term' and lid = '$studentid' ";
              $result=mysqli_query($con,$sql);
               $row=mysqli_fetch_array($result);
             $numsbt= $row["numsbt"];
             $sql = "SELECT SUM(score) as tot  from `lhpweekrecord` WHERE subjid = '$sbjid' AND term = '$term' and lid = '$studentid' ";
             $result=mysqli_query($con,$sql);
              $row=mysqli_fetch_array($result);
            $totsmt = $row["tot"];
                ?>
            <tr>
                <td><?php echo $count++ ?></td>
             
             <td> 
    <button class="btn btn-basic"><strong><?php echo $studentid." - ".$studentname; ?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $numwk ?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $numsbt ?></strong></button>
             </td>

             <td> 
    <button class="btn btn-basic"><strong><?php echo $numwk * 10?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $totsmt?></strong></button>
             </td>
             <td> 
    <a href="viewweekly.php?id=<?php echo $studentid?>&term=<?php echo $term?>&sbjid=<?php echo $sbjid?>&cid=<?php echo $classid?>"  class="btn btn-primary"><strong>View Weekly Assessments</strong></a>
             </td>
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                    <th>S/N</th>
                                       <th >Learners' Details</th>
										<th>Number of Weeks</th>
                                        <th>Number of Records</th>
                                        <th>Mark Obtainable</th>
                                        <th>Scored Awarded</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
