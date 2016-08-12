$(document).ready(function(){
	//global vars
	var form 				= $("#stylizedForm");
	var title 				= $("#title");
	var titleInfo 			= $("#titleInfo");
	var password			= $("#password");
	var passwordInfo		= $("#passwordInfo");
	var captcha			= $("#captcha");
	var captchaInfo		= $("#captchaInfo");

	//On blur
	title.blur(validateTitleName);
	captcha.blur(validatePassword);
	captcha.blur(validateCaptcha);

	//On key press
	title.keyup(validateTitleName);
	captcha.keyup(validatePassword);
	captcha.keyup(validateCaptcha);

	//On Submitting
	form.submit(function(){
		if(validateTitleName() && validatePassword() && validateCaptcha())
			return true
		else
			return false;
	});

	function validateTitleName(){
		//if it's NOT valid
		if(title.val().length < 3){
			title.addClass("errortxt");
			titleInfo.text("We need more than 2 letters for the title!");
			titleInfo.addClass("errortxt");
			return false;
		}
		//if it's valid
		else{
			title.removeClass("errortxt");
			titleInfo.text("");
			titleInfo.removeClass("errortxt");
			return true;
		}
	}

	function validatePassword(){
		//it's NOT valid
		if(password.val().length <4){
			password.addClass("errortxt");
			passwordInfo.text("Please remember: At least 4 characters to edit this article.");
			passwordInfo.addClass("errortxt");
			return false;
		}
		//it's valid
		else{
			password.removeClass("errortxt");
			passwordInfo.text("You will need to edit this article.");
			passwordInfo.removeClass("errortxt");
			return true;
		}
	}


	function validateCaptcha(){
		//it's NOT valid
		if(captcha.val().length <4){
			captcha.addClass("errortxt");
			captchaInfo.text("Please read and type the verification number");
			captchaInfo.addClass("errortxt");
			return false;
		}
		//it's valid
		else{
			captcha.removeClass("errortxt");
			captchaInfo.text("Please read and type the verification number");
			captchaInfo.removeClass("errortxt");
			return true;
		}
	}
});