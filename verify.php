<?php 
include 'header.php';
include 'database.php';
?>
<body>
       <?php
 		$verfiy= new database;
		$verfiy->connectdb();      		       
		if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){

			$email = $_GET['email']; // Set email variable
			$hash = $_GET['hash']; // Set hash variable
			$sql= "SELECT email, hash FROM user_table WHERE email='".$email."' AND hash='".$hash."'";
			echo $sql;	
			$match=$verfiy->conn->query($sql);
									 
			if($match->rowCount() > 0){
			try {
				$sql = "UPDATE `user_table` SET `hash` = ' ' WHERE `email` ='".$email."'";
				//echo $sql;
				$verfiy->conn->exec($sql);
				header("location: login.php");
				//$verfiy->conn->exec("UPDATE FROM user_table SET 'hash'='' WHERE email=".$email." AND hash=".$hash."");
				return true;
				}
			catch(PDOException $e)
					{
					echo $sql . "<br>" . $e->getMessage();
				  }	
				
				echo '<div class="statusmsg">Your account has been activated, you can now login</div>';
			}else{
				// No match -> invalid url or account has already been activated.
				echo '<div class="statusmsg">The url is either invalid or you already have activated your account.</div>';
			}
						 
		}else{
			// Invalid approach
			echo '<div class="statusmsg">Invalid approach, please use the link that has been send to your email.</div>';
		}
             
        ?>
  </body>
</html>