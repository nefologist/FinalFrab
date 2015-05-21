<?php

class database{
	
	private $dsn;
	private $username;
	private $password; 
	public $conn;
	
	
	public function __construct(){
	
		$this->dsn="mysql:dbname=frabman";
		$this->username="frabman";
		$this->password="frabman";				
	}// end of constructor

	
	public function  connectdb(){
				
		try{
			
			$this->conn= new PDO($this->dsn,$this->username,$this->password);
			$this->conn->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}catch ( PDOException $e) {
			echo "Connection Failed".$e->getMessage();
		}
	}// end of connectdb function
	
	public function disconnectdb(){
		$this->conn = "";
	} // end of disconnectdb function 
	
	public function try_query($sql)
	{
		try {
			$this->conn->exec($sql);
			return true;
		}
		catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
			return false;
  		}
	}
	
	public function select_query($sql)
	{
		$result = $dbconn->conn->query($sql);
		if($result->rowCount() != 1)
		{
			echo "Error Please contact administrator.";
			return;
		}
		return $result;
	}
}//End of class

?>