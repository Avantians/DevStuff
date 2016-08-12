$(document).ready(function(){
	//global vars
	var form 			= $("#stylizedForm");
	var title 			= $("#title");
	var section		 	= $("#section");
	var ctype 		= $("#ctype");


	//On blur
	title.blur(validateTitle);
	ctype.blur(validateCtype);
	section.blur(validateStype);

	//On key press
	title.keyup(validateTitle);
	ctype.keyup(validateCtype);
	section.keyup(validateStype);

	//On Submitting
	form.submit(function(){
		if(validateTitle() & validateCtype() & validateStype())
			return true
		else
			return false;
	});

	function validateTitle(){
		//if it's NOT valid
		if(title.val().length < 3){
			title.addClass("error");
			return false;
		}
		//if it's valid
		else{
			title.removeClass("error");
			return true;
		}
	}

	function validateCtype(){
		//it's NOT valid
		if(ctype.val() == ""){
			ctype.addClass("error");
			return false;
		}
		//it's valid
		else{
			ctype.removeClass("error");
			return true;
		}
	}

	function validateStype(){
		//it's NOT valid
		if(section.val() == ""){
			section.addClass("error");
			return false;
		}
		//it's valid
		else{
			section.removeClass("error");
			return true;
		}
	}
});