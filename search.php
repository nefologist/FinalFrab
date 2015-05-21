<?php 
include 'header.php';
include_once 'logindb.php';

$dbconn = new logindb();
$dbconn->connectdb();

if(isset($_GET['course_code']))
{
	$cc =  $_GET['course_code'];
	$sql = "SELECT * FROM `available_course` WHERE course_code = '$cc' AND disabled = 0;";
	$result = $dbconn->conn->query($sql);
	if($result->rowCount() == 0)
	{
		$cc = '%';
	}
}
else
{
	$cc = '%';
}

if(isset($_GET['course_name']))
{
	$cn =  $_GET['course_name'];
	$sql = "SELECT course_name FROM `available_course` a, `course` c WHERE c.course_code = a.course_code AND c.course_name = '$cn' AND a.disabled = 0 AND c.disabled = 0;";
	$result = $dbconn->conn->query($sql);
	if($result->rowCount() == 0)
	{
		$cn = '%';
	}
}
else
{
	$cn = '%';
}

if(isset($_GET['lecturer']))
{
	$l = $_GET['lecturer'];
	$sql = "SELECT * FROM `available_course` a, course c  WHERE lecturer_id = '$l' AND a.disabled = 0 AND c.disabled = 0;";
	$result = $dbconn->conn->query($sql);
	if($result->rowCount() == 0)
	{
		$l = '%';
	}
}
else
{
	$l = '%';
}

$sql ="SELECT a.course_id, a.course_code, c.course_name, u.fname, u.lname, a.section FROM `available_course` a, `user_table` u, `course` c WHERE a.course_code = c.course_code AND a.lecturer_id = u.userid AND a.disabled = 0 AND c.disabled = 0 AND a.course_code LIKE '$cc' AND c.course_name LIKE '$cn' AND a.lecturer_id LIKE '$l'";
$result = $dbconn->conn->query($sql);

echo "<div class='row'> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class='col-md-3'><!-- THE LEFT TAB -->
	</div>
	
	
	<div class='col-md-6'> <!-- THE MIDDLE TAB -->
	<table class='table table-hover'>
		<thead>";?>

<?php if($result->rowCount() > 0)
{
	echo "<tr>
		<th>Course Id</th>
		<th>Course Code</th>
		<th>Course Name</th>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Section</th>
		<th>Add</th>
	</tr>
	</thead>
	<tbody>";
	foreach($result->fetchAll(PDO::FETCH_OBJ) as $final)
	{
		echo "<tr>";
		echo "<td>" . $final->course_id . "</td>";
		echo "<td>" . $final->course_code . "</td>";
		echo "<td>" . $final->course_name  . "</td>";
		echo "<td>" . $final->fname . "</td>";
		echo "<td>" . $final->lname . "</td>";
		echo "<td>" . $final->section . "</td>";
		echo "<td><button type='button' id='add' name='add' value='$final->course_id' onclick='searchAddCourse(this.value)'>Add Course</button></td>";
		echo "</tr>";
	}
	echo  "</tbody>
	</table>";
}
else
{
	echo "<tr><th>There are no results to your search!</th></tr></thead></table>";
}?><?php echo "
	
	</div>	
	
	<div class='col-md-3'><!-- THE RIGHT TAB -->
	
	</div>
</div> ";

?>