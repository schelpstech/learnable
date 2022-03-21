<?php
include "conf.php";
        	$term = $_SESSION['term'];
			$classid = $_SESSION['classalloc'];
			
			$sql = "SELECT * FROM lhpclass WHERE  classid  = '$classid'  ";
				$result=mysqli_query($con,$sql);
				 $row=mysqli_fetch_array($result);
              
               $classname = $row["classname"];

?>
    <div id="cadata" class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                       <h4 style="text-align:center;"><?php echo $term.' Affective Domain Rating and Attendance records for '.$classname ?>.</h4><br>
					     
                            
            <p><strong> Term  :  </strong> <?php echo $term?></p>
                <p><strong> Class  : </strong> <?php echo $classname ?></p>
      
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                       <th>S/N</th>
                                       <th >Learners' Details</th>
										<th>Total Present Days</th>
                                        <th>Leadership Ratings</th>
                                        <th>Eloquence  Ratings</th>
                                        <th>Neatness  Ratings</th>
                                        <th>Creativity  Ratings</th>
                                        <th>Responsiveness  Ratings</th>
                                        <th>Teacher's Comment</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                               
                                    
                                     <tbody>
				
				
				
				 <?php
		
			include_once './conn.php';

            $count=1;
            $query=$conn->prepare("SELECT *  from lhpaffective WHERE classid = '$classid' AND term = '$term'   ORDER BY rectime DESC");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
                
                
            ?>
            <?php
               
                 $affid =  $row->affid;
                 $studentid =  $row->uname;
                  $present =  $row->total_present;
                  $lead =  $row->rating1;
                  $eloq =  $row->rating2;
                  $neat =  $row->rating3;
                  $create =  $row->rating4;
                  $response =  $row->rating5;
                  $comment =  $row->comment;
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
    <button class="btn btn-basic"><strong><?php echo $present ?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $lead ?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $eloq ?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $neat ?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $create ?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $response ?></strong></button>
             </td>
             <td> 
    <button class="btn btn-basic"><strong><?php echo $comment ?></strong></button>
             </td>
             <td> 
    <a href="editaffective.php?recordid=<?php echo $affid?>" type="button" class="btn btn-primary"><strong>Edit</strong></a>
             </td>
            </tr>
            <?php }?>
            </tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                    <th>S/N</th>
                                       <th >Learners' Details</th>
										<th>Total Present Days</th>
                                        <th>Leadership Ratings</th>
                                        <th>Eloquence  Ratings</th>
                                        <th>Neatness  Ratings</th>
                                        <th>Creativity  Ratings</th>
                                        <th>Responsiveness  Ratings</th>
                                        <th>Teacher's Comment</th>
                                        <th>Edit</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
