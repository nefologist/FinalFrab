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

?>

<div name="profile_form_holder" id="profile_form_holder">
	<form action="teacher_profile.php" method="post">
    	
    </form>
</div>



<?php include 'footer.php';?>