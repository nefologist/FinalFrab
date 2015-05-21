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

if(isset($_POST['add']))
{
	if(!empty($_POST['student_id']))
	{
		foreach($_POST['student_id'] as $student)
		{
			$s_peices = explode("," , $student);
			$ss = [$s_peices[0]];
			$_SESSION['teacher']->add_student($s_peices[1], $ss);
		}
	}
}

if(isset($_POST['remove']))
{
	if(!empty($_POST['student_id']))
	{
		foreach($_POST['student_id'] as $student)
		{
			$s_peices = explode("," , $student);
			$ss = [$s_peices[0]];
			$_SESSION['teacher']->remove_student($s_peices[1], $ss);
		}
	}
}

if(isset($_POST['close_add_students']))
{
	header("location:lecturer_home.php");
	exit;
}

?>

<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
	</div>
	
	
	<div class="col-md-6"> <!-- THE MIDDLE TAB -->
	<form class="form-horizontal" role="form" action="add_student_course.php" method="post" name="add_student" id="add_student">
	<?php
		$sql = "SELECT c.course_code, c.course_name, a.course_id, a.section, userid, fname, lname FROM `user_table` u, `available_course` a, `course` c, `temp_course` t WHERE `verified` = 1 AND u.`disabled` = 0 AND a.`disabled` = 0 AND u.userid = t.student_id AND a.course_code = c.course_code AND a.course_id = t.course_id AND a.lecturer_id = " . $_SESSION['teacher_id'];
		$result = $_SESSION['teacher']->dbconn->conn->query($sql);			  
		echo '<div name="add_display_div" id="add_display_div">';
		echo "<table class='table table-hover'>
			<thead>
			  <tr>
			  	<th>select</th>
				<th>Course Code</th>
				<th>Course Name</th>
				<th>Section</th>
				<th>Student ID</th>
				<th>First Name</th>
				<th>Last Name</th>
			  </tr>
			</thead>
			<tbody>"; 
				foreach ($result->fetchAll(PDO::FETCH_OBJ) as $students)
				{
					echo "<tr>
					<td> <input type='checkbox' name='student_id[]' value='$students->userid,$students->course_id'></td>
					<td> $students->course_code</td>
					<td> $students->course_name</td>
					<td> $students->section</td>
					<td> $students->userid</td>
					<td> $students->fname</td>
					<td> $students->lname</td></tr>";
				}
			echo "
			</tbody>
		</table>";
		if($result->rowCount() == 0)
		{
			echo "There are no more students to add.<br>";
		}
	?> <div class="form-group">        
        <div class="col-sm-offset-2 col-sm-10">
    	<input class="btn btn-default" type="submit" id="add" name="add" value="Add">
        <input class="btn btn-default" type="submit" id="remove" name="remove" value="Remove">
       </div>
    </div>
</form>
<form class="form-horizontal" role="form" method="POST" action="verify_students.php" name="close_verify_form" id="close_verify_form">
	<div class="form-group">        
        <div class="col-sm-offset-2 col-sm-10">
     <input class="btn btn-default" type="submit" name="close_add_students" id="close_add_students" value="Quit">
            </div>
    </div>
</form>

	<div class="col-md-3"><!-- THE RIGHT TAB -->
	
	</div>
</div> 


<?php include 'footer.php';?>