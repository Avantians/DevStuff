$(document).ready(function(){
	//global vars
	var form 				= $("#stylizedForm");
	var yname 			= $("#yname");
	var yemail 			= $("#yemail");
	var title 					= $("#title");

	var fulltxt 				= $("#fulltxt");
	var captcha 			= $("#captcha");
	var pwd		 			= $("#pwd");
	//On blur
	yname.blur(validateYname);
	yemail.blur(validateYemail);
	title.blur(validateTitle);
	fulltxt.blur(validateFulltxt);
	captcha.blur(validateCaptcha);
	pwd.blur(validatePwd);

	//On key press
	yname.keyup(validateYname);
	yemail.keyup(validateYemail);
	title.keyup(validateTitle);
	fulltxt.keyup(validateFulltxt);
	captcha.keyup(validateCaptcha);
	pwd.keyup(validatePwd);

	//On Submitting
	form.submit(function(){
		if( validateYname()  & validateYemail() & validateTitle() & validateFulltxt()  & validateCaptcha() & validatePwd())
			return true
		else
			return false;
	});
	
	function validateYname(){
		//if it's NOT valid
		if(yname.val().length < 2){
			yname.addClass("error");
			return false;
		}
		//if it's valid
		else{
			var myYname = yname.val(); //string contains characters and white spaces
			if (/^\s+$/.test(myYname)){
				yname.addClass("error");
				return false;
			}
			else {
				yname.removeClass("error");
				return true;
			}
		}
	}
	
	function validateYemail(){
		//testing regular expression
		var myEmail = yemail.val();
		var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
		
		if(yemail.val().length < 3){
			yemail.addClass("error");
			return false;
		}
		//if it's valid
		else{
			//if it's valid email
			if(filter.test(myEmail)){
				yemail.removeClass("error");
				return true;
			}
			else{
					//string contains characters and white spaces
					if (/^\s+$/.test(myEmail)){
						yemail.addClass("error");
						return false;
					}
					else {
						yemail.addClass("error");
						return false;
					}
			}
		}
	}
			
	function validateTitle(){
		//if it's NOT valid
		if(title.val().length < 3){
			title.addClass("error");
			return false;
		}
		//if it's valid
		else{
			var myTitle = title.val(); //string contains characters and white spaces
			if (/^\s+$/.test(myTitle)){
				title.addClass("error");
				return false;
			}
			else {
				title.removeClass("error");
				return true;
			}
		}
	}

	function validateFulltxt(){
		//it's NOT valid
		if(fulltxt.val().length <4){
			fulltxt.addClass("error");
			return false;
		}
		//it's valid
		else{
			var myFulltxt = fulltxt.val(); //string contains characters and white spaces
			if (/^\s+$/.test(myFulltxt)){
				fulltxt.addClass("error");
				return false;
			}
			else {
				fulltxt.removeClass("error");
				return true;
			}
		}
	}
	
	function validateCaptcha(){
		//if it's NOT valid
		if(captcha.val().length < 3){
			captcha.addClass("error");
			return false;
		}
		//if it's valid
		else{
			var myCaptcha = captcha.val(); //ONLY Numbers
			if (/^[0-9]+$/.test(myCaptcha)){
				captcha.removeClass("error");
				return true;
			}
			else {
				captcha.addClass("error");
				return false;
			}
		}
	}	
	
	function validatePwd(){
		//if it's NOT valid
		if(pwd.val().length < 4){
			pwd.addClass("error");
			return false;
		}
		//if it's valid
		else{
			var myPwd = pwd.val(); //ONLY Numbers
			if (/^[0-9]+$/.test(myPwd)){
				pwd.removeClass("error");
				return true;
			}
			else {
				pwd.addClass("error");
				return false;
			}
		}
	}	
});