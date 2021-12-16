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
                       <h4 style="text-align:center;"><?php echo $term ?> CA Scoresheet for <?php echo $subject. ' in '. $classname ?>.</h4><br>
					     
                            
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
										<th>CA Scores</th>
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
		$sql = "SELECT `status` FROM lhpresultconfig WHERE term  = '$term' ";
        $result=mysqli_query($con,$sql);
         $row=mysqli_fetch_array($result);
      
       $status = $row["status"];
  
			include_once './conn.php';

            $count=1;
            $query=$conn->prepare("SELECT *  from lhpresultrecord WHERE subjid = '$sbjid' AND term = '$term'  and score > 0 ORDER BY rectime DESC");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
                
                
            ?>
            <?php
                 $id =  $row->id;
                 $studentid =  $row->lid;
                  $score =  $row->score;
                  
             $sql = "SELECT fname FROM lhpuser WHERE uname  = '$studentid' ";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
              
               $studentname = $row["fname"];   

                ?>
            <tr>
                <td><?php echo $count++ ?></td>
             
             <td> 
    <button class="btn btn-basic"><strong><?php echo $studentid." - ".$studentname; ?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $score ?></strong></button>
             </td>
             <td> 
               <?php
               
               if($status == 0){
                echo
                '<a href="editcascore.php?recordid='.$id.'" type="button" class="btn btn-primary"><strong>Edit</strong></a>';
            }
            else{
                echo '<button disabled type="button" class="btn btn-danger">Edit Locked</button>';
            }
             ?>
             </td>
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>S/N</th>
                                       <th >Learners' Details</th>
										<th>CA Scores</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
