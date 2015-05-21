<?php 
	include_once('database.php');
	
	class admin 
	{
		
		public $fname;
		public $lname;
		public $admin_id;
		public $account_type;
		public $imageurl;
		public $verified;
		public $disabled;
		public $disabled_by;
		public $email;
		public $address;
		public $dob;
		public $gender;
		public $phone;
		public $deparment;
		public $password;
		public $dbconn;
		
			
		public function __construct($admin_id) // the constructor for this class will only run after the system has checked that the current user is a teacher.
		{
			$this->dbconn=new database();	//Create a connection to the database using an object of database Class
			$this->dbconn->connectdb();
			$sql = "SELECT * FROM user_table WHERE userid = '$admin_id' AND account_type = 'Admin' limit 1";
			$result = $this->dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: Something went wrong when logging on use: $admin_id.";
				return false;
			}
			$result = $result->fetch(PDO::FETCH_OBJ);
			$this->fname = $result->fname;
			$this->lname = $result->lname;
			$this->admin_id = $result->userid;
			$this->account_type = $result->account_type;
			$this->image_url = $result->image_url;
			$this->verified = $result->verified;
			$this->disabled = $result->disabled;
			$this->disabled_by = $result->disabled_by;
			$this->email = $result->email;
			$this->address = $result->address;
			$this->dob = $result->dob;
			$this->gender = $result->gender;
			$this->address = $result->address;
			$this->phone = $result->phone_number;
			$this->deparment = $result->department_id;
			$this->password = $result->passwd;
			return true;
		}
		
		public function add_course($course_code, $section)
		//$course_code will come from the gui which will get the proper id be
		{
			$sql = "SELECT * FROM course WHERE course_code='$course_code' limit 1";
			$result = $this->dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: could not add this course: '$course_code'.";
				return;
			}
			$sql = "SELECT * FROM user_table WHERE ( userid = '$this->lecturer_id' AND account_type = 'Teacher' ) limit 1";
			$result = $this->dbconn->conn->query($sql);
			if($result->rowCount() != 1)
			{
				echo "Error: could not add this course: '$course_code' because lecturer id : '$this->lecturer_id' is invalid";
				return;
			}
			
			$sql = "SELECT semester_id FROM semester ORDER BY semester_id DESC LIMIT 1";
			$result = $this->dbconn->conn->query($sql);
			if($result->rowCount() != 1)
			{
				echo "Error: could not add this course: '$course_code' because semeseter does not exist. Please contact administrator.";
				return;
			}
			$semesterid = $result->fetch(PDO::FETCH_OBJ);
			$semesterid = $semesterid->semester_id;
			if(!is_int($section))
			{
				echo "Section: $section  must be a number! please enter a valid value [1-15] for section.";
				return;
			}
			$sql = "INSERT INTO `frabman`.`available_course` (`course_id`, `lecturer_id`, `course_code`, `semester_id`, `section`) VALUES (NULL, '$this->lecturer_id', '$course_code', '$semesterid', '$section');";
			
			if ($this->dbconn->try_query($sql))
			{
				echo ("Course has been Added!");
			}
		}
		//end function add course
		
		public function add_assessment($course_id, $assessment_name, $assessment_weight)
		//  the course id will be generated automatically from the gui 
		{
			$sql = "INSERT INTO `frabman`.`grade` (`grade_id`, `course_id`, `assessment_name`, `assessment_weight`) VALUES (NULL, '$course_id', '$assessment_name', '$assessment_weight');";
			
			if( $this->dbconn->try_query($sql) )
			{
				echo "grade for : $assessment_name with weight: $assessment_weight was added";
			}
		}
		//end function add assessment
		
		public function modify_assessment($assessment_id, $assessment_name, $assessment_weight) // there are no checks within this function because once the teacher signs in only then will they get access to the function
		//the assesment_id will come directly from the gui, once the user selects a grade then it will automatically get the id
		{			
			$sql ="UPDATE `frabman`.`grade` SET `assessment_name` = '$assessment_name', `assessment_weight` = '$assessment_weight' WHERE `grade`.`grade_id` = $assessment_id AND `grade`.`disabled` = 0;";
			if ($this->dbconn->try_query($sql))
			{
				$sql = "SELECT student_id FROM student_grades WHERE grade_id = $assessment_id";
				$result = $this->dbconn->conn->query($sql);
				foreach($result->fetchAll(PDO::FETCH_OBJ) as $student_id)
				{
					$sql = "UPDATE `frabman`.`student_grades` SET `grade` = '$grade' WHERE `student_grades`.`grade_id` = $assessment_id AND `student_grades`.`student_id` = '$student_id';";
					if(!$this->dbconn->try_query($sql))
					{
						echo "Grade did not modify!";
						return;
					}
					$this->calculate_grade($course_id, $student_id);
				}
				
				echo "assessment has been modified.";
			}
			
		}
		//end function modify assessment
		
		public function verify_teacher(array $student_id)//function that will allow lecture or admin to student so that they can access the functions of the website
		//ADmin should inherit this class
		{
			$sql = "SELECT userid FROM user_table WHERE userid = $this->lecturer_id AND (account_type = 'Admin')";
			$result  = $this->dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: user: $this->lecturer_id is not an admin/lecturer.";
				return;
			}
			
			
			foreach($student_id as $student)
			{
				$sql = "UPDATE `frabman`.`user_table` SET `disabled`= 0, `disabled_by` = '0', `verified` = '1' WHERE `user_table`.`userid` = '$student' AND `user_table`.`hash` = '';";
				$this->dbconn->try_query($sql);
			}
			echo "student(s) have been verified.";
			
		}
		public function reject_student(array $student_id)//function that will allow lecture or admin to student so that they can access the functions of the website
		//ADmin should inherit this class
		{
			$sql = "SELECT userid FROM user_table WHERE userid = $this->lecturer_id AND (account_type = 'Admin' OR account_type = 'Teacher')";
			$result  = $this->dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: user: $this->lecturer_id is not an admin/lecturer.";
				return;
			}
			
			
			foreach($student_id as $student)
			{
				$sql = "UPDATE `frabman`.`user_table` SET `disabled` = '1', `disabled_by` = 'Rejected by: $this->fname $this->lname for: $student[1]' WHERE `user_table`.`userid` = '$student[0]' AND `user_table`.`hash` = '';";
				$this->dbconn->try_query($sql);
			}
			echo "student(s) have been rejected.";
		}
		//end rejected class
		
		public function add_student($course_id, array $student_id)//function that will add a student(s) to a course 
		{
			$sql = "SELECT `course_id` FROM `available_course` WHERE `course_id` = $course_id AND `lecturer_id` = $this->lecturer_id AND `disabled` = 0";
			$result = $this->dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: could not add student to course because you are not the owner.";
				return;
			}
			
			foreach($student_id as $student)
			{
				$sql = "INSERT INTO enrolled_courses SELECT * FROM temp_course WHERE `course_id` = $course_id AND `student_id` = '$student'";
				$this->dbconn->try_query($sql);
				$sql = "SELECT * FROM enrolled_courses WHERE `course_id` = $course_id AND `student_id` = '$student'";
				$result = $this->dbconn->conn->query($sql);
				if($result->rowCount() !=  1)
				{
					echo "Error: Somemthing went wrong when adding student with id: $student to course: $course_id. please contact admin.";
					continue;
				}
				
				$sql = "SELECT grade_id FROM `grade` WHERE `course_id` = $course_id AND `disabled` = 0";
				$result = $this->dbconn->conn->query($sql);
				foreach($result->fetchAll(PDO::FETCH_OBJ) as $grade_id)
				{
					$sql = "INSERT INTO `frabman`.`student_grades` (`grade_id`, `student_id`, `grade`) VALUES ('$grade_id->grade_id', '$student', '75')";
					$this->dbconn->try_query($sql);
				}
				$sql = "INSERT INTO `frabman`.`student_ranks` (`course_id`, `student_id`, `rank_id`, `course_grade`) VALUES ('$course_id', '$student', '3', '75');";
				$this->dbconn->try_query($sql);
				
				$sql = "DELETE FROM `frabman`.`temp_course` WHERE `temp_course`.`course_id` = $course_id AND `temp_course`.`student_id` = '$student'";
				$this->dbconn->try_query($sql);
			}
			
			
			echo "Student(s) have been added to the course.";
		}
		//End add student
		
		public function view_student($student_id) 
		// FUNCTION return's all student information for each course that he/she has taken/ is enrolled in
		{
			$sql = "SELECT * FROM user_table WHERE ( userid = '$this->lecturer_id' AND (account_type = 'Teacher' OR account_type = 'Admin') ) limit 1";
			$result = $this->dbconn->conn->query($sql);
			if($result->rowCount() != 1)
			{
				echo "Error: could not view student because lecturer id : '$this->lecturer_id' does not have privilages to view. Please contact administrator.";
				return;
			}
			
			$sql = "SELECT a.course_id, c.*, u.fname, u.lname, ranks.rank_name, r.course_grade FROM `user_table` u, `ranks`, `course` c, `available_course` a, `student_ranks` r WHERE c.course_code = a.course_code AND u.userid = a.lecturer_id AND ranks.rank_id = r.rank_id AND a.course_id = r.course_id and r.student_id = $student_id";
			$result = $this->dbconn->conn->query($sql);
			if($result->rowCount() == 0)
			{
				echo "Error: no grades found! Please contact administrator.";
				return;
			}
			return $result->fetchAll(PDO::FETCH_OBJ);
		}
		//end of view student
		
		public function disable_account($user_id)
		{
			$sql = "SELECT userid FROM user_table WHERE userid = $this->lecture_id AND account_type = 'Admin'";
			$result  = $this->dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: user: $lecture_id is not an admin/lecturer.";
				return;
			}
			
			$sql = "UPDATE `frabman`.`user_table` SET `disabled` = '1', `disabled_by` = '$this->fname $this->lname' WHERE `user_table`.`userid` = $student_id; AND `user_table`.`account_type` = 'Student'";
			$this->dbconn->try_query($sql);
		}
		//end of disable student function
		
		public function enable_student($student_id)
		{
			$sql = "SELECT userid FROM user_table WHERE userid = $this->lecture_id AND (account_type = 'Admin' OR account_type = 'Teacher')";
			$result  = $dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: user: $this->lecture_id is not an admin/lecturer.";
				return;
			}
			
			$sql = "UPDATE `frabman`.`user_table` SET `disabled` = '0', `disabled_by` = '0' WHERE `user_table`.`userid` = $student_id AND `user_table`.`account_type` = 'Student';";
			$this->dbconn->try_query($sql);
		}
		//end of enabled student function
		
		public function disable_course($course_id)
		{
			$sql = "SELECT `course_id` FROM `available_course` WHERE `course_id` = $course_id AND `lecturer_id` = $this->lecturer_id AND `disabled` = 0";
			$result = $this->dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: could not disable course because you are not the owner. Or the course has been disabled";
				return;
			}
			
			$sql = "UPDATE `frabman`.`available_course` SET `disabled` = '1' WHERE `available_course`.`course_id` = $course_id;";
			$this->dbconn->try_query($sql);
			$sql = "UPDATE `grade` SET `disabled`= 0 WHERE `course_id`= $course_id;";
			$this->dbconn->try_query($sql);
		}
		
		public function top_students($course_id, $num)
		{
			$sql = "SELECT userid FROM user_table WHERE userid = $this->lecture_id AND (account_type = 'Admin' OR account_type = 'Teacher')";
			$result  = $dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: user: $this->lecture_id is not an admin/lecturer.";
				return;
			}
			$sql = "SELECT * FROM `student_ranks` WHERE `course_id` = $course_id ORDER BY `student_ranks`.`course_grade` DESC LIMIT $num";
			$result = $this->dbconn->conn->query($sql);
			return $result->fetchAll(PDO::FETCH_OBJ);
		}
		
		public function add_teacher($fname,$lname,$sid,$email,$phone,$address,$gender,$dob, $password,$profilepic)
		{
		$regdate= date("F j, Y, g:i a");
		$password=md5($password);
		$confirmation = md5( rand(0,1000) );
		if ($gender == "Male"){
			$profilepic= "profilepic/default-male.jpg";
		}else{
			$profilepic= "profilepic/default-female.jpg";
		}
		
	$sql = "INSERT INTO `user_table` (`userid`, `disabled`, `disabled_by`, `Verified`, `fname`, `lname`, `email`, `passwd`, `registration_date`, `address`, `dob`, `gender`, `phone_number`, `major_id`, `department_id`, `account_type`, `image_url`, `hash`) VALUES ('$sid', '0', ' ', '0', '$fname', '$lname', '$email', '$password', '$regdate', '$address', '$dob', '$gender', '$phone', '1', '1', 'Teacher', '$profilepic', '$confirmation')";
		
	try {
		$this->dbconn->try_query($sql);
		$to = $email;
		$subject = 'Signup Verification';
		$message = '		 
		Thanks for signing up to Frabman!
		Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
		 
		------------------------
		Username: '.$sid.'
		Password: '.$password.'
		------------------------		 
		Please click this link to activate your account:
		
		 
		'; // Our message above including the link
							 
		$headers = 'From:noreply@yourwebsite.com' . "\r\n"; // Set from headers
		mail($to, $subject, $message, $headers); // Send our email  
		
		//header("Location: verify.php?email='$email'&hash='$confirmation'");
		
		return true;
		}
	catch(PDOException $e)
			{
			echo "<script>alert('Account Already Exist Please Try Again')</script>";
  		  }	
		
		}
		
		function add_new_course($course_code, $course_name)
		{
			$sql = "SELECT userid FROM user_table WHERE userid = $this->admin_id AND account_type = 'Admin'";
			$result  = $dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: user: $this->admin_id is not an admin.";
				return;
			}
			
			$sql = "INSERT INTO `frabman`.`course` (`course_code`, `course_name`, `disabled`) VALUES ('$course_code', '$course_name', '0');";
			$this->dbconn->try_query($sq1);
			echo "course added!";
		}
		
	}
	//end teacher class
	
	//create admin function to add teacher
	//create admin function to verify teacher. 
	//create admin function to disable teacher
	//create admin function to enable teacher
	//create admin function to add course
	//create admin function to disable course
	//create admin function to enable course
	//create addin function to add department
	//create addin function to disable department
	//create admin function to add major
	//create addin function to disable major
	
	/* testing add grade and modify grade functions
	add_assessment(9, "Exam", 20);
	add_assessment(9, "Test1", 20);
	add_assessment(9, "Test", 20);
	$sql = "SELECT grade_id FROM grade ORDER BY grade_id DESC LIMIT 1;";
	$result = $dbconn->conn->query($sql);
	$result = $result->fetch(PDO::FETCH_OBJ);
	modify_assessment($result->grade_id, "Final Project", .22);*/
	
	/*testing to verify students
	$students = [ 2, 'bbc'];	
	verify_student( 3, $students);
	$students = [1];
	verify_student(6, $students);*/
	
	/*//testing to add students to course
	$students = [ 2, 'bbc'];
	add_student(2, 6, $students);
	add_student(2, 3, $students);
	$students = [1];
	add_student(2, 3, $students);*/
	
	/*//testing to modify grades to the course
	modify_grade(2, 3, 14, 2, 95.8);
	echo "grade added";*/
	
	/*//testing the calculate final grade function
	calculate_grade(2, 1);
	calculate_grade(9, 2);
	calculate_grade(1, 2);
	echo "grade calculated.";*/
	
	/*//testing check student
	echo view_student(6, 1);
	echo view_student(6, 2);*/
	
	/*//testing disable student
	disable_student(3, 2);*/
	
	/*//testing enable student
	enable_student(3, 20);
	
	$dbconn = new database;
	$dbconn->connectdb();
	
	//testing teacher constructor
	$teacher = new teacher(6);
	echo $teacher->fname . $teacher->lname;
	
	// testing add grade and modify grade functions
	/*$teacher->add_course('CMPS3232', 1);
	$sql = "SELECT course_id FROM available_course ORDER BY course_id DESC LIMIT 1;";
	$result = $dbconn->conn->query($sql);
	$result = $result->fetch(PDO::FETCH_OBJ);
	
	//testing add assesement through teacher class
	$teacher->add_assessment($result->course_id, "Exam", .30);
	$teacher->add_assessment($result->course_id, "Test1", .20);
	$teacher->add_assessment($result->course_id, "Test2", .20);
	$teacher->add_assessment($result->course_id, "Test3", .20);
	$teacher->add_assessment($result->course_id, "Quiz1", .05);
	$teacher->add_assessment($result->course_id, "Quiz2", .02);
	$teacher->add_assessment($result->course_id, "Quiz3", .03);
	
	//checking modify assesment through teacher class
	$sql = "SELECT grade_id FROM grade ORDER BY grade_id DESC LIMIT 1;";
	$result = $dbconn->conn->query($sql);
	$result = $result->fetch(PDO::FETCH_OBJ);
	$teacher->modify_assessment($result->grade_id, "Take Home", .03);
	
	//cehcking verify student through teacher class
	$students = [7, 'bbc'];
	$teacher->verify_student($students);
	
	//checking reject student through teacher class
	$students = [[8,"last name"]];
	$teacher->reject_student($students);
	
	//testing the verify after being rejected through the teacher class
	$students = [8];
	$teacher->verify_student($students);
	
	//testing that the add students works
	$students = ['bbc', 7, 8];
	$teacher->add_student(10, $students);
	
	//testing view student function 
	echo '</br>';echo '</br>';
	echo var_dump($teacher->view_student(2));
	echo '</br>';echo '</br>';
	echo var_dump($teacher->view_student(8));*/
	
?>