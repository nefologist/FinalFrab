<?php
include('database.php');
require 'PHPMailer/PHPMailerAutoload.php';
//require 'PHPMailer/PHPMailerAutoload.php';
define('GUSER', ''); // GMail username
define('GPWD', ''); // GMail password

class student{
	
	public $fname;
	public $lname;
	public $email;
	public $phoneNumber;
	public $address;
	public $dob;
	public $studentid;
	public $courses;
	public $account_type;
	public $imageurl;
	public $verified; // hash account verify by email
	public $verify;
	public $disable;
	public $disabledBy;
	private $password;
	private $opendb;
	
		
	public function __construct(){
		$this->opendb=new database();	//Create a connection to the database using an object of database Class
		$this->opendb->connectdb();
		
	}
	
	 function __destruct() {
      $this->opendb->conn = null;
   }
	
	//Object to create every time a student logs on to the site, will accept an username and password
	public function getStudent($studentid, $password){
		//Get the query for the student
		$password = md5($password);
		$sql="SELECT * FROM `user_table` WHERE userid='$studentid' and passwd='$password' limit 1";
				$result=$this->opendb->conn->query($sql);
		
		if ($result->rowCount() > 0) { // Check if the result we obtain is empty
			$user=$result->fetch(PDO::FETCH_OBJ);	//Place the result in a object for easier handling and retrival
			$this->fname=$user->fname;	//Setting Properties for the class student
			$this->lname=$user->lname;
			$this->studentid=$user->userid;
			$this->account_type=$user->account_type;
			$this->imageurl=$user->image_url;
			$this->verified=$user->hash;
			$this->email=$user->email;
			$this->phoneNumber=$user->phone_number;
			$this->address=$user->address;
			$this->dob=$user->dob;
			$this->verify=$user->verified;
			$this->disable= $user->disabled;
			$this->disabledBy=$user->disabled_by;
			return true;
		} else {
		    $this->opendb->conn = null ;
		    return false;
		    exit();       		
		}	

	} //END OF GETSTUDENT CLASS
	
/******GET STUDENT INFORMATION MAINLY COURSES TO POPULATE MY TAB FIELD WITH COURSE INFORMATION **********/

	public function getStudentInfo($studentid){
		$sql = "SELECT u.fname,u.lname,ac.course_code,ac.section,y.semester_year, ec.course_id, c.course_name
				FROM user_table AS u
				INNER JOIN available_course AS ac
					ON u.userid=ac.lecturer_id
				INNER JOIN course c
					ON c.course_code=ac.course_code
				INNER JOIN semester y
					ON ac.semester_id = y.semester_id
				INNER JOIN enrolled_courses ec
					on ac.course_id=ec.course_id
				WHERE
					ec.student_id='$studentid'";
		$result=$this->opendb->conn->query($sql);
		
		if($result->rowCount() > 0){
			$userInfo=$result->fetchAll(PDO::FETCH_ASSOC); //Using all due to more than one course and when printing we got a warning for using fetch only
			$this->courses=$userInfo;
			return true;
		}else{
			//$this->opendb->conn = null ;
			return false;
			//exit();
		}
		
		
				
	}//END OF GETSTUDENTINFO
	
/******INSERT A NEW STUDENT ENTRY TO THE DATABASE**********/	
	public function setNewStudent($fname,$lname,$sid,$major,$email,$phone,$address,$gender,$dob, $password,$profilepic){
		$regdate= date("F j, Y, g:i a");
		$password=md5($password);
		$confirmation = md5( rand(0,1000) );
		if ($gender == "Male"){
			$profilepic= "profilepic/default-male.jpg";
		}else{
			$profilepic= "profilepic/default-female.jpg";
		}
		
	$sql = "INSERT INTO `user_table` (`userid`, `disabled`, `disabled_by`, `Verified`, `fname`, `lname`, `email`, `passwd`, `registration_date`, `address`, `dob`, `gender`, `phone_number`, `major_id`, `department_id`, `account_type`, `image_url`, `hash`) VALUES ('$sid', '0', '', '0', '$fname', '$lname', '$email', '$password', '$regdate', '$address', '$dob', '$gender', '$phone', '$major', '1', 'Student', '$profilepic', '$confirmation')";
	try {
		
		
		$this->opendb->conn->exec($sql);
		$subject = 'Signup Verification';
		$body    =  '		 
		Thanks for signing up to Frabman!
		Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
		 
		------------------------
		Username: '.$sid.'
		Password: '.$password.'
		------------------------		 
		Please click this link to activate your account:
		127.0.0.1/frabman/verify.php?email='.$email.'&hash='.$confirmation.'
		
		 
		'; 
	//	$this->smtpmailer($email,'admin','The Frabman',$subject,$body);
		$verifylink="verify.php?email=".$email."&hash=".$confirmation;

		echo "<script>alert('Successfully Sign Up email has been send please follow the Link to Verify')</script>"; 
		echo "<a href='$verifylink'>Verify Link</a>";
		return true;
		}
	catch(PDOException $e)
			{
			echo "<script>alert('Error With the Submission Please Try Again')</script>";
  		  }	
		
	}
	
/**********END OF STUDENT ENTRY**************************/
	
	
		public function fiveStar(){
			$sql = "SELECT u.fname, u.lname, a.course_code, a.course_id, r.rank_url FROM student_ranks sg, user_table u, available_course a, ranks r WHERE sg.course_id=a.course_id AND r.rank_id=sg.rank_id AND sg.student_id=u.userid AND sg.rank_id=1";
			$result = $this->opendb->conn->query($sql);
			if($result->rowCount() > 0){			
				$generals=$result->fetchAll(PDO::FETCH_OBJ); 
				return $generals;
				return true;
				
			}else{
				return false;
			}
		}
	
	public function getGradeStudent($student,$course){

		$sql="SELECT g.assessment_name, g.assessment_weight, sg.grade, g.grade_id FROM student_grades sg , grade g WHERE sg.grade_id=g.grade_id AND sg.student_id='$student' AND g.course_id='$course'";
					
		$result=$this->opendb->conn->query($sql);
		if($result->rowCount() > 0){			
			$grades=$result->fetchAll(PDO::FETCH_OBJ); //Using all due to more than one course and when printing we got a warning for using fetch only
			return $grades;
		}else{
			return false;
		}	
		
		
	}
	
	public function getAvailableCourse(){
		$sql= "SELECT a.course_code, a.section, a.course_id, c.course_name FROM available_course a, course c WHERE a.course_code = c.course_code and a.disabled='0'";
		$result = $this->opendb->conn->query($sql);
		if($result->rowCount() > 0){	
	
			$courses=$result->fetchAll(PDO::FETCH_OBJ); //Using all due to more than one course and when printing we got a warning for using fetch only
			return $courses;
	
		//	$this->opendb->conn = null;
		}else{
			//$this->opendb->conn = null;
			return false;
		//	exit();
		}	
		
		
	}
	
	public function getMajor(){
		$sql= "SELECT * FROM `major`";
		$result = $this->opendb->conn->query($sql);
		if($result->rowCount() > 0){			
			$courses=$result->fetchAll(PDO::FETCH_OBJ); //Using all due to more than one course and when printing we got a warning for using fetch only
			return $courses;
		//	$this->opendb->conn = null;
		}else{
		//	$this->opendb->conn = null;
			return false;
			exit();
		}	
		
		
	}

	
	public function upDateStudent($studentid, $argument){
		$sql = "UPDATE user_table SET image_url= '$argument' WHERE userid= '$studentid'";
		try {
			$this->opendb->conn->exec($sql);
			return true;
		}
		catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
  		  }	

	}
	
	public function getRank($studentid){
		$sql= "select u.fname, r.rank_url, r.rank_name FROM ranks r, student_ranks s, user_table u WHERE s.student_id= u.userid and r.rank_id=s.rank_id and s.student_id='$studentid'";
		$result = $this->opendb->conn->query($sql);
		if($result->rowCount() > 0){			
			$rank=$result->fetchAll(PDO::FETCH_OBJ); //Using all due to more than one course and when printing we got a warning for using fetch only
			return $rank;
			//$this->opendb->conn = null;
		}else{
		//	$this->opendb->conn = null;
			return false;
			exit();
		}	
		
	
	}
	
public function updateProfile($studentid,$fname,$lname,$email,$phone,$address,$gender,$dob, $password,$profilepic){
		$password=md5($password);
		$sql="UPDATE `user_table` SET `fname` = '$fname', `lname` = '$lname', `email` ='$email', `address` = '$address',`passwd` = '$password', `dob` = '$dob', `phone_number` = '$phone' WHERE `userid` = '$studentid';";
		
		try {
			$this->opendb->conn->exec($sql);
			return true;
		}
		catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
  		  }	
	}
	
	
	public function addCourse($studentid,$courseid){
		$sql = "INSERT INTO `temp_course` (`course_id`, `student_id`) VALUES ('$courseid', '$studentid');";
		try {
			$this->opendb->conn->exec($sql);
			return true;
		}
		catch(PDOException $e){
			return false;
			//echo $sql . "<br>" . $e->getMessage();
  		  }	

	}
	
	public function showTempCourse($studentid){
	$sql="SELECT DISTINCT c.course_name, t.course_id, c.course_code, a.section FROM available_course a, temp_course t, course c WHERE c.course_code=a.course_code AND a.course_id=t.course_id AND t.student_id='$studentid'";
	
	$result = $this->opendb->conn->query($sql);
		if($result->rowCount() > 0){			
			$tempCourse=$result->fetchAll(PDO::FETCH_OBJ); 
			return $tempCourse;
			return true;
		}else{
			return false;
		}	
		
	}
	
	public function deleteCourse($studentid,$courseid){
		$sql = "DELETE FROM `temp_course` WHERE `course_id` ='$courseid' AND `student_id` = '$studentid'";
		try {
			$this->opendb->conn->exec($sql);
			return true;
		}
		catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
  		  }	

	}
	
	public function myHistory($studentid){
		$sql="SELECT c.course_name, c.course_code, a.section, sm.semester_year, a.course_id FROM enrolled_courses e, available_course a, course c, semester sm WHERE a.semester_id=sm.semester_id AND c.course_code=a.course_code AND a.course_id=e.course_id AND a.disabled=1 AND e.student_id='$studentid'";
		
		$result = $this->opendb->conn->query($sql);
		if($result->rowCount() > 0){			
			$tempCourse=$result->fetchAll(PDO::FETCH_OBJ); 
			return $tempCourse;
			return true;
		}else{
			return false;
		}	
		
		
	}
	
	public function resetPasswordByStudentId($student, $genePassword){
		$password=md5($genePassword);
		$emailQuery= "SELECT email FROM `user_table` WHERE userid='$student'";
		$result = $this->opendb->conn->query($emailQuery);
		if($result->rowCount() > 0){			
			$infoStudent=$result->fetch(PDO::FETCH_OBJ); 
			$email=$infoStudent->email;
			//$this->opendb->conn = null;
		}
	
		
		$sql="UPDATE user_table SET `passwd` = '$password' WHERE `userid` = '$student'";
		try {
		$this->opendb->conn->exec($sql);
		$to = $email;
		$subject = 'Signup Verification';
		$message = '		 
		Your Password has been reset on Frabman Site!
		New Password is listed Below: 		 
		------------------------
		Username: '.$student.'
		Password: '.$genePassword.'
		------------------------

		 
		'; // Our message above including the link							 
		$headers = 'From:noreply@frabman.com' . "\r\n"; // Set from headers
		mail($to, $subject, $message, $headers); // Send our email  		
		return true;
		}
	catch(PDOException $e)
			{
			echo $sql . "<br>" . $e->getMessage();
  		  }	
	}
	
	public function resetPasswordByEmail($email, $genePassword){
		$password=md5($genePassword);
		$sql="UPDATE user_table SET `passwd` ='$password' WHERE `email` = '$email'";
		
		try {
		$this->opendb->conn->exec($sql);
		$to = $email;
		$subject = 'Signup Verification';
		$message = '		 
		Your Password has been reset on Frabman Site!
		New Password is listed Below: 		 
		------------------------
		Username: '.$student.'
		Password: '.$genePassword.'
		------------------------

		 
		'; // Our message above including the link							 
		$headers = 'From:noreply@frabman.com' . "\r\n"; // Set from headers
		mail($to, $subject, $message, $headers); // Send our email  		
		return true;
		}
	catch(PDOException $e)
			{
			echo $sql . "<br>" . $e->getMessage();
  		  }	
	}
	
	public function generateGeneral(){
	$sql="SELECT u.fname, u.lname, u.image_url FROM student_ranks sr, user_table u WHERE u.userid=sr.student_id and sr.rank_id > 5 Order By rand() LIMIT 1";
	
	$result = $this->opendb->conn->query($sql);
		if($result->rowCount() > 0){			
			$tempCourse=$result->fetch(PDO::FETCH_OBJ); 
			return $tempCourse;
			return true;
			
		}else{
			return false;
		}	
}


	public function getTeachers(){
		$sql="SELECT userid, fname, lname FROM `user_table` WHERE account_type='Teacher'";
		$result = $this->opendb->conn->query($sql);
		if($result->rowCount() > 0){			
			$teachers=$result->fetchAll(PDO::FETCH_OBJ); 
			return $teachers;
			return true;
			
		}else{
			return false;
		}
	}
	
		
		public function getCourseGrade($gradeId){
		$sql="SELECT sg.grade, u.fname, u.lname FROM student_grades sg, user_table u WHERE u.userid=sg.student_id AND sg.grade_id='$gradeId'";
		$result = $this->opendb->conn->query($sql);
		if($result->rowCount() > 0){			
			$courseGrade=$result->fetchAll(PDO::FETCH_OBJ); 
			return $courseGrade;
			return true;
			
		}else{
			return false;
		}
	}
	
	
public function smtpmailer($to, $from, $from_name, $subject, $body) { 
	global $error;
	$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465; 
	$mail->Username = GUSER;  
	$mail->Password = GPWD;           
	$mail->SetFrom($from, $from_name);
	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->AddAddress($to);
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
		return false;
	} else {
		$error = 'Message sent!';
		return true;
	}
}
	
	
	
	
	
	
} //END OF STUDENT CLASS


//$studentObj= new student();
//var_dump($studentObj->getGradeStudent(5,1));
//$studentObj->deleteCourse(2007115242,12);

?>