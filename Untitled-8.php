<?php 
include 'header.php';
include 'teacher.php';
$_SESSION['teacher'] = new teacher($_SESSION['teacher_id']);
?>


<?php include 'footer.php';?>