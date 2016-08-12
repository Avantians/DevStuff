$(document).ready(function(){
	//global vars
	var form 				= $("#stylizedForm");
	var title 				= $("#title");

	//On blur
	title.blur(validateTitle);

	//On key press
	title.keyup(validateTitle);

	//On Submitting
	form.submit(function(){
		if(validateTitle())
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
});