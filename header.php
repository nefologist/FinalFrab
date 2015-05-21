<?php 
session_start();
include_once'logindb.php';
$dbconn = new logindb();
$dbconn->connectdb();

if(isset($_GET['status'])){
	
	$sql = "SELECT session_id FROM sessions ORDER BY session_id DESC LIMIT 1";
	$result = $dbconn->conn->query($sql);
	$sessionid = $result->fetch(PDO::FETCH_OBJ);
	$sessionid = $sessionid->session_id;
	
	if($_SESSION['acctype'] == 'Student')
	$sql = "UPDATE `frabman`.`sessions` SET `logout_date` = NOW() WHERE `sessions`.`user_id` = " . $_SESSION['userName'] . " AND `sessions`.`session_id`= " . $sessionid;
	elseif($_SESSION['acctype'] == 'Teacher')
	$sql = "UPDATE `frabman`.`sessions` SET `logout_date` = NOW() WHERE `sessions`.`user_id` = " . $_SESSION['teacher_id'] . " AND `sessions`.`session_id`= " . $sessionid;
	else
	$sql = "UPDATE `frabman`.`sessions` SET `logout_date` = NOW() WHERE `sessions`.`user_id` = " . $_SESSION['admin_id'] . " AND `sessions`.`session_id`= " . $sessionid;
	$result = $dbconn->conn->query($sql);
	if(isset( $_COOKIE[session_name()])) // destroy the cookie which stores the session id
	{
		setcookie( session_name(), "", time()-3600, "/");
	}
	$_SESSION = array();
	session_unset();
	session_destroy();
	header("location:home.php");
	exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Rank System</title>
<!------Yserri MY SLIDE --------->
	<!-- <link rel="stylesheet" type="text/css" href="style.css"> -->
     <script type="jqueryui-1.11.4//jquery-ui.js"></script>
	<script src="jqueryui-1.11.4/jquery.min.js"></script>
	<link rel="stylesheet" href="bootstrap3.3.4/css/bootstrap.min.css">
 	<script src="bootstrap3.3.4/js/bootstrap.min.js"></script>	
    <script type="text/javascript" src="jqueryui-1.11.4/jquery.js"></script>
    <script src="rank_ajax.js"></script>
</head>

<body>
	<div align="center"  >
	 <!-- <img alt="header" src="webimages/frabman_header.jpg"> -->
	</div> 	
<!--Yserri Menu -->
        
             <nav class="navbar navbar-default ">
              <div class="container">
                <ul class="nav navbar-nav">
				<?php 
				
				if(!empty($_SESSION)){ 
					if(isset($_SESSION['acctype']))
					{
						switch ($_SESSION['acctype']){
							case "Student":
							if($_SESSION['verified'] == false && $_SESSION['disable'] == false )
							;
							else if ($_SESSION['verified'] == true && $_SESSION['disable']== false ){
								echo '<h1 class="logo"><a href="home.php">The Frabman</a></h1>';
								echo '<li class="active"><a href="home.php">Home</a></li>';
								echo "<li><a href='profile.php' name='Profile'>Profile</a></li>";
								echo "<li><a href='grades.php' name='Profile'>My Grades</a></li>";
								echo "<li><a href='profile.php' onclick='showHidden()' name='search'><span class='glyphicon glyphicon-search'></span>Search</a></li>";
							}
								break;
							case "Admin":
								echo '<h1 class="logo"><a href="admin_home.php">UB RANK</a></h1>';
								echo '<i class="icon-remove menu-close"></i>';
								echo '<li class="active"><a href="admin_home.php">Home</a></li>';
								echo "<li><a href='adminprofile.php' name='Profile'>Profile</a></li>";
								break;
							case "Teacher":
							if($_SESSION['verified'] == 1 && $_SESSION['disable'] == 1 )
							;
							else if ($_SESSION['verified'] == 1 && $_SESSION['disable']== 0 )
							{	
								echo '<li><a href="lecturer_home.php">UB RANK</a></li>';
								echo '<li><a href="lecturer_home.php">Home</a></li>';
								echo "<li><a href='editprofilelecture.php' name='Profile'>Profile</a></li>";
								echo "<li><a href='verify_students.php' name='verify_students'>Verify Students</a></li>";
								echo "<li><a href='lecturer_add_course.php' name='Add_Course'>Add Course</a></li>";
								echo "<li><a href='lecturer_manual_add_assessment.php' name='Add_Assessment'>Add Assessment</a></li>";
								echo "<li><a href='modify_assessment.php' name='Modify_Assessment'>Modify Assessment</a></li>";
								echo "<li><a href='add_student_course.php' name='add_students'>Add Students to Course</a></li>";
								echo "<li><a href='lecturer_grade_student.php' name='grade_student'>Grade Assessments</a></li>";
								echo "<li><a href='lecturer_enable_disable.php' name='lock_account'>Lock/Unlock Student</a></li>";
								echo "<li><a href='showGenerals.php' name='show_generals'>Show General</a></li>";
							}
								break;
						}
					}
				} 
					else{ 
					echo '<h1 class="logo"><a href="home.php">LeFrabman</a></h1>';
					echo '<i class="icon-remove menu-close"></i>';
					echo '<li class="active"><a href="home.php">Home</a></li>';
				}
				?>
				<li><a href="about.php">About</a></li>
				   <?php if(!empty($_SESSION))
				   { 
					echo "<li><a href='?status=logout' id='logout' name='login'>Logout</a></li>";
					} 
					else{
					echo "<li><a href='login.php'>Login</a></li>";
				}
				?>
                
            </ul>
          </div>
        </nav>
        </body>
