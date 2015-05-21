<?php 
require_once "student.php";
require_once "database.php";

$functionName = filter_input(INPUT_GET, 'functionName');

function rand_passwd( $length = 8, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' ) {
    return substr( str_shuffle( $chars ), 0, $length );
}


function resetPassword($resetInfo){
	if (!filter_var($resetInfo, FILTER_VALIDATE_EMAIL)) {
	  $genePassword = rand_passwd();
	  echo $genePassword;
	  $resetPasswrd= new student();
	  $resetPasswrd->resetPasswordByStudentId($resetInfo,$genePassword);
	  
	  }else{
		$resetPasswrd= new student();
	 	$resetPasswrd->resetPasswordByEmail($resetInfo,$genePassword);
		
	}
}

function getGeneral(){
	$general= new student();
	$showMe=$general->generateGeneral();
	return $showMe;	
	return true;
}

function setSession(){
	$_SESSION['date']; 
	$_SESSION['userName'];
	
	
	
}


?>