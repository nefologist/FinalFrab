<?php
include 'student.php';
include 'header.php';
/*print "<pre>";
	print_r($gradesCourse);	
	print "</pre>";		
*/
if(!isset($_SESSION['acctype']))
	header("Location: home.php");
$studentObj=new student();
$studentObj->getStudentInfo($_SESSION['userName']);
$currentCourse =$studentObj->getAvailableCourse();
$lectures=$studentObj->getTeachers();
 	
if(isset($_GET['action'])){
	$action = $_GET['action'];
	if($action ==  "addCourse"){
	if($studentObj->addCourse($_SESSION['userName'],$_GET['courseid'])){
	header("Location: profile.php");
	}else
		echo "<h3>Already Enrolled in the Course</h3>";
	}else if($action == "deleteCourse"){
		$studentObj->deleteCourse($_SESSION['userName'],$_GET['courseid']);
		}
}	
?>

<div class="container">
  <div class="modal fade" id="addCourseModal" role="dialog">
    <div class="modal-dialog">    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Select the Course: </h4>
        </div>
        <div class="modal-body">         
           <div class="form-group">
            <label class="control-label col-sm-3" for="type">Course ID:</label>
                <div class="col-sm-7">          
                    <select class="form-control" id="courseID" name="courseID">
                    <?php 
						foreach($currentCourse as $value ){
                        echo "<option value='$value->course_id'>$value->course_name-Section: $value->section</option>";
                        }?>
                    </select>                    
                </div>
            </div>       
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default" id="add" name="submit" value="addCourse" onclick="submitCourse(this.value)"> Add Course</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>         
        </div>
      </div>
      
    </div>
  </div>
  
</div>


<div class="container">
  <div class="modal fade" id="deleteCourseModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Select the Course: </h4>
        </div>
        <div class="modal-body">         
             <div class="form-group">
           	 <label class="control-label col-sm-3" for="type">Course ID:</label>
                <div class="col-sm-7">          
                    <select class="form-control" id="deletecourseID" name="courseID">
                    <?php 
						if($tempCourse=$studentObj->showTempCourse($_SESSION['userName'])){
						foreach($tempCourse as $value ){
                        echo "<option value='$value->course_id'>$value->course_name $value->section</option>";
                        }}else{ echo "You have no avaiable ";
						}?>
                    </select>                    
                </div>
            </div>
            
        
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default" id="add" name="submit" value="deleteCourse" onclick="deleteCourse(this.value)">Delete Course</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
         
        </div>
      </div>
      
    </div>
  </div>
  
</div>


<!--THE MODAL IS ABOVE!!!!!-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
            <?php echo "<h3>Welcome Back ".$_SESSION['fname']." ".$_SESSION['lname']."</h3>";
                echo "<img alt='' class='img-responsive' width='250' height='150' src=".$_SESSION['imageurl'].">";
            ?>
            <?php if($ranks=$studentObj->getRank($_SESSION['userName'])){;
	
			foreach($ranks as $key){
				echo "<img alt='' style='float: left; width: 25%; margin-right: 1%; margin-bottom: 0.5em;' class='img-responsive' src=".$key->rank_url."> $key->rank_name<br>";
			}}else;
			?>
            <br><br><br><br><br><br>
            <a href="editprofile.php">Edit Profile</a>
        </div>
        <!-- LIST OF ALL THE STUDENT COURSES WILL BE CLICKABLE FOR MORE INFORMATION -->
        <div class="col-sm-8" >
            <div class="container">
                    <div id="content">
                    
                    <h2>Enrolled Courses</h2>
                      <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                          <?php if(!empty($studentObj->courses)){
						  foreach ($studentObj->courses as $row){
							$rankGrade=0;
                            echo "<li><a href=#".$row['course_code']." data-toggle='tab'>".$row['course_name']."</a></li>";}
						  ?> 
                      </ul>
                      <div id="my-tab-content" class="tab-content">
                      <?php
					   	$rankGrade=0;				  
						foreach ($studentObj->courses as $row){?>
					  	<div class='tab-pane' id="<?php echo $row['course_code']?>">                         
						<h4><?php echo $row['course_code']?></h4>
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
								 	if($gradeInfo=$studentObj->getGradeStudent($_SESSION['userName'],$row['course_id'])){
										foreach($gradeInfo as $value){
								  ?> 
                              	<tr>							 
									<td>
									<a href="showGrades.php?gradeId=<?php echo $value->grade_id?>"><?php echo $value->assessment_name?></td></a>
									<td><?php echo $value->assessment_weight."%"?></td>
									<td><?php echo $value->grade?></td>
                                    <td><?php 
									$percenageGrade = $value->assessment_weight / 100* $value->grade;
									echo $percenageGrade;?></td>
                                    </tr>
                                    <?php }echo "</tbody></table></div>";}else{
										echo "<tr><td>No Grade is Allocated as yet!</td></tr></tbody></table></div>";
									}}
						
									?>
								  
							 					
							
						  </table>
						</div>
                      <?php }else echo "You are Currently Not enrolled in any classes<br>";?>
                      
                      <h4 hidden=""></h4><br><br>
                      
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCourseModal">Add Course</button> 
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteCourseModal">Delete Course</button>

                      </div>
                                
                    </div>  <br><br><br>
                             
         <!--   </div>   -->
       
       
      <form class="form-horizontal" name="search" method="GET" action="search.php" id="search">	
        <div class="form-group">
            <label class="control-label col-sm-2" for="type">Course Code:</label>
                <div class="col-sm-3">          
                    <select class="form-control" id="course_code" name="course_code">
                    <option value='%'>Anything</option>
                   <?php 
						foreach($currentCourse as $value )
						{
                        echo "<option value='$value->course_code'>$value->course_code</option>";
                        }
					?>
                    </select>
                    
                </div>
            </div>
            <div class="form-group">
            <label class="control-label col-sm-2" for="type">Course Name:</label>
                    <div class="col-sm-3">          
                        <select class="form-control" id="course_name" name="course_name">
                        <option value='%'>Anything</option>
                           <?php 
                                foreach($currentCourse as $value ){
                                echo "<option value='$value->course_name'>$value->course_name-Section: $value->section</option>";
                            }?>
                        </select>
                    </div>	
                </div> 
                
                <div class="form-group">
            	<label class="control-label col-sm-2" for="type">Lecture Name:</label>
                <div class="col-sm-3">          
                    <select class="form-control" id="lecturer" name="lecturer">
                    <option value='%'>Anything</option>
                    <?php foreach($lectures as $value ){
                        echo "<option value='$value->userid'>$value->fname"." "."$value->lname</option>";
                        }?>
                    </select>
                </div>	
                </div>        
    
            <div class="form-group">        
              <div class="col-sm-offset-2 col-sm-3">
                <button type="submit" name="search" id="search" class="btn btn-default">Search</button>
               </div>
            </div>
	</form>
        </div>
 		             
        
<!---ADD ON COLOUM PLEASE DONT GO OVER BOARD--->
</div>


<?php include 'footer.php';?>