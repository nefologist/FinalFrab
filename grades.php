<?php
include 'header.php';
include 'student.php';

$myHistory= new student();

if(!isset($_SESSION['acctype']))
{
	if(isset( $_COOKIE[session_name()])) // destroy the cookie which stores the session id
	{
		setcookie( session_name(), "", time()-3600, "/");
	}
	$_SESSION = array();
	session_unset();
	session_destroy();
	header("location:home.php");
}

?>
<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
   	</div>
	
	
	<div class="col-md-6"> <!-- THE MIDDLE TAB -->
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
         <div class="panel panel-default">
         
         	<?php if($gradeHistory=$myHistory->myHistory($_SESSION['userName']))
			foreach($gradeHistory as $info){?>
            <div class="panel-heading" role="tab" id="headingOne">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $info->course_code,$info->course_id ?>" aria-expanded="true" aria-controls="collapseOne">
                  <?php echo $info->course_name?>
                </a>
              </h4>
            </div>
            <div id="<?php echo $info->course_code,$info->course_id;?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
              <div class="panel-body">
                <table class='table table-hover'>
							<thead>
							  <tr>
								<th>Assessment Type</th>
								<th>Assessment Weight</th>
								<th>Grade</th>
                                <th>Percentage Grade </th>
							  </tr>
							</thead>
							<tbody>
								  
                                  <?php 
								 	if($gradeInfo=$myHistory->getGradeStudent($_SESSION['userName'],$info->course_id)){
										foreach($gradeInfo as $value){
								  ?> 
                                  <tr>								 
									<td><?php echo $value->assessment_name?></td>
									<td><?php $aw = $value->assessment_weight * 100; echo $aw ."%";?></td>
									<td><?php echo $value->grade?></td>
                                    <td><?php 
									$percenageGrade = $value->assessment_weight * $value->grade;
									echo $percenageGrade;?></td>
                                    </tr></div>
                                    <?php }}else echo "You have no History At the Moment";
						
									?>
								  
								  					
							</tbody>
						  </table>
              </div>
            </div>
          </div>
          
          
          
        </div> <?php };?>
    </div>	
	
    <div class="col-md-3"><!-- THE RIGHT TAB -->	
	</div>
    
</div> 


<?php  include 'footer.php';?>