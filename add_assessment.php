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

$sql = "SELECT course_id, course_code FROM available_course ORDER BY course_id DESC LIMIT 1";
$result = $_SESSION['teacher']->dbconn->conn->query($sql);
$course_id = $result->fetch(PDO::FETCH_OBJ);
$course_code = $course_id->course_code;
$course_id = $course_id->course_id;

if(isset($_POST['submit_assessments']))
{
	if((int)$_POST['assessment_amount'] == 1)
	{
		$_SESSION['teacher']->add_assessment($course_id, $_POST['assessment_name'], (int)$_POST['assessment_weight']/100);
	}
	else
	{
		for($i = 0; $i < (int)$_POST['assessment_amount']; $i++)
		{
			$_SESSION['teacher']->add_assessment($course_id, $_POST['assessment_name'] . ( $i + 1 ), (int)$_POST['assessment_weight']/100);
		}
	}
	$sql = "SELECT assessment_name, assessment_weight FROM grade WHERE course_id = $course_id";
	$result = $_SESSION['teacher']->dbconn->conn->query($sql);			  
	echo '<div name="grade_display_div" id="grade_display_div">';
	echo "<h4>$course_code</h4>";
    echo "<table class='table table-hover'>
		<thead>
		  <tr>
			<th>Assessment Name</th>
			<th>Assessment Weight</th>
		  </tr>
		</thead>
		<tbody>"; 
			foreach ($result->fetchAll(PDO::FETCH_OBJ) as $grade)
			{								 
				echo "<tr><td> $grade->assessment_name</td>
				<td> $grade->assessment_weight</td></tr>";
			}
		echo "
		</tbody>
	  </table>
	  </div>";
}

if(isset($_POST['close_assessments']))
{
	$sql = "SELECT assessment_name, assessment_weight FROM grade WHERE course_id = $course_id";
	$result = $_SESSION['teacher']->dbconn->conn->query($sql);
	if($result->rowCount() == 0)
	{
		echo "<script>alert('Your must enter a grade!')</script>";
	}
	else 
	{
		$_SESSION['course_code'] = "";
		echo "<script>alert('Your course is ready!')</script>";
		header("location:lecturer_home.php");
		exit;
	}
}

?>

<div name="add_assessment_div" id="add_assessment_div">
	<form method="POST" action="add_assessment.php" name="add_assessment_form" id="add_assessment_form">
    	<label for="assessment_name">Assessment Name:</label><input type="text" name="assessment_name" id="assessment_name"><br>
        <label for="assessment_amount">Assessment Amount(1 if unique):</label><input type="number" min="1" max="5" name="assessment_amount" id="assessment_amount"><br>
        <label for="assessment_weight">Assessment Weight: </label><input type="number" name="assessment_weight" id="assessment_weight"><br>
        <input type="submit" name="submit_assessments" id="submit_assessments">
    </form>
    <form method="POST" action="add_assessment.php" name="close_assessment_form" id="close_assessment_form">
     	<input type="submit" name="close_assessments" id="close_assessments" value="Quit">
    </form>
</div>


<?php include 'footer.php';?>