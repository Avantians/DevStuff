$(document).ready(function(){
	//global vars
	var form 				= $("#stylizedForm");
	var title 				= $("#title");
	var menutype		 = $("#menutype");
	var alias 				= $("#alias");

	//On blur
	title.blur(validateTitle);
	menutype.blur(validateMenutype);
	alias.blur(validateAlias);

	//On key press
	title.keyup(validateTitle);
	menutype.keyup(validateMenutype);
	alias.keyup(validateAlias);

	//On Submitting
	form.submit(function(){
		if(validateTitle() & validateMenutype() & validateAlias())
			return true
		else
			return false;
	});

	function validateTitle(){
		//if it's NOT valid
		if(title.val().length < 2){
			title.addClass("error");
			return false;
		}
		//if it's valid
		else{
			title.removeClass("error");
			return true;
		}
	}

	function validateAlias(){
		//it's NOT valid
		if(alias.val().length <4){
			alias.addClass("error");
			return false;
		}
		//it's valid
		else{
			alias.removeClass("error");
			return true;
		}
	}

	function validateMenutype(){
		//it's NOT valid
		if(menutype.val() == ""){
			menutype.addClass("error");
			return false;
		}
		//it's valid
		else{
			menutype.removeClass("error");
			return true;
		}
	}
});