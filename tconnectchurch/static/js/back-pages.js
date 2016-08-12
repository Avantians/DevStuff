$(document).ready(function(){
	//global vars
	var form 				= $("#stylizedForm");
	var title 				= $("#title");
	//var fulltxt 				= $("#fulltxt");

	//On blur
	title.blur(validateTitle);
	//fulltxt.blur(validateFulltxt);

	//On key press
	title.keyup(validateTitle);
	//fulltxt.keyup(validateFulltxt);

	//On Submitting
	form.submit(function(){
		if(validateTitle()){
			return true
		}
		else{
			return false;
		}
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

	function validateFulltxt(){
		//it's NOT valid
		if(fulltxt.val().length <4){
			fulltxt.addClass("error");
			return false;
		}
		//it's valid
		else{
			fulltxt.removeClass("error");
			return true;
		}
	}
});