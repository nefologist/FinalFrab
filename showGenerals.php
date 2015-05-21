<?php
include "header.php";
include "student.php";
$general= new student();
$showMeWho = $general->fiveStar();
?>
<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
	</div>
	
	
	<div class="col-md-6"> <!-- THE MIDDLE TAB -->
	<?php		echo '<table class="table table-hover">
							<thead>
							  <tr>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Course Code</th>
								<th>Rank</th>
                               </tr>
							</thead>
							<tbody>';
				foreach($showMeWho as $value){
								  ?> 
                              	<tr>							 
									<td><?php echo $value->fname?></td>
									<td><?php echo $value->lname?></td>
                                    <td><?php echo $value->course_code?></td>
									<td><img src="<?php echo $value->rank_url?>" width="40" height="30"></td>
                                 </tr>
                                    <?php }?>
	
	</div>
	
	
	<div class="col-md-3"><!-- THE RIGHT TAB -->
	
	</div>
</div> 
