$(document).ready(function(){
	//global vars
	var form 					= $("#stylizedForm");
	var first_name 			= $("#first_name");
	var first_nameInfo 		= $("#first_nameInfo");
	var last_name 			= $("#last_name");
	var last_nameInfo 		= $("#last_nameInfo");
	var username			= $("#username");
	var usernameInfo		= $("#usernameInfo");
	var password			= $("#password");
	var passwordInfo		= $("#passwordInfo");
	var re_password 		= $("#re_password");
	var re_passwordInfo	= $("#re_passwordInfo");

	//On blur
	first_name.blur(validateFirstName);
	last_name.blur(validateLastName);
	username.blur(validateEmail);
	password.blur(validatePassword);
	re_password.blur(validatePassword2);

	//On key press
	first_name.keyup(validateFirstName);
	last_name.keyup(validateLastName);
	password.keyup(validatePassword);
	re_password.keyup(validatePassword2);

	//On Submitting
	form.submit(function(){
		if(validateFirstName() & validateLastName() & validateEmail() & validatePassword() & validatePassword2())
			return true
		else
			return false;
	});

	//validation functions
	function validateEmail(){
		//testing regular expression
		var a = $("#username").val();
		var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
		//if it's valid email
		if(filter.test(a)){
			username.removeClass("error");
			usernameInfo.text("Please use valid E-mail, you will need it to log in!");
			usernameInfo.removeClass("error");
			return true;
		}
		//if it's NOT valid
		else{
			username.addClass("error");
			usernameInfo.text("Please type a valid e-mail!");
			usernameInfo.addClass("error");
			return false;
		}
	}
	function validateFirstName(){
		//if it's NOT valid
		if(first_name.val().length < 3){
			first_name.addClass("error");
			first_nameInfo.text("We need your first name with more than 2 letters!");
			first_nameInfo.addClass("error");
			return false;
		}
		//if it's valid
		else{
			first_name.removeClass("error");
			first_nameInfo.text("What's your first name?");
			first_nameInfo.removeClass("error");
			return true;
		}
	}
	function validateLastName(){
		//if it's NOT valid
		if(last_name.val().length < 3){
			last_name.addClass("error");
			last_nameInfo.text("We need your last name with more than 2 letters!");
			last_nameInfo.addClass("error");
			return false;
		}
		//if it's valid
		else{
			last_name.removeClass("error");
			last_nameInfo.text("What's your last name?");
			last_nameInfo.removeClass("error");
			return true;
		}
	}
	function validatePassword(){
		var a = $("#password");
		var b = $("#re_password");

		//it's NOT valid
		if(password.val().length <5){
			password.addClass("error");
			passwordInfo.text("Please remember: At least 5 characters: letters, numbers, # and !");
			passwordInfo.addClass("error");
			return false;
		}
		//it's valid
		else{
			password.removeClass("error");
			passwordInfo.text("At least 5 characters: letters, numbers, # and !");
			passwordInfo.removeClass("error");
			validatePassword2();
			return true;
		}
	}
	function validatePassword2(){
		var a = $("#password");
		var b = $("#re_password");
		//are NOT valid
		if( password.val() != re_password.val() ){
			re_password.addClass("error");
			re_passwordInfo.text("Passwords doesn't match!");
			re_passwordInfo.addClass("error");
			return false;
		}
		//are valid
		else{
			re_password.removeClass("error");
			re_passwordInfo.text("Confirm password");
			re_passwordInfo.removeClass("error");
			return true;
		}
	}

});