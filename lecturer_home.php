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
?>

<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
	
    </div>	
	
	<H3 id="status" align="center">
	<?php 
		
		if(empty($_SESSION))
			echo "Welcome To the Frabman Rank Me Now!"; 
			else if($_SESSION['verified']==false && $_SESSION['disable'] == false )
				echo "Your Account Need to Be Verified Please Check Your Email or Contact Admin";
			else if($_SESSION['verified']==false && $_SESSION['disable'] == true )
					echo "Your Account is Current Rejected Please Contact " . $_SESSION['disabledBy'];  
				else if($_SESSION['verified']==true && $_SESSION['disable'] == false ) 
					echo "Welcome ".$_SESSION['teacher']->fname." To the Frabman Rank Me Now!";
					
				
		?></H3>

<?php include 'footer.php';?>