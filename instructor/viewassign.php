
		 <?php
			session_start();
			$lclass = strtoupper($_SESSION['classd']);
			$act = "Test";
			
			include_once './conn.php';
				
            $count=1;
            $query=$conn->prepare("select * from classact WHERE `classname` = '$lclass' AND `activity` = '$act' ");
           $query->setFetchMode(PDO::FETCH_OBJ);
           $query->execute();
            while($row=$query->fetch())
            {
				
            ?>
		
		
		 <tr>
				<td><?php echo $count++ ?></td>
                <td><?php echo $row->term ?></td>
				<td><?php echo $row->week ?></td>
				<td><?php echo $row->subject ?></td>
				<td><?php echo $row->topic ?></td>
                <td><a href="<?php echo $row->actlink ?>">Click to take Assignment</a> </td>
				<td> <button type="button" class="btn btn-success btn-sm update" data-toggle="modal" data-keyboard="false" data-backdrop="static" data-target="#update_country"
			
			data-term_modal="<?=$row['term'];?>"
			data-week_modal="<?=$row['week'];?>"
			data-subject_modal="<?=$row['subject'];?>"
			data-class_modal="<?=$row['classname'];?>"
			data-topic_modal="<?=$row['topic'];?>"
			data-teach_modal="<?=$row['staffid'];?>"
			
				">Submit</button> </td>
            </tr>
			
		
 <?php }?>
	