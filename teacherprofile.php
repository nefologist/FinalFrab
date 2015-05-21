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

$lecture = new teacher($_SESSION['teacher_id']);
		
?>


<!--THE MODAL IS ABOVE!!!!!-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
            <?php echo "<h3>Welcome Back ".$lecture->fname." ".$lecture->lname."</h3>";
                echo "<img alt='' class='img-responsive' width='250' height='150' src=".$lecture->imageurl.">";
            ?>
           <a href="editprofilelecture.php">Edit Profile</a>
        </div>
        <!-- LIST OF ALL THE STUDENT COURSES WILL BE CLICKABLE FOR MORE INFORMATION -->
        <div class="col-sm-8" >
            <div class="container">
                    <div id="content">
                    
                    </div>
                                
           </div>           
        </div>
           
        </div>
 		             
        
<!---ADD ON COLOUM PLEASE DONT GO OVER BOARD--->
</div>


<?php include 'footer.php';?>