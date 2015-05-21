<?php
//include 'student.php';
include 'header.php';
include 'admin.php';
$admin = new admin($_SESSION['admin_id']);
		
?>


<!--THE MODAL IS ABOVE!!!!!-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
            <?php echo "<h3>Welcome Back ".$admin->fname."</h3>";
                echo "<img alt='' class='img-responsive' width='250' height='150' src=".$admin->imageurl.">";
            ?>
           <a href="editprofilelecture.php">Edit Profile</a>
        </div>
        <!-- LIST OF ALL THE STUDENT COURSES WILL BE CLICKABLE FOR MORE INFORMATION -->
        <div class="col-sm-8" >
            <div class="container">
                    <div id="content">
                    <a href="teacherForm.php">Add New Lecturer</a>
                    </div>
                                
           </div>           
        </div>
           
        </div>
 		             
        
<!---ADD ON COLOUM PLEASE DONT GO OVER BOARD--->
</div>


<?php include 'footer.php';?>