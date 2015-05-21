<?php
include "header.php";
include "student.php";

if(isset($_GET['gradeId'])){
	$grade= new student();
	if($showGrade=$grade->getCourseGrade($_GET['gradeId']))
		{
			echo '<table class="table table-hover">
							<thead>
							  <tr>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Grade</th>
                               </tr>
							</thead>
							<tbody>';
				foreach($showGrade as $value){
								  ?> 
                              	<tr>							 
									<td><?php echo $value->fname?></td>
									<td><?php echo $value->lname?></td>
									<td><?php echo $value->grade?></td>
                                 </tr>
                                    <?php }
			
		}else{
			echo "No Grade";
		}
		

	
}
?>