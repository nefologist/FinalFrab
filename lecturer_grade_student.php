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

if(isset($_POST['modify_assessments']))
{
	$sql = "SELECT s.*, a.course_id FROM student_grades s, available_course a, grade g WHERE g.course_id = a.course_id AND s.grade_id = g.grade_id AND a.disabled = 0 AND a.lecturer_id = " . $_SESSION['teacher_id'];
	$result = $_SESSION['teacher']->dbconn->conn->query($sql);
	foreach ($result->fetchAll(PDO::FETCH_OBJ) as $grade)
	{								 
		$grade_id = $_POST['f_grade_id'.$grade->grade_id.$grade->student_id];
		$course_id = $_POST['f_course'.$grade->grade_id.$grade->student_id];
		$student_id = $_POST['f_student'.$grade->grade_id.$grade->student_id];
		$s_grade = $_POST['f_grade'.$grade->grade_id.$grade->student_id];
		$_SESSION['teacher']->modify_grade($course_id, $grade_id, $student_id, $s_grade );
	}
}

if(isset($_POST['close_m_assessments']))
{
	header("location:lecturer_home.php");
	exit;
}

?>

<!--<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

<!--	<div class="col-md-3"><!-- THE LEFT TAB -->
<div>
    <form method="post" action="lecturer_grade_student.php" name="course_select" id="course_select">
    <label for="course">Course</label>
    	<select class="form-control" name="course" id="course">
        <option value="">Any Course</option>
			<?php
                $sql = "SELECT a.course_code, c.course_name, a.section FROM available_course a, course c WHERE a.course_code = c.course_code AND lecturer_id = " . $_SESSION['teacher_id'] ." AND a.disabled = 0;";
                $result = $_SESSION['teacher']->dbconn->conn->query($sql);
				foreach($result->fetchAll(PDO::FETCH_OBJ) as $course)
				{
					if(isset($_POST['course']) && $_POST['course'] == $course->course_code)
						echo '<option value="' . $course->course_code . '" selected="selected">' . $course->course_name . ' ' . $course->section. '</option>';
					else
						echo '<option value="' . $course->course_code . '">' . $course->course_name . ' ' . $course->section . '</option>';
				}
				
            ?>
        </select>
        
        <label for="assessment">Assessment</label>
        <select class="form-control" name="assessment" id="assessment">
        <option value="">Any Assessment</option>
			<?php
                $sql = "SELECT c.course_name, a.section, g.assessment_name, g.grade_id FROM `course` c, `grade` g, `available_course` a WHERE a.course_id = g.course_id AND c.course_code = a.course_code AND a.lecturer_id = '". $_SESSION['teacher_id'] ."'";
                $result = $_SESSION['teacher']->dbconn->conn->query($sql);
                foreach($result->fetchAll(PDO::FETCH_OBJ) as $assessment)
                {
                    if(isset($_POST['assessment']) && $_POST['assessment'] == $course->course_code)
                        echo '<option value="' . $assessment->grade_id . '" selected="selected">' . $assessment->course_name . ': sec: ' . $assessment->section.' '. $assessment->assessment_name .'</option>';
                    else
                        echo '<option value="' . $assessment->grade_id . '>' . $assessment->course_name . ' ' . $assessment->section .': sec: '. $assessment->assessment_name .'</option>';
                }
            ?>
        </select>
        
        <label for="student">Student</label>
        <select class="form-control" name="student" id="student">
        <option value="">Any Student</option>
			<?php
                $sql = "SELECT s.student_id, u.fname, u.lname FROM `student_grades` s, `user_table` u WHERE s.student_id = u.userid";
                $result = $_SESSION['teacher']->dbconn->conn->query($sql);
                foreach($result->fetchAll(PDO::FETCH_OBJ) as $student)
                {
                    if(isset($_POST['student']) && $_POST['student'] == $student->student_id)
                        echo '<option value="' . $student->student_id . '" selected="selected">' . $student->student_id . ': ' . $student->fname. ' ' . $student->lname .'</option>';
                    else
                        echo '<option value="' . $student->student_id . '>' . $student->student_id . ': ' . $student->fname. ' ' . $student->lname .'</option>';
                }
            ?>
        </select>
        
        <input type="submit" name="view_grades" id="view_grades" value="View Grades">
	</form>
    </div>
	
	
	<div> <!-- THE MIDDLE TAB -->
    
    
	<?php
			echo '<form class="form-horizontal" role="form" action="lecturer_grade_student.php" method="post" name="grade_student" id="grade_student">';
			if(isset($_POST['course']))
			{
				$gcourse = $_POST['course_id'];
			}
			else
			{
				$gcourse = '%';
			}
			
			if(isset($_POST['grade_id']))
			{
				$ggrade = $_POST['grade_id'];
			}
			else
			{
				$ggrade = '%';
			}
			
			if(isset($_POST['student_id']))
			{
				$gstudent = $_POST['student_id'];
			}
			else
			{
				$gstudent = '%';
			}
			
			$sql = "SELECT a.course_id, a.course_code, a.section, s.grade_id, s.student_id, g.assessment_name, u.fname, u.lname, s.grade FROM `available_course` a, `grade` g, `student_grades` s, `user_table` u WHERE a.course_id = g.course_id AND g.grade_id = s.grade_id AND s.student_id = u.userid AND a.lecturer_id = " . $_SESSION['teacher_id'] . " AND a.course_id LIKE '" . $gcourse . "' AND s.grade_id LIKE '" . $ggrade . "' AND s.student_id LIKE '" . $gstudent . "'";
			
			$result = $_SESSION['teacher']->dbconn->conn->query($sql);
			//echo '<div name="grade_display_div" id="grade_display_div">';
			echo "<table class='table table-hover'>
				<thead>
				  <tr>
					<th>Course_code</th>
					<th>Section</th>
					<th>Assessment Name</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Grade (%)</th>
				  </tr>
				</thead>
				<tbody>"; 
					foreach ($result->fetchAll(PDO::FETCH_OBJ) as $grade)
					{								 
						echo"<tr>
						<td><input type='hidden' name='f_course".$grade->grade_id.$grade->student_id."' value='$grade->course_id'>$grade->course_code</td>
						<td>$grade->section</td>
						<td><input type='hidden' name='f_grade_id".$grade->grade_id.$grade->student_id."' value='$grade->grade_id'>$grade->assessment_name</td>
						<td><input type='hidden' name='f_student".$grade->grade_id.$grade->student_id."' value='$grade->student_id'>$grade->fname</td>
						<td>$grade->lname</td>
						<td><input type='number' min='0' name='f_grade".$grade->grade_id.$grade->student_id."' value='$grade->grade' required='required'></td></tr>";
					}
				echo "
				</tbody>
			</table>";
		?>
		 <div class="form-group">        
			  <div class="col-sm-offset-2 col-sm-10">
				 <input type="submit" class="btn btn-default" id="modify_assessments" name="modify_assessments">
			  </div>
		</div>    
	</form>
    
 <form class="form-horizontal" role="form" method="POST" action="modify_assessment.php" name="close_assessment_form" id="close_assessment_form">
   		 <div class="form-group">        
              <div class="col-sm-offset-2 col-sm-10">
                 <input type="submit" class="btn btn-default" name="close_m_assessments" id="close_m_assessments" value="Quit">
              </div>
          </div>
		
     
</form>
    
		
	</div>
	
	

    

<?php include 'footer.php';?>