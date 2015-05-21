// JavaScript Document
var IDLE_TIMEOUT = 1800; //seconds
var _idleSecondsTimer = null;
var _idleSecondsCounter = 0;

document.onclick = function() {
    _idleSecondsCounter = 0;
};
document.onmousemove = function() {
    _idleSecondsCounter = 0;
};
document.onkeypress = function() {
    _idleSecondsCounter = 0;
};

_idleSecondsCounter = window.setInterval(CheckIdleTime, 1000);

function CheckIdleTime() {
    _idleSecondsCounter++;
   if (_idleSecondsCounter >= IDLE_TIMEOUT) {
        document.getElementById("logout").click();
    }
}


function checkFormat(str,errorId){
	var Reg = new RegExp("^[a-zA-Z]+$");
	if(Reg.test(str.value)){
		document.getElementById(errorId).innerHTML="";
		document.getElementById("submit").disabled=false;
	}else{
		
		document.getElementById(errorId).innerHTML= "Invalid Name Must Not Contain numbers";
	}


}

function comparePassword(){
	var password_1= document.getElementById("password").value;
	var password_2= document.getElementById("repassword").value;
	if(password_1 === password_2){	
		document.getElementById("errorPassword").innerHTML="";
		document.getElementById("submit").disabled=false;
	}else{
		document.getElementById("errorPassword").innerHTML= "Password Must Match";
		document.getElementById("submit").disabled=true;

	}
	
}
function makerequest(serverPage, objID) {
		var obj = document.getElementById(objID);
		xmlhttp.open("GET", serverPage);
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		obj.innerHTML = xmlhttp.responseText;
}
}
xmlhttp.send(null);
}

function checkPassword(checkPassword,dbpassword){
	if(checkPassword == dbpassword){
		document.getElementById("submit").disabled=false;
		document.getElementById("currentpwd").innerHTML="";	
	}
	else
	{
		document.getElementById("currentpwd").innerHTML= "Please Enter Your Current Password ";
		document.getElementById("submit").disabled=true;

	}	
	
}

function submitCourse(elementid){
	var course= document.getElementById("courseID").value;
	window.location.href="profile.php?action="+elementid+"&courseid="+course;
	alert("You have been Added to the Course");
}
function deleteCourse(elementid){
	var course= document.getElementById("deletecourseID").value;
	window.location.href="profile.php?action="+elementid+"&courseid="+course;
	alert("You have been Deleted from the Course");
}

function searchAddCourse(courseId){
	window.location.href="profile.php?action=addCourse&courseid="+courseId;
	alert("You have been Added to the Course");
}

 $(function() {
	$("button#submit").click(function(){
	$.ajax({
    type: "POST",
	url: "userController.php",
	data: $('form.contact').serialize(),
        	success: function(msg){
                 $("#thanks").html(msg)
        $("#form-content").modal('hide');	
         },
	error: function(){
	alert("failure");
	}
      	});
	});
});


function showHidden(){
	document.getElementById("search").style.visibility="visible";
}
