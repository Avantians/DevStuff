$(document).ready(function(){
	//global vars
	var form 				= $("#stylizedForm");
	var members_firstname 				= $("#members_firstname");
	var members_lastname 				= $("#members_lastname");
	var members_email 				= $("#members_email");
	var members_password		 	= $("#members_password");
	var members_password2		= $("#members_password2");

	//On blur
	members_firstname.blur(validateFirst);
	members_lastname.blur(validateLast);
	members_email.blur(validateEmail);
	members_password.blur(validatePass1);
	members_password2.blur(validatePass2);

	//On key press
	members_firstname.keyup(validateFirst);
	members_lastname.keyup(validateLast);
	members_email.keyup(validateEmail);
	members_password.keyup(validatePass1);
	members_password2.keyup(validatePass2);

	//On Submitting
	form.submit(function(){
		if(validateFirst() & validateLast()  & validateEmail() & validatePass1()  & validatePass2())
			return true
		else
			return false;
	});

	function validateFirst(){
		//if it's NOT valid
		if(members_firstname.val().length < 1){
			members_firstname.addClass("error");
			return false;
		}
		//if it's valid
		else{
			members_firstname.removeClass("error");
			return true;
		}
	}

	function validateLast(){
		//if it's NOT valid
		if(members_lastname.val().length < 1){
			members_lastname.addClass("error");
			return false;
		}
		//if it's valid
		else{
			members_lastname.removeClass("error");
			return true;
		}
	}

	function validateEmail(){
		//testing regular expression
		var a = $("#members_email").val();
		var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
		//if it's valid email
		if(filter.test(a)){
			members_email.removeClass("error");
			return true;
		}
		//if it's NOT valid
		else{
			members_email.addClass("error");
			return false;
		}
	}

	function validatePass1(){
		var apw = $("#members_password").val();
    //Must contain at least one upper case letter, one lower case letter and one digit.
    var StrongPass = /^(?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])\S{5,}$/;

		if(StrongPass.test(apw)){
			members_password.removeClass("error");
			validatePass2();			
			return true;
		}
		//if it's NOT valid
		else{
			members_password.addClass("error");
			return false;
		}
	
	}
	function validatePass2(){
		var a = $("#members_password");
		var b = $("#members_password2");
		//are NOT valid
		if( members_password.val() != members_password2.val() ){
			members_password2.addClass("error");
			return false;
		}
		//are valid
		else{
			members_password2.removeClass("error");
			return true;
		}
	}
});