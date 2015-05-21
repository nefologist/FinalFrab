<?php
include 'header.php';
include 'userController.php';

if(isset($_POST['submit'])){
	resetPassword($_POST['userName']);	
}
?>
 <body>
    <div class="container">
          
        <form action="forgetpwd.php" method="POST" name="login" autocomplete="off" class="form-horizontal" role="form" >
        <div class="form-group">         
          <div class="col-sm-offset-4 col-sm-4">
          	<label for="userID">Please Enter Username or Password</label>
         	 <input type="text" class="form-control" name="userName" id="userID" placeholder="Enter User ID or Email" required>
          </div>
        </div>

        <div class="form-group">
        <div class="col-sm-offset-4 col-sm-2">
        <input type="submit" class="btn btn-default" name="submit" value="Reset Password">
       
        </div>
         </div>
      </form>
      
    </div>

  </body>