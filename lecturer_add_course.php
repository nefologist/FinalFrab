<?php 
include 'header.php';
include 'teacher.php';

if(!isset($_SESSION['acctype']) || $_SESSION['acctype'] != "Teacher")
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

$_SESSION['teacher'] = new teacher($_SESSION['teacher_id']);

if(isset($_POST['submit_add_course']))
{
	if($_SESSION['teacher']->add_course($_POST['course'], (int)$_POST['section']))
	{
		header("location:add_assessment.php");
		exit;
	}
	else
		echo "<script>alert('That class section already exists, try agian with another section')</script>";
}

?>
<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
	</div>
	
	
	<div class="col-md-6"> <!-- THE MIDDLE TAB -->
    
	<form class="form-horizontal" name="add_course_form" id="add_course_form" method="post" action="lecturer_add_course.php">
    	<div class="form-group">
            <label class="control-label col-sm-2"  for="course">Course</label>
            <div class="col-sm-4">
                <select class="form-control" name="course" id="course" required>
                <option value="">Select a course</option>
                <?php
                    $sql = "SELECT * FROM course WHERE disabled = 0;";
                    $result = $_SESSION['teacher']->dbconn->conn->query($sql);
                    foreach($result->fetchAll(PDO::FETCH_OBJ) as $course)
                    {
                        if(isset($_POST['course']) && $_POST['course'] == $course->course_code)
                            echo '<option value="' . $course->course_code . '" selected="selected">' . $course->course_name . '	</option>';
                        else
                            echo '<option value="' . $course->course_code . '">' . $course->course_name . '</option>';
                    }
                ?>
            </select>
        </div>
     </div>        
    
        
     <div class="form-group">
        <label class="control-label col-sm-2" for="section">Section No. </label>
        <div class="col-sm-4">
        <input type="number" class="control-label col-sm-5" name="section" id="section" max="15" min="1" >
      	</div>
    </div>   
    
        <div class="form-group">
             <div class="col-sm-4">
            <input class="btn btn-default" type="submit" name="submit_add_course" id="submit_add_course">
            </div>
        </div>        
    </form>
    
	
	</div>
</div>	
	
	<div class="col-md-3"><!-- THE RIGHT TAB -->
	
	</div>


<?php include 'footer.php';?>