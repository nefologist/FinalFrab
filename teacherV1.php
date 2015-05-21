<?php 
	include('database.php');
	
	class teacher 
	{
		
		public $fname;
		public $lname;
		public $lecturer_id;
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
		private $password;
		private $dbconn;
		
			
		public function __construct($lecturer_id) // the constructor for this class will only run after the system has checked that the current user is a teacher.
		{
			$this->dbconn=new database();	//Create a connection to the database using an object of database Class
			$this->dbconn->connectdb();
			$sql = "SELECT * FROM user_table WHERE userid = $lecturer_id AND account_type = 'Teacher' limit 1";
			$result = $this->dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: Something went wrong when logging on use: $lecturer_id.";
				return false;
			}
			$result = $result->fetch(PDO::FETCH_OBJ);
			$this->fname = $result->fname;
			$this->lname = $result->lname;
			$this->lecturer_id = $result->userid;
			$this->account_type = $result->account_type;
			$this->imageurl = $result->lname;
			$this->verified = $result->Verified;
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
		
		
		
	}
	
	
	function add_course($course_code, $lecturer_id, $section)
	//$course_code will come from the gui which will get the proper id be
	{
		$dbconn = new database;
		$dbconn->connectdb();
		$sql = "SELECT * FROM course WHERE course_code='$course_code' limit 1";
		$result = $dbconn->conn->query($sql);
		if($result->rowCount() !=  1)
		{
			echo "Error: could not add this course: '$course_code'.";
			return;
		}
		$sql = "SELECT * FROM user_table WHERE ( userid = '$lecturer_id' AND account_type = 'Teacher' ) limit 1";
		$result = $dbconn->conn->query($sql);
		if($result->rowCount() != 1)
		{
			echo "Error: could not add this course: '$course_code' because lecturer id : '$lecturer_id' is invalid";
			return;
		}
		
		$sql = "SELECT semester_id FROM semester ORDER BY semester_id DESC LIMIT 1";
		$result = $dbconn->conn->query($sql);
		if($result->rowCount() != 1)
		{
			echo "Error: could not add this course: '$course_code' because semeseter does not exist. Please contact administrator.";
			return;
		}
		$semesterid = $result->fetch(PDO::FETCH_OBJ);
		$semesterid = $semesterid->semester_id;
		if(!is_int($section))
		{
			echo "Section: $section  must be a number! please enter a valid value [1-10] for section.";
			return;
		}
		$sql = "INSERT INTO `frabman`.`available_course` (`course_id`, `lecturer_id`, `course_code`, `semester_id`, `section`) VALUES (NULL, '$lecturer_id', '$course_code', '$semesterid', '$section');";
		
		if ($dbconn->try_query($sql))
		{
			echo ("Course has been Added!");
		}
	}
	
	function add_assessment($course_id, $assessment_name, $assessment_weight)
	//  the course id will be generated automatically from the gui 
	{
		$dbconn = new database;
		$dbconn->connectdb();
		
		$sql = "INSERT INTO `frabman`.`grade` (`grade_id`, `course_id`, `assessment_name`, `assessment_weight`) VALUES (NULL, '$course_id', '$assessment_name', '$assessment_weight');";
		
		if( $dbconn->try_query($sql) )
		{
			echo "grade for : $assessment_name with weight: $assessment_weight was added";
		}
	}
	
	function modify_assessment($assessment_id, $assessment_name, $assessment_weight) // there are no checks within this function because once the teacher signs in only then will they get access to the function
	//the assesment_id will come directly from the gui, once the user selects a grade then it will automatically get the id
	{
		$dbconn = new database;
		$dbconn->connectdb();
		
		$sql ="UPDATE `frabman`.`grade` SET `assessment_name` = '$assessment_name', `assessment_weight` = '$assessment_weight' WHERE `grade`.`grade_id` = $assessment_id AND `grade`.`disabled` = 0;";
		if ($dbconn->try_query($sql))
		{
			echo "assessment has been modified.";
		}
	}
	
	function verify_student($lecture_id, array $student_id)//function that will allow lecture or admin to student so that they can access the functions of the website
	//ADmin should inherit this class
	{
		$dbconn = new database;
		$dbconn->connectdb();
		
		$sql = "SELECT userid FROM user_table WHERE userid = $lecture_id AND (account_type = 'Admin' OR account_type = 'Teacher')";
		$result  = $dbconn->conn->query($sql);
		if($result->rowCount() !=  1)
		{
			echo "Error: user: $lecture_id is not an admin/lecturer.";
			return;
		}
		
		
		foreach($student_id as $student)
		{
			$sql = "UPDATE `frabman`.`user_table` SET `Verified` = '1' WHERE `user_table`.`userid` = '$student';";
			$dbconn->try_query($sql);
		}
		echo "student(s) have been verified.";
		
	}
	
	function add_student($course_id, $lecturer_id, array $student_id)//function that will add a student(s) to a course 
	{
		$dbconn = new database;
		$dbconn->connectdb();
		
		$sql = "SELECT `course_id` FROM `available_course` WHERE `course_id` = $course_id AND `lecturer_id` = $lecturer_id AND `disabled` = 0";
		$result = $dbconn->conn->query($sql);
		if($result->rowCount() !=  1)
		{
			echo "Error: could not add student to course because you are not the owner.";
			return;
		}
		
		foreach($student_id as $student)
		{
			$sql = "INSERT INTO enrolled_courses SELECT * FROM temp_course WHERE `course_id` = $course_id AND `student_id` = '$student'";
			$dbconn->try_query($sql);
			$sql = "SELECT * FROM enrolled_courses WHERE `course_id` = $course_id AND `student_id` = '$student'";
			$result = $dbconn->conn->query($sql);
			if($result->rowCount() !=  1)
			{
				echo "Error: Somemthing went wrong when adding student with id: $student to course: $course_id. please contact admin.";
				continue;
			}
			
			$sql = "SELECT grade_id FROM `grade` WHERE `course_id` = $course_id AND `disabled` = 0";
			$result = $dbconn->conn->query($sql);
			foreach($result->fetchAll(PDO::FETCH_OBJ) as $grade_id)
			{
				$sql = "INSERT INTO `frabman`.`student_grades` (`grade_id`, `student_id`, `grade`) VALUES ('$grade_id->grade_id', '$student', '75')";
				$dbconn->try_query($sql);
			}
			$sql = "INSERT INTO `frabman`.`student_ranks` (`course_id`, `student_id`, `rank_id`, `course_grade`) VALUES ('$course_id', '$student', '3', '75');";
			$dbconn->try_query($sql);
			
			
			$sql = "DELETE FROM `frabman`.`temp_course` WHERE `temp_course`.`course_id` = $course_id AND `temp_course`.`student_id` = '$student'";
			$dbconn->try_query($sql);
		}
		
		
		echo "Student(s) have been added to the course.";
	}
	
	function modify_grade($course_id, $lecturer_id, $grade_id, $student_id, $grade)
	{
		$dbconn = new database;
		$dbconn->connectdb();
		
		$sql = "SELECT `course_id` FROM `available_course` WHERE `course_id` = $course_id AND `lecturer_id` = $lecturer_id AND `disabled` = 0";
		$result = $dbconn->conn->query($sql);
		if($result->rowCount() !=  1)
		{
			echo "Error: could not add grade to course because you are not the owner.";
			return;
		}
		
		$sql = "UPDATE `frabman`.`student_grades` SET `grade` = '$grade' WHERE `student_grades`.`grade_id` = $grade_id AND `student_grades`.`student_id` = '$student_id';";
		if(!$dbconn->try_query($sql))
		{
			echo "Grade did not modify!";
			return;
		}
		calculate_grade($course_id, $student_id);
		
	}
	
	function calculate_grade($course_id, $student_id)
	{
		$dbconn = new database;
		$dbconn->connectdb();
		$sql = "SELECT grade, assessment_weight FROM `student_grades` s, `grade` g WHERE s.`student_id` = $student_id AND g.`grade_id` = s.`grade_id`";
		$result = $dbconn->conn->query($sql);
		$final = 0;
		foreach($result->fetchAll(PDO::FETCH_OBJ) as $grade)
		{
			$final = $final + ($grade->grade * $grade->assessment_weight);
		}
		$rank = get_rank($final);
		
		$sql = "UPDATE `frabman`.`student_ranks` SET `rank_id` = '$rank', `course_grade` = '$final' WHERE `student_ranks`.`course_id` = $course_id AND `student_ranks`.`student_id` = '$student_id'";
		$dbconn->try_query($sql);
		
	}
	
	function get_rank($final_grade)
	{
		if($final_grade >= 95)
		{
			$rank_id = 1;
		}
		elseif($final_grade >= 85 && $final_grade < 95)
		{
			$rank_id = 2;
		}
		elseif($final_grade >= 75 && $final_grade < 85)
		{
			$rank_id = 3;
		}
		elseif($final_grade >= 60 && $final_grade < 75)
		{
			$rank_id = 4;
		}
		elseif($final_grade >= 50 && $final_grade < 60)
		{
			$rank_id = 5;
		}
		elseif($final_grade >= 40 && $final_grade < 50)
		{
			$rank_id = 6;
		}
		elseif($final_grade >= 30 && $final_grade < 40)
		{
			$rank_id = 7;
		}
		elseif($final_grade >= 20 && $final_grade < 30)
		{
			$rank_id = 8;
		}
		elseif($final_grade >= 10 && $final_grade < 20)
		{
			$rank_id = 9;
		}
		else
		{
			$rank_id = 10;
		}
		return $rank_id;
	}
	
	function view_student($lecturer_id, $student_id) // FUNCTION return's all student information for each course that he/she has taken/ is enrolled in
	{
		$dbconn = new database;
		$dbconn->connectdb();
		$sql = "SELECT * FROM user_table WHERE ( userid = '$lecturer_id' AND (account_type = 'Teacher' OR account_type = 'Admin') ) limit 1";
		$result = $dbconn->conn->query($sql);
		if($result->rowCount() != 1)
		{
			echo "Error: could not view student because lecturer id : '$lecturer_id' does not have privilages to view. Please contact administrator.";
			return;
		}
		
		$sql = "SELECT a.course_id, c.*, u.fname, u.lname, ranks.rank_name, r.course_grade FROM `user_table` u, `ranks`, `course` c, `available_course` a, `student_ranks` r WHERE c.course_code = a.course_code AND u.userid = a.lecturer_id AND ranks.rank_id = r.rank_id AND a.course_id = r.course_id and r.student_id = $student_id";
		$result = $dbconn->conn->query($sql);
		if($result->rowCount() == 0)
		{
			echo "Error: no grades found! Please contact administrator.";
			return;
		}
		return var_dump($result->fetchAll(PDO::FETCH_OBJ));
	}
		
	function disable_student($lecture_id, $student_id)
	{
		$dbconn = new database;
		$dbconn->connectdb();
		
		$sql = "SELECT userid FROM user_table WHERE userid = $lecture_id AND (account_type = 'Admin' OR account_type = 'Teacher')";
		$result  = $dbconn->conn->query($sql);
		if($result->rowCount() !=  1)
		{
			echo "Error: user: $lecture_id is not an admin/lecturer.";
			return;
		}
		
		$sql = "UPDATE `frabman`.`user_table` SET `disabled` = '1', `disabled_by` = '$lecture_id' WHERE `user_table`.`userid` = $student_id; AND `user_table`.`account_type` = 'Student'";
		$dbconn->try_query($sql);
	}
	
	function enable_student($lecture_id, $student_id)
	{
		$dbconn = new database;
		$dbconn->connectdb();
		
		$sql = "SELECT userid FROM user_table WHERE userid = $lecture_id AND (account_type = 'Admin' OR account_type = 'Teacher')";
		$result  = $dbconn->conn->query($sql);
		if($result->rowCount() !=  1)
		{
			echo "Error: user: $lecture_id is not an admin/lecturer.";
			return;
		}
		
		$sql = "UPDATE `frabman`.`user_table` SET `disabled` = '0', `disabled_by` = '0' WHERE `user_table`.`userid` = $student_id AND `user_table`.`account_type` = 'Student';";
		$dbconn->try_query($sql);
	}
	
	//create admin function to add teacher
	//create admin function to verify teacher. 
	//create admin function to disable teacher
	//create admin function to enable teacher
	//create admin function to add course
	//create admin function to disable course
	//create admin function to enable course
	
	
	
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
	enable_student(3, 20);*/
	
	//testing teacher constructor
	$teacher = new teacher(1);
	echo $teacher->fname . $teacher->lname;
	
?>