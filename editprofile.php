<?php
include 'header.php';
include 'student.php';

$info = new student;
$info->getStudent($_SESSION['userName'],$_SESSION['password']);

if(isset($_POST['submit'])){
	$fname= ucfirst(htmlspecialchars($_POST['fname']));
	$lname= ucfirst(htmlspecialchars($_POST['lname']));	
	$phone=htmlspecialchars($_POST['phone']);
	$address=htmlspecialchars($_POST['address']);
	$gender=htmlspecialchars($_POST['sex']);
	$dob=htmlspecialchars($_POST['dob']);
	$email= htmlspecialchars($_POST['email']);
	$password= htmlspecialchars($_POST['password']);
	$profilepic="";
	
	if($info->updateProfile($_SESSION['userName'],$fname,$lname,$email,$phone,$address,$gender,$dob, $password,$profilepic)){
		
	}
	if($_FILES['profilePic']['error'] != UPLOAD_ERR_NO_FILE)
	{
		$target_dir = "profilepic/";
		$target_file = $target_dir.basename($_FILES['profilePic']['name']); //GET THE NAME OF THE FILE
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		$fileName = $_FILES['profilePic']['name']; 	//GET THE NAME TO BE STORED
		$tmpName  = $_FILES['profilePic']['tmp_name']; //USES A TEMP FILE TO UPLOAD TO THE SERVER
		
		
		$check = getimagesize($_FILES["profilePic"]["tmp_name"]);
		if($check !== false) { 				//CHECK THE OVER ALL SIZE OF THE FILE
			move_uploaded_file($tmpName,$target_file); //PROCESS WIH UPLOADING
			$filePath = $target_dir . $fileName;
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
		$info->upDateStudent($_SESSION['userName'],$filePath);	
		
	}
	


}

?>
<script>
tempPassword = '<?php echo $_SESSION['password'] ;?>';
</script>

<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
	</div>
	
	
	<div class="col-md-6"> <!-- THE MIDDLE TAB -->
	<H3>Edit Profile</H3><br />

<!-- New Student form is Calling student.php to create a object  -->
<form class="form-horizontal" role="form" name="newStudent" method="post" action="editprofile.php" enctype="multipart/form-data" >

		<div class="form-group">
		<label class="control-label col-sm-2" for="type">First Name:</label>
		  	<div class="col-sm-6">          
				<input type="text" class="form-control" id="fname" name="fname" value="<?php echo $_SESSION['fname'] ?>" required>				
			</div>
		</div>
		<div class="form-group">
		<label class="control-label col-sm-2" for="type">Last Name:</label>
		  	<div class="col-sm-6">          
				<input type="text" class="form-control" id="lname" name="lname" value="<?php echo $_SESSION['lname'] ?>" required>
			</div>
		</div>

		
		<div class="form-group">
		<label class="control-label col-sm-2" for="type">Email:</label>
		  	<div class="col-sm-6">          
				<input type="email" class="form-control" id="email" name="email" value="<?php echo $info->email ?>" placeholder="Enter a valid email address" >				
			</div>			
		</div>
        <div class="form-group">
		<label class="control-label col-sm-2" for="type">Phone Number:</label>
		  	<div class="col-sm-6">          
				<input type="text" class="form-control" id="phone" name="phone" value="<?php echo $info->phoneNumber ?>" placeholder="Enter a Phone Number xxx-xxx" >				
			</div>			
		</div>
        <div class="form-group">
		<label class="control-label col-sm-2" for="type">Address:</label>
		  	<div class="col-sm-6">          
				<input type="text" class="form-control" id="address" name="address" value="<?php echo $info->address ?>" >	
                <div id="suggestion"></div>
				<div id="map"></div>			
			</div>			
		</div>
         
         <div class="form-group">
		<label class="control-label col-sm-2" for="type">Date of Birth:</label>
		  	<div class="col-sm-6">          
				<input type="date" class="form-control" id="dob" name="dob" value="<?php echo $info->dob ?>"  >				
			</div>			
		</div>
          <div class="form-group">
		<label class="control-label col-sm-2" for="type">Gender:</label>
		  	<div class="col-sm-6">          
				<input type="radio" name="sex" value="Male" checked>Male<br>
				<input type="radio" name="sex" value="Female">Female			
			</div>			
		</div>
        
        <div class="form-group">
		<label class="control-label col-sm-2" for="type">Enter Current Password:</label>
		  	<div class="col-sm-6">          
		<input type="password" class="form-control" onblur="checkPassword(this.value,tempPassword)" id="currentpassword" name="password" placeholder="Enter Current Password" required ><span id="currentpwd"></span>			
			</div>			
		</div>	
		<div class="form-group">
		<label class="control-label col-sm-2" for="type">Enter Password:</label>
		  	<div class="col-sm-6">          
				<input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" value="<?php echo $_SESSION['password'] ?>" required >			
			</div>			
		</div>
		<div class="form-group">
		<label class="control-label col-sm-2" for="type">Re-Enter Password:</label>
		  	<div class="col-sm-6">          
				<input type="password" class="form-control" onblur="comparePassword()" id="repassword" name="repassword" placeholder="Re-Enter Password" value="<?php echo $_SESSION['password'] ?>" required><span id="errorPassword"></span>			
			</div>			
		</div>	
        
        <div class="form-group">
        <label class="control-label col-sm-2" for="type">Profile Picture:</label>
        <div class="col-sm-6">
              <input type="file" lass="form-control" name="profilePic" id="profilePic">
            </div>
         </div> 

		<div class="form-group">        
		  <div class="col-sm-offset-2 col-sm-10">
			<button type="submit" name="submit" id="submit" class="btn btn-default">Submit</button>
			<button type="button" name="reset" class="btn btn-default">Reset</button>
		  </div>
		</div>

</form>
	</div>
	
	
	<div class="col-md-3"><!-- THE RIGHT TAB -->
	
	</div>
</div> 




<?php include 'footer.php';?>
