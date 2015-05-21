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

if(isset($_POST['verify']) && isset($_POST['student']))
{
	$_SESSION['teacher']->view_student($_POST['student_id']);
}

if(isset($_POST['reject']))
{
	$_SESSION['teacher']->disable_student($_POST['student_id']);
}

if(isset($_POST['close_v_assessments']))
{
	header("location:lecturer_home.php");
	exit;
}

?>
<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
	</div>
	
	
	<div class="col-md-6"> <!-- THE MIDDLE TAB -->
	<form class="form-horizontal" role="form" action="lecturer_view_student.php" method="post" name="enable_student" id="enable_student">
	<?php
		$sql = "SELECT a.course_id, c.*, u.fname, u.lname, ranks.rank_name, r.student_id, r.course_grade FROM `user_table` u, `ranks`, `course` c, `available_course` a, `student_ranks` r WHERE c.course_code = a.course_code AND u.userid = a.lecturer_id AND ranks.rank_id = r.rank_id AND a.course_id = r.course_id AND u.account_type = 'Student'";
		$result = $_SESSION['teacher']->dbconn->conn->query($sql);			  
		echo "<table class='table table-hover'>
			<thead>
			  <tr>
			  	<th>Select to Rank</th>
				<th>Course Id</th>
				<th>Course Code</th>
				<th>Course Name</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Rank Name</th>
				<th>Course Grade</th>
			  </tr>
			</thead>
			<tbody>"; 
			if($result->rowCount() > 0){
				foreach ($result->fetchAll(PDO::FETCH_OBJ) as $students)
				{
					echo "<tr>
					<td> <input type='radio' name='student_id' value='$students->student_id'></td>
					<td> $students->course_id</td>
					<td> $students->course_code</td>
					<td> $students->course_name</td>
					<td> $students->fname</td>
					<td> $students->lname</td>
					<td> $students->rank_name</td>
					<td> $students->course_grade</td></tr>";
				}}
				else
				{
					echo "<td colspan='5'>There are no students</td>";
				}
			echo "
			</tbody>
		</table>";
		if($result->rowCount() == 0)
		{
			echo "There are no more students at the Moment.<br>";
		}
	?>
    <div class="form-group">        
        <div class="col-sm-offset-2 col-sm-10">
        <input type="submit" class="btn btn-default" id="verify" name="verify" value="View Profile">
        </div>
    </div>
</form>
	
	</div>
	
	
	<div class="col-md-3"><!-- THE RIGHT TAB -->
	
	</div>
</div> 

</div>

<?php include 'footer.php';?>