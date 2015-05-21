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
	$_SESSION['teacher']->enable_student($_POST['student_id']);
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
	<form class="form-horizontal" role="form" action="lecturer_enable_disable.php" method="post" name="enable_student" id="enable_student">
	<?php
		$sql = "SELECT userid, disabled_by, fname, lname, dob FROM `user_table` WHERE `disabled` = 0 AND `account_type` = 'Student'";
		$result = $_SESSION['teacher']->dbconn->conn->query($sql);			  
		echo "<table class='table table-hover'>
			<thead>
			  <tr>
			  	<th>Select to Enable</th>
				<th>Student ID</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>D.O.B</th>
			  </tr>
			</thead>
			<tbody>"; 
			if($result->rowCount() > 0){
				foreach ($result->fetchAll(PDO::FETCH_OBJ) as $students)
				{
					echo "<tr>
					<td> <input type='checkbox' name='student_id[]' value='$students->userid'></td>
					<td> $students->userid</td>
					<td> $students->fname</td>
					<td> $students->lname</td>
					<td> $students->dob</td></tr>";
				}}
				else
				{
					echo "<td colspan='5'>There are no Enabled students</td>";
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
        <input type="submit" class="btn btn-default" id="verify" name="verify" value="Enable">
        </div>
    </div>
</form>
	<form class="form-horizontal" role="form" action="lecturer_enable_disable.php" method="post" name="disable_student" id="disable_student">
	<?php
		$sql = "SELECT userid, disabled_by, fname, lname, dob FROM `user_table` WHERE `disabled` = 1 AND `account_type` = 'Student'";
		$result = $_SESSION['teacher']->dbconn->conn->query($sql);			  
		echo "<table class='table table-hover'>
			<thead>
			  <tr>
			  	<th>Select to Disable</th>
				<th>Disabled By</th>
				<th>Student ID</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>D.O.B</th>
			  </tr>
			</thead>
			<tbody>"; 
			if($result->rowCount() > 0){
				foreach ($result->fetchAll(PDO::FETCH_OBJ) as $students)
				{
					echo "<tr>.
					<td> <input type='checkbox' name='student_id[]' value='$students->userid'></td>
					<td>$students->disabled_by</td>
					<td>$students->userid</td>
					<td>$students->fname</td>
					<td>$students->lname</td>
					<td>$students->dob</td></tr>";
				}}
				else
				{
					echo "<td colspan='5'>There are no Disabled students</td>";
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
        <input type="submit" class="btn btn-default" id="reject" name="reject" value="Disable">
        </div>
    </div>
</form>
	</div>
	
	
	<div class="col-md-3"><!-- THE RIGHT TAB -->
	
	</div>
</div> 

</div>

<?php include 'footer.php';?>