<?php 
include 'student.php';
include 'header.php';
include 'userController.php';


?>

<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
		
    	</div>	
	
	
					
				
		
	<!-- THE MIDDLE TAB -->    
	
	<!------Yserri MY SLIDE --------->
            <div class="col-md-6" >
            <H3 align="center">
	<?php if(empty($_SESSION))
			echo "Welcome To the Frabman Rank Me Now!"; 
			else if($_SESSION['verified']==false && $_SESSION['disable'] == false )
				echo "Your Account Need to Be Verified Please Check Your Email or Contact Admin";
			else if($_SESSION['verified']==false && $_SESSION['disable'] == true )
					echo "Your Account is Current Rejected Please Contact ".$_SESSION['disabledBy'];  
				else if($_SESSION['verified']==true && $_SESSION['disable'] == false ) 
					echo "Welcome ".$_SESSION['fname']." To the Frabman Rank Me Now!";?></H3>
                  
                  <div class="container">
                   <?php if ($showMe=getGeneral()) { ?>        
                  <img src="<?php echo $showMe->image_url?>" class="img-rounded" alt="Cinque Terre" width="600" height="450" align="center"> 
                </div>         
                    <p class="flex-caption" align="center"><?php echo $showMe->fname." ".$showMe->lname." "?>is a Well Claimed General!</p>
				   <?php };?>    
            </div>
    <!------Yserri MY SLIDE --------->
      	
	<div class="col-md-3">	
	<!-- THE RIGHT TAB -->
	
	</div>
    
</div> 


<?php include 'footer.php';?>