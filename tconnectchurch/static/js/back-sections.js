$(document).ready(function(){
	//global vars
	var form 			= $("#stylizedForm");
	var title 			= $("#title");
	var tname 		= $("#tname");
	var stype		 	= $("#stype");

	//On blur
	title.blur(validateTitle);
	tname.blur(validateTname);
	stype.blur(validateStype);

	//On key press
	title.keyup(validateTitle);
	tname.keyup(validateTname);
	stype.keyup(validateStype);

	//On Submitting
	form.submit(function(){
		if(validateTitle() & validateTname() & validateStype())
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

	function validateTname(){
		//it's NOT valid
		if(tname.val().length <4){
			tname.addClass("error");
			return false;
		}
		//it's valid
		else{
			tname.removeClass("error");
			return true;
		}
	}

	function validateStype(){
		//it's NOT valid
		if(stype.val() == ""){
			stype.addClass("error");
			return false;
		}
		//it's valid
		else{
			stype.removeClass("error");
			return true;
		}
	}
});