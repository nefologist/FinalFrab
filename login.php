<?php


include 'header.php';

if(isset($_POST['submit'])){
	
	if(isset($_POST["userName"]))
	{
		$userName=$_POST["userName"];
		$userName=htmlspecialchars($userName);
		$_SESSION['userName'] =$_POST["userName"];
	}
	else
	{
		$userName = false;
		echo '<script> alert(" You Forgot to enter your username."); </script>';
	}
	
	if(!empty($_POST["password"]))
	{
		$password=$_POST["password"];
		$password=htmlspecialchars($password);
	}
	else
	{
		$password = false;
		echo '<script> alert(" You Forgot to enter your password."); </script>';
	}
	
	if($userName && $password)
	{	$sql= "SELECT `account_type`, `verified`, `disabled`, `disabled_by` FROM `user_table` WHERE `passwd`= MD5('$password') AND `userid` = '$userName'";
		$result = $dbconn->conn->query($sql);
		if($result->rowCount() == 1)
		{
			$sql = "INSERT INTO `frabman`.`sessions` (`session_id`, `user_id`, `login_date`, `logout_date`) VALUES (NULL, '$userName', NOW(), '')";
			$dbconn->try_query($sql);
			$result = $result->fetch(PDO::FETCH_OBJ);
			if($result->account_type == "Admin")
			{
				$_SESSION['acctype'] = "Admin";
				$_SESSION['verified'] = $result->verified;
				$_SESSION['disable'] = $result->disabled;
				$_SESSION['disabledBy'] = $result->disabled_by;
				$_SESSION['admin_id'] = $userName;
				header("location: admin_home.php");
				exit;
			}
			elseif($result->account_type == "Teacher")
			{
				$_SESSION['acctype'] = "Teacher";
				$_SESSION['verified'] = $result->verified;
				$_SESSION['disable'] = $result->disabled;
				$_SESSION['disabledBy'] = $result->disabled_by;
				$_SESSION['teacher_id'] = $userName;
				header("location: lecturer_home.php");
				exit;
			}
			elseif($result->account_type == "Student")
			{
				include("student.php");
				$studentObj= new student();
				if($studentObj->getStudent($userName, $password)){
					$_SESSION['fname']=$studentObj->fname;
					$_SESSION['lname']=$studentObj->lname;
					$_SESSION['userName']=$studentObj->studentid;
					$_SESSION['password']=$password;
					$_SESSION['acctype']=$studentObj->account_type;
					$_SESSION['imageurl']=$studentObj->imageurl;
					$_SESSION['verified']=$studentObj->verify;
					$_SESSION['disable']=$studentObj->disable;
					$_SESSION['disabledBy']=$studentObj->disabledBy;
					$_SESSION['date']= date("F j, Y, g:i a");
					header("Location: home.php");
					exit();
				}
			}
			else{
				echo "<script>alert('invalid UserID')</script>";
			}
		}
		else
		{
			echo "<script>alert('invalid UserID/password')</script>";
		}
	}	
}

?>

 <body>
    <div class="container">
          
        <form action="login.php" method="POST" name="login" autocomplete="off" class="form-horizontal" role="form" >
        <div class="form-group">
          <label class="control-label col-sm-4" for="userID">User ID:</label>
          <div class="col-sm-4">
         	 <input type="text" class="form-control" name="userName" id="userName" placeholder="Enter User ID" required>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-4" for="pwd">Password:</label>
          	<div class="col-sm-4">
          <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
        	</div>
        </div>
        <div class="form-group">
        <div class="col-sm-offset-4 col-sm-2">
        <input type="button" class="btn btn-default" name="signUp" value="Sign Up" onClick="location.href='studentForm.php'">
        <input type="submit" class="btn btn-default" name="submit" value="Login">
        <label ><input type="checkbox"> Remember me</label> <a href="forgetpwd.php"> Forget Password</a>
          
        </div>
         </div>
      </form>
      
    </div>

  </body>


<?php include 'footer.php';?>