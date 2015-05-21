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

if(isset($_POST['verify']))
{
	$_SESSION['teacher']->verify_student($_POST['student_id']);
}

if(isset($_POST['reject']))
{
	$_SESSION['teacher']->reject_student($_POST['student_id']);
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
	<form class="form-horizontal" role="form" action="verify_students.php" method="post" name="verify_student" id="verify_student">
	<?php
		$sql = "SELECT userid, verified, fname, lname, dob FROM `user_table` WHERE `verified` = 0 AND `account_type` = 'Student'";
		$result = $_SESSION['teacher']->dbconn->conn->query($sql);			  
		echo "<table class='table table-hover'>
			<thead>
			  <tr>
			  	<th>Select</th>
				<th>Student ID</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>D.O.B</th>
			  </tr>
			</thead>
			<tbody>"; 
				foreach ($result->fetchAll(PDO::FETCH_OBJ) as $students)
				{
					echo "<tr>
					<td> <input type='checkbox' name='student_id[]' value='$students->userid'></td>
					<td> <input type='checkbox' name='error[$students->userid]' value='id$students->userid' >$students->userid</td>
					<td> <input type='checkbox' name='error[$students->userid]' value='fname$students->userid' >$students->fname</td>
					<td> <input type='checkbox' name='error[$students->userid]' value='lname$students->userid' >$students->lname</td>
					<td> <input type='checkbox' name='error[$students->userid]' value='dob$students->userid' >$students->dob</td></tr>";
				}
			echo "
			</tbody>
		</table>";
		if($result->rowCount() == 0)
		{
			echo "There are no more students to Verify at the Moment.<br>";
		}
	?>
    <div class="form-group">        
        <div class="col-sm-offset-2 col-sm-10">
        <input type="submit" class="btn btn-default" id="verify" name="verify" value="verify">
        <input type="submit" class="btn btn-default" id="reject" name="reject" value="reject">
        </div>
    </div>
</form>
	
	</div>
	
	
	<div class="col-md-3"><!-- THE RIGHT TAB -->
	
	</div>
</div> 

</div>

<?php include 'footer.php';?>