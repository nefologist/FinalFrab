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
	$sql = "SELECT g.grade_id FROM grade g, available_course a WHERE g.course_id = a.course_id AND a.disabled = 0 AND a.lecturer_id = " . $_SESSION['teacher_id'];
	$result = $_SESSION['teacher']->dbconn->conn->query($sql);
	foreach ($result->fetchAll(PDO::FETCH_OBJ) as $grade)
	{								 
		$grade_id = $grade->grade_id;
		$grade_percent = $_POST['weight'.$grade_id]/100;
		$grade_name = $_POST['name'.$grade_id];
		echo $grade_name;
		$_SESSION['teacher']->modify_assessment($grade_id, $grade_name, $grade_percent );
	}
}

if(isset($_POST['close_m_assessments']))
{
	header("location:lecturer_home.php");
	exit;
}
?>


<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
    <form method="post" action="modify_assessment.php" name="course_select" id="course_select">
    	<select class="form-control" name="course" id="course" required>
			<?php
                $sql = "SELECT a.course_code, c.course_name, a.section FROM available_course a, course c WHERE a.course_code = c.course_code AND lecturer_id = " . $_SESSION['teacher_id'] ." AND a.disabled = 0;";
                $result = $_SESSION['teacher']->dbconn->conn->query($sql);
                foreach($result->fetchAll(PDO::FETCH_OBJ) as $course)
                {
                    if(isset($_POST['course']) && $_POST['course'] == $course->course_code)
                        echo '<option value="' . $course->course_code . '" selected="selected">' . $course->course_name . " " . $course->section. '</option>';
                    else
                        echo '<option value="' . $course->course_code . '">' . $course->course_name . '</option>';
                }
            ?>
        </select>
        <input type="submit" name="view_course" id="view_course" value="View Course">
	</form>
    </div>
	
	
	<div class="col-md-6"> <!-- THE MIDDLE TAB -->
    
    
	<?php
			echo '<form class="form-horizontal" role="form" action="modify_assessment.php" method="post" name="modify_assessment_form" id="modify_assessment_form">';
			if(isset($_POST['course']))
			$sql = "SELECT g.grade_id, a.course_code, section, assessment_name, assessment_weight FROM grade g, available_course a WHERE g.course_id = a.course_id AND a.disabled = 0 AND a.lecturer_id = " . $_SESSION['teacher_id'] . " AND a.course_code = '" . $_POST['course'] . "'";
			else
			$sql = "SELECT g.grade_id, a.course_code, section, assessment_name, assessment_weight FROM grade g, available_course a WHERE g.course_id = a.course_id AND a.disabled = 0 AND a.lecturer_id = " . $_SESSION['teacher_id'];
			
			$result = $_SESSION['teacher']->dbconn->conn->query($sql);
			echo '<div name="grade_display_div" id="grade_display_div">';
			echo "<table class='table table-hover'>
				<thead>
				  <tr>
					<th>Course_code</th>
					<th>Section</th>
					<th>Assessment Name</th>
					<th>Assessment Weight (%)</th>
				  </tr>
				</thead>
				<tbody>"; 
					foreach ($result->fetchAll(PDO::FETCH_OBJ) as $grade)
					{								 
						$show_grade = $grade->assessment_weight*100;
						//$show_grade = $show_grade . "%";
						echo"<tr>
						<td>$grade->course_code</td>
						<td>$grade->section</td>
						<td><input type='text' name='name$grade->grade_id' value='$grade->assessment_name' required='required'></td>
						<td><input type='number' min='1' name='weight$grade->grade_id' value='$show_grade' required='required'></td></tr>";
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
	
	
	<div class="col-md-3"><!-- THE RIGHT TAB -->
	
	</div>


<form class="form-horizontal" role="form" method="POST" action="modify_assessment.php" name="close_assessment_form" id="close_assessment_form">
   
   		 <div class="form-group">        
		  <div class="col-sm-offset-2 col-sm-10">
         	
		  </div>
		</div>
     
</form>


<?php include 'footer.php';?>